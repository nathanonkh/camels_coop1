<?php
/**
 * Authentication Middleware
 * ตรวจสอบการ Login ก่อนเข้าถึงหน้าต่างๆ
 * 
 * PHP 8.0+
 */

class AuthMiddleware
{

    /**
     * ตรวจสอบว่า User ได้ Login แล้วหรือไม่
     * ถ้ายังไม่ได้ login ให้ redirect ไปหน้า login
     * 
     * @return bool
     */
    public static function check(): bool
    {
        // ตรวจสอบว่ามี session หรือไม่
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ตรวจสอบว่า login แล้วหรือไม่
        if (!isLoggedIn()) {
            // เก็บ URL ที่ต้องการเข้าถึงไว้ใน session
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '';

            // Flash message
            setFlashMessage('warning', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');

            // Redirect ไปหน้า login
            redirect(url('auth/login'));
            exit();
        }

        // ตรวจสอบ Session Timeout
        self::checkTimeout();

        return true;
    }

    /**
     * ตรวจสอบว่า User เป็น Admin หรือไม่
     * 
     * @return bool
     */
    public static function isAdmin(): bool
    {
        if (!isLoggedIn()) {
            redirect(url('auth/login'));
            exit();
        }

        $currentUser = getCurrentUser();

        if ($currentUser['role'] !== 'admin') {
            // ไม่มีสิทธิ์เข้าถึง
            setFlashMessage('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            redirect(url('dashboard'));
            exit();
        }

        return true;
    }

    /**
     * ตรวจสอบ Role ของ User
     * 
     * @param string|array $roles - Role หรือ Array ของ roles ที่อนุญาต
     * @return bool
     */
    public static function hasRole(string|array $roles): bool
    {
        if (!isLoggedIn()) {
            redirect(url('auth/login'));
            exit();
        }

        $currentUser = getCurrentUser();
        $userRole = $currentUser['role'] ?? '';

        // ถ้าส่งมาเป็น string ให้แปลงเป็น array
        if (is_string($roles)) {
            $roles = [$roles];
        }

        // ตรวจสอบว่า user มี role ที่อนุญาตหรือไม่
        if (!in_array($userRole, $roles)) {
            setFlashMessage('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            redirect(url('dashboard'));
            exit();
        }

        return true;
    }

    /**
     * ตรวจสอบว่าเป็น Guest (ยังไม่ได้ login)
     * ใช้สำหรับหน้า login/register ที่ไม่ต้องการให้คนที่ login แล้วเข้าถึง
     * 
     * @return bool
     */
    public static function guest(): bool
    {
        if (isLoggedIn()) {
            // ถ้า login แล้วให้ไป dashboard
            redirect(url('dashboard'));
            exit();
        }

        return true;
    }

    /**
     * ตรวจสอบ Session Timeout
     * 
     * @return void
     */
    private static function checkTimeout(): void
    {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return;
        }

        // คำนวณเวลาที่ไม่มีการใช้งาน
        $inactiveTime = time() - $_SESSION['last_activity'];

        // ถ้าไม่มีการใช้งานเกิน SESSION_LIFETIME (2 ชั่วโมง)
        if ($inactiveTime > SESSION_LIFETIME) {
            // Session timeout - ลบ session และ redirect ไป login
            session_unset();
            session_destroy();

            // สร้าง session ใหม่
            session_start();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            setFlashMessage('warning', 'เซสชันหมดอายุเนื่องจากไม่มีการใช้งาน กรุณาเข้าสู่ระบบใหม่');
            redirect(url('auth/login'));
            exit();
        }

        // อัปเดต last activity time
        $_SESSION['last_activity'] = time();
    }

    /**
     * ตรวจสอบ CSRF Token
     * 
     * @param string $token
     * @return bool
     */
    public static function verifyCsrf(string $token): bool
    {
        if (!verifyCsrfToken($token)) {
            setFlashMessage('error', 'Invalid security token. กรุณาลองใหม่อีกครั้ง');
            redirect($_SERVER['HTTP_REFERER'] ?? url('dashboard'));
            exit();
        }

        return true;
    }

    /**
     * Redirect to intended URL (หลัง login)
     * 
     * @return void
     */
    public static function redirectToIntended(): void
    {
        $intendedUrl = $_SESSION['intended_url'] ?? url('dashboard');
        unset($_SESSION['intended_url']);

        redirect($intendedUrl);
    }

    /**
     * ตรวจสอบว่า User ยังใช้งานอยู่หรือไม่
     * (สำหรับ AJAX requests)
     * 
     * @return bool
     */
    public static function isActive(): bool
    {
        if (!isLoggedIn()) {
            return false;
        }

        // อัปเดต last activity
        $_SESSION['last_activity'] = time();

        return true;
    }

    /**
     * Get current user's role
     * 
     * @return string|null
     */
    public static function getRole(): ?string
    {
        if (!isLoggedIn()) {
            return null;
        }

        return $_SESSION['role'] ?? null;
    }

    /**
     * Get current user's ID
     * 
     * @return int|null
     */
    public static function getUserId(): ?int
    {
        if (!isLoggedIn()) {
            return null;
        }

        return $_SESSION['user_id'] ?? null;
    }
}