<?php
/**
 * Financial Ratio Model
 * จัดการอัตราส่วนทางการเงิน
 * 
 * PHP 8.0+
 */

class FinancialRatio extends Database
{

    /**
     * บันทึกอัตราส่วนทางการเงิน
     * 
     * @param int $coopId
     * @param string $reportDate
     * @param array $ratios
     * @return bool|string
     */
    public function saveRatios(int $coopId, string $reportDate, array $ratios): bool|string
    {
        try {
            // เตรียมข้อมูลสำหรับบันทึก
            $data = [
                'coop_id' => $coopId,
                'report_date' => $reportDate,
                'calculated_date' => date('Y-m-d H:i:s'),
            ];

            // รวม ratios ทั้งหมดเข้า data - ใช้ชื่อฟิลด์ที่ตรงกับฐานข้อมูล
            foreach ($ratios as $dimension => $dimensionRatios) {
                foreach ($dimensionRatios as $code => $ratio) {
                    if (isset($ratio['value'])) {
                        $data[$code] = $ratio['value'];
                    }
                }
            }

            // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
            $existing = $this->getRatios($coopId, $reportDate);

            if ($existing) {
                // อัปเดต
                $where = ['coop_id' => $coopId, 'report_date' => $reportDate];
                unset($data['coop_id']);
                unset($data['report_date']);

                return $this->update('financial_ratios', $data, $where);
            } else {
                // สร้างใหม่
                return $this->insert('financial_ratios', $data);
            }

        } catch (Exception $e) {
            error_log("Save Ratios Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงอัตราส่วนทางการเงิน
     * 
     * @param int $coopId
     * @param string $reportDate
     * @return array|false
     */
    public function getRatios(int $coopId, string $reportDate): array|false
    {
        try {
            $sql = "SELECT * FROM financial_ratios 
                    WHERE coop_id = :coop_id AND report_date = :report_date 
                    LIMIT 1";

            $this->query($sql);
            $this->bind(':coop_id', $coopId);
            $this->bind(':report_date', $reportDate);

            return $this->fetch();

        } catch (Exception $e) {
            error_log("Get Ratios Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงอัตราส่วนตามมิติ
     * 
     * @param int $coopId
     * @param string $reportDate
     * @param string $dimension
     * @return array
     */
    public function getRatiosByDimension(int $coopId, string $reportDate, string $dimension): array
    {
        $allRatios = $this->getRatios($coopId, $reportDate);

        if (!$allRatios) {
            return [];
        }

        // กำหนด field names ตามมิติ - ตรงกับฐานข้อมูล
        $dimensionFields = [
            'capital' => [
                'debt_to_equity',
                'reserve_to_assets',
                'return_on_equity',
                'equity_growth_rate',
                'debt_growth_rate'
            ],
            'asset' => [
                'overdue_ratio',
                'asset_turnover',
                'return_on_assets',
                'asset_growth_rate'
            ],
            'management' => [
                'business_growth_rate'
            ],
            'earning' => [
                'profit_per_member',
                'savings_per_member',
                'debt_per_member',
                'operating_expense_ratio',
                'net_profit_margin',
                'reserve_growth_rate',
                'other_reserve_growth_rate',
                'profit_growth_rate'
            ],
            'liquidity' => [
                'current_ratio',
                'inventory_turnover',
                'average_inventory_days',
                'collection_ratio'
            ]
        ];

        $fields = $dimensionFields[$dimension] ?? [];
        $result = [];

        foreach ($fields as $field) {
            if (isset($allRatios[$field]) && $allRatios[$field] !== null) {
                $result[$field] = $allRatios[$field];
            }
        }

        return $result;
    }

    /**
     * ดึง Benchmarks จากฐานข้อมูล
     * 
     * @param int|null $coopType
     * @param int|null $coopSize
     * @return array
     */
    public function getBenchmarks(?int $coopType = null, ?int $coopSize = null): array
    {
        try {
            $sql = "SELECT * FROM ratio_benchmarks 
                    WHERE (coop_type IS NULL OR coop_type = :coop_type)
                    AND (coop_size IS NULL OR coop_size = :coop_size)";

            $this->query($sql);
            $this->bind(':coop_type', $coopType);
            $this->bind(':coop_size', $coopSize);

            $results = $this->fetchAll();

            // จัดรูปแบบเป็น array โดยใช้ ratio_code เป็น key
            $benchmarks = [];
            foreach ($results as $row) {
                $benchmarks[$row['ratio_code']] = $row;
            }

            return $benchmarks;

        } catch (Exception $e) {
            error_log("Get Benchmarks Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงประวัติอัตราส่วน
     * 
     * @param int $coopId
     * @param int $limit
     * @return array
     */
    public function getRatioHistory(int $coopId, int $limit = 12): array
    {
        try {
            $sql = "SELECT * FROM financial_ratios 
                    WHERE coop_id = :coop_id 
                    ORDER BY report_date DESC 
                    LIMIT :limit";

            $this->query($sql);
            $this->bind(':coop_id', $coopId);
            $this->bind(':limit', $limit, PDO::PARAM_INT);

            return $this->fetchAll();

        } catch (Exception $e) {
            error_log("Get Ratio History Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ลบอัตราส่วน
     * 
     * @param int $coopId
     * @param string $reportDate
     * @return bool
     */
    public function deleteRatios(int $coopId, string $reportDate): bool
    {
        try {
            return $this->delete('financial_ratios', [
                'coop_id' => $coopId,
                'report_date' => $reportDate
            ]);

        } catch (Exception $e) {
            error_log("Delete Ratios Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * คำนวณอัตราส่วนโดยใช้ Stored Procedure
     * 
     * @param int $coopId
     * @param string $reportDate
     * @return bool
     */
    public function calculateRatiosUsingProcedure(int $coopId, string $reportDate): bool
    {
        try {
            $sql = "CALL sp_calculate_ratios(:coop_id, :report_date)";
            $this->query($sql);
            $this->bind(':coop_id', $coopId);
            $this->bind(':report_date', $reportDate);

            return $this->execute();

        } catch (Exception $e) {
            error_log("Calculate Ratios Using Procedure Error: " . $e->getMessage());
            return false;
        }
    }
}