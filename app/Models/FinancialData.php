<?php
/**
 * Financial Data Model
 * จัดการข้อมูลทางการเงิน 5 มิติ (C-A-M-E-L)
 * 
 * PHP 8.0+
 */

class FinancialData extends Database
{

    /**
     * บันทึกข้อมูลงบดุล (Balance Sheet)
     * 
     * @param int $coopId
     * @param array $data
     * @return bool|string
     */
    public function saveBalanceSheet(int $coopId, array $data): bool|string
    {
        try {
            // เตรียมข้อมูลสำหรับบันทึก
            $insertData = [
                'coop_id' => $coopId,
                'report_date' => $data['report_date'],
                'report_period' => $data['report_period'] ?? 1,
                'fiscal_year' => $data['fiscal_year'],

                // สินทรัพย์หมุนเวียน
                'current_assets' => $data['current_assets'] ?? 0,
                'cash_and_cash_equivalent' => $data['cash_and_cash_equivalent'] ?? 0,
                'short_term_investments' => $data['short_term_investments'] ?? 0,
                'account_receivable_short' => $data['account_receivable_short'] ?? 0,
                'loan_receivable_short' => $data['loan_receivable_short'] ?? 0,
                'inventory' => $data['inventory'] ?? 0,
                'other_current_assets' => $data['other_current_assets'] ?? 0,

                // สินทรัพย์ไม่หมุนเวียน
                'long_term_investments' => $data['long_term_investments'] ?? 0,
                'loan_receivable_long' => $data['loan_receivable_long'] ?? 0,
                'account_receivable_long' => $data['account_receivable_long'] ?? 0,
                'fixed_assets' => $data['fixed_assets'] ?? 0,
                'intangible_assets' => $data['intangible_assets'] ?? 0,
                'other_non_current_assets' => $data['other_non_current_assets'] ?? 0,
                'total_assets' => $data['total_assets'] ?? 0,

                // หนี้สินหมุนเวียน
                'current_liabilities' => $data['current_liabilities'] ?? 0,
                'account_payable' => $data['account_payable'] ?? 0,
                'short_term_loan' => $data['short_term_loan'] ?? 0,
                'member_deposits' => $data['member_deposits'] ?? 0,
                'other_current_liabilities' => $data['other_current_liabilities'] ?? 0,

                // หนี้สินไม่หมุนเวียน
                'long_term_loan' => $data['long_term_loan'] ?? 0,
                'other_non_current_liabilities' => $data['other_non_current_liabilities'] ?? 0,
                'total_liabilities' => $data['total_liabilities'] ?? 0,

                // ทุน
                'share_capital' => $data['share_capital'] ?? 0,
                'legal_reserve' => $data['legal_reserve'] ?? 0,
                'other_reserve' => $data['other_reserve'] ?? 0,
                'retained_earnings' => $data['retained_earnings'] ?? 0,
                'total_equity' => $data['total_equity'] ?? 0,

                // ข้อมูลอื่นๆ
                'total_members_period' => $data['total_members_period'] ?? 0,
                'overdue_receivable' => $data['overdue_receivable'] ?? 0,
                'due_receivable' => $data['due_receivable'] ?? 0,
                'paid_on_time_receivable' => $data['paid_on_time_receivable'] ?? 0,
            ];

            // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
            $existing = $this->getBalanceSheet($coopId, $data['report_date']);

            if ($existing) {
                // อัปเดต
                $where = ['coop_id' => $coopId, 'report_date' => $data['report_date']];
                unset($insertData['coop_id']);
                unset($insertData['report_date']);

                return $this->update('financial_data', $insertData, $where);
            } else {
                // สร้างใหม่
                return $this->insert('financial_data', $insertData);
            }

        } catch (Exception $e) {
            error_log("Save Balance Sheet Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * บันทึกข้อมูลงบกำไรขาดทุน (Income Statement)
     * 
     * @param int $coopId
     * @param array $data
     * @return bool|string
     */
    public function saveIncomeStatement(int $coopId, array $data): bool|string
    {
        try {
            $insertData = [
                'coop_id' => $coopId,
                'report_date' => $data['report_date'],
                'report_period' => $data['report_period'] ?? 1,
                'fiscal_year' => $data['fiscal_year'],

                // รายได้
                'revenue_credit' => $data['revenue_credit'] ?? 0,
                'revenue_sales' => $data['revenue_sales'] ?? 0,
                'revenue_procurement' => $data['revenue_procurement'] ?? 0,
                'revenue_processing' => $data['revenue_processing'] ?? 0,
                'revenue_service' => $data['revenue_service'] ?? 0,
                'revenue_other' => $data['revenue_other'] ?? 0,
                'total_revenue' => $data['total_revenue'] ?? 0,

                // ต้นทุน
                'cost_credit' => $data['cost_credit'] ?? 0,
                'cost_sales' => $data['cost_sales'] ?? 0,
                'cost_procurement' => $data['cost_procurement'] ?? 0,
                'cost_processing' => $data['cost_processing'] ?? 0,
                'cost_service' => $data['cost_service'] ?? 0,
                'total_cost' => $data['total_cost'] ?? 0,

                // กำไรขั้นต้น
                'gross_profit' => $data['gross_profit'] ?? 0,

                // ค่าใช้จ่าย
                'operating_expenses' => $data['operating_expenses'] ?? 0,
                'personnel_expenses' => $data['personnel_expenses'] ?? 0,
                'administrative_expenses' => $data['administrative_expenses'] ?? 0,
                'depreciation' => $data['depreciation'] ?? 0,

                // กำไร/ขาดทุน
                'operating_income' => $data['operating_income'] ?? 0,
                'other_income' => $data['other_income'] ?? 0,
                'other_expenses' => $data['other_expenses'] ?? 0,
                'net_profit' => $data['net_profit'] ?? 0,

                // การจัดสรรกำไร
                'dividend' => $data['dividend'] ?? 0,
                'legal_reserve_allocation' => $data['legal_reserve_allocation'] ?? 0,
                'other_reserve_allocation' => $data['other_reserve_allocation'] ?? 0,
            ];

            // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
            $existing = $this->getIncomeStatement($coopId, $data['report_date']);

            if ($existing) {
                // อัปเดต
                $where = ['coop_id' => $coopId, 'report_date' => $data['report_date']];
                unset($insertData['coop_id']);
                unset($insertData['report_date']);

                return $this->update('income_statement', $insertData, $where);
            } else {
                // สร้างใหม่
                return $this->insert('income_statement', $insertData);
            }

        } catch (Exception $e) {
            error_log("Save Income Statement Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูลงบดุล
     * 
     * @param int $coopId
     * @param string $reportDate
     * @return array|false
     */
    public function getBalanceSheet(int $coopId, string $reportDate): array|false
    {
        try {
            $sql = "SELECT * FROM financial_data 
                    WHERE coop_id = :coop_id AND report_date = :report_date 
                    LIMIT 1";

            $this->query($sql);
            $this->bind(':coop_id', $coopId);
            $this->bind(':report_date', $reportDate);

            return $this->fetch();

        } catch (Exception $e) {
            error_log("Get Balance Sheet Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูลงบกำไรขาดทุน
     * 
     * @param int $coopId
     * @param string $reportDate
     * @return array|false
     */
    public function getIncomeStatement(int $coopId, string $reportDate): array|false
    {
        try {
            $sql = "SELECT * FROM income_statement 
                    WHERE coop_id = :coop_id AND report_date = :report_date 
                    LIMIT 1";

            $this->query($sql);
            $this->bind(':coop_id', $coopId);
            $this->bind(':report_date', $reportDate);

            return $this->fetch();

        } catch (Exception $e) {
            error_log("Get Income Statement Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูลทั้งหมด (งบดุล + งบกำไรขาดทุน)
     * 
     * @param int $coopId
     * @param string $reportDate
     * @return array
     */
    public function getAllFinancialData(int $coopId, string $reportDate): array
    {
        return [
            'balance_sheet' => $this->getBalanceSheet($coopId, $reportDate) ?: [],
            'income_statement' => $this->getIncomeStatement($coopId, $reportDate) ?: []
        ];
    }

    /**
     * ดึงรายการข้อมูลทางการเงินทั้งหมดของสหกรณ์
     * 
     * @param int $coopId
     * @param int $limit
     * @return array
     */
    public function getFinancialDataList(int $coopId, int $limit = 20): array
    {
        try {
            $sql = "SELECT fd.*, ci.coop_name, ci.coop_code
                    FROM financial_data fd
                    LEFT JOIN coop_info ci ON fd.coop_id = ci.coop_id
                    WHERE fd.coop_id = :coop_id
                    ORDER BY fd.report_date DESC
                    LIMIT :limit";

            $this->query($sql);
            $this->bind(':coop_id', $coopId);
            $this->bind(':limit', $limit, PDO::PARAM_INT);

            return $this->fetchAll();

        } catch (Exception $e) {
            error_log("Get Financial Data List Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ลบข้อมูลทางการเงิน
     * 
     * @param int $coopId
     * @param string $reportDate
     * @return bool
     */
    public function deleteFinancialData(int $coopId, string $reportDate): bool
    {
        try {
            $this->beginTransaction();

            // ลบงบดุล
            $this->delete('financial_data', [
                'coop_id' => $coopId,
                'report_date' => $reportDate
            ]);

            // ลบงบกำไรขาดทุน
            $this->delete('income_statement', [
                'coop_id' => $coopId,
                'report_date' => $reportDate
            ]);

            $this->commit();
            return true;

        } catch (Exception $e) {
            $this->rollback();
            error_log("Delete Financial Data Error: " . $e->getMessage());
            return false;
        }
    }
}