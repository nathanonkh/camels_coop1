<?php
/**
 * Authentication Controller
 * จัดการการ Login, Logout และ Authentication
 * 
 * PHP 8.0+
 */

require_once APP_PATH . '/Models/User.php';

class AuthController
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
     * แสดงหน้า Login Form
     * 
     * @return void
     */
    public function showLoginForm(): void
    {
        // ถ้า login แล้วให้ redirect ไป dashboard
        if (isLoggedIn()) {
            redirect(url('dashboard'));
            return;
        }

        // แสดงหน้า login
        require_once APP_PATH . '/Views/auth/login.php';
    }

    /**
     * ประมวลผลการ Login
     * 
     * @return void
     */
    public function login(): void
    {
        // ตรวจสอบว่าเป็น POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('auth/login'));
            return;
        }

        // ดึงข้อมูลจาก Form
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        // Validate input
        $errors = $this->validateLoginInput($username, $password);

        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            $_SESSION['old_username'] = $username;
            redirect(url('auth/login'));
            return;
        }

        // ตรวจสอบ CSRF Token
        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
            redirect(url('auth/login'));
            return;
        }

        // ตรวจสอบ Rate Limiting (ป้องกัน Brute Force)
        if (!$this->checkLoginAttempts($username)) {
            setFlashMessage('error', 'มีการพยายาม login มากเกินไป กรุณารอ 15 นาที');
            redirect(url('auth/login'));
            return;
        }

        // ทำการ Login
        $user = $this->userModel->login($username, $password);

        if ($user) {
            // Login สำเร็จ
            $this->createSession($user, $rememberMe);

            // เคลียร์ login attempts
            $this->clearLoginAttempts($username);

            // Flash message
            setFlashMessage('success', 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับคุณ ' . $user['full_name']);

            // Redirect ไป dashboard
            redirect(url('dashboard'));

        } else {
            // Login ไม่สำเร็จ
            $this->recordLoginAttempt($username);

            setFlashMessage('error', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            $_SESSION['old_username'] = $username;

            redirect(url('auth/login'));
        }
    }

    /**
     * Logout
     * 
     * @return void
     */
    public function logout(): void
    {
        // บันทึก Activity Log ก่อน logout
        if (isLoggedIn()) {
            $userId = $_SESSION['user_id'];

            // Log activity (ต้องสร้าง instance ใหม่เพราะจะ clear session)
            $db = new Database();
            $db->query("INSERT INTO activity_logs (user_id, action, description, ip_address) 
                        VALUES (:user_id, 'logout', 'ออกจากระบบ', :ip)");
            $db->bind(':user_id', $userId);
            $db->bind(':ip', $_SERVER['REMOTE_ADDR'] ?? '');
            $db->execute();
        }

        // ลบ Session
        session_unset();
        session_destroy();

        // สร้าง Session ใหม่
        session_start();

        // สร้าง CSRF Token ใหม่
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Flash message
        setFlashMessage('success', 'ออกจากระบบเรียบร้อยแล้ว');

        // Redirect ไปหน้า login
        redirect(url('auth/login'));
    }

    /**
     * สร้าง Session สำหรับ User
     * 
     * @param array $user
     * @param bool $rememberMe
     * @return void
     */
    private function createSession(array $user, bool $rememberMe = false): void
    {
        // Regenerate session ID เพื่อความปลอดภัย
        session_regenerate_id(true);

        // เก็บข้อมูลใน Session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'] ?? '';
        $_SESSION['role'] = $user['role']; // เป็น string แล้ว (admin/user/viewer)
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();

        // Remember Me functionality
        if ($rememberMe) {
            // ตั้งค่า Session cookie ให้อยู่ได้ 30 วัน
            $cookieParams = session_get_cookie_params();
            setcookie(
                session_name(),
                session_id(),
                time() + (30 * 24 * 60 * 60), // 30 days
                $cookieParams['path'],
                $cookieParams['domain'],
                $cookieParams['secure'],
                $cookieParams['httponly']
            );
        }
    }

    /**
     * Validate Login Input
     * 
     * @param string $username
     * @param string $password
     * @return array
     */
    private function validateLoginInput(string $username, string $password): array
    {
        $errors = [];

        if (empty($username)) {
            $errors[] = 'กรุณากรอกชื่อผู้ใช้';
        }

        if (empty($password)) {
            $errors[] = 'กรุณากรอกรหัสผ่าน';
        }

        if (strlen($username) < 3) {
            $errors[] = 'ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร';
        }

        if (strlen($password) < 4) {
            $errors[] = 'รหัสผ่านต้องมีอย่างน้อย 4 ตัวอักษร';
        }

        return $errors;
    }

    /**
     * ตรวจสอบจำนวนครั้งที่พยายาม Login
     * (ป้องกัน Brute Force Attack)
     * 
     * @param string $username
     * @return bool
     */
    private function checkLoginAttempts(string $username): bool
    {
        $key = 'login_attempts_' . md5($username);

        if (!isset($_SESSION[$key])) {
            return true;
        }

        $attempts = $_SESSION[$key];

        // ถ้าพยายาม login เกิน 5 ครั้งใน 15 นาที
        if ($attempts['count'] >= 5) {
            $timeDiff = time() - $attempts['time'];

            if ($timeDiff < 900) { // 15 minutes
                return false;
            }

            // ถ้าเกิน 15 นาทีแล้วให้ reset
            unset($_SESSION[$key]);
        }

        return true;
    }

    /**
     * บันทึกการพยายาม Login ที่ไม่สำเร็จ
     * 
     * @param string $username
     * @return void
     */
    private function recordLoginAttempt(string $username): void
    {
        $key = 'login_attempts_' . md5($username);

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'time' => time()
            ];
        } else {
            $_SESSION[$key]['count']++;
            $_SESSION[$key]['time'] = time();
        }
    }

    /**
     * เคลียร์ Login Attempts
     * 
     * @param string $username
     * @return void
     */
    private function clearLoginAttempts(string $username): void
    {
        $key = 'login_attempts_' . md5($username);
        unset($_SESSION[$key]);
    }

    /**
     * ตรวจสอบ Session Timeout
     * 
     * @return bool
     */
    public function checkSessionTimeout(): bool
    {
        if (!isLoggedIn()) {
            return false;
        }

        // ตรวจสอบว่า session หมดอายุหรือไม่ (2 ชั่วโมง)
        if (isset($_SESSION['last_activity'])) {
            $inactiveTime = time() - $_SESSION['last_activity'];

            if ($inactiveTime > SESSION_LIFETIME) {
                // Session timeout
                $this->logout();
                setFlashMessage('warning', 'เซสชันหมดอายุ กรุณา login ใหม่');
                return false;
            }
        }

        // อัปเดต last activity
        $_SESSION['last_activity'] = time();

        return true;
    }
}