<?php
/**
 * ไฟล์การตั้งค่าฐานข้อมูล
 * Database Configuration File
 * 
 * สำหรับเชื่อมต่อกับ MySQL Database ด้วย PDO
 * PHP 8.0.30 + MySQL 8.0+
 */

// การตั้งค่าฐานข้อมูล
define('DB_HOST', 'localhost');
define('DB_NAME', 'camels_coop');
define('DB_USER', 'root');
define('DB_PASS', ''); // ใส่รหัสผ่านของคุณที่นี่
define('DB_CHARSET', 'utf8mb4');

/**
 * สร้างการเชื่อมต่อ PDO
 * 
 * @return PDO|null
 */
function getDatabaseConnection(): ?PDO
{
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

        return $pdo;

    } catch (PDOException $e) {
        // Log error (ในระบบจริงควร log ลงไฟล์)
        error_log("Database Connection Error: " . $e->getMessage());

        // แสดงข้อความ error ในโหมด development
        if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
            die("เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage());
        } else {
            die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาติดต่อผู้ดูแลระบบ");
        }
    }
}

/**
 * ทดสอบการเชื่อมต่อฐานข้อมูล
 * 
 * @return bool
 */
function testDatabaseConnection(): bool
{
    try {
        $pdo = getDatabaseConnection();
        if ($pdo !== null) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}