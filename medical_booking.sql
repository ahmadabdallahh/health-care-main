-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 01:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medical_booking`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `BookAppointment` (IN `p_user_id` INT, IN `p_doctor_id` INT, IN `p_clinic_id` INT, IN `p_appointment_date` DATE, IN `p_appointment_time` TIME, IN `p_notes` TEXT, OUT `p_appointment_id` INT)   BEGIN
    DECLARE appointment_datetime DATETIME;
    
    SET appointment_datetime = CONCAT(p_appointment_date, ' ', p_appointment_time);
    
    INSERT INTO appointments (user_id, doctor_id, clinic_id, appointment_date, appointment_time, appointment_datetime, notes)
    VALUES (p_user_id, p_doctor_id, p_clinic_id, p_appointment_date, p_appointment_time, appointment_datetime, p_notes);
    
    SET p_appointment_id = LAST_INSERT_ID();
    
    -- إنشاء تذكيرات تلقائية
    INSERT INTO reminder_logs (user_id, appointment_id, reminder_type, status)
    VALUES (p_user_id, p_appointment_id, 'email', 'sent');
    
    INSERT INTO push_notifications (user_id, title, message, type)
    VALUES (p_user_id, 'موعد جديد', CONCAT('تم حجز موعد جديد في ', p_appointment_date), 'appointment');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAvailableTimes` (IN `p_doctor_id` INT, IN `p_date` DATE)   BEGIN
    DECLARE day_name VARCHAR(20);
    SET day_name = LOWER(DAYNAME(p_date));
    
    SELECT 
        TIME_FORMAT(wh.start_time, '%H:%i') as available_time
    FROM working_hours wh
    WHERE wh.doctor_id = p_doctor_id 
    AND wh.day_of_week = day_name
    AND wh.is_available = 1
    AND TIME_FORMAT(wh.start_time, '%H:%i') NOT IN (
        SELECT TIME_FORMAT(a.appointment_time, '%H:%i')
        FROM appointments a
        WHERE a.doctor_id = p_doctor_id 
        AND a.appointment_date = p_date
        AND a.status IN ('pending', 'confirmed')
    )
    ORDER BY wh.start_time;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `clinic_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `appointment_datetime` datetime DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `doctor_id`, `clinic_id`, `appointment_date`, `appointment_time`, `appointment_datetime`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2024-08-10', '10:00:00', '2024-08-10 10:00:00', 'confirmed', 'موعد دوري للكشف', '2025-08-10 18:49:55', '2025-08-10 18:49:55'),
(2, 2, 2, 2, '2024-08-11', '14:00:00', '2024-08-11 14:00:00', 'pending', 'كشف عيون', '2025-08-10 18:49:55', '2025-08-10 18:49:55'),
(3, 3, 3, 3, '2024-08-12', '09:00:00', '2024-08-12 09:00:00', 'confirmed', 'تنظيف أسنان', '2025-08-10 18:49:55', '2025-08-10 18:49:55'),
(4, 4, 4, 4, '2024-08-13', '11:00:00', '2024-08-13 11:00:00', 'pending', 'كشف طفل', '2025-08-10 18:49:55', '2025-08-10 18:49:55'),
(5, 5, 5, 5, '2024-08-14', '13:00:00', '2024-08-14 13:00:00', 'confirmed', 'متابعة حمل', '2025-08-10 18:49:55', '2025-08-10 18:49:55');

--
-- Triggers `appointments`
--
DELIMITER $$
CREATE TRIGGER `send_automatic_reminders` AFTER INSERT ON `appointments` FOR EACH ROW BEGIN
    -- إرسال تذكير فوري
    INSERT INTO push_notifications (user_id, title, message, type)
    VALUES (NEW.user_id, 'تأكيد الحجز', 'تم تأكيد حجز موعدك بنجاح', 'appointment');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_doctor_rating` AFTER INSERT ON `appointments` FOR EACH ROW BEGIN
    -- هنا يمكن إضافة منطق لتحديث تقييم الطبيب بناءً على تقييمات المرضى
    -- (يتطلب جدول منفصل لتقييمات المرضى)
    UPDATE doctors 
    SET updated_at = NOW()
    WHERE id = NEW.doctor_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `appointment_info_view`
