<?php
/**
 * CAMELS Assessment Model
 * จัดการผลการประเมิน CAMELS
 * 
 * PHP 8.0+
 */

class CamelsAssessment extends Database
{

    /**
     * บันทึกผลการประเมิน CAMELS
     * 
     * @param array $data
     * @return bool|string
     */
    public function saveAssessment(array $data): bool|string
    {
        try {
            $insertData = [
                'coop_id' => $data['coop_id'],
                'report_date' => $data['report_date'],
                'assessed_date' => date('Y-m-d H:i:s'),

                // คะแนนแต่ละมิติ (1-5)
                'capital_score' => $this->convertScoreToRating($data['capital_score'] ?? 0),
                'asset_score' => $this->convertScoreToRating($data['asset_score'] ?? 0),
                'management_score' => $this->convertScoreToRating($data['management_score'] ?? 0),
                'earning_score' => $this->convertScoreToRating($data['earning_score'] ?? 0),
                'liquidity_score' => $this->convertScoreToRating($data['liquidity_score'] ?? 0),
                'sensitivity_score' => NULL, // จะเพิ่มในอนาคต

                // คะแนนรวม
                'total_score' => $data['overall_score'] ?? 0,
                'overall_rating' => $data['rating_text'] ?? '',
                'risk_level' => $data['risk_level'] ?? 'ปานกลาง',

                // คำแนะนำ (บันทึกเป็น JSON)
                'strengths' => json_encode($data['strengths'] ?? [], JSON_UNESCAPED_UNICODE),
                'weaknesses' => json_encode($data['improvements'] ?? [], JSON_UNESCAPED_UNICODE),
                'recommendations' => json_encode($data['priorities'] ?? [], JSON_UNESCAPED_UNICODE),
            ];

            // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
            $existing = $this->getAssessment($data['coop_id'], $data['report_date']);

            if ($existing) {
                // อัปเดต
                $where = ['coop_id' => $data['coop_id'], 'report_date' => $data['report_date']];
                unset($insertData['coop_id']);
                unset($insertData['report_date']);

                return $this->update('camels_assessment', $insertData, $where);
            } else {
                // สร้างใหม่
                return $this->insert('camels_assessment', $insertData);
            }

        } catch (Exception $e) {
            error_log("Save Assessment Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * แปลงคะแนน (0-100) เป็น Rating (1-5)
     * 1 = ดีมาก, 2 = ดี, 3 = พอใช้, 4 = ควรปรับปรุง, 5 = มีปัญหา
     */
    private function convertScoreToRating(float $score): int
    {
        if ($score >= 85)
            return 1; // ดีมาก
        if ($score >= 70)
            return 2; // ดี
        if ($score >= 55)
            return 3; // พอใช้
        if ($score >= 40)
            return 4; // ควรปรับปรุง
        return 5; // มีปัญหา
    }

    /**
     * แปลง Rating (1-5) เป็นคะแนน (0-100)
     */
    private function convertRatingToScore(int $rating): float
    {
        return match ($rating) {
            1 => 90.0,  // ดีมาก
            2 => 77.5,  // ดี
            3 => 62.5,  // พอใช้
            4 => 47.5,  // ควรปรับปรุง
            5 => 25.0,  // มีปัญหา
            default => 0.0
        };
    }

    /**
     * ดึงผลการประเมิน CAMELS
     * 
     * @param int $coopId
     * @param string $reportDate
     * @return array|false
     */
    public function getAssessment(int $coopId, string $reportDate): array|false
    {
        try {
            $sql = "SELECT * FROM camels_assessment 
                    WHERE coop_id = :coop_id AND report_date = :report_date 
                    LIMIT 1";

            $this->query($sql);
            $this->bind(':coop_id', $coopId);
            $this->bind(':report_date', $reportDate);

            $result = $this->fetch();

            if ($result) {
                // แปลง JSON strings กลับเป็น arrays
                $result['strengths'] = json_decode($result['strengths'] ?? '[]', true);
                $result['weaknesses'] = json_decode($result['weaknesses'] ?? '[]', true);
                $result['recommendations'] = json_decode($result['recommendations'] ?? '[]', true);
            }

            return $result;

        } catch (Exception $e) {
            error_log("Get Assessment Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงผลการประเมินล่าสุด
     * 
     * @param int $coopId
     * @return array|false
     */
    public function getLatestAssessment(int $coopId): array|false
    {
        try {
            $sql = "SELECT * FROM camels_assessment 
                    WHERE coop_id = :coop_id 
                    ORDER BY assessed_date DESC 
                    LIMIT 1";

            $this->query($sql);
            $this->bind(':coop_id', $coopId);

            $result = $this->fetch();

            if ($result) {
                // แปลง JSON strings กลับเป็น arrays
                $result['strengths'] = json_decode($result['strengths'] ?? '[]', true);
                $result['weaknesses'] = json_decode($result['weaknesses'] ?? '[]', true);
                $result['recommendations'] = json_decode($result['recommendations'] ?? '[]', true);
            }

            return $result;

        } catch (Exception $e) {
            error_log("Get Latest Assessment Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงประวัติการประเมิน
     * 
     * @param int $coopId
     * @param int $limit
     * @return array
     */
    public function getAssessmentHistory(int $coopId, int $limit = 12): array
    {
        try {
            $sql = "SELECT ca.*, ci.coop_name, ci.coop_code
                    FROM camels_assessment ca
                    LEFT JOIN coop_info ci ON ca.coop_id = ci.coop_id
                    WHERE ca.coop_id = :coop_id 
                    ORDER BY ca.report_date DESC 
                    LIMIT :limit";

            $this->query($sql);
            $this->bind(':coop_id', $coopId);
            $this->bind(':limit', $limit, PDO::PARAM_INT);

            $results = $this->fetchAll();

            // แปลง ratings เป็น scores สำหรับแสดงผล
            foreach ($results as &$result) {
                // ใช้ total_score ที่บันทึกไว้แล้วแทนการแปลง
                // ถ้าไม่มี total_score ให้แปลงจาก ratings
                if (!isset($result['total_score']) || $result['total_score'] == 0) {
                    $result['capital_score'] = $this->convertRatingToScore((int) ($result['capital_score'] ?? 0));
                    $result['asset_score'] = $this->convertRatingToScore((int) ($result['asset_score'] ?? 0));
                    $result['management_score'] = $this->convertRatingToScore((int) ($result['management_score'] ?? 0));
                    $result['earning_score'] = $this->convertRatingToScore((int) ($result['earning_score'] ?? 0));
                    $result['liquidity_score'] = $this->convertRatingToScore((int) ($result['liquidity_score'] ?? 0));
                }
            }

            return $results;

        } catch (Exception $e) {
            error_log("Get Assessment History Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงสถิติการประเมิน
     * 
     * @param int $coopId
     * @return array
     */
    public function getAssessmentStats(int $coopId): array
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_assessments,
                        AVG(total_score) as avg_score,
                        MAX(total_score) as max_score,
                        MIN(total_score) as min_score
                    FROM camels_assessment 
                    WHERE coop_id = :coop_id";

            $this->query($sql);
            $this->bind(':coop_id', $coopId);

            return $this->fetch() ?: [];

        } catch (Exception $e) {
            error_log("Get Assessment Stats Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ลบผลการประเมิน
     * 
     * @param int $coopId
     * @param string $reportDate
     * @return bool
     */
    public function deleteAssessment(int $coopId, string $reportDate): bool
    {
        try {
            return $this->delete('camels_assessment', [
                'coop_id' => $coopId,
                'report_date' => $reportDate
            ]);

        } catch (Exception $e) {
            error_log("Delete Assessment Error: " . $e->getMessage());
            return false;
        }
    }
}