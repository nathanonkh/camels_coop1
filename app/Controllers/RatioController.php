<?php
/**
 * Ratio Controller
 * คำนวณและแสดงอัตราส่วนทางการเงิน
 * 
 * PHP 8.0+
 */

require_once APP_PATH . '/Middleware/AuthMiddleware.php';
require_once APP_PATH . '/Models/FinancialData.php';
require_once APP_PATH . '/Models/FinancialRatio.php';
require_once APP_PATH . '/Helpers/RatioCalculator.php';

class RatioController
{

    private FinancialData $financialModel;
    private FinancialRatio $ratioModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->financialModel = new FinancialData();
        $this->ratioModel = new FinancialRatio();
    }

    /**
     * แสดงหน้าอัตราส่วนทางการเงิน
     * 
     * @return void
     */
    public function index(): void
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
            setFlashMessage('error', 'ไม่พบข้อมูลทางการเงินสำหรับสหกรณ์นี้');
            redirect(url('financial/input'));
            return;
        }

        // คำนวณอัตราส่วน
        $balanceSheet = $financialData['balance_sheet'];
        $incomeStatement = $financialData['income_statement'] ?? [];

        $ratios = RatioCalculator::calculateAll($balanceSheet, $incomeStatement);

        // บันทึกอัตราส่วนลงฐานข้อมูล
        $this->ratioModel->saveRatios($coopId, $reportDate, $ratios);

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

        // Log activity
        $this->logActivity($coopId, 'calculate_ratios', 'คำนวณอัตราส่วนทางการเงิน');

        // ตั้งค่า Page
        $pageTitle = 'อัตราส่วนทางการเงิน';
        $currentPage = 'ratio';

        // แสดงหน้า View
        require_once APP_PATH . '/Views/layouts/header.php';
        require_once APP_PATH . '/Views/ratio/index.php';
        require_once APP_PATH . '/Views/layouts/footer.php';
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
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ];

            $db->insert('activity_logs', $data);

        } catch (Exception $e) {
            error_log("Log Activity Error: " . $e->getMessage());
        }
    }

    /**
     * Export อัตราส่วนเป็น JSON (สำหรับ API)
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

        $ratios = $this->ratioModel->getRatios($coopId, $reportDate);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($ratios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}