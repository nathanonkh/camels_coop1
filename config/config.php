<?php
/**
 * ไฟล์การตั้งค่าระบบหลัก
 * Main Configuration File
 * 
 * กำหนดค่าต่างๆ ของระบบ CAMELS Analysis
 * PHP 8.0.30
 */

// ========================================
// การตั้งค่าทั่วไป (General Settings)
// ========================================

// Base URL ของระบบ (ปรับตาม environment ของคุณ)
define('BASE_URL', 'http://localhost/camels_coop/public/');

// Root path ของโปรเจค
define('ROOT_PATH', dirname(__DIR__));

// ชื่อระบบ
define('SITE_NAME', 'ระบบวิเคราะห์ CAMELS สำหรับสหกรณ์');
define('SITE_NAME_SHORT', 'CAMELS Analysis');

// เวอร์ชันระบบ
define('VERSION', '2.0.0');

// ========================================
// การตั้งค่าโหมดการทำงาน (Environment)
// ========================================

// โหมด Debug (true = development, false = production)
define('DEBUG_MODE', true);

// แสดง Error ทั้งหมด (สำหรับ development)
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ========================================
// การตั้งค่าเวลา (Timezone)
// ========================================

date_default_timezone_set('Asia/Bangkok');

// ========================================
// การตั้งค่า Session
// ========================================

// ชื่อ Session
define('SESSION_NAME', 'CAMELS_SESSION');

// อายุ Session (วินาที) - 2 ชั่วโมง
define('SESSION_LIFETIME', 7200);

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // เปลี่ยนเป็น 1 ถ้าใช้ HTTPS
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

    session_name(SESSION_NAME);
    session_start();
}

// ========================================
// การตั้งค่าไฟล์และโฟลเดอร์
// ========================================

// Path ต่างๆ
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// URL สำหรับ Assets
define('ASSETS_URL', BASE_URL . 'public/assets/');
define('CSS_URL', ASSETS_URL . 'css/');
define('JS_URL', ASSETS_URL . 'js/');
define('IMG_URL', ASSETS_URL . 'images/');

// ========================================
// การตั้งค่าการอัปโหลดไฟล์
// ========================================

// ขนาดไฟล์สูงสุดที่อัปโหลดได้ (MB)
define('MAX_UPLOAD_SIZE', 10);

// ประเภทไฟล์ที่อนุญาต
define('ALLOWED_FILE_TYPES', [
    'image' => ['jpg', 'jpeg', 'png', 'gif'],
    'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
]);

// ========================================
// การตั้งค่าการแบ่งหน้า (Pagination)
// ========================================

define('ITEMS_PER_PAGE', 20);

// ========================================
// การตั้งค่า Security
// ========================================

// CSRF Token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ========================================
// การตั้งค่าภาษา (Language)
// ========================================

define('DEFAULT_LANGUAGE', 'th');
define('CHARSET', 'UTF-8');

// ========================================
// การตั้งค่าการแสดงผล
// ========================================

// รูปแบบวันที่ (Thai format)
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i:s');
define('TIME_FORMAT', 'H:i:s');

// รูปแบบตัวเลข (Thai format with comma)
define('NUMBER_DECIMALS', 2);
define('NUMBER_DECIMAL_SEPARATOR', '.');
define('NUMBER_THOUSANDS_SEPARATOR', ',');

// ========================================
// การตั้งค่า Email (สำหรับส่ง notification)
// ========================================

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-password');
define('SMTP_FROM_EMAIL', 'noreply@camels.local');
define('SMTP_FROM_NAME', SITE_NAME);

// ========================================
// การตั้งค่า Logging
// ========================================

define('LOG_PATH', STORAGE_PATH . '/logs/');
define('LOG_FILE', LOG_PATH . 'app.log');
define('ERROR_LOG_FILE', LOG_PATH . 'error.log');

// ========================================
// Helper Functions
// ========================================

/**
 * สร้าง URL เต็ม
 * 
 * @param string $path
 * @return string
 */
function url(string $path = ''): string
{
    return BASE_URL . ltrim($path, '/');
}

/**
 * สร้าง URL สำหรับ Assets
 * 
 * @param string $path
 * @return string
 */
function asset(string $path): string
{
    return ASSETS_URL . ltrim($path, '/');
}

/**
 * Redirect ไปยัง URL
 * 
 * @param string $url
 * @param int $statusCode
 * @return void
 */
function redirect(string $url, int $statusCode = 302): void
{
    header('Location: ' . $url, true, $statusCode);
    exit();
}

/**
 * ตรวจสอบว่ามีการ login หรือไม่
 * 
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * ดึงข้อมูล User ที่ login
 * 
 * @return array|null
 */
function getCurrentUser(): ?array
{
    if (isLoggedIn()) {
        return [
            'user_id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null,
            'role' => $_SESSION['role'] ?? null,
        ];
    }
    return null;
}

/**
 * ฟอร์แมตตัวเลขแบบไทย
 * 
 * @param float $number
 * @param int $decimals
 * @return string
 */
function formatNumber(float $number, int $decimals = NUMBER_DECIMALS): string
{
    return number_format($number, $decimals, NUMBER_DECIMAL_SEPARATOR, NUMBER_THOUSANDS_SEPARATOR);
}

/**
 * ฟอร์แมตวันที่แบบไทย
 * 
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate(string $date, string $format = DATE_FORMAT): string
{
    if (empty($date)) {
        return '';
    }

    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }

    return date($format, $timestamp);
}

/**
 * สร้าง CSRF Token field สำหรับ form
 * 
 * @return string
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * ตรวจสอบ CSRF Token
 * 
 * @param string $token
 * @return bool
 */
function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input เพื่อป้องกัน XSS
 * 
 * @param string $data
 * @return string
 */
function sanitize(string $data): string
{
    return htmlspecialchars($data, ENT_QUOTES, CHARSET);
}

/**
 * แสดง Flash Message
 * 
 * @param string $type (success, error, warning, info)
 * @param string $message
 * @return void
 */
function setFlashMessage(string $type, string $message): void
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * ดึง Flash Message
 * 
 * @return array|null
 */
function getFlashMessage(): ?array
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// ========================================
// โหลดไฟล์การตั้งค่าฐานข้อมูล
// ========================================

require_once CONFIG_PATH . '/database.php';