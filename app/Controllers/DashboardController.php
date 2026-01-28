<?php
/**
 * Dashboard Controller
 * แสดงหน้า Dashboard หลักของระบบ
 * 
 * PHP 8.0+
 */

require_once APP_PATH . '/Middleware/AuthMiddleware.php';
require_once APP_PATH . '/Models/User.php';

class DashboardController
{

    private User $userModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * แสดงหน้า Dashboard
     * 
     * @return void
     */
    public function index(): void
    {
        // ตรวจสอบการ Login
        AuthMiddleware::check();

        // ดึงข้อมูล User ปัจจุบัน
        $currentUser = getCurrentUser();

        // ดึงสถิติต่างๆ
        $stats = $this->getStatistics($currentUser);

        // ดึงข้อมูลการวิเคราะห์ล่าสุด
        $recentAnalysis = $this->getRecentAnalysis($currentUser);

        // ดึง Activity Logs ล่าสุด
        $recentActivities = $this->getRecentActivities($currentUser['user_id']);

        // ตั้งค่า Page Title
        $pageTitle = 'หน้าแรก';
        $currentPage = 'dashboard';

        // แสดงหน้า Dashboard
        require_once APP_PATH . '/Views/layouts/header.php';
        require_once APP_PATH . '/Views/dashboard/index.php';
        require_once APP_PATH . '/Views/layouts/footer.php';
    }

    /**
     * ดึงสถิติต่างๆ สำหรับ Dashboard
     * 
     * @param array $currentUser
     * @return array
     */
    private function getStatistics(array $currentUser): array
    {
        $db = new Database();

        $stats = [
            'total_coops' => 0,
            'total_analysis' => 0,
            'pending_analysis' => 0,
            'total_users' => 0
        ];

        try {
            // จำนวนสหกรณ์ทั้งหมด
            $db->query("SELECT COUNT(*) as total FROM coop_info WHERE status = 1");
            $result = $db->fetch();
            $stats['total_coops'] = (int) ($result['total'] ?? 0);

            // จำนวนการวิเคราะห์ทั้งหมด
            // $db->query("SELECT COUNT(*) as total FROM camels_assessments");
            // $result = $db->fetch();
            // $stats['total_analysis'] = (int)($result['total'] ?? 0);

            // จำนวนการวิเคราะห์ที่รอดำเนินการ (ตัวอย่าง)
            $stats['total_analysis'] = 0; // จะใช้งานจริงเมื่อมีตาราง camels_assessments
            $stats['pending_analysis'] = 0;

            // จำนวนผู้ใช้ทั้งหมด (Admin only)
            if ($currentUser['role'] === 'admin') {
                $stats['total_users'] = $this->userModel->countUsers();
            }

        } catch (Exception $e) {
            error_log("Dashboard Statistics Error: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * ดึงข้อมูลการวิเคราะห์ล่าสุด
     * 
     * @param array $currentUser
     * @return array
     */
    private function getRecentAnalysis(array $currentUser): array
    {
        $db = new Database();
        $analysis = [];

        try {
            // ดึงข้อมูลการวิเคราะห์ล่าสุด (เมื่อมีตาราง camels_assessments แล้ว)
            // $sql = "SELECT ca.*, ci.coop_name, ci.coop_code 
            //         FROM camels_assessments ca
            //         LEFT JOIN coop_info ci ON ca.coop_id = ci.coop_id
            //         ORDER BY ca.assessment_date DESC
            //         LIMIT 5";

            // $db->query($sql);
            // $analysis = $db->fetchAll();

            // ตัวอย่างข้อมูล (ใช้ชั่วคราว)
            $analysis = [];

        } catch (Exception $e) {
            error_log("Get Recent Analysis Error: " . $e->getMessage());
        }

        return $analysis;
    }

    /**
     * ดึง Activity Logs ล่าสุด
     * 
     * @param int $userId
     * @return array
     */
    private function getRecentActivities(int $userId): array
    {
        $db = new Database();
        $activities = [];

        try {
            $sql = "SELECT al.*, u.full_name 
                    FROM activity_logs al
                    LEFT JOIN users u ON al.user_id = u.user_id
                    WHERE al.user_id = :user_id
                    ORDER BY al.created_date DESC
                    LIMIT 10";

            $db->query($sql);
            $db->bind(':user_id', $userId);
            $activities = $db->fetchAll();

        } catch (Exception $e) {
            error_log("Get Recent Activities Error: " . $e->getMessage());
        }

        return $activities;
    }

    /**
     * ดึงข้อมูลสหกรณ์ทั้งหมด
     * 
     * @return array
     */
    public function getCooperatives(): array
    {
        $db = new Database();

        try {
            $sql = "SELECT coop_id, coop_code, coop_name, coop_type, province
                    FROM coop_info
                    WHERE status = 1
                    ORDER BY coop_name ASC";

            $db->query($sql);
            return $db->fetchAll();

        } catch (Exception $e) {
            error_log("Get Cooperatives Error: " . $e->getMessage());
            return [];
        }
    }
}