-- (See below for the actual view)
--
CREATE TABLE `appointment_info_view` (
`id` int(11)
,`appointment_date` date
,`appointment_time` time
,`appointment_datetime` datetime
,`status` enum('pending','confirmed','completed','cancelled')
,`notes` text
,`created_at` timestamp
,`patient_name` varchar(100)
,`patient_phone` varchar(20)
,`patient_email` varchar(100)
,`doctor_name` varchar(100)
,`doctor_phone` varchar(20)
,`clinic_name` varchar(200)
,`hospital_name` varchar(200)
,`specialty_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `governorate` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `governorate`, `created_at`) VALUES
(1, 'القاهرة', 'القاهرة', '2025-08-10 18:49:54'),
(2, 'الإسكندرية', 'الإسكندرية', '2025-08-10 18:49:54'),
(3, 'الجيزة', 'الجيزة', '2025-08-10 18:49:54'),
(4, 'المنوفية', 'المنوفية', '2025-08-10 18:49:54'),
(5, 'الشرقية', 'الشرقية', '2025-08-10 18:49:54'),
(6, 'الغربية', 'الغربية', '2025-08-10 18:49:54'),
(7, 'أسيوط', 'أسيوط', '2025-08-10 18:49:54'),
(8, 'سوهاج', 'سوهاج', '2025-08-10 18:49:54'),
(9, 'قنا', 'قنا', '2025-08-10 18:49:54'),
(10, 'الأقصر', 'الأقصر', '2025-08-10 18:49:54'),
(11, 'أسوان', 'أسوان', '2025-08-10 18:49:54'),
(12, 'بني سويف', 'بني سويف', '2025-08-10 18:49:54'),
(13, 'الفيوم', 'الفيوم', '2025-08-10 18:49:54'),
(14, 'المنيا', 'المنيا', '2025-08-10 18:49:54'),
(15, 'دمياط', 'دمياط', '2025-08-10 18:49:54'),
(16, 'بورسعيد', 'بورسعيد', '2025-08-10 18:49:54'),
(17, 'الإسماعيلية', 'الإسماعيلية', '2025-08-10 18:49:54'),
(18, 'السويس', 'السويس', '2025-08-10 18:49:54'),
(19, 'شرم الشيخ', 'جنوب سيناء', '2025-08-10 18:49:54'),
(20, 'دهب', 'جنوب سيناء', '2025-08-10 18:49:54'),
(21, 'القاهرة', 'القاهرة', '2025-08-10 22:28:24'),
(22, 'الإسكندرية', 'الإسكندرية', '2025-08-10 22:28:24'),
(23, 'الجيزة', 'الجيزة', '2025-08-10 22:28:24'),
(24, 'المنوفية', 'المنوفية', '2025-08-10 22:28:24'),
(25, 'الشرقية', 'الشرقية', '2025-08-10 22:28:24'),
(26, 'الغربية', 'الغربية', '2025-08-10 22:28:24'),
(27, 'أسيوط', 'أسيوط', '2025-08-10 22:28:24'),
(28, 'سوهاج', 'سوهاج', '2025-08-10 22:28:24'),
(29, 'قنا', 'قنا', '2025-08-10 22:28:24'),
(30, 'الأقصر', 'الأقصر', '2025-08-10 22:28:24'),
(31, 'أسوان', 'أسوان', '2025-08-10 22:28:24'),
(32, 'بني سويف', 'بني سويف', '2025-08-10 22:28:24'),
(33, 'الفيوم', 'الفيوم', '2025-08-10 22:28:24'),
(34, 'المنيا', 'المنيا', '2025-08-10 22:28:24'),
(35, 'دمياط', 'دمياط', '2025-08-10 22:28:24'),
(36, 'بورسعيد', 'بورسعيد', '2025-08-10 22:28:24'),
(37, 'الإسماعيلية', 'الإسماعيلية', '2025-08-10 22:28:24'),
(38, 'السويس', 'السويس', '2025-08-10 22:28:24'),
(39, 'شرم الشيخ', 'جنوب سيناء', '2025-08-10 22:28:24'),
(40, 'دهب', 'جنوب سيناء', '2025-08-10 22:28:24'),
(41, 'القاهرة', 'القاهرة', '2025-08-10 22:33:10'),
(42, 'الإسكندرية', 'الإسكندرية', '2025-08-10 22:33:10'),
(43, 'الجيزة', 'الجيزة', '2025-08-10 22:33:10'),
(44, 'المنوفية', 'المنوفية', '2025-08-10 22:33:10'),
(45, 'الشرقية', 'الشرقية', '2025-08-10 22:33:10'),
(46, 'الغربية', 'الغربية', '2025-08-10 22:33:10'),
(47, 'أسيوط', 'أسيوط', '2025-08-10 22:33:10'),
(48, 'سوهاج', 'سوهاج', '2025-08-10 22:33:10'),
(49, 'قنا', 'قنا', '2025-08-10 22:33:10'),
(50, 'الأقصر', 'الأقصر', '2025-08-10 22:33:10'),
(51, 'أسوان', 'أسوان', '2025-08-10 22:33:10'),
(52, 'بني سويف', 'بني سويف', '2025-08-10 22:33:10'),
(53, 'الفيوم', 'الفيوم', '2025-08-10 22:33:10'),
(54, 'المنيا', 'المنيا', '2025-08-10 22:33:10'),
(55, 'دمياط', 'دمياط', '2025-08-10 22:33:10'),
(56, 'بورسعيد', 'بورسعيد', '2025-08-10 22:33:10'),
(57, 'الإسماعيلية', 'الإسماعيلية', '2025-08-10 22:33:10'),
(58, 'السويس', 'السويس', '2025-08-10 22:33:10'),
(59, 'شرم الشيخ', 'جنوب سيناء', '2025-08-10 22:33:10'),
(60, 'دهب', 'جنوب سيناء', '2025-08-10 22:33:10');

-- --------------------------------------------------------

--
-- Table structure for table `clinics`
--

CREATE TABLE `clinics` (
  `id` int(11) NOT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `specialty_id` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `rating` decimal(3,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clinics`
--

INSERT INTO `clinics` (`id`, `hospital_id`, `name`, `specialty_id`, `address`, `phone`, `email`, `description`, `image`, `consultation_fee`, `rating`, `created_at`) VALUES
(1, 1, 'عيادة القلب', 1, 'الطابق الأول، مستشفى القاهرة العام', '02-23658975', NULL, 'عيادة متخصصة في أمراض القلب', NULL, 300.00, 4.60, '2025-08-10 18:49:54'),
(2, 1, 'عيادة العيون', 2, 'الطابق الثاني، مستشفى القاهرة العام', '02-23658976', NULL, 'عيادة متخصصة في أمراض العيون', NULL, 250.00, 4.40, '2025-08-10 18:49:54'),
(3, 2, 'عيادة الأسنان', 3, 'الطابق الأول، مستشفى المعادي', '02-25258964', NULL, 'عيادة متخصصة في طب الأسنان', NULL, 400.00, 4.70, '2025-08-10 18:49:54'),
(4, 2, 'عيادة الأطفال', 4, 'الطابق الأول، مستشفى المعادي', '02-25258965', NULL, 'عيادة متخصصة في طب الأطفال', NULL, 200.00, 4.30, '2025-08-10 18:49:54'),
(5, 3, 'عيادة النساء والولادة', 5, 'الطابق الأول، مستشفى مصر الجديدة', '02-24158975', NULL, 'عيادة متخصصة في صحة المرأة', NULL, 350.00, 4.50, '2025-08-10 18:49:54'),
(6, 3, 'عيادة الجلدية', 6, 'الطابق الثاني، مستشفى مصر الجديدة', '02-24158976', NULL, 'عيادة متخصصة في أمراض الجلد', NULL, 280.00, 4.20, '2025-08-10 18:49:54'),
(7, 4, 'عيادة العظام', 7, 'الطابق الأول، مستشفى الإسكندرية العام', '03-45678913', NULL, 'عيادة متخصصة في أمراض العظام', NULL, 320.00, 4.40, '2025-08-10 18:49:54'),
(8, 4, 'عيادة الأعصاب', 8, 'الطابق الثاني، مستشفى الإسكندرية العام', '03-45678914', NULL, 'عيادة متخصصة في أمراض الأعصاب', NULL, 380.00, 4.60, '2025-08-10 18:49:54'),
(9, 5, 'عيادة الباطنة', 9, 'الطابق الأول، مستشفى الجيزة التخصصي', '02-34567891', NULL, 'عيادة متخصصة في الأمراض الباطنية', NULL, 220.00, 4.30, '2025-08-10 18:49:54'),
(10, 5, 'عيادة الأنف والأذن والحنجرة', 10, 'الطابق الثاني، مستشفى الجيزة التخصصي', '02-34567892', NULL, 'عيادة متخصصة في أمراض الأنف والأذن', NULL, 300.00, 4.50, '2025-08-10 18:49:54');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `hospital_id`, `created_at`) VALUES
(1, 'قسم القلب', 'قسم متخصص في أمراض القلب والأوعية الدموية', 1, '2025-08-10 18:49:54'),
(2, 'قسم العيون', 'قسم متخصص في أمراض العيون والرؤية', 1, '2025-08-10 18:49:54'),
(3, 'قسم الأسنان', 'قسم متخصص في طب الأسنان', 2, '2025-08-10 18:49:54'),
(4, 'قسم الأطفال', 'قسم متخصص في رعاية الأطفال', 2, '2025-08-10 18:49:54'),
(5, 'قسم النساء والولادة', 'قسم متخصص في صحة المرأة والولادة', 3, '2025-08-10 18:49:54'),
(6, 'قسم الجلدية', 'قسم متخصص في أمراض الجلد', 3, '2025-08-10 18:49:54'),
(7, 'قسم العظام', 'قسم متخصص في أمراض العظام والمفاصل', 4, '2025-08-10 18:49:54'),
(8, 'قسم الأعصاب', 'قسم متخصص في أمراض الجهاز العصبي', 4, '2025-08-10 18:49:54'),
(9, 'قسم الباطنة', 'قسم متخصص في الأمراض الباطنية', 5, '2025-08-10 18:49:54'),
(10, 'قسم الأنف والأذن والحنجرة', 'قسم متخصص في أمراض الأنف والأذن والحنجرة', 5, '2025-08-10 18:49:54');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `specialty_id` int(11) DEFAULT NULL,
  `clinic_id` int(11) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `education` text DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `full_name`, `specialty_id`, `clinic_id`, `hospital_id`, `department_id`, `phone`, `email`, `experience_years`, `education`, `image`, `rating`, `is_active`, `consultation_fee`, `bio`, `created_at`, `user_id`) VALUES
