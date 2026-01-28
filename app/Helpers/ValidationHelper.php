<?php
/**
 * Validation Helper
 * ตรวจสอบความถูกต้องของข้อมูลที่กรอก
 * 
 * PHP 8.0+
 */

class ValidationHelper
{

    /**
     * ตรวจสอบข้อมูลทางการเงิน
     * 
     * @param array $data
     * @return array - คืนค่า array ของ errors (ถ้าไม่มี error คืนค่า [])
     */
    public static function validateFinancialData(array $data): array
    {
        $errors = [];

        // ตรวจสอบข้อมูลพื้นฐาน
        if (empty($data['coop_id'])) {
            $errors[] = 'กรุณาเลือกสหกรณ์';
        }

        if (empty($data['report_date'])) {
            $errors[] = 'กรุณาระบุวันที่รายงาน';
        }

        if (empty($data['fiscal_year'])) {
            $errors[] = 'กรุณาระบุปีงบประมาณ';
        }

        // ตรวจสอบงบดุล (Balance Sheet)
        if (isset($data['total_assets']) && $data['total_assets'] < 0) {
            $errors[] = 'สินทรัพย์รวมต้องไม่น้อยกว่า 0';
        }

        if (isset($data['total_liabilities']) && $data['total_liabilities'] < 0) {
            $errors[] = 'หนี้สินรวมต้องไม่น้อยกว่า 0';
        }

        if (isset($data['total_equity']) && $data['total_equity'] < 0) {
            $errors[] = 'ทุนรวมต้องไม่น้อยกว่า 0';
        }

        // ตรวจสอบสมการบัญชี: สินทรัพย์ = หนี้สิน + ทุน
        if (isset($data['total_assets'], $data['total_liabilities'], $data['total_equity'])) {
            $total_assets = floatval($data['total_assets']);
            $total_liabilities = floatval($data['total_liabilities']);
            $total_equity = floatval($data['total_equity']);

            $difference = abs($total_assets - ($total_liabilities + $total_equity));

            // อนุญาตให้ต่างกันได้ไม่เกิน 0.01 (เพื่อป้องกัน floating point error)
            if ($difference > 0.01) {
                $errors[] = 'สมการบัญชีไม่สมดุล: สินทรัพย์รวม (' . number_format($total_assets, 2) . ') ≠ หนี้สิน + ทุน (' . number_format($total_liabilities + $total_equity, 2) . ')';
            }
        }

        // ตรวจสอบงบกำไรขาดทุน (Income Statement)
        if (isset($data['total_revenue']) && $data['total_revenue'] < 0) {
            $errors[] = 'รายได้รวมต้องไม่น้อยกว่า 0';
        }

        if (isset($data['total_cost']) && $data['total_cost'] < 0) {
            $errors[] = 'ต้นทุนรวมต้องไม่น้อยกว่า 0';
        }

        // ตรวจสอบจำนวนสมาชิก
        if (isset($data['total_members_period']) && $data['total_members_period'] < 0) {
            $errors[] = 'จำนวนสมาชิกต้องไม่น้อยกว่า 0';
        }

        return $errors;
    }

    /**
     * ตรวจสอบตัวเลข
     * 
     * @param mixed $value
     * @return float
     */
    public static function sanitizeNumber(mixed $value): float
    {
        // ลบ comma ออก
        $value = str_replace(',', '', $value);

        // แปลงเป็น float
        return floatval($value);
    }

    /**
     * ตรวจสอบวันที่
     * 
     * @param string $date
     * @return bool
     */
    public static function validateDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * ตรวจสอบว่าเป็นตัวเลขบวก
     * 
     * @param mixed $value
     * @return bool
     */
    public static function isPositiveNumber(mixed $value): bool
    {
        return is_numeric($value) && floatval($value) >= 0;
    }

    /**
     * Sanitize array ของตัวเลข
     * 
     * @param array $data
     * @param array $fields
     * @return array
     */
    public static function sanitizeNumberFields(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $data[$field] = self::sanitizeNumber($data[$field]);
            }
        }

        return $data;
    }

    /**
     * ตรวจสอบข้อมูลสหกรณ์
     * 
     * @param array $data
     * @return array
     */
    public static function validateCoopData(array $data): array
    {
        $errors = [];

        if (empty($data['coop_name'])) {
            $errors[] = 'กรุณาระบุชื่อสหกรณ์';
        }

        if (empty($data['coop_code'])) {
            $errors[] = 'กรุณาระบุรหัสสหกรณ์';
        }

        if (empty($data['coop_type'])) {
            $errors[] = 'กรุณาเลือกประเภทสหกรณ์';
        }

        return $errors;
    }

    /**
     * สร้างข้อความ error เป็น HTML
     * 
     * @param array $errors
     * @return string
     */
    public static function formatErrors(array $errors): string
    {
        if (empty($errors)) {
            return '';
        }

        $html = '<div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">';
        $html .= '<div class="font-medium mb-2">พบข้อผิดพลาด:</div>';
        $html .= '<ul class="list-disc list-inside space-y-1">';

        foreach ($errors as $error) {
            $html .= '<li>' . sanitize($error) . '</li>';
        }

        $html .= '</ul></div>';

        return $html;
    }
}