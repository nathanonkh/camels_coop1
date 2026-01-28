<?php
/**
 * Report Controller
 * สร้างและ Export รายงาน PDF
 * 
 * PHP 8.0+
 */

require_once APP_PATH . '/Middleware/AuthMiddleware.php';
require_once APP_PATH . '/Models/FinancialData.php';
require_once APP_PATH . '/Models/FinancialRatio.php';
require_once APP_PATH . '/Models/CamelsAssessment.php';
require_once APP_PATH . '/Helpers/RatioCalculator.php';
require_once APP_PATH . '/Helpers/CamelsCalculator.php';
require_once APP_PATH . '/Helpers/PdfHelper.php';

class ReportController
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
     * สร้างและแสดง PDF Report
     * 
     * @return void
     */
    public function generatePdf(): void
    {
        // ตรวจสอบการ Login
        AuthMiddleware::check();

        // ดึงพารามิเตอร์
        $coopId = (int) ($_GET['coop_id'] ?? 0);
        $reportDate = $_GET['report_date'] ?? '';

        // ตรวจสอบพารามิเตอร์
        if (!$coopId || !$reportDate) {
            setFlashMessage('error', 'กรุณาระบุสหกรณ์และวันที่รายงาน');
            redirect(url('dashboard'));
            return;
        }

        // ดึงข้อมูลทางการเงิน
        $financialData = $this->financialModel->getAllFinancialData($coopId, $reportDate);

        if (empty($financialData['balance_sheet'])) {
            setFlashMessage('error', 'ไม่พบข้อมูลทางการเงิน');
            redirect(url('dashboard'));
            return;
        }

        // คำนวณอัตราส่วน
        $balanceSheet = $financialData['balance_sheet'];
        $incomeStatement = $financialData['income_statement'] ?? [];

        $ratios = RatioCalculator::calculateAll($balanceSheet, $incomeStatement);

        // ดึง Benchmarks
        $coopType = $this->getCoopType($coopId);
        $benchmarks = $this->ratioModel->getBenchmarks($coopType, null);

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

        // ดึงข้อมูลสหกรณ์
        $coopInfo = $this->getCoopInfo($coopId);

        // เตรียมข้อมูลสำหรับ PDF
        $data = [
            'coopInfo' => $coopInfo,
            'reportDate' => $reportDate,
            'camelsResult' => $camelsResult,
            'ratios' => $ratios,
            'financialData' => $financialData
        ];

        // สร้าง HTML
        $html = PdfHelper::generateCamelsReportHtml($data);

        // บันทึก Activity Log
        $this->logActivity($coopId, 'export_pdf', 'Export รายงาน PDF');

        // แสดง HTML (ให้ Browser จัดการ Print to PDF)
        echo $html;
    }

    /**
     * ดาวน์โหลด PDF โดยตรง (ถ้าต้องการ)
     * 
     * @return void
     */
    public function downloadPdf(): void
    {
        // ตรวจสอบการ Login
        AuthMiddleware::check();

        // เรียกใช้ generatePdf() แล้ว trigger print dialog
        $this->generatePdf();

        // เพิ่ม JavaScript สำหรับ auto-print
        echo '<script>window.onload = function() { window.print(); };</script>';
    }

    /**
     * แสดงหน้าตัวอย่างรายงาน
     * 
     * @return void
     */
    public function preview(): void
    {
        // ตรวจสอบการ Login
        AuthMiddleware::check();

        $coopId = (int) ($_GET['coop_id'] ?? 0);
        $reportDate = $_GET['report_date'] ?? '';

        if (!$coopId || !$reportDate) {
            setFlashMessage('error', 'กรุณาระบุสหกรณ์และวันที่รายงาน');
            redirect(url('dashboard'));
            return;
        }

        // ใช้ generatePdf() เพื่อแสดงตัวอย่าง
        $this->generatePdf();
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
}