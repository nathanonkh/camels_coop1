<?php
/**
 * Financial Controller
 * จัดการฟอร์มกรอกข้อมูลทางการเงิน
 * 
 * PHP 8.0+
 */

require_once APP_PATH . '/Middleware/AuthMiddleware.php';
require_once APP_PATH . '/Models/FinancialData.php';
require_once APP_PATH . '/Helpers/ValidationHelper.php';

class FinancialController
{

    private FinancialData $financialModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->financialModel = new FinancialData();
    }

    /**
     * แสดงฟอร์มกรอกข้อมูล
     * 
     * @return void
     */
    public function showForm(): void
    {
        // ตรวจสอบการ Login
        AuthMiddleware::check();

        // ดึงรายชื่อสหกรณ์
        $cooperatives = $this->getCooperatives();

        // ดึงข้อมูลเดิม (ถ้ามี)
        $existingData = [];
        if (isset($_GET['coop_id']) && isset($_GET['report_date'])) {
            $existingData = $this->financialModel->getAllFinancialData(
                (int) $_GET['coop_id'],
                $_GET['report_date']
            );
        }

        // ตั้งค่า Page
        $pageTitle = 'กรอกข้อมูลเพื่อวิเคราะห์';
        $currentPage = 'financial-input';

        // แสดงหน้าฟอร์ม
        require_once APP_PATH . '/Views/layouts/header.php';
        require_once APP_PATH . '/Views/financial/input-form.php';
        require_once APP_PATH . '/Views/layouts/footer.php';
    }

    /**
     * บันทึกข้อมูลทางการเงิน
     * 
     * @return void
     */
    public function saveData(): void
    {
        // ตรวจสอบการ Login
        AuthMiddleware::check();

        // ตรวจสอบว่าเป็น POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('financial/input'));
            return;
        }

        // ตรวจสอบ CSRF Token
        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid security token. กรุณาลองใหม่อีกครั้ง');
            redirect(url('financial/input'));
            return;
        }

        // ดึงข้อมูลจากฟอร์ม
        $coopId = (int) ($_POST['coop_id'] ?? 0);
        $reportDate = $_POST['report_date'] ?? '';
        $fiscalYear = $_POST['fiscal_year'] ?? date('Y');
        $reportPeriod = (int) ($_POST['report_period'] ?? 1);

        // เตรียมข้อมูลงบดุล
        $balanceSheetData = $this->prepareBalanceSheetData($_POST, $reportDate, $fiscalYear, $reportPeriod);

        // เตรียมข้อมูลงบกำไรขาดทุน
        $incomeStatementData = $this->prepareIncomeStatementData($_POST, $reportDate, $fiscalYear, $reportPeriod);

        // Validate ข้อมูล
        $errors = ValidationHelper::validateFinancialData(array_merge(
            ['coop_id' => $coopId, 'report_date' => $reportDate, 'fiscal_year' => $fiscalYear],
            $balanceSheetData,
            $incomeStatementData
        ));

        if (!empty($errors)) {
            $_SESSION['financial_errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            redirect(url('financial/input'));
            return;
        }

        // บันทึกข้อมูล
        try {
            $this->financialModel->beginTransaction();

            // บันทึกงบดุล
            $balanceResult = $this->financialModel->saveBalanceSheet($coopId, $balanceSheetData);

            // บันทึกงบกำไรขาดทุน
            $incomeResult = $this->financialModel->saveIncomeStatement($coopId, $incomeStatementData);

            if ($balanceResult && $incomeResult) {
                $this->financialModel->commit();

                // บันทึก Activity Log
                $this->logActivity($coopId, 'save_financial_data', 'บันทึกข้อมูลทางการเงิน');

                setFlashMessage('success', 'บันทึกข้อมูลทางการเงินสำเร็จ');

                // Redirect ไปหน้าอัตราส่วนทางการเงิน
                redirect(url('ratio/view?coop_id=' . $coopId . '&report_date=' . $reportDate));
            } else {
                $this->financialModel->rollback();
                setFlashMessage('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                redirect(url('financial/input'));
            }

        } catch (Exception $e) {
            $this->financialModel->rollback();
            error_log("Save Financial Data Error: " . $e->getMessage());
            setFlashMessage('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            redirect(url('financial/input'));
        }
    }

    /**
     * เตรียมข้อมูลงบดุล
     * 
     * @param array $post
     * @param string $reportDate
     * @param string $fiscalYear
     * @param int $reportPeriod
     * @return array
     */
    private function prepareBalanceSheetData(array $post, string $reportDate, string $fiscalYear, int $reportPeriod): array
    {
        $fields = [
            'current_assets',
            'cash_and_cash_equivalent',
            'short_term_investments',
            'account_receivable_short',
            'loan_receivable_short',
            'inventory',
            'other_current_assets',
            'long_term_investments',
            'loan_receivable_long',
            'account_receivable_long',
            'fixed_assets',
            'intangible_assets',
            'other_non_current_assets',
            'total_assets',
            'current_liabilities',
            'account_payable',
            'short_term_loan',
            'member_deposits',
            'other_current_liabilities',
            'long_term_loan',
            'other_non_current_liabilities',
            'total_liabilities',
            'share_capital',
            'legal_reserve',
            'other_reserve',
            'retained_earnings',
            'total_equity',
            'total_members_period',
            'overdue_receivable',
            'due_receivable',
            'paid_on_time_receivable'
        ];

        $data = ValidationHelper::sanitizeNumberFields($post, $fields);
        $data['report_date'] = $reportDate;
        $data['fiscal_year'] = $fiscalYear;
        $data['report_period'] = $reportPeriod;

        return $data;
    }

    /**
     * เตรียมข้อมูลงบกำไรขาดทุน
     * 
     * @param array $post
     * @param string $reportDate
     * @param string $fiscalYear
     * @param int $reportPeriod
     * @return array
     */
    private function prepareIncomeStatementData(array $post, string $reportDate, string $fiscalYear, int $reportPeriod): array
    {
        $fields = [
            'revenue_credit',
            'revenue_sales',
            'revenue_procurement',
            'revenue_processing',
            'revenue_service',
            'revenue_other',
            'total_revenue',
            'cost_credit',
            'cost_sales',
            'cost_procurement',
            'cost_processing',
            'cost_service',
            'total_cost',
            'gross_profit',
            'operating_expenses',
            'personnel_expenses',
            'administrative_expenses',
            'depreciation',
            'operating_income',
            'other_income',
            'other_expenses',
            'net_profit',
            'dividend',
            'legal_reserve_allocation',
            'other_reserve_allocation'
        ];

        $data = ValidationHelper::sanitizeNumberFields($post, $fields);
        $data['report_date'] = $reportDate;
        $data['fiscal_year'] = $fiscalYear;
        $data['report_period'] = $reportPeriod;

        return $data;
    }

    /**
     * ดึงรายชื่อสหกรณ์
     * 
     * @return array
     */
    private function getCooperatives(): array
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