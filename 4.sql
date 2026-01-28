-- =====================================================
-- ฐานข้อมูล CAMELS Analysis สำหรับสหกรณ์และกลุ่มเกษตรกร
-- สำหรับ XAMPP 8.0.30 / MySQL 8.0+ / PHP 8.0.30
-- อัปเดต: 28 มกราคม 2026 (เพิ่มระบบผู้ใช้และ Activity Logs)
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;
SET CHARACTER_SET_CLIENT = utf8mb4;

-- สร้างฐานข้อมูล
CREATE DATABASE IF NOT EXISTS `camels_coop` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `camels_coop`;

-- =====================================================
-- ส่วนที่ 1: ตารางข้อมูลหลัก (Core Tables)
-- =====================================================

-- =====================================================
-- ตาราง 1.1: ประเภทสหกรณ์ (อ้างอิงก่อน)
-- =====================================================
DROP TABLE IF EXISTS `coop_types`;
CREATE TABLE `coop_types` (
  `type_id` tinyint NOT NULL COMMENT 'รหัสประเภท',
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อประเภท',
  `type_description` text COLLATE utf8mb4_unicode_ci COMMENT 'คำอธิบายประเภท',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางประเภทสหกรณ์';

INSERT INTO `coop_types` VALUES
(1, 'สหกรณ์การเกษตร', 'สหกรณ์เพื่อส่งเสริมการเกษตร'),
(2, 'สหกรณ์ประมง', 'สหกรณ์เพื่อส่งเสริมการประมง'),
(3, 'สหกรณ์นิคม', 'สหกรณ์ในนิคมสหกรณ์'),
(4, 'สหกรณ์ร้านค้า', 'สหกรณ์ค้าส่ง-ค้าปลีก'),
(5, 'สหกรณ์บริการ', 'สหกรณ์ให้บริการ'),
(6, 'สหกรณ์ออมทรัพย์', 'สหกรณ์เพื่อการออมและให้กู้'),
(7, 'สหกรณ์เครดิตยูเนี่ยน', 'สหกรณ์สินเชื่อ'),
(8, 'กลุ่มเกษตรกร', 'กลุ่มเกษตรกร'),
(9, 'อื่นๆ', 'ประเภทอื่นๆ');

-- =====================================================
-- ตาราง 1.2: ข้อมูลสหกรณ์
-- =====================================================
DROP TABLE IF EXISTS `coop_info`;
CREATE TABLE `coop_info` (
  `coop_id` int NOT NULL AUTO_INCREMENT COMMENT 'รหัสสหกรณ์อัตโนมัติ',
  `coop_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'รหัสสหกรณ์',
  `coop_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อสหกรณ์',
  `coop_type` tinyint NOT NULL COMMENT 'ประเภทสหกรณ์: 1=การเกษตร,2=ประมง,3=นิคม,4=ร้านค้า,5=บริการ,6=ออมทรัพย์,7=เครดิตยูเนี่ยน,8=กลุ่มเกษตรกร',
  `coop_size` tinyint DEFAULT NULL COMMENT 'ขนาดสหกรณ์: 1=เล็ก,2=กลาง,3=ใหญ่,4=ใหญ่มาก,5=ใหญ่พิเศษ',
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'จังหวัด',
  `district` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'อำเภอ',
  `address` text COLLATE utf8mb4_unicode_ci COMMENT 'ที่อยู่',
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'อีเมล',
  `established_date` date DEFAULT NULL COMMENT 'วันที่ก่อตั้ง',
  `total_members` int DEFAULT 0 COMMENT 'จำนวนสมาชิกทั้งหมด',
  `status` tinyint DEFAULT 1 COMMENT 'สถานะ: 1=ใช้งาน,0=ไม่ใช้งาน',
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างข้อมูล',
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขข้อมูลล่าสุด',
  PRIMARY KEY (`coop_id`),
  UNIQUE KEY `coop_code` (`coop_code`),
  KEY `idx_coop_type` (`coop_type`),
  KEY `idx_coop_size` (`coop_size`),
  KEY `idx_status` (`status`),
  KEY `idx_province` (`province`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางข้อมูลสหกรณ์';

-- =====================================================
-- ส่วนที่ 2: ตารางระบบผู้ใช้และความปลอดภัย (User & Security)
-- =====================================================

-- =====================================================
-- ตาราง 2.1: ผู้ใช้งานระบบ
-- =====================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT COMMENT 'รหัสผู้ใช้อัตโนมัติ',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อผู้ใช้งาน',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'รหัสผ่านที่เข้ารหัสแล้ว',
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อ-นามสกุล',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'อีเมล',
  `role` tinyint DEFAULT 2 COMMENT 'บทบาท: 1=Admin,2=User,3=Viewer',
  `coop_id` int DEFAULT NULL COMMENT 'รหัสสหกรณ์ที่สังกัด (NULL = ดูได้ทุกสหกรณ์)',
  `status` tinyint DEFAULT 1 COMMENT 'สถานะ: 1=ใช้งาน,0=ระงับการใช้งาน',
  `last_login` datetime DEFAULT NULL COMMENT 'วันที่เข้าใช้งานล่าสุด',
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบัญชี',
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขข้อมูลล่าสุด',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_coop_id` (`coop_id`),
  KEY `idx_status` (`status`),
  KEY `idx_role` (`role`),
  CONSTRAINT `fk_user_coop` FOREIGN KEY (`coop_id`) REFERENCES `coop_info` (`coop_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางผู้ใช้งานระบบ';

-- สร้างผู้ใช้ Admin เริ่มต้น (password: admin123)
-- ใช้ SHA2-256 เพื่อความปลอดภัย
INSERT INTO `users` (`username`, `password`, `full_name`, `role`, `status`, `created_date`) VALUES
('admin', SHA2('admin123', 256), 'ผู้ดูแลระบบ', 1, 1, NOW());

-- =====================================================
-- ตาราง 2.2: บันทึกกิจกรรมการใช้งาน (Activity Logs)
-- =====================================================
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `log_id` int NOT NULL AUTO_INCREMENT COMMENT 'รหัสบันทึกอัตโนมัติ',
  `user_id` int DEFAULT NULL COMMENT 'รหัสผู้ใช้ที่ทำรายการ',
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'การกระทำ (เช่น INSERT, UPDATE, DELETE, LOGIN)',
  `table_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ชื่อตารางที่ถูกกระทำ',
  `record_id` int DEFAULT NULL COMMENT 'รหัสของข้อมูลที่ถูกกระทำ',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'รายละเอียดการกระทำ',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP Address ของผู้ใช้',
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่-เวลาที่ทำรายการ',
  PRIMARY KEY (`log_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_date` (`created_date`),
  KEY `idx_action` (`action`),
  KEY `idx_table_name` (`table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางบันทึกกิจกรรมการใช้งานระบบ';

-- =====================================================
-- ตาราง 2.3: การตั้งค่าระบบ (System Settings)
-- =====================================================
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `setting_id` int NOT NULL AUTO_INCREMENT COMMENT 'รหัสการตั้งค่าอัตโนมัติ',
  `setting_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'คีย์การตั้งค่า',
  `setting_value` text COLLATE utf8mb4_unicode_ci COMMENT 'ค่าที่ตั้งไว้',
  `setting_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'text' COMMENT 'ชนิดของข้อมูล: text, number, boolean, json',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'คำอธิบาย',
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขล่าสุด',
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางการตั้งค่าระบบ';

-- ข้อมูลการตั้งค่าเริ่มต้น
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('system_name', 'ระบบวิเคราะห์อัตราส่วนทางการเงิน CAMELS', 'text', 'ชื่อระบบ'),
('fiscal_year_start', '10', 'number', 'เดือนเริ่มต้นปีบัญชี (1-12)'),
('auto_calculate_ratios', '1', 'boolean', 'คำนวณอัตราส่วนอัตโนมัติ (1=เปิด, 0=ปิด)'),
('visitor_count', '92538', 'number', 'จำนวนผู้เข้าชมระบบ'),
('session_timeout', '3600', 'number', 'ระยะเวลา Session หมดอายุ (วินาที)'),
('max_login_attempts', '5', 'number', 'จำนวนครั้งสูงสุดที่พยายาม Login ก่อนถูกล็อค'),
('enable_email_notification', '0', 'boolean', 'เปิดการแจ้งเตือนทางอีเมล (1=เปิด, 0=ปิด)');

-- =====================================================
-- ส่วนที่ 3: ตารางข้อมูล CAMELS (CAMELS Data)
-- =====================================================

-- =====================================================
-- ตาราง 3.1: เกณฑ์การให้คะแนน CAMELS
-- =====================================================
DROP TABLE IF EXISTS `camels_scoring_criteria`;
CREATE TABLE `camels_scoring_criteria` (
  `criteria_id` int NOT NULL AUTO_INCREMENT COMMENT 'รหัสเกณฑ์อัตโนมัติ',
  `coop_type` tinyint NOT NULL COMMENT 'ประเภทสหกรณ์',
  `ratio_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'รหัสอัตราส่วน (C1, C2, A1, ...)',
  `ratio_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่ออัตราส่วน',
  `good_min` decimal(10,4) DEFAULT NULL COMMENT 'ค่าต่ำสุดของระดับ "ดี"',
  `good_max` decimal(10,4) DEFAULT NULL COMMENT 'ค่าสูงสุดของระดับ "ดี"',
  `fair_min` decimal(10,4) DEFAULT NULL COMMENT 'ค่าต่ำสุดของระดับ "พอใช้"',
  `fair_max` decimal(10,4) DEFAULT NULL COMMENT 'ค่าสูงสุดของระดับ "พอใช้"',
  `poor_min` decimal(10,4) DEFAULT NULL COMMENT 'ค่าต่ำสุดของระดับ "ควรปรับปรุง"',
  `poor_max` decimal(10,4) DEFAULT NULL COMMENT 'ค่าสูงสุดของระดับ "ควรปรับปรุง"',
  `is_higher_better` tinyint DEFAULT 1 COMMENT '1 = ค่าสูงดีกว่า, 0 = ค่าต่ำดีกว่า',
  PRIMARY KEY (`criteria_id`),
  UNIQUE KEY `coop_ratio_unique` (`coop_type`,`ratio_code`),
  KEY `idx_ratio_code` (`ratio_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเกณฑ์การให้คะแนน CAMELS';

-- เกณฑ์การให้คะแนนสำหรับสหกรณ์ออมทรัพย์ (ประเภท 6)
INSERT INTO `camels_scoring_criteria` 
(`coop_type`, `ratio_code`, `ratio_name`, `good_min`, `good_max`, `fair_min`, `fair_max`, `poor_min`, `poor_max`, `is_higher_better`) 
VALUES
-- C: Capital Strength
(6, 'C1', 'อัตราส่วนทุนสำรองต่อสินทรัพย์', 0.10, NULL, 0.05, 0.10, NULL, 0.05, 1),
(6, 'C2', 'อัตราการเติบโตทุนของสหกรณ์', 10.00, NULL, 5.00, 10.00, NULL, 5.00, 1),
(6, 'C3', 'อัตราผลตอบแทนต่อส่วนของทุน', 15.00, NULL, 10.00, 15.00, NULL, 10.00, 1),
(6, 'C4', 'อัตราส่วนหนี้สินต่อทุน', NULL, 8.00, 8.00, 12.00, 12.00, NULL, 0),
(6, 'C5', 'อัตราการเติบโตของหนี้', NULL, 15.00, 15.00, 25.00, 25.00, NULL, 0),

-- A: Asset Quality
(6, 'A1', 'อัตราหนี้ค้างชำระ', NULL, 3.00, 3.00, 5.00, 5.00, NULL, 0),
(6, 'A2', 'อัตราหมุนของสินทรัพย์', 0.15, NULL, 0.10, 0.15, NULL, 0.10, 1),
(6, 'A3', 'อัตราผลตอบแทนต่อสินทรัพย์', 2.00, NULL, 1.00, 2.00, NULL, 1.00, 1),
(6, 'A4', 'อัตราการเติบโตของสินทรัพย์', 10.00, NULL, 5.00, 10.00, NULL, 5.00, 1),

-- M: Management Quality
(6, 'M1', 'อัตราการเติบโตของธุรกิจ', 10.00, NULL, 5.00, 10.00, NULL, 5.00, 1),

-- E: Earning Sufficiency
(6, 'E1', 'กำไรต่อสมาชิก', 1000.00, NULL, 500.00, 1000.00, NULL, 500.00, 1),
(6, 'E2', 'เงินออมต่อสมาชิก', 50000.00, NULL, 30000.00, 50000.00, NULL, 30000.00, 1),
(6, 'E3', 'อัตราค่าใช้จ่ายดำเนินงานต่อกำไร', NULL, 60.00, 60.00, 80.00, 80.00, NULL, 0),
(6, 'E4', 'อัตราการเติบโตของทุนสำรอง', 10.00, NULL, 5.00, 10.00, NULL, 5.00, 1),
(6, 'E5', 'อัตราการเติบโตของทุนสะสมอื่น', 10.00, NULL, 5.00, 10.00, NULL, 5.00, 1),
(6, 'E6', 'หนี้สินต่อสมาชิก', 100000.00, NULL, 50000.00, 100000.00, NULL, 50000.00, 1),
(6, 'E7', 'อัตราการเติบโตของกำไร', 10.00, NULL, 5.00, 10.00, NULL, 5.00, 1),
(6, 'E8', 'อัตรากำไรสุทธิ', 10.00, NULL, 5.00, 10.00, NULL, 5.00, 1),

-- L: Liquidity
(6, 'L1', 'อัตราส่วนทุนหมุนเวียน', 1.50, NULL, 1.20, 1.50, NULL, 1.20, 1),
(6, 'L2', 'อัตราหมุนของสินค้า', 6.00, NULL, 4.00, 6.00, NULL, 4.00, 1),
(6, 'L3', 'อัตราลูกหนี้ระยะสั้นที่ชำระได้ตามกำหนด', 95.00, NULL, 90.00, 95.00, NULL, 90.00, 1);

-- เกณฑ์การให้คะแนนสำหรับสหกรณ์การเกษตร (ประเภท 1)
INSERT INTO `camels_scoring_criteria` 
(`coop_type`, `ratio_code`, `ratio_name`, `good_min`, `good_max`, `fair_min`, `fair_max`, `poor_min`, `poor_max`, `is_higher_better`) 
VALUES
-- C: Capital Strength
(1, 'C1', 'อัตราส่วนทุนสำรองต่อสินทรัพย์', 0.08, NULL, 0.04, 0.08, NULL, 0.04, 1),
(1, 'C2', 'อัตราการเติบโตทุนของสหกรณ์', 8.00, NULL, 4.00, 8.00, NULL, 4.00, 1),
(1, 'C3', 'อัตราผลตอบแทนต่อส่วนของทุน', 12.00, NULL, 8.00, 12.00, NULL, 8.00, 1),
(1, 'C4', 'อัตราส่วนหนี้สินต่อทุน', NULL, 10.00, 10.00, 15.00, 15.00, NULL, 0),
(1, 'C5', 'อัตราการเติบโตของหนี้', NULL, 20.00, 20.00, 30.00, 30.00, NULL, 0),

-- A: Asset Quality
(1, 'A1', 'อัตราหนี้ค้างชำระ', NULL, 5.00, 5.00, 8.00, 8.00, NULL, 0),
(1, 'A2', 'อัตราหมุนของสินทรัพย์', 0.12, NULL, 0.08, 0.12, NULL, 0.08, 1),
(1, 'A3', 'อัตราผลตอบแทนต่อสินทรัพย์', 1.50, NULL, 0.80, 1.50, NULL, 0.80, 1),
(1, 'A4', 'อัตราการเติบโตของสินทรัพย์', 8.00, NULL, 4.00, 8.00, NULL, 4.00, 1),

-- M: Management Quality
(1, 'M1', 'อัตราการเติบโตของธุรกิจ', 8.00, NULL, 4.00, 8.00, NULL, 4.00, 1),

-- E: Earning Sufficiency
(1, 'E1', 'กำไรต่อสมาชิก', 800.00, NULL, 400.00, 800.00, NULL, 400.00, 1),
(1, 'E2', 'เงินออมต่อสมาชิก', 30000.00, NULL, 15000.00, 30000.00, NULL, 15000.00, 1),
(1, 'E3', 'อัตราค่าใช้จ่ายดำเนินงานต่อกำไร', NULL, 70.00, 70.00, 85.00, 85.00, NULL, 0),
(1, 'E4', 'อัตราการเติบโตของทุนสำรอง', 8.00, NULL, 4.00, 8.00, NULL, 4.00, 1),
(1, 'E5', 'อัตราการเติบโตของทุนสะสมอื่น', 8.00, NULL, 4.00, 8.00, NULL, 4.00, 1),
(1, 'E6', 'หนี้สินต่อสมาชิก', 80000.00, NULL, 40000.00, 80000.00, NULL, 40000.00, 1),
(1, 'E7', 'อัตราการเติบโตของกำไร', 8.00, NULL, 4.00, 8.00, NULL, 4.00, 1),
(1, 'E8', 'อัตรากำไรสุทธิ', 8.00, NULL, 4.00, 8.00, NULL, 4.00, 1),

-- L: Liquidity
(1, 'L1', 'อัตราส่วนทุนหมุนเวียน', 1.30, NULL, 1.10, 1.30, NULL, 1.10, 1),
(1, 'L2', 'อัตราหมุนของสินค้า', 5.00, NULL, 3.00, 5.00, NULL, 3.00, 1),
(1, 'L3', 'อัตราลูกหนี้ระยะสั้นที่ชำระได้ตามกำหนด', 92.00, NULL, 85.00, 92.00, NULL, 85.00, 1);

-- =====================================================
-- ตาราง 3.2: ข้อมูลสำหรับกรอกข้อมูล CAMELS
-- =====================================================
DROP TABLE IF EXISTS `camels_input_data`;
CREATE TABLE `camels_input_data` (
  `input_id` int NOT NULL AUTO_INCREMENT COMMENT 'รหัสข้อมูลอัตโนมัติ',
  `coop_id` int NOT NULL COMMENT 'รหัสสหกรณ์',
  `report_date` date NOT NULL COMMENT 'วันที่รายงาน',
  `fiscal_year` year NOT NULL COMMENT 'ปีงบประมาณ',
  
  -- C1: อัตราส่วนทุนสำรองต่อสินทรัพย์
  `c1_legal_reserve` decimal(15,2) DEFAULT 0.00 COMMENT 'C1: ทุนสำรอง (บาท)',
  `c1_total_assets` decimal(15,2) DEFAULT 0.00 COMMENT 'C1: สินทรัพย์ทั้งสิ้น (บาท)',
  
  -- C2: อัตราการเติบโตทุนของสหกรณ์
  `c2_equity_current` decimal(15,2) DEFAULT 0.00 COMMENT 'C2: ทุนของสหกรณ์งวดปัจจุบัน (บาท)',
  `c2_equity_previous` decimal(15,2) DEFAULT 0.00 COMMENT 'C2: ทุนของสหกรณ์งวดก่อน (บาท)',
  
  -- C3: อัตราผลตอบแทนต่อส่วนของทุน
  `c3_net_profit` decimal(15,2) DEFAULT 0.00 COMMENT 'C3: กำไรสุทธิ (บาท)',
  `c3_avg_equity` decimal(15,2) DEFAULT 0.00 COMMENT 'C3: ทุนของสหกรณ์ถัวเฉลี่ย (บาท)',
  
  -- C4: อัตราส่วนหนี้สินต่อทุน
  `c4_total_liabilities` decimal(15,2) DEFAULT 0.00 COMMENT 'C4: หนี้สินทั้งสิ้น (บาท)',
  `c4_total_equity` decimal(15,2) DEFAULT 0.00 COMMENT 'C4: ทุนของสหกรณ์ (บาท)',
  
  -- C5: อัตราการเติบโตของหนี้
  `c5_liabilities_current` decimal(15,2) DEFAULT 0.00 COMMENT 'C5: หนี้สินทั้งสิ้นงวดปัจจุบัน (บาท)',
  `c5_liabilities_previous` decimal(15,2) DEFAULT 0.00 COMMENT 'C5: หนี้สินทั้งสิ้นงวดก่อน (บาท)',
  
  -- A1: อัตราหนี้ค้างชำระ
  `a1_overdue_receivable` decimal(15,2) DEFAULT 0.00 COMMENT 'A1: หนี้ที่ไม่สามารถชำระได้ตามกำหนด (บาท)',
  `a1_due_receivable` decimal(15,2) DEFAULT 0.00 COMMENT 'A1: หนี้ที่ถึงกำหนดชำระ (บาท)',
  
  -- A2: อัตราหมุนของสินทรัพย์
  `a2_revenue_main` decimal(15,2) DEFAULT 0.00 COMMENT 'A2: ขาย/บริการ - รายได้ธุรกิจหลัก (บาท)',
  `a2_avg_total_assets` decimal(15,2) DEFAULT 0.00 COMMENT 'A2: สินทรัพย์ทั้งสิ้นถัวเฉลี่ย (บาท)',
  
  -- A3: อัตราผลตอบแทนต่อสินทรัพย์
  `a3_operating_income` decimal(15,2) DEFAULT 0.00 COMMENT 'A3: กำไรจากการดำเนินงาน (บาท)',
  `a3_avg_total_assets` decimal(15,2) DEFAULT 0.00 COMMENT 'A3: สินทรัพย์ทั้งสิ้นถัวเฉลี่ย (บาท)',
  
  -- A4: อัตราการเติบโตของสินทรัพย์
  `a4_assets_current` decimal(15,2) DEFAULT 0.00 COMMENT 'A4: สินทรัพย์ทั้งสิ้นงวดปัจจุบัน (บาท)',
  `a4_assets_previous` decimal(15,2) DEFAULT 0.00 COMMENT 'A4: สินทรัพย์ทั้งสิ้นงวดก่อน (บาท)',
  
  -- M1: อัตราการเติบโตของธุรกิจ
  `m1_business_value_current` decimal(15,2) DEFAULT 0.00 COMMENT 'M1: มูลค่าธุรกิจงวดปัจจุบัน (บาท)',
  `m1_business_value_previous` decimal(15,2) DEFAULT 0.00 COMMENT 'M1: มูลค่าธุรกิจงวดก่อน (บาท)',
  
  -- E1: กำไรต่อสมาชิก
  `e1_net_profit` decimal(15,2) DEFAULT 0.00 COMMENT 'E1: กำไรสุทธิ (บาท)',
  `e1_total_members` int DEFAULT 0 COMMENT 'E1: จำนวนสมาชิก (คน)',
  
  -- E2: เงินออมต่อสมาชิก
  `e2_member_deposits` decimal(15,2) DEFAULT 0.00 COMMENT 'E2: เงินรับฝากสมาชิก (บาท)',
  `e2_share_capital` decimal(15,2) DEFAULT 0.00 COMMENT 'E2: ทุนเรือนหุ้น (บาท)',
  
  -- E3: อัตราค่าใช้จ่ายดำเนินงานต่อกำไร
  `e3_operating_expenses` decimal(15,2) DEFAULT 0.00 COMMENT 'E3: ค่าใช้จ่ายดำเนินงาน (บาท)',
  `e3_operating_income` decimal(15,2) DEFAULT 0.00 COMMENT 'E3: กำไรก่อนหักค่าใช้จ่ายดำเนินงาน (บาท)',
  
  -- E4: อัตราการเติบโตของทุนสำรอง
  `e4_reserve_current` decimal(15,2) DEFAULT 0.00 COMMENT 'E4: ทุนสำรองงวดปัจจุบัน (บาท)',
  `e4_reserve_previous` decimal(15,2) DEFAULT 0.00 COMMENT 'E4: ทุนสำรองงวดก่อน (บาท)',
  
  -- E5: อัตราการเติบโตของทุนสะสมอื่น
  `e5_other_reserve_current` decimal(15,2) DEFAULT 0.00 COMMENT 'E5: ทุนสะสมอื่นงวดปัจจุบัน (บาท)',
  `e5_other_reserve_previous` decimal(15,2) DEFAULT 0.00 COMMENT 'E5: ทุนสะสมอื่นงวดก่อน (บาท)',
  
  -- E6: หนี้สินต่อสมาชิก
  `e6_loan_receivable` decimal(15,2) DEFAULT 0.00 COMMENT 'E6: ลูกหนี้เงินกู้ (บาท)',
  `e6_service_receivable` decimal(15,2) DEFAULT 0.00 COMMENT 'E6: ลูกหนี้ค่าบริการอื่น (บาท)',
  `e6_account_receivable` decimal(15,2) DEFAULT 0.00 COMMENT 'E6: ลูกหนี้การค้า (บาท)',
  
  -- E7: อัตราการเติบโตของกำไร
  `e7_profit_current` decimal(15,2) DEFAULT 0.00 COMMENT 'E7: กำไรสุทธิงวดปัจจุบัน (บาท)',
  `e7_profit_previous` decimal(15,2) DEFAULT 0.00 COMMENT 'E7: กำไรสุทธิงวดก่อน (บาท)',
  
  -- E8: อัตรากำไรสุทธิ
  `e8_net_profit` decimal(15,2) DEFAULT 0.00 COMMENT 'E8: กำไรสุทธิ (บาท)',
  `e8_revenue_main` decimal(15,2) DEFAULT 0.00 COMMENT 'E8: ขาย/บริการ - รายได้ธุรกิจหลัก (บาท)',
  
  -- L1: อัตราส่วนทุนหมุนเวียน
  `l1_current_assets` decimal(15,2) DEFAULT 0.00 COMMENT 'L1: สินทรัพย์หมุนเวียน (บาท)',
  `l1_current_liabilities` decimal(15,2) DEFAULT 0.00 COMMENT 'L1: หนี้สินหมุนเวียน (บาท)',
  
  -- L2: อัตราหมุนของสินค้า
  `l2_cost_sales` decimal(15,2) DEFAULT 0.00 COMMENT 'L2: ต้นทุนสินค้าขาย (บาท)',
  `l2_avg_inventory` decimal(15,2) DEFAULT 0.00 COMMENT 'L2: สินค้าคงเหลือถัวเฉลี่ย (บาท)',
  
  -- L3: อัตราลูกหนี้ระยะสั้นที่ชำระได้ตามกำหนด
  `l3_paid_on_time` decimal(15,2) DEFAULT 0.00 COMMENT 'L3: ลูกหนี้ระยะสั้นที่ชำระได้ตามกำหนด (บาท)',
  `l3_due_short_term` decimal(15,2) DEFAULT 0.00 COMMENT 'L3: ลูกหนี้ระยะสั้นที่ถึงกำหนดชำระ (บาท)',
  
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างข้อมูล',
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขข้อมูลล่าสุด',
  
  PRIMARY KEY (`input_id`),
  UNIQUE KEY `coop_input_unique` (`coop_id`,`report_date`),
  KEY `idx_report_date` (`report_date`),
  KEY `idx_fiscal_year` (`fiscal_year`),
  CONSTRAINT `fk_input_coop` FOREIGN KEY (`coop_id`) REFERENCES `coop_info` (`coop_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางข้อมูลสำหรับกรอกข้อมูล CAMELS';

-- =====================================================
-- ตาราง 3.3: อัตราส่วนทางการเงิน (CAMELS) - ผลการคำนวณ
-- =====================================================
DROP TABLE IF EXISTS `financial_ratios`;
CREATE TABLE `financial_ratios` (
  `ratio_id` int NOT NULL AUTO_INCREMENT COMMENT 'รหัสอัตราส่วนอัตโนมัติ',
  `coop_id` int NOT NULL COMMENT 'รหัสสหกรณ์',
  `report_date` date NOT NULL COMMENT 'วันที่รายงาน',
  
  -- C: Capital Strength (ความเพียงพอของเงินทุนต่อความเสี่ยง)
  `c1_reserve_to_assets` decimal(10,4) DEFAULT NULL COMMENT 'C1: อัตราส่วนทุนสำรองต่อสินทรัพย์ (เท่า) = ทุนสำรอง ÷ สินทรัพย์ทั้งสิ้น',
  `c1_score` decimal(5,2) DEFAULT NULL COMMENT 'C1: คะแนน (0-5)',
  `c1_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'C1: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `c2_equity_growth_rate` decimal(10,4) DEFAULT NULL COMMENT 'C2: อัตราการเติบโตทุนของสหกรณ์ (%) = (ทุนปีปัจจุบัน - ทุนปีก่อน) ÷ ทุนปีก่อน × 100',
  `c2_score` decimal(5,2) DEFAULT NULL COMMENT 'C2: คะแนน (0-5)',
  `c2_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'C2: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `c3_return_on_equity` decimal(10,4) DEFAULT NULL COMMENT 'C3: อัตราผลตอบแทนต่อส่วนของทุน (%) = กำไรสุทธิ × 100 ÷ ทุนของสหกรณ์ถัวเฉลี่ย',
  `c3_score` decimal(5,2) DEFAULT NULL COMMENT 'C3: คะแนน (0-5)',
  `c3_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'C3: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `c4_debt_to_equity` decimal(10,4) DEFAULT NULL COMMENT 'C4: อัตราส่วนหนี้สินต่อทุน (เท่า) = หนี้สินทั้งสิ้น ÷ ทุนของสหกรณ์',
  `c4_score` decimal(5,2) DEFAULT NULL COMMENT 'C4: คะแนน (0-5)',
  `c4_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'C4: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `c5_debt_growth_rate` decimal(10,4) DEFAULT NULL COMMENT 'C5: อัตราการเติบโตของหนี้ (%) = (หนี้ปีปัจจุบัน - หนี้ปีก่อน) ÷ หนี้ปีก่อน × 100',
  `c5_score` decimal(5,2) DEFAULT NULL COMMENT 'C5: คะแนน (0-5)',
  `c5_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'C5: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  -- A: Asset Quality (คุณภาพของสินทรัพย์)
  `a1_overdue_receivable_ratio` decimal(10,4) DEFAULT NULL COMMENT 'A1: อัตราหนี้ค้างชำระ (%) = หนี้ค้างชำระ ÷ หนี้ที่ถึงกำหนดชำระ × 100',
  `a1_score` decimal(5,2) DEFAULT NULL COMMENT 'A1: คะแนน (0-5)',
  `a1_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'A1: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `a2_asset_turnover` decimal(10,4) DEFAULT NULL COMMENT 'A2: อัตราหมุนของสินทรัพย์ (เท่า) = รายได้จากการขาย/บริการ ÷ สินทรัพย์ทั้งสิ้นถัวเฉลี่ย',
  `a2_score` decimal(5,2) DEFAULT NULL COMMENT 'A2: คะแนน (0-5)',
  `a2_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'A2: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `a3_return_on_assets` decimal(10,4) DEFAULT NULL COMMENT 'A3: อัตราผลตอบแทนต่อสินทรัพย์ (%) = กำไรจากการดำเนินงาน ÷ สินทรัพย์ทั้งสิ้นถัวเฉลี่ย × 100',
  `a3_score` decimal(5,2) DEFAULT NULL COMMENT 'A3: คะแนน (0-5)',
  `a3_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'A3: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `a4_asset_growth_rate` decimal(10,4) DEFAULT NULL COMMENT 'A4: อัตราการเติบโตของสินทรัพย์ (%) = (สินทรัพย์ปีปัจจุบัน - สินทรัพย์ปีก่อน) ÷ สินทรัพย์ปีก่อน × 100',
  `a4_score` decimal(5,2) DEFAULT NULL COMMENT 'A4: คะแนน (0-5)',
  `a4_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'A4: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  -- M: Management Quality (คุณภาพการบริหารจัดการ)
  `m1_business_growth_rate` decimal(10,4) DEFAULT NULL COMMENT 'M1: อัตราการเติบโตของธุรกิจ (%) = (มูลค่าธุรกิจปีปัจจุบัน - มูลค่าธุรกิจปีก่อน) ÷ มูลค่าธุรกิจปีก่อน × 100',
  `m1_score` decimal(5,2) DEFAULT NULL COMMENT 'M1: คะแนน (0-5)',
  `m1_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'M1: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  -- E: Earning Sufficiency (ความเพียงพอของกำไร)
  `e1_earning_per_member` decimal(10,4) DEFAULT NULL COMMENT 'E1: กำไรต่อสมาชิก (บาท/คน) = กำไรสุทธิ ÷ จำนวนสมาชิก',
  `e1_score` decimal(5,2) DEFAULT NULL COMMENT 'E1: คะแนน (0-5)',
  `e1_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E1: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `e2_saving_per_member` decimal(10,4) DEFAULT NULL COMMENT 'E2: เงินออมต่อสมาชิก (บาท/คน) = (เงินรับฝากสมาชิก + ทุนเรือนหุ้น) ÷ จำนวนสมาชิก',
  `e2_score` decimal(5,2) DEFAULT NULL COMMENT 'E2: คะแนน (0-5)',
  `e2_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E2: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `e3_operating_expense_ratio` decimal(10,4) DEFAULT NULL COMMENT 'E3: อัตราค่าใช้จ่ายดำเนินงานต่อกำไร (%) = ค่าใช้จ่ายดำเนินงาน ÷ กำไรก่อนหักค่าใช้จ่าย × 100',
  `e3_score` decimal(5,2) DEFAULT NULL COMMENT 'E3: คะแนน (0-5)',
  `e3_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E3: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `e4_reserve_growth_rate` decimal(10,4) DEFAULT NULL COMMENT 'E4: อัตราการเติบโตของทุนสำรอง (%) = (ทุนสำรองปัจจุบัน - ทุนสำรองก่อน) ÷ ทุนสำรองก่อน × 100',
  `e4_score` decimal(5,2) DEFAULT NULL COMMENT 'E4: คะแนน (0-5)',
  `e4_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E4: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `e5_other_reserve_growth_rate` decimal(10,4) DEFAULT NULL COMMENT 'E5: อัตราการเติบโตของทุนสะสมอื่น (%) = (ทุนสะสมอื่นปัจจุบัน - ทุนสะสมอื่นก่อน) ÷ ทุนสะสมอื่นก่อน × 100',
  `e5_score` decimal(5,2) DEFAULT NULL COMMENT 'E5: คะแนน (0-5)',
  `e5_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E5: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `e6_debt_per_member` decimal(10,4) DEFAULT NULL COMMENT 'E6: หนี้สินต่อสมาชิก (บาท/คน) = (ลูกหนี้เงินกู้ + ลูกหนี้บริการ + ลูกหนี้การค้า) ÷ จำนวนสมาชิก',
  `e6_score` decimal(5,2) DEFAULT NULL COMMENT 'E6: คะแนน (0-5)',
  `e6_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E6: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `e7_profit_growth_rate` decimal(10,4) DEFAULT NULL COMMENT 'E7: อัตราการเติบโตของกำไร (%) = (กำไรสุทธิปัจจุบัน - กำไรสุทธิก่อน) ÷ กำไรสุทธิก่อน × 100',
  `e7_score` decimal(5,2) DEFAULT NULL COMMENT 'E7: คะแนน (0-5)',
  `e7_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E7: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `e8_net_profit_margin` decimal(10,4) DEFAULT NULL COMMENT 'E8: อัตรากำไรสุทธิ (%) = กำไรสุทธิ ÷ รายได้จากการขาย/บริการ × 100',
  `e8_score` decimal(5,2) DEFAULT NULL COMMENT 'E8: คะแนน (0-5)',
  `e8_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E8: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  -- L: Liquidity (สภาพคล่อง)
  `l1_current_ratio` decimal(10,4) DEFAULT NULL COMMENT 'L1: อัตราส่วนทุนหมุนเวียน (เท่า) = สินทรัพย์หมุนเวียน ÷ หนี้สินหมุนเวียน',
  `l1_score` decimal(5,2) DEFAULT NULL COMMENT 'L1: คะแนน (0-5)',
  `l1_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'L1: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `l2_inventory_turnover` decimal(10,4) DEFAULT NULL COMMENT 'L2: อัตราหมุนของสินค้า (เท่า) = ต้นทุนสินค้าขาย ÷ สินค้าคงเหลือถัวเฉลี่ย',
  `l2_score` decimal(5,2) DEFAULT NULL COMMENT 'L2: คะแนน (0-5)',
  `l2_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'L2: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  `l3_collection_ratio` decimal(10,4) DEFAULT NULL COMMENT 'L3: อัตราลูกหนี้ระยะสั้นที่ชำระได้ตามกำหนด (%) = ลูกหนี้ที่ชำระตรงเวลา ÷ ลูกหนี้ที่ถึงกำหนด × 100',
  `l3_score` decimal(5,2) DEFAULT NULL COMMENT 'L3: คะแนน (0-5)',
  `l3_rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'L3: ระดับ (ดี/พอใช้/ควรปรับปรุง)',
  
  -- คะแนนรวมและระดับรวม
  `total_score` decimal(6,2) DEFAULT NULL COMMENT 'คะแนนรวมทั้งหมด',
  `total_rating` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ระดับการประเมินรวม',
  
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างข้อมูล',
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขข้อมูลล่าสุด',
  
  PRIMARY KEY (`ratio_id`),
  UNIQUE KEY `coop_ratio_unique` (`coop_id`,`report_date`),
  KEY `idx_report_date` (`report_date`),
  KEY `idx_total_score` (`total_score`),
  CONSTRAINT `fk_ratio_coop` FOREIGN KEY (`coop_id`) REFERENCES `coop_info` (`coop_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางอัตราส่วนทางการเงิน CAMELS (ผลการคำนวณ)';

-- =====================================================
-- ส่วนที่ 4: Stored Procedures และ Functions
-- =====================================================

DELIMITER $$

-- =====================================================
-- Procedure: คำนวณอัตราส่วน CAMELS ทั้งหมด
-- =====================================================
DROP PROCEDURE IF EXISTS `sp_calculate_camels`$$
CREATE PROCEDURE `sp_calculate_camels`(
  IN p_input_id INT
)
BEGIN
  DECLARE v_coop_id INT;
  DECLARE v_report_date DATE;
  DECLARE v_coop_type TINYINT;
  
  -- ตัวแปรสำหรับเก็บค่าอัตราส่วน
  DECLARE v_c1_ratio DECIMAL(10,4);
  DECLARE v_c2_ratio DECIMAL(10,4);
  DECLARE v_c3_ratio DECIMAL(10,4);
  DECLARE v_c4_ratio DECIMAL(10,4);
  DECLARE v_c5_ratio DECIMAL(10,4);
  DECLARE v_a1_ratio DECIMAL(10,4);
  DECLARE v_a2_ratio DECIMAL(10,4);
  DECLARE v_a3_ratio DECIMAL(10,4);
  DECLARE v_a4_ratio DECIMAL(10,4);
  DECLARE v_m1_ratio DECIMAL(10,4);
  DECLARE v_e1_ratio DECIMAL(10,4);
  DECLARE v_e2_ratio DECIMAL(10,4);
  DECLARE v_e3_ratio DECIMAL(10,4);
  DECLARE v_e4_ratio DECIMAL(10,4);
  DECLARE v_e5_ratio DECIMAL(10,4);
  DECLARE v_e6_ratio DECIMAL(10,4);
  DECLARE v_e7_ratio DECIMAL(10,4);
  DECLARE v_e8_ratio DECIMAL(10,4);
  DECLARE v_l1_ratio DECIMAL(10,4);
  DECLARE v_l2_ratio DECIMAL(10,4);
  DECLARE v_l3_ratio DECIMAL(10,4);
  
  -- ดึงข้อมูล coop_id และ report_date
  SELECT coop_id, report_date INTO v_coop_id, v_report_date
  FROM camels_input_data WHERE input_id = p_input_id;
  
  -- ดึงข้อมูลประเภทสหกรณ์
  SELECT coop_type INTO v_coop_type
  FROM coop_info WHERE coop_id = v_coop_id;
  
  -- คำนวณอัตราส่วนทั้งหมดจากข้อมูล input
  SELECT 
    -- C1: อัตราส่วนทุนสำรองต่อสินทรัพย์
    CASE WHEN c1_total_assets > 0 THEN c1_legal_reserve / c1_total_assets ELSE NULL END,
    
    -- C2: อัตราการเติบโตทุนของสหกรณ์
    CASE WHEN c2_equity_previous > 0 THEN ((c2_equity_current - c2_equity_previous) / c2_equity_previous) * 100 ELSE NULL END,
    
    -- C3: อัตราผลตอบแทนต่อส่วนของทุน
    CASE WHEN c3_avg_equity > 0 THEN (c3_net_profit / c3_avg_equity) * 100 ELSE NULL END,
    
    -- C4: อัตราส่วนหนี้สินต่อทุน
    CASE WHEN c4_total_equity > 0 THEN c4_total_liabilities / c4_total_equity ELSE NULL END,
    
    -- C5: อัตราการเติบโตของหนี้
    CASE WHEN c5_liabilities_previous > 0 THEN ((c5_liabilities_current - c5_liabilities_previous) / c5_liabilities_previous) * 100 ELSE NULL END,
    
    -- A1: อัตราหนี้ค้างชำระ
    CASE WHEN a1_due_receivable > 0 THEN (a1_overdue_receivable / a1_due_receivable) * 100 ELSE NULL END,
    
    -- A2: อัตราหมุนของสินทรัพย์
    CASE WHEN a2_avg_total_assets > 0 THEN a2_revenue_main / a2_avg_total_assets ELSE NULL END,
    
    -- A3: อัตราผลตอบแทนต่อสินทรัพย์
    CASE WHEN a3_avg_total_assets > 0 THEN (a3_operating_income / a3_avg_total_assets) * 100 ELSE NULL END,
    
    -- A4: อัตราการเติบโตของสินทรัพย์
    CASE WHEN a4_assets_previous > 0 THEN ((a4_assets_current - a4_assets_previous) / a4_assets_previous) * 100 ELSE NULL END,
    
    -- M1: อัตราการเติบโตของธุรกิจ
    CASE WHEN m1_business_value_previous > 0 THEN ((m1_business_value_current - m1_business_value_previous) / m1_business_value_previous) * 100 ELSE NULL END,
    
    -- E1: กำไรต่อสมาชิก
    CASE WHEN e1_total_members > 0 THEN e1_net_profit / e1_total_members ELSE NULL END,
    
    -- E2: เงินออมต่อสมาชิก
    CASE WHEN e1_total_members > 0 THEN (e2_member_deposits + e2_share_capital) / e1_total_members ELSE NULL END,
    
    -- E3: อัตราค่าใช้จ่ายดำเนินงานต่อกำไร
    CASE WHEN e3_operating_income > 0 THEN (e3_operating_expenses / e3_operating_income) * 100 ELSE NULL END,
    
    -- E4: อัตราการเติบโตของทุนสำรอง
    CASE WHEN e4_reserve_previous > 0 THEN ((e4_reserve_current - e4_reserve_previous) / e4_reserve_previous) * 100 ELSE NULL END,
    
    -- E5: อัตราการเติบโตของทุนสะสมอื่น
    CASE WHEN e5_other_reserve_previous > 0 THEN ((e5_other_reserve_current - e5_other_reserve_previous) / e5_other_reserve_previous) * 100 ELSE NULL END,
    
    -- E6: หนี้สินต่อสมาชิก
    CASE WHEN e1_total_members > 0 THEN (e6_loan_receivable + e6_service_receivable + e6_account_receivable) / e1_total_members ELSE NULL END,
    
    -- E7: อัตราการเติบโตของกำไร
    CASE WHEN e7_profit_previous > 0 THEN ((e7_profit_current - e7_profit_previous) / e7_profit_previous) * 100 ELSE NULL END,
    
    -- E8: อัตรากำไรสุทธิ
    CASE WHEN e8_revenue_main > 0 THEN (e8_net_profit / e8_revenue_main) * 100 ELSE NULL END,
    
    -- L1: อัตราส่วนทุนหมุนเวียน
    CASE WHEN l1_current_liabilities > 0 THEN l1_current_assets / l1_current_liabilities ELSE NULL END,
    
    -- L2: อัตราหมุนของสินค้า
    CASE WHEN l2_avg_inventory > 0 THEN l2_cost_sales / l2_avg_inventory ELSE NULL END,
    
    -- L3: อัตราลูกหนี้ระยะสั้นที่ชำระได้ตามกำหนด
    CASE WHEN l3_due_short_term > 0 THEN (l3_paid_on_time / l3_due_short_term) * 100 ELSE NULL END
    
  INTO 
    v_c1_ratio, v_c2_ratio, v_c3_ratio, v_c4_ratio, v_c5_ratio,
    v_a1_ratio, v_a2_ratio, v_a3_ratio, v_a4_ratio,
    v_m1_ratio,
    v_e1_ratio, v_e2_ratio, v_e3_ratio, v_e4_ratio, v_e5_ratio, v_e6_ratio, v_e7_ratio, v_e8_ratio,
    v_l1_ratio, v_l2_ratio, v_l3_ratio
  FROM camels_input_data
  WHERE input_id = p_input_id;
  
  -- ลบข้อมูลเก่าถ้ามี
  DELETE FROM financial_ratios WHERE coop_id = v_coop_id AND report_date = v_report_date;
  
  -- บันทึกผลการคำนวณลงตาราง financial_ratios
  INSERT INTO financial_ratios (
    coop_id, report_date,
    
    -- C: Capital Strength
    c1_reserve_to_assets, c1_score, c1_rating,
    c2_equity_growth_rate, c2_score, c2_rating,
    c3_return_on_equity, c3_score, c3_rating,
    c4_debt_to_equity, c4_score, c4_rating,
    c5_debt_growth_rate, c5_score, c5_rating,
    
    -- A: Asset Quality
    a1_overdue_receivable_ratio, a1_score, a1_rating,
    a2_asset_turnover, a2_score, a2_rating,
    a3_return_on_assets, a3_score, a3_rating,
    a4_asset_growth_rate, a4_score, a4_rating,
    
    -- M: Management Quality
    m1_business_growth_rate, m1_score, m1_rating,
    
    -- E: Earning Sufficiency
    e1_earning_per_member, e1_score, e1_rating,
    e2_saving_per_member, e2_score, e2_rating,
    e3_operating_expense_ratio, e3_score, e3_rating,
    e4_reserve_growth_rate, e4_score, e4_rating,
    e5_other_reserve_growth_rate, e5_score, e5_rating,
    e6_debt_per_member, e6_score, e6_rating,
    e7_profit_growth_rate, e7_score, e7_rating,
    e8_net_profit_margin, e8_score, e8_rating,
    
    -- L: Liquidity
    l1_current_ratio, l1_score, l1_rating,
    l2_inventory_turnover, l2_score, l2_rating,
    l3_collection_ratio, l3_score, l3_rating,
    
    created_date, updated_date
  )
  VALUES (
    v_coop_id, v_report_date,
    
    -- C1
    v_c1_ratio,
    fn_calculate_score(v_coop_type, 'C1', v_c1_ratio),
    fn_get_rating(v_coop_type, 'C1', v_c1_ratio),
    
    -- C2
    v_c2_ratio,
    fn_calculate_score(v_coop_type, 'C2', v_c2_ratio),
    fn_get_rating(v_coop_type, 'C2', v_c2_ratio),
    
    -- C3
    v_c3_ratio,
    fn_calculate_score(v_coop_type, 'C3', v_c3_ratio),
    fn_get_rating(v_coop_type, 'C3', v_c3_ratio),
    
    -- C4
    v_c4_ratio,
    fn_calculate_score(v_coop_type, 'C4', v_c4_ratio),
    fn_get_rating(v_coop_type, 'C4', v_c4_ratio),
    
    -- C5
    v_c5_ratio,
    fn_calculate_score(v_coop_type, 'C5', v_c5_ratio),
    fn_get_rating(v_coop_type, 'C5', v_c5_ratio),
    
    -- A1
    v_a1_ratio,
    fn_calculate_score(v_coop_type, 'A1', v_a1_ratio),
    fn_get_rating(v_coop_type, 'A1', v_a1_ratio),
    
    -- A2
    v_a2_ratio,
    fn_calculate_score(v_coop_type, 'A2', v_a2_ratio),
    fn_get_rating(v_coop_type, 'A2', v_a2_ratio),
    
    -- A3
    v_a3_ratio,
    fn_calculate_score(v_coop_type, 'A3', v_a3_ratio),
    fn_get_rating(v_coop_type, 'A3', v_a3_ratio),
    
    -- A4
    v_a4_ratio,
    fn_calculate_score(v_coop_type, 'A4', v_a4_ratio),
    fn_get_rating(v_coop_type, 'A4', v_a4_ratio),
    
    -- M1
    v_m1_ratio,
    fn_calculate_score(v_coop_type, 'M1', v_m1_ratio),
    fn_get_rating(v_coop_type, 'M1', v_m1_ratio),
    
    -- E1
    v_e1_ratio,
    fn_calculate_score(v_coop_type, 'E1', v_e1_ratio),
    fn_get_rating(v_coop_type, 'E1', v_e1_ratio),
    
    -- E2
    v_e2_ratio,
    fn_calculate_score(v_coop_type, 'E2', v_e2_ratio),
    fn_get_rating(v_coop_type, 'E2', v_e2_ratio),
    
    -- E3
    v_e3_ratio,
    fn_calculate_score(v_coop_type, 'E3', v_e3_ratio),
    fn_get_rating(v_coop_type, 'E3', v_e3_ratio),
    
    -- E4
    v_e4_ratio,
    fn_calculate_score(v_coop_type, 'E4', v_e4_ratio),
    fn_get_rating(v_coop_type, 'E4', v_e4_ratio),
    
    -- E5
    v_e5_ratio,
    fn_calculate_score(v_coop_type, 'E5', v_e5_ratio),
    fn_get_rating(v_coop_type, 'E5', v_e5_ratio),
    
    -- E6
    v_e6_ratio,
    fn_calculate_score(v_coop_type, 'E6', v_e6_ratio),
    fn_get_rating(v_coop_type, 'E6', v_e6_ratio),
    
    -- E7
    v_e7_ratio,
    fn_calculate_score(v_coop_type, 'E7', v_e7_ratio),
    fn_get_rating(v_coop_type, 'E7', v_e7_ratio),
    
    -- E8
    v_e8_ratio,
    fn_calculate_score(v_coop_type, 'E8', v_e8_ratio),
    fn_get_rating(v_coop_type, 'E8', v_e8_ratio),
    
    -- L1
    v_l1_ratio,
    fn_calculate_score(v_coop_type, 'L1', v_l1_ratio),
    fn_get_rating(v_coop_type, 'L1', v_l1_ratio),
    
    -- L2
    v_l2_ratio,
    fn_calculate_score(v_coop_type, 'L2', v_l2_ratio),
    fn_get_rating(v_coop_type, 'L2', v_l2_ratio),
    
    -- L3
    v_l3_ratio,
    fn_calculate_score(v_coop_type, 'L3', v_l3_ratio),
    fn_get_rating(v_coop_type, 'L3', v_l3_ratio),
    
    NOW(), NOW()
  );
  
  -- คำนวณคะแนนรวมและระดับรวม
  CALL sp_update_total_score(v_coop_id, v_report_date);
  
END$$

-- =====================================================
-- Procedure: อัปเดตคะแนนรวมและระดับรวม
-- =====================================================
DROP PROCEDURE IF EXISTS `sp_update_total_score`$$
CREATE PROCEDURE `sp_update_total_score`(
  IN p_coop_id INT,
  IN p_report_date DATE
)
BEGIN
  DECLARE v_total_score DECIMAL(6,2);
  DECLARE v_total_rating VARCHAR(50);
  
  -- คำนวณคะแนนรวม (เฉลี่ยจากทุกอัตราส่วนที่มีคะแนน)
  SELECT 
    ROUND(
      (IFNULL(c1_score, 0) + IFNULL(c2_score, 0) + IFNULL(c3_score, 0) + IFNULL(c4_score, 0) + IFNULL(c5_score, 0) +
       IFNULL(a1_score, 0) + IFNULL(a2_score, 0) + IFNULL(a3_score, 0) + IFNULL(a4_score, 0) +
       IFNULL(m1_score, 0) +
       IFNULL(e1_score, 0) + IFNULL(e2_score, 0) + IFNULL(e3_score, 0) + IFNULL(e4_score, 0) + 
       IFNULL(e5_score, 0) + IFNULL(e6_score, 0) + IFNULL(e7_score, 0) + IFNULL(e8_score, 0) +
       IFNULL(l1_score, 0) + IFNULL(l2_score, 0) + IFNULL(l3_score, 0)
      ) / 21, 2
    ) INTO v_total_score
  FROM financial_ratios
  WHERE coop_id = p_coop_id AND report_date = p_report_date;
  
  -- กำหนดระดับรวม
  IF v_total_score >= 4.00 THEN
    SET v_total_rating = 'ดีมาก (Strong)';
  ELSEIF v_total_score >= 3.00 THEN
    SET v_total_rating = 'ดี (Satisfactory)';
  ELSEIF v_total_score >= 2.00 THEN
    SET v_total_rating = 'พอใช้ (Fair)';
  ELSEIF v_total_score >= 1.00 THEN
    SET v_total_rating = 'ควรปรับปรุง (Marginal)';
  ELSE
    SET v_total_rating = 'ต้องปรับปรุงเร่งด่วน (Unsatisfactory)';
  END IF;
  
  -- อัปเดตคะแนนรวม
  UPDATE financial_ratios
  SET 
    total_score = v_total_score,
    total_rating = v_total_rating,
    updated_date = NOW()
  WHERE coop_id = p_coop_id AND report_date = p_report_date;
  
END$$

-- =====================================================
-- Function: คำนวณคะแนนตามเกณฑ์
-- =====================================================
DROP FUNCTION IF EXISTS `fn_calculate_score`$$
CREATE FUNCTION `fn_calculate_score`(
  p_coop_type TINYINT,
  p_ratio_code VARCHAR(10),
  p_value DECIMAL(10,4)
)
RETURNS DECIMAL(5,2)
DETERMINISTIC
BEGIN
  DECLARE v_score DECIMAL(5,2);
  DECLARE v_good_min DECIMAL(10,4);
  DECLARE v_good_max DECIMAL(10,4);
  DECLARE v_fair_min DECIMAL(10,4);
  DECLARE v_fair_max DECIMAL(10,4);
  DECLARE v_poor_min DECIMAL(10,4);
  DECLARE v_poor_max DECIMAL(10,4);
  DECLARE v_is_higher_better TINYINT;
  
  -- ถ้าค่าเป็น NULL ให้คืนค่า 0
  IF p_value IS NULL THEN
    RETURN 0.00;
  END IF;
  
  -- ดึงเกณฑ์การให้คะแนน
  SELECT good_min, good_max, fair_min, fair_max, poor_min, poor_max, is_higher_better
  INTO v_good_min, v_good_max, v_fair_min, v_fair_max, v_poor_min, v_poor_max, v_is_higher_better
  FROM camels_scoring_criteria
  WHERE coop_type = p_coop_type AND ratio_code = p_ratio_code
  LIMIT 1;
  
  -- คำนวณคะแนน
  IF v_is_higher_better = 1 THEN
    -- ค่าสูงดีกว่า
    IF p_value >= IFNULL(v_good_min, p_value) THEN
      SET v_score = 5.00;
    ELSEIF p_value >= IFNULL(v_fair_min, 0) AND p_value < IFNULL(v_fair_max, p_value) THEN
      SET v_score = 3.00;
    ELSE
      SET v_score = 1.00;
    END IF;
  ELSE
    -- ค่าต่ำดีกว่า
    IF p_value <= IFNULL(v_good_max, p_value) THEN
      SET v_score = 5.00;
    ELSEIF p_value > IFNULL(v_fair_min, 0) AND p_value <= IFNULL(v_fair_max, p_value) THEN
      SET v_score = 3.00;
    ELSE
      SET v_score = 1.00;
    END IF;
  END IF;
  
  RETURN v_score;
END$$

-- =====================================================
-- Function: ให้ระดับตามเกณฑ์
-- =====================================================
DROP FUNCTION IF EXISTS `fn_get_rating`$$
CREATE FUNCTION `fn_get_rating`(
  p_coop_type TINYINT,
  p_ratio_code VARCHAR(10),
  p_value DECIMAL(10,4)
)
RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
  DECLARE v_rating VARCHAR(20);
  DECLARE v_good_min DECIMAL(10,4);
  DECLARE v_good_max DECIMAL(10,4);
  DECLARE v_fair_min DECIMAL(10,4);
  DECLARE v_fair_max DECIMAL(10,4);
  DECLARE v_is_higher_better TINYINT;
  
  -- ถ้าค่าเป็น NULL ให้คืนค่า NULL
  IF p_value IS NULL THEN
    RETURN 'ไม่มีข้อมูล';
  END IF;
  
  -- ดึงเกณฑ์การให้คะแนน
  SELECT good_min, good_max, fair_min, fair_max, is_higher_better
  INTO v_good_min, v_good_max, v_fair_min, v_fair_max, v_is_higher_better
  FROM camels_scoring_criteria
  WHERE coop_type = p_coop_type AND ratio_code = p_ratio_code
  LIMIT 1;
  
  -- กำหนดระดับ
  IF v_is_higher_better = 1 THEN
    -- ค่าสูงดีกว่า
    IF p_value >= IFNULL(v_good_min, p_value) THEN
      SET v_rating = 'ดี';
    ELSEIF p_value >= IFNULL(v_fair_min, 0) AND p_value < IFNULL(v_fair_max, p_value) THEN
      SET v_rating = 'พอใช้';
    ELSE
      SET v_rating = 'ควรปรับปรุง';
    END IF;
  ELSE
    -- ค่าต่ำดีกว่า
    IF p_value <= IFNULL(v_good_max, p_value) THEN
      SET v_rating = 'ดี';
    ELSEIF p_value > IFNULL(v_fair_min, 0) AND p_value <= IFNULL(v_fair_max, p_value) THEN
      SET v_rating = 'พอใช้';
    ELSE
      SET v_rating = 'ควรปรับปรุง';
    END IF;
  END IF;
  
  RETURN v_rating;
END$$

DELIMITER ;

-- =====================================================
-- ส่วนที่ 5: Views สำหรับการแสดงผล
-- =====================================================

-- =====================================================
-- View: ข้อมูลภาพรวมสหกรณ์พร้อมผลการประเมินล่าสุด
-- =====================================================
DROP VIEW IF EXISTS `v_coop_overview`;
CREATE VIEW `v_coop_overview` AS
SELECT 
  c.coop_id,
  c.coop_code,
  c.coop_name,
  ct.type_name AS coop_type_name,
  c.province,
  c.district,
  c.total_members,
  c.status,
  r.report_date AS latest_report_date,
  r.total_score,
  r.total_rating
FROM coop_info c
LEFT JOIN coop_types ct ON c.coop_type = ct.type_id
LEFT JOIN financial_ratios r ON c.coop_id = r.coop_id
  AND r.report_date = (
    SELECT MAX(report_date) 
    FROM financial_ratios 
    WHERE coop_id = c.coop_id
  )
ORDER BY c.coop_name;

-- =====================================================
-- View: สรุปผลการประเมิน CAMELS
-- =====================================================
DROP VIEW IF EXISTS `v_camels_summary`;
CREATE VIEW `v_camels_summary` AS
SELECT 
  r.coop_id,
  c.coop_name,
  ct.type_name AS coop_type_name,
  r.report_date,
  
  -- C: Capital Strength
  r.c1_reserve_to_assets, r.c1_rating,
  r.c4_debt_to_equity, r.c4_rating,
  
  -- A: Asset Quality
  r.a3_return_on_assets, r.a3_rating,
  
  -- E: Earning Sufficiency
  r.e3_operating_expense_ratio, r.e3_rating,
  r.e8_net_profit_margin, r.e8_rating,
  
  -- L: Liquidity
  r.l1_current_ratio, r.l1_rating,
  r.l3_collection_ratio, r.l3_rating,
  
  -- คะแนนรวม
  r.total_score,
  r.total_rating
FROM financial_ratios r
INNER JOIN coop_info c ON r.coop_id = c.coop_id
INNER JOIN coop_types ct ON c.coop_type = ct.type_id
ORDER BY r.report_date DESC, c.coop_name;

-- =====================================================
-- View: รายงานกิจกรรมผู้ใช้ล่าสุด
-- =====================================================
DROP VIEW IF EXISTS `v_recent_activities`;
CREATE VIEW `v_recent_activities` AS
SELECT 
  a.log_id,
  u.username,
  u.full_name,
  a.action,
  a.table_name,
  a.description,
  a.ip_address,
  a.created_date
FROM activity_logs a
LEFT JOIN users u ON a.user_id = u.user_id
ORDER BY a.created_date DESC
LIMIT 100;

-- =====================================================
-- ส่วนที่ 6: ข้อมูลตัวอย่าง (Sample Data)
-- =====================================================

-- ตัวอย่างสหกรณ์
INSERT INTO `coop_info` 
(`coop_code`, `coop_name`, `coop_type`, `coop_size`, `province`, `district`, `total_members`, `status`, `created_date`) 
VALUES
('COOP001', 'สหกรณ์ออมทรัพย์ครูขอนแก่น', 6, 4, 'ขอนแก่น', 'เมืองขอนแก่น', 5000, 1, NOW()),
('COOP002', 'สหกรณ์การเกษตรบ้านไผ่', 1, 2, 'ขอนแก่น', 'บ้านไผ่', 350, 1, NOW()),
('COOP003', 'สหกรณ์ร้านค้าชุมชนมหาสารคาม', 4, 1, 'มหาสารคาม', 'เมืองมหาสารคาม', 120, 1, NOW());

-- =====================================================
-- สิ้นสุดการสร้างฐานข้อมูล
-- =====================================================
-- 
-- หมายเหตุ:
-- 1. ไฟล์นี้รองรับ MySQL 8.0+ และ XAMPP 8.0.30
-- 2. ใช้ UTF-8 (utf8mb4) สำหรับรองรับภาษาไทยและ Emoji
-- 3. มีระบบ User Authentication และ Activity Logging
-- 4. สามารถขยายเพิ่มเติมได้ตามความต้องการ
-- 
-- การใช้งาน:
-- 1. Import ไฟล์นี้ผ่าน phpMyAdmin หรือ MySQL Command Line
-- 2. Login ด้วย username: admin, password: admin123
-- 3. เปลี่ยนรหัสผ่านทันทีหลังจาก Login ครั้งแรก
-- 
-- =====================================================