(1, 'د. أحمد محمد علي', 1, 1, 1, 1, '01012345678', 'ahmed.ali@hospital.com', 15, 'دكتوراه في أمراض القلب - جامعة القاهرة', NULL, 4.80, 1, 300.00, 'طبيب قلب متخصص مع خبرة 15 عام في تشخيص وعلاج أمراض القلب والأوعية الدموية', '2025-08-10 18:49:54', NULL),
(2, 'د. فاطمة أحمد حسن', 2, 2, 1, 2, '01012345679', 'fatima.hassan@hospital.com', 12, 'دكتوراه في طب العيون - جامعة عين شمس', NULL, 4.60, 1, 250.00, 'طبيبة عيون متخصصة في جراحات العيون المتقدمة والليزر', '2025-08-10 18:49:54', NULL),
(3, 'د. محمد سعيد أحمد', 3, 3, 2, 3, '01012345680', 'mohamed.saeed@hospital.com', 18, 'دكتوراه في طب الأسنان - جامعة الإسكندرية', NULL, 4.90, 1, 400.00, 'طبيب أسنان متخصص في زراعة الأسنان وتقويم الأسنان', '2025-08-10 18:49:54', NULL),
(4, 'د. سارة محمود علي', 4, 4, 2, 4, '01012345681', 'sara.mahmoud@hospital.com', 10, 'دكتوراه في طب الأطفال - جامعة القاهرة', NULL, 4.50, 1, 200.00, 'طبيبة أطفال متخصصة في رعاية الأطفال حديثي الولادة', '2025-08-10 18:49:54', NULL),
(5, 'د. خالد عبد الرحمن', 5, 5, 3, 5, '01012345682', 'khaled.abdulrahman@hospital.com', 14, 'دكتوراه في طب النساء والولادة - جامعة الأزهر', NULL, 4.70, 1, 350.00, 'طبيب نساء وولادة متخصص في الولادة الطبيعية والقيصرية', '2025-08-10 18:49:54', NULL),
(6, 'د. نورا أحمد محمد', 6, 6, 3, 6, '01012345683', 'nora.ahmed@hospital.com', 8, 'دكتوراه في طب الجلدية - جامعة القاهرة', NULL, 4.30, 1, 280.00, 'طبيبة جلدية متخصصة في علاج الأمراض الجلدية والليزر', '2025-08-10 18:49:54', NULL),
(7, 'د. عمر محمد حسن', 7, 7, 4, 7, '01012345684', 'omar.mohamed@hospital.com', 16, 'دكتوراه في طب العظام - جامعة الإسكندرية', NULL, 4.40, 1, 320.00, 'طبيب عظام متخصص في جراحات العظام والمفاصل', '2025-08-10 18:49:54', NULL),
(8, 'د. ليلى أحمد سعيد', 8, 8, 4, 8, '01012345685', 'layla.ahmed@hospital.com', 13, 'دكتوراه في طب الأعصاب - جامعة عين شمس', NULL, 4.60, 1, 380.00, 'طبيبة أعصاب متخصصة في تشخيص وعلاج أمراض الجهاز العصبي', '2025-08-10 18:49:54', NULL),
(9, 'د. يوسف محمد علي', 9, 9, 5, 9, '01012345686', 'youssef.mohamed@hospital.com', 11, 'دكتوراه في طب الباطنة - جامعة القاهرة', NULL, 4.30, 1, 220.00, 'طبيب باطنة متخصص في الأمراض الباطنية والجهاز الهضمي', '2025-08-10 18:49:54', NULL),
(10, 'د. رنا أحمد حسن', 10, 10, 5, 10, '01012345687', 'rana.ahmed@hospital.com', 9, 'دكتوراه في طب الأنف والأذن والحنجرة - جامعة الأزهر', NULL, 4.50, 1, 300.00, 'طبيبة أنف وأذن وحنجرة متخصصة في جراحات الأنف والأذن', '2025-08-10 18:49:54', NULL),
(11, 'Ahmad Abdallah', NULL, NULL, NULL, NULL, '01006429525', 'test@main.com', NULL, NULL, '', 0.00, 1, 0.00, NULL, '2025-08-10 20:08:22', 19);

