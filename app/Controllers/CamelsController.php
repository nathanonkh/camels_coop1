<?php
/**
 * CAMELS Controller
 * แสดงผลการประเมิน CAMELS พร้อม Visualization
 * 
 * PHP 8.0+
 */

require_once APP_PATH . '/Middleware/AuthMiddleware.php';
require_once APP_PATH . '/Models/FinancialData.php';
require_once APP_PATH . '/Models/FinancialRatio.php';
require_once APP_PATH . '/Models/CamelsAssessment.php';
require_once APP_PATH . '/Helpers/RatioCalculator.php';
require_once APP_PATH . '/Helpers/CamelsCalculator.php';

class CamelsController
{

    private FinancialData $financialModel;
    private FinancialRatio $ratioModel;
    private CamelsAssessment $assessmentModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->financialModel = new FinancialData();
        $this->ratioModel = new FinancialRatio();
        $this->assessmentModel = new CamelsAssessment();
    }

    /**
     * แสดงผลการประเมิน CAMELS
     * 
     * @return void
     */
    public function showResult(): void
    {
        // ตรวจสอบการ Login
        AuthMiddleware::check();

        // ดึงพารามิเตอร์
        $coopId = (int) ($_GET['coop_id'] ?? 0);
        $reportDate = $_GET['report_date'] ?? '';

        // ตรวจสอบพารามิเตอร์
        if (!$coopId || !$reportDate) {
            setFlashMessage('error', 'กรุณาระบุสหกรณ์และวันที่รายงาน');
            redirect(url('financial/input'));
            return;
        }

        // ดึงข้อมูลทางการเงิน
        $financialData = $this->financialModel->getAllFinancialData($coopId, $reportDate);

        if (empty($financialData['balance_sheet'])) {
            setFlashMessage('error', 'ไม่พบข้อมูลทางการเงิน');
            redirect(url('financial/input'));
            return;
        }

        // คำนวณอัตราส่วน
        $balanceSheet = $financialData['balance_sheet'];
        $incomeStatement = $financialData['income_statement'] ?? [];

        $ratios = RatioCalculator::calculateAll($balanceSheet, $incomeStatement);

        // ดึง Benchmarks
        $coopType = $this->getCoopType($coopId);
        $benchmarks = $this->ratioModel->getBenchmarks($coopType, null);

        // ดึงข้อมูลสหกรณ์
        $coopInfo = $this->getCoopInfo($coopId);

        // เพิ่ม Rating ให้แต่ละอัตราส่วน
        foreach ($ratios as $dimension => &$dimensionRatios) {
            foreach ($dimensionRatios as $code => &$ratio) {
                $benchmark = $benchmarks[$code] ?? null;
                $ratio['rating'] = RatioCalculator::getRating($code, $ratio['value'], $benchmark);
                $ratio['benchmark'] = $benchmark;
            }
        }

        // คำนวณคะแนน CAMELS
        $camelsResult = CamelsCalculator::calculateAll($ratios);

        // บันทึกผลการประเมิน
        $this->saveAssessmentResult($coopId, $reportDate, $camelsResult);

        // Log activity
        $this->logActivity($coopId, 'camels_assessment', 'ประเมินผล CAMELS');

        // ตั้งค่า Page
        $pageTitle = 'ผลการวิเคราะห์ CAMELS';
        $currentPage = 'camels-result';

        // แสดงหน้า View
        require_once APP_PATH . '/Views/layouts/header.php';
        require_once APP_PATH . '/Views/camels/result.php';
        require_once APP_PATH . '/Views/layouts/footer.php';
    }

    /**
     * บันทึกผลการประเมิน
     * 
     * @param int $coopId
     * @param string $reportDate
     * @param array $camelsResult
     * @return void
     */
    private function saveAssessmentResult(int $coopId, string $reportDate, array $camelsResult): void
    {
        $data = [
            'coop_id' => $coopId,
            'report_date' => $reportDate,

            'capital_score' => $camelsResult['scores']['capital']['score'],
            'asset_score' => $camelsResult['scores']['asset']['score'],
            'management_score' => $camelsResult['scores']['management']['score'],
            'earning_score' => $camelsResult['scores']['earning']['score'],
            'liquidity_score' => $camelsResult['scores']['liquidity']['score'],
            'sensitivity_score' => 0, // ค่าเริ่มต้น (ยังไม่ได้คำนวณ Sensitivity)

            'total_score' => $camelsResult['overall_score'],
            'overall_rating' => $camelsResult['rating']['name'],
            'risk_level' => $this->getRiskLevel($camelsResult['overall_score']),

            'strengths' => json_encode($camelsResult['recommendations']['strengths'], JSON_UNESCAPED_UNICODE),
            'weaknesses' => json_encode($camelsResult['recommendations']['improvements'], JSON_UNESCAPED_UNICODE),
            'recommendations' => json_encode($camelsResult['recommendations']['priorities'], JSON_UNESCAPED_UNICODE),
        ];

        $this->assessmentModel->saveAssessment($data);
    }

    /**
     * กำหนดระดับความเสี่ยง
     * 
     * @param float $overallScore
     * @return string
     */
    private function getRiskLevel(float $overallScore): string
    {
        if ($overallScore >= 85) {
            return 'ต่ำ';
        } elseif ($overallScore >= 70) {
            return 'ต่ำ';
        } elseif ($overallScore >= 55) {
            return 'ปานกลาง';
        } elseif ($overallScore >= 40) {
            return 'สูง';
        } else {
            return 'สูงมาก';
        }
    }

    /**
     * ดึงประเภทสหกรณ์
     * 
     * @param int $coopId
     * @return int|null
     */
    private function getCoopType(int $coopId): ?int
    {
        $db = new Database();

        try {
            $sql = "SELECT coop_type FROM coop_info WHERE coop_id = :coop_id LIMIT 1";
            $db->query($sql);
            $db->bind(':coop_id', $coopId);
            $result = $db->fetch();

            return $result ? (int) $result['coop_type'] : null;

        } catch (Exception $e) {
            error_log("Get Coop Type Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงข้อมูลสหกรณ์
     * 
     * @param int $coopId
     * @return array|false
     */
    private function getCoopInfo(int $coopId): array|false
    {
        $db = new Database();

        try {
            $sql = "SELECT * FROM coop_info WHERE coop_id = :coop_id LIMIT 1";
            $db->query($sql);
            $db->bind(':coop_id', $coopId);

            return $db->fetch();

        } catch (Exception $e) {
            error_log("Get Coop Info Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * บันทึก Activity Log
     * 
     * @param int $coopId
     * @param string $action
     * @param string $description
     * @return void
     */
    private function logActivity(int $coopId, string $action, string $description): void
    {
        $db = new Database();

        try {
            $data = [
                'user_id' => $_SESSION['user_id'] ?? 0,
                'action' => $action,
                'description' => $description . ' (สหกรณ์ ID: ' . $coopId . ')',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            ];

            $db->insert('activity_logs', $data);

        } catch (Exception $e) {
            error_log("Log Activity Error: " . $e->getMessage());
        }
    }

    /**
     * แสดง Trend Analysis
     * 
     * @return void
     */
    public function showTrend(): void
    {
        // ตรวจสอบการ Login
        AuthMiddleware::check();

        // ดึงพารามิเตอร์
        $coopId = (int) ($_GET['coop_id'] ?? 0);

        if (!$coopId) {
            setFlashMessage('error', 'กรุณาระบุสหกรณ์');
            redirect(url('dashboard'));
            return;
        }

        // ดึงประวัติการประเมิน
        $history = $this->assessmentModel->getAssessmentHistory($coopId, 12);

        if (empty($history)) {
            setFlashMessage('warning', 'ยังไม่มีข้อมูลประวัติการประเมิน');
            redirect(url('dashboard'));
            return;
        }

        // ดึงสถิติ
        $stats = $this->assessmentModel->getAssessmentStats($coopId);

        // ดึงข้อมูลสหกรณ์
        $coopInfo = $this->getCoopInfo($coopId);

        // ตั้งค่า Page
        $pageTitle = 'แนวโน้มการพัฒนา';
        $currentPage = 'trend';

        // แสดงหน้า View
        require_once APP_PATH . '/Views/layouts/header.php';
        require_once APP_PATH . '/Views/charts/trend.php';
        require_once APP_PATH . '/Views/layouts/footer.php';
    }

    /**
     * Export ผลการประเมินเป็น JSON
     * 
     * @return void
     */
    public function exportJson(): void
    {
        AuthMiddleware::check();

        $coopId = (int) ($_GET['coop_id'] ?? 0);
        $reportDate = $_GET['report_date'] ?? '';

        if (!$coopId || !$reportDate) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing parameters']);
            return;
        }

        $assessment = $this->assessmentModel->getAssessment($coopId, $reportDate);

        if ($assessment) {
            // แปลง JSON strings กลับเป็น arrays
            $assessment['strengths'] = json_decode($assessment['strengths'] ?? '[]', true);
            $assessment['weaknesses'] = json_decode($assessment['weaknesses'] ?? '[]', true);
            $assessment['recommendations'] = json_decode($assessment['recommendations'] ?? '[]', true);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($assessment, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}