-- --------------------------------------------------------

--
-- Stand-in structure for view `doctor_info_view`
-- (See below for the actual view)
--
CREATE TABLE `doctor_info_view` (
`id` int(11)
,`full_name` varchar(100)
,`phone` varchar(20)
,`email` varchar(100)
,`experience_years` int(11)
,`rating` decimal(3,2)
,`consultation_fee` decimal(10,2)
,`bio` text
,`is_active` tinyint(1)
,`specialty_name` varchar(100)
,`hospital_name` varchar(200)
,`clinic_name` varchar(200)
,`department_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedules`
--

CREATE TABLE `doctor_schedules` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `day_of_week` enum('sunday','monday','tuesday','wednesday','thursday','friday','saturday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_schedules`
--

INSERT INTO `doctor_schedules` (`id`, `doctor_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(1, 1, 'sunday', '09:00:00', '17:00:00'),
(2, 1, 'monday', '09:00:00', '17:00:00'),
(3, 1, 'tuesday', '09:00:00', '17:00:00'),
(4, 1, 'wednesday', '09:00:00', '17:00:00'),
(5, 1, 'thursday', '09:00:00', '17:00:00'),
(6, 2, 'sunday', '10:00:00', '18:00:00'),
(7, 2, 'monday', '10:00:00', '18:00:00'),
(8, 2, 'tuesday', '10:00:00', '18:00:00'),
(9, 2, 'wednesday', '10:00:00', '18:00:00'),
(10, 2, 'thursday', '10:00:00', '18:00:00'),
(11, 3, 'saturday', '08:00:00', '16:00:00'),
(12, 3, 'sunday', '08:00:00', '16:00:00'),
(13, 3, 'monday', '08:00:00', '16:00:00'),
(14, 3, 'tuesday', '08:00:00', '16:00:00'),
(15, 3, 'wednesday', '08:00:00', '16:00:00'),
(16, 4, 'sunday', '09:00:00', '15:00:00'),
(17, 4, 'monday', '09:00:00', '15:00:00'),
(18, 4, 'tuesday', '09:00:00', '15:00:00'),
(19, 4, 'wednesday', '09:00:00', '15:00:00'),
(20, 4, 'thursday', '09:00:00', '15:00:00'),
(21, 5, 'sunday', '08:00:00', '14:00:00'),
(22, 5, 'monday', '08:00:00', '14:00:00'),
(23, 5, 'tuesday', '08:00:00', '14:00:00'),
(24, 5, 'wednesday', '08:00:00', '14:00:00'),
(25, 5, 'thursday', '08:00:00', '14:00:00');

-- --------------------------------------------------------

--
-- Stand-in structure for view `doctor_stats_view`
-- (See below for the actual view)
--
CREATE TABLE `doctor_stats_view` (
`id` int(11)
,`full_name` varchar(100)
,`rating` decimal(3,2)
,`consultation_fee` decimal(10,2)
,`total_appointments` bigint(21)
,`completed_appointments` bigint(21)
,`pending_appointments` bigint(21)
,`cancelled_appointments` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `type` enum('حكومي','خاص') DEFAULT 'حكومي',
  `rating` decimal(3,2) DEFAULT 0.00,
  `is_24h` tinyint(1) DEFAULT 0,
  `has_emergency` tinyint(1) DEFAULT 0,
  `has_insurance` tinyint(1) DEFAULT 0,
  `city_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `name`, `address`, `phone`, `email`, `website`, `description`, `image`, `type`, `rating`, `is_24h`, `has_emergency`, `has_insurance`, `city_id`, `created_at`) VALUES
(1, 'مستشفى القاهرة العام', 'شارع القصر العيني، القاهرة', '02-23658974', 'info@cairohospital.com', 'www.cairohospital.com', 'مستشفى عام متكامل الخدمات', NULL, 'حكومي', 4.50, 0, 1, 1, 1, '2025-08-10 18:49:54'),
(2, 'مستشفى المعادي', 'شارع النصر، المعادي، القاهرة', '02-25258963', 'info@maadi-hospital.com', 'www.maadi-hospital.com', 'مستشفى خاص بمعايير عالمية', NULL, 'حكومي', 4.80, 0, 1, 1, 1, '2025-08-10 18:49:54'),
(3, 'مستشفى مصر الجديدة', 'شارع الثورة، مصر الجديدة', '02-24158974', 'info@newcairo-hospital.com', 'www.newcairo-hospital.com', 'مستشفى حديث التجهيزات', NULL, 'حكومي', 4.20, 0, 0, 1, 1, '2025-08-10 18:49:54'),
(4, 'مستشفى الإسكندرية العام', 'شارع الإبراهيمية، الإسكندرية', '03-45678912', 'info@alexhospital.com', 'www.alexhospital.com', 'مستشفى عام في الإسكندرية', NULL, 'حكومي', 4.30, 0, 1, 1, 2, '2025-08-10 18:49:54'),
(5, 'مستشفى الجيزة التخصصي', 'شارع الهرم، الجيزة', '02-34567890', 'info@gizahospital.com', 'www.gizahospital.com', 'مستشفى تخصصي في الجيزة', NULL, 'حكومي', 4.60, 0, 1, 0, 3, '2025-08-10 18:49:54');

-- --------------------------------------------------------

--
-- Table structure for table `push_notifications`
--

CREATE TABLE `push_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('appointment','reminder','system') DEFAULT 'system',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `push_notifications`
--

INSERT INTO `push_notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 'تأكيد الموعد', 'تم تأكيد موعدك مع د. أحمد محمد علي في 10 أغسطس 2024', 'appointment', 0, '2025-08-10 18:49:55'),
(2, 2, 'تذكير بالموعد', 'تذكير: موعدك مع د. فاطمة أحمد حسن غداً في 2:00 مساءً', 'reminder', 0, '2025-08-10 18:49:55'),
(3, 3, 'موعد جديد', 'تم حجز موعد جديد مع د. محمد سعيد أحمد', 'appointment', 0, '2025-08-10 18:49:55'),
(4, 4, 'تحديث الموعد', 'تم تحديث موعدك مع د. سارة محمود علي', 'appointment', 1, '2025-08-10 18:49:55'),
(5, 5, 'تذكير بالموعد', 'تذكير: موعدك مع د. خالد عبد الرحمن بعد 6 ساعات', 'reminder', 0, '2025-08-10 18:49:55');

-- --------------------------------------------------------

--
-- Table structure for table `reminder_logs`
--

CREATE TABLE `reminder_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `reminder_type` enum('email','sms','push') NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('sent','failed') DEFAULT 'sent',
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reminder_logs`
--

INSERT INTO `reminder_logs` (`id`, `user_id`, `appointment_id`, `reminder_type`, `sent_at`, `status`, `error_message`) VALUES
(1, 1, 1, 'email', '2025-08-10 18:49:55', 'sent', NULL),
(2, 1, 1, 'sms', '2025-08-10 18:49:55', 'sent', NULL),
(3, 2, 2, 'email', '2025-08-10 18:49:55', 'sent', NULL),
(4, 3, 3, 'push', '2025-08-10 18:49:55', 'sent', NULL),
(5, 4, 4, 'sms', '2025-08-10 18:49:55', 'sent', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reminder_settings`
--

CREATE TABLE `reminder_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_enabled` tinyint(1) DEFAULT 1,
  `sms_enabled` tinyint(1) DEFAULT 1,
  `push_enabled` tinyint(1) DEFAULT 1,
  `reminder_hours_before` int(11) DEFAULT 24,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reminder_settings`
--

INSERT INTO `reminder_settings` (`id`, `user_id`, `email_enabled`, `sms_enabled`, `push_enabled`, `reminder_hours_before`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 24, '2025-08-10 18:49:55', '2025-08-10 18:49:55'),
(2, 2, 1, 0, 1, 12, '2025-08-10 18:49:55', '2025-08-10 18:49:55'),
(3, 3, 1, 1, 0, 48, '2025-08-10 18:49:55', '2025-08-10 18:49:55'),
(4, 4, 0, 1, 1, 24, '2025-08-10 18:49:55', '2025-08-10 18:49:55'),
(5, 5, 1, 1, 1, 6, '2025-08-10 18:49:55', '2025-08-10 18:49:55');

-- --------------------------------------------------------

--
-- Table structure for table `specialties`
--

CREATE TABLE `specialties` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specialties`
--

INSERT INTO `specialties` (`id`, `name`, `description`, `icon`) VALUES
(1, 'طب القلب', 'تخصص في أمراض القلب والأوعية الدموية', 'heart'),
(2, 'طب العيون', 'تخصص في أمراض العيون والرؤية', 'eye'),
(3, 'طب الأسنان', 'تخصص في صحة الفم والأسنان', 'tooth'),
(4, 'طب الأطفال', 'تخصص في رعاية الأطفال', 'baby'),
(5, 'طب النساء والولادة', 'تخصص في صحة المرأة والولادة', 'female'),
(6, 'طب الجلدية', 'تخصص في أمراض الجلد', 'skin'),
(7, 'طب العظام', 'تخصص في أمراض العظام والمفاصل', 'bone'),
(8, 'طب الأعصاب', 'تخصص في أمراض الجهاز العصبي', 'brain'),
(9, 'طب الباطنة', 'تخصص في الأمراض الباطنية', 'stomach'),
(10, 'طب الأنف والأذن والحنجرة', 'تخصص في أمراض الأنف والأذن والحنجرة', 'ear'),
(11, 'طب النفسية', 'تخصص في الأمراض النفسية والعصبية', 'brain'),
(12, 'طب التجميل', 'تخصص في جراحات التجميل', 'scissors');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `role` enum('patient','doctor','hospital') DEFAULT 'patient',
  `city_id` int(11) DEFAULT NULL,
  `insurance_provider` varchar(100) DEFAULT NULL,
  `insurance_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_type` varchar(50) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `date_of_birth`, `gender`, `role`, `city_id`, `insurance_provider`, `insurance_number`, `created_at`, `updated_at`, `user_type`) VALUES
(1, 'ahmed_patient', 'ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'أحمد محمد علي', '01012345688', '1990-05-15', 'male', 'patient', 1, 'شركة التأمين المصرية', 'INS001234', '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(2, 'fatima_patient', 'fatima@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'فاطمة أحمد حسن', '01012345689', '1985-08-22', 'female', 'patient', 2, 'شركة التأمين المصرية', 'INS001235', '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(3, 'mohamed_patient', 'mohamed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'محمد سعيد أحمد', '01012345690', '1992-12-10', 'male', 'patient', 3, NULL, NULL, '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(4, 'sara_patient', 'sara@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'سارة محمود علي', '01012345691', '1988-03-25', 'female', 'patient', 1, 'شركة التأمين المصرية', 'INS001236', '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(5, 'khaled_patient', 'khaled@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'خالد عبد الرحمن', '01012345692', '1995-07-18', 'male', 'patient', 2, NULL, NULL, '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(6, 'dr_ahmed', 'dr.ahmed@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. أحمد محمد علي', '01012345678', '1975-05-15', 'male', 'doctor', 1, NULL, NULL, '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(7, 'dr_fatima', 'dr.fatima@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. فاطمة أحمد حسن', '01012345679', '1980-08-22', 'female', 'doctor', 1, NULL, NULL, '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(8, 'dr_mohamed', 'dr.mohamed@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. محمد سعيد أحمد', '01012345680', '1978-12-10', 'male', 'doctor', 1, NULL, NULL, '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(9, 'dr_sara', 'dr.sara@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. سارة محمود علي', '01012345681', '1982-03-25', 'female', 'doctor', 1, NULL, NULL, '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(10, 'dr_khaled', 'dr.khaled@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. خالد عبد الرحمن', '01012345682', '1976-07-18', 'male', 'doctor', 1, NULL, NULL, '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(11, 'cairo_hospital', 'admin@cairohospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مستشفى القاهرة العام', '02-23658974', '1980-01-01', 'male', 'hospital', 1, NULL, NULL, '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(12, 'maadi_hospital', 'admin@maadi-hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مستشفى المعادي', '02-25258963', '1985-01-01', 'male', 'hospital', 1, NULL, NULL, '2025-08-10 18:49:54', '2025-08-10 18:49:54', 'user'),
(13, 'newcairo_hospital', 'admin@newcairo-hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مستشفى مصر الجديدة', '02-24158974', '1990-01-01', 'male', 'hospital', 1, NULL, NULL, '2025-08-10 18:49:54', '2025-08-10 19:56:24', 'admin'),
(19, 'test.main', 'test@main.com', '$2y$10$uzJrWgOkIWXd7gz/e8Y77O6jcYS0mestPekCEhMbZCD9wlzrEtniy', 'Ahmad Abdallah', '01006429525', '1990-06-09', 'male', 'doctor', NULL, NULL, NULL, '2025-08-10 20:08:22', '2025-08-10 20:12:13', 'doctor'),
(20, 'طبيب اختبار', 'doctor_1754865596@example.com', '$2y$10$lyh2A1kDJle6QQLdpmEHsuAbdSEu0w1qLXckYxRV3rZHTTcOYd7w2', 'طبيب اختبار', NULL, NULL, 'male', 'doctor', NULL, NULL, NULL, '2025-08-10 22:39:56', '2025-08-10 22:39:56', 'user'),
(28, 'طبيب اختبار1754866640', 'doctor_1754866639@example.com', '$2y$10$BVslalpGwFTw4IEo.HqsiOPPy7ZenV8RMv3kQI9RErwbA1Dw1AX9y', '', NULL, NULL, 'male', 'doctor', NULL, NULL, NULL, '2025-08-10 22:57:20', '2025-08-10 22:57:20', 'user'),
(30, 'طبيب اختبار1754866657', 'doctor_1754866657@example.com', '$2y$10$Giq7QUoAQcykhE0IH8.hSuaDQGFcNA.fmNoPdyzmYwYDl5qcZQDmC', '', NULL, NULL, 'male', 'doctor', NULL, NULL, NULL, '2025-08-10 22:57:37', '2025-08-10 22:57:37', 'user'),
(32, 'طبيب اختبار1754866742', 'doctor_1754866742@example.com', '$2y$10$XTwqmuYkOoC3Ox2TZspviuSRJIS85l2BqRUQUHTPvW4w.cQl/ThJC', '', NULL, NULL, 'male', 'doctor', NULL, NULL, NULL, '2025-08-10 22:59:02', '2025-08-10 22:59:02', 'user'),
(34, 'طبيب اختبار1754866809', 'doctor_1754866809@example.com', '$2y$10$qrg0J6..J5DtSLkYOSxa2Oy5yNAVZBJFkYKHc69BLvpy6YMJYr.wK', '', NULL, NULL, 'male', 'doctor', NULL, NULL, NULL, '2025-08-10 23:00:09', '2025-08-10 23:00:09', 'user'),
(36, 'طبيب اختبار1754866872', 'doctor_1754866872@example.com', '$2y$10$XSgEgrpcoBzhWJwLBBd6p.fjZd/Dq.SHMNQBqfHHjrPCec9RkO4vW', '', NULL, NULL, 'male', 'doctor', NULL, NULL, NULL, '2025-08-10 23:01:12', '2025-08-10 23:01:12', 'user'),
(38, 'طبيب اختبار1754866923', 'doctor_1754866923@example.com', '$2y$10$NUInxK36mB38ixdBPtC0le5VHymXT0EuQ62nw79T/FS8r53FUMR2a', '', NULL, NULL, 'male', 'doctor', NULL, NULL, NULL, '2025-08-10 23:02:03', '2025-08-10 23:02:03', 'user'),
(40, 'طبيب اختبار1754866942', 'doctor_1754866942@example.com', '$2y$10$LY.SrxQLyulGqs2Ce.1tLevS0bCrScWzMOUpuDbvmkUUAzsv8VOcG', '', NULL, NULL, 'male', 'doctor', NULL, NULL, NULL, '2025-08-10 23:02:22', '2025-08-10 23:02:22', 'user'),
(42, 'طبيب اختبار1754866991', 'doctor_1754866991@example.com', '$2y$10$mYiN2EGI5tJNescQ6DIVEOeH6n74bvVSvShiP4oD1pHreOQZsCfDO', '', NULL, NULL, 'male', 'doctor', NULL, NULL, NULL, '2025-08-10 23:03:11', '2025-08-10 23:03:11', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `working_hours`
--

CREATE TABLE `working_hours` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `day_of_week` enum('sunday','monday','tuesday','wednesday','thursday','friday','saturday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `working_hours`
--

INSERT INTO `working_hours` (`id`, `doctor_id`, `day_of_week`, `start_time`, `end_time`, `is_available`, `created_at`) VALUES
(1, 1, 'sunday', '09:00:00', '17:00:00', 1, '2025-08-10 18:49:55'),
(2, 1, 'monday', '09:00:00', '17:00:00', 1, '2025-08-10 18:49:55'),
(3, 1, 'tuesday', '09:00:00', '17:00:00', 1, '2025-08-10 18:49:55'),
(4, 1, 'wednesday', '09:00:00', '17:00:00', 1, '2025-08-10 18:49:55'),
(5, 1, 'thursday', '09:00:00', '17:00:00', 1, '2025-08-10 18:49:55'),
(6, 2, 'sunday', '10:00:00', '18:00:00', 1, '2025-08-10 18:49:55'),
(7, 2, 'monday', '10:00:00', '18:00:00', 1, '2025-08-10 18:49:55'),
(8, 2, 'tuesday', '10:00:00', '18:00:00', 1, '2025-08-10 18:49:55'),
(9, 2, 'wednesday', '10:00:00', '18:00:00', 1, '2025-08-10 18:49:55'),
(10, 2, 'thursday', '10:00:00', '18:00:00', 1, '2025-08-10 18:49:55'),
(11, 3, 'saturday', '08:00:00', '16:00:00', 1, '2025-08-10 18:49:55'),
(12, 3, 'sunday', '08:00:00', '16:00:00', 1, '2025-08-10 18:49:55'),
(13, 3, 'monday', '08:00:00', '16:00:00', 1, '2025-08-10 18:49:55'),
(14, 3, 'tuesday', '08:00:00', '16:00:00', 1, '2025-08-10 18:49:55'),
(15, 3, 'wednesday', '08:00:00', '16:00:00', 1, '2025-08-10 18:49:55'),
(16, 4, 'sunday', '09:00:00', '15:00:00', 1, '2025-08-10 18:49:55'),
(17, 4, 'monday', '09:00:00', '15:00:00', 1, '2025-08-10 18:49:55'),
(18, 4, 'tuesday', '09:00:00', '15:00:00', 1, '2025-08-10 18:49:55'),
(19, 4, 'wednesday', '09:00:00', '15:00:00', 1, '2025-08-10 18:49:55'),
(20, 4, 'thursday', '09:00:00', '15:00:00', 1, '2025-08-10 18:49:55'),
(21, 5, 'sunday', '08:00:00', '14:00:00', 1, '2025-08-10 18:49:55'),
(22, 5, 'monday', '08:00:00', '14:00:00', 1, '2025-08-10 18:49:55'),
(23, 5, 'tuesday', '08:00:00', '14:00:00', 1, '2025-08-10 18:49:55'),
(24, 5, 'wednesday', '08:00:00', '14:00:00', 1, '2025-08-10 18:49:55'),
(25, 5, 'thursday', '08:00:00', '14:00:00', 1, '2025-08-10 18:49:55');

-- --------------------------------------------------------

--
-- Structure for view `appointment_info_view`
--
DROP TABLE IF EXISTS `appointment_info_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `appointment_info_view`  AS SELECT `a`.`id` AS `id`, `a`.`appointment_date` AS `appointment_date`, `a`.`appointment_time` AS `appointment_time`, `a`.`appointment_datetime` AS `appointment_datetime`, `a`.`status` AS `status`, `a`.`notes` AS `notes`, `a`.`created_at` AS `created_at`, `u`.`full_name` AS `patient_name`, `u`.`phone` AS `patient_phone`, `u`.`email` AS `patient_email`, `d`.`full_name` AS `doctor_name`, `d`.`phone` AS `doctor_phone`, `c`.`name` AS `clinic_name`, `h`.`name` AS `hospital_name`, `s`.`name` AS `specialty_name` FROM (((((`appointments` `a` join `users` `u` on(`a`.`user_id` = `u`.`id`)) join `doctors` `d` on(`a`.`doctor_id` = `d`.`id`)) join `clinics` `c` on(`a`.`clinic_id` = `c`.`id`)) join `hospitals` `h` on(`c`.`hospital_id` = `h`.`id`)) join `specialties` `s` on(`d`.`specialty_id` = `s`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `doctor_info_view`
--
DROP TABLE IF EXISTS `doctor_info_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `doctor_info_view`  AS SELECT `d`.`id` AS `id`, `d`.`full_name` AS `full_name`, `d`.`phone` AS `phone`, `d`.`email` AS `email`, `d`.`experience_years` AS `experience_years`, `d`.`rating` AS `rating`, `d`.`consultation_fee` AS `consultation_fee`, `d`.`bio` AS `bio`, `d`.`is_active` AS `is_active`, `s`.`name` AS `specialty_name`, `h`.`name` AS `hospital_name`, `c`.`name` AS `clinic_name`, `dep`.`name` AS `department_name` FROM ((((`doctors` `d` left join `specialties` `s` on(`d`.`specialty_id` = `s`.`id`)) left join `hospitals` `h` on(`d`.`hospital_id` = `h`.`id`)) left join `clinics` `c` on(`d`.`clinic_id` = `c`.`id`)) left join `departments` `dep` on(`d`.`department_id` = `dep`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `doctor_stats_view`
--
DROP TABLE IF EXISTS `doctor_stats_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `doctor_stats_view`  AS SELECT `d`.`id` AS `id`, `d`.`full_name` AS `full_name`, `d`.`rating` AS `rating`, `d`.`consultation_fee` AS `consultation_fee`, count(`a`.`id`) AS `total_appointments`, count(case when `a`.`status` = 'completed' then 1 end) AS `completed_appointments`, count(case when `a`.`status` = 'pending' then 1 end) AS `pending_appointments`, count(case when `a`.`status` = 'cancelled' then 1 end) AS `cancelled_appointments` FROM (`doctors` `d` left join `appointments` `a` on(`d`.`id` = `a`.`doctor_id`)) GROUP BY `d`.`id`, `d`.`full_name`, `d`.`rating`, `d`.`consultation_fee` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clinic_id` (`clinic_id`),
  ADD KEY `idx_appointments_time` (`appointment_time`),
  ADD KEY `idx_appointments_status` (`status`),
  ADD KEY `idx_appointments_user` (`user_id`),
  ADD KEY `idx_appointments_doctor` (`doctor_id`),
  ADD KEY `idx_appointments_date` (`appointment_date`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clinics`
--
ALTER TABLE `clinics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_clinics_hospital` (`hospital_id`),
  ADD KEY `idx_clinics_specialty` (`specialty_id`),
  ADD KEY `idx_clinics_rating` (`rating`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_departments_hospital` (`hospital_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `clinic_id` (`clinic_id`),
  ADD KEY `idx_doctors_hospital` (`hospital_id`),
  ADD KEY `idx_doctors_active` (`is_active`),
  ADD KEY `idx_doctors_specialty` (`specialty_id`),
  ADD KEY `idx_doctors_rating` (`rating`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doctor_schedules_doctor` (`doctor_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hospitals_city` (`city_id`),
  ADD KEY `idx_hospitals_rating` (`rating`),
  ADD KEY `idx_hospitals_emergency` (`has_emergency`),
  ADD KEY `idx_hospitals_insurance` (`has_insurance`);

--
-- Indexes for table `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_push_notifications_user` (`user_id`),
  ADD KEY `idx_push_notifications_read` (`is_read`);

--
-- Indexes for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reminder_logs_user` (`user_id`),
  ADD KEY `idx_reminder_logs_appointment` (`appointment_id`);

--
-- Indexes for table `reminder_settings`
--
ALTER TABLE `reminder_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reminder_settings_user` (`user_id`);

--
-- Indexes for table `specialties`
--
ALTER TABLE `specialties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_city` (`city_id`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_username` (`username`);

--
-- Indexes for table `working_hours`
--
ALTER TABLE `working_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_working_hours_doctor` (`doctor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `clinics`
--
ALTER TABLE `clinics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `push_notifications`
--
ALTER TABLE `push_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reminder_settings`
--
ALTER TABLE `reminder_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `specialties`
--
ALTER TABLE `specialties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `working_hours`
--
ALTER TABLE `working_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clinics`
--
ALTER TABLE `clinics`
  ADD CONSTRAINT `clinics_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clinics_ibfk_2` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `doctors_ibfk_2` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctors_ibfk_3` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `doctors_ibfk_4` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `doctors_ibfk_5` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `doctors_ibfk_6` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_doctor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD CONSTRAINT `doctor_schedules_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD CONSTRAINT `hospitals_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hospitals_ibfk_2` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD CONSTRAINT `push_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  ADD CONSTRAINT `reminder_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reminder_logs_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reminder_settings`
--
ALTER TABLE `reminder_settings`
  ADD CONSTRAINT `reminder_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `working_hours`
--
ALTER TABLE `working_hours`
  ADD CONSTRAINT `working_hours_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
