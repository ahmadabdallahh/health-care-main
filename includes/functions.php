<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the config file is included to have access to get_db_connection()
require_once __DIR__ . '/../config.php';

// ====================================================================
// CONFIGURATION & CONSTANTS
// ====================================================================

// Define Base URL for asset linking if not already defined
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/app-demo/');
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ====================================================================
// DATABASE & CORE FUNCTIONS
// ====================================================================

require_once __DIR__ . '/../config/database.php';

/**
 * Redirect to a specific page.
 * @param string $url
 */
function redirect($url)
{
    // If the URL is relative, prepend BASE_URL
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = BASE_URL . $url;
    }
    header("Location: {$url}");
    exit();
}

// ====================================================================
// USER & AUTHENTICATION FUNCTIONS
// ====================================================================

/**
 * Check if a user is logged in.
 * @return bool
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Get the full data record for the currently logged-in user.
 * @return array|false The user's data as an associative array, or false if not found.
 */
function get_logged_in_user()
{
    if (!is_logged_in()) {
        return false;
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // If profile image is empty or file does not exist, set default avatar
            if (empty($user['profile_image']) || !file_exists($user['profile_image'])) {
                $user['profile_image'] = 'assets/images/default-avatar.png';
            }
        }

        return $user ? $user : null;

    } catch (Exception $e) {
        error_log("Get user error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check user role. Helper function for role checks.
 * @param string $role
 * @return bool
 */
function check_user_role($role)
{
    return is_logged_in() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === $role;
}

/**
 * Check if the logged-in user is an admin.
 * @return bool
 */
function is_admin()
{
    return check_user_role('admin');
}

/**
 * Check if the logged-in user is a doctor.
 * @return bool
 */
function is_doctor()
{
    return check_user_role('doctor');
}

/**
 * Check if the logged-in user is a patient.
 * @return bool
 */
function is_patient()
{
    return check_user_role('patient');
}

/**
 * Check if the logged-in user is a hospital representative.
 * @return bool
 */
function is_hospital()
{
    return check_user_role('hospital');
}

/**
 * Log out the current user.
 */
function logout_user()
{
    session_unset();
    session_destroy();
    redirect('login.php');
}

// ====================================================================
// INPUT SANITIZATION & VALIDATION FUNCTIONS
// ====================================================================

/**
 * Clean input data.
 * @param string $data
 * @return string
 */
function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email format.
 * @param string $email
 * @return bool
 */
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate password strength.
 * @param string $password
 * @return bool
 */
function validate_password($password)
{
    // Password must be at least 8 characters, with 1 uppercase, 1 lowercase, and 1 number.
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
}

/**
 * Hash a password.
 * @param string $password
 * @return string|false
 */
function hash_password($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

// ====================================================================
// OTHER APPLICATION-SPECIFIC FUNCTIONS
// ====================================================================

/**
 * Check for appointment conflicts.
 * @param int $doctor_id
 * @param string $appointment_date
 * @return bool
 */
function check_appointment_conflict($doctor_id, $appointment_date)
{
    try {
        $db = new Database();
        $conn = $db->getConnection();

        // Check for appointments within a 30-minute window of the requested time
        $stmt = $conn->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date BETWEEN DATE_SUB(?, INTERVAL 29 MINUTE) AND DATE_ADD(?, INTERVAL 29 MINUTE)");
        $stmt->execute([$doctor_id, $appointment_date, $appointment_date]);

        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        // Log error or handle it gracefully
        return true; // Assume conflict on DB error to be safe
    }
}

// Note: Other functions like register_user, book_appointment, etc., would go here.
// They are removed for this fix to avoid clutter, assuming they exist elsewhere or are not causing re-declaration issues.

// Enhanced input sanitization
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Validate phone number with international format support
function validate_phone_enhanced($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Check if it's a valid Egyptian phone number
    if (preg_match('/^(01)[0-9]{9}$/', $phone)) {
        return true;
    }

    // Check if it's a valid international format
    if (preg_match('/^\+[0-9]{10,15}$/', $phone)) {
        return true;
    }

    return false;
}

// Enhanced email validation
function validate_email_enhanced($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Check for common typos and disposable email domains
    $disposable_domains = ['tempmail.com', '10minutemail.com', 'guerrillamail.com'];
    $domain = substr(strrchr($email, "@"), 1);

    if (in_array(strtolower($domain), $disposable_domains)) {
        return false;
    }

    return true;
}

// Enhanced password validation
function validate_password_enhanced($password) {
    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل'];
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return ['success' => false, 'message' => 'يجب أن تحتوي على حرف كبير'];
    }

    if (!preg_match('/[a-z]/', $password)) {
        return ['success' => false, 'message' => 'يجب أن تحتوي على حرف صغير'];
    }

    if (!preg_match('/[0-9]/', $password)) {
        return ['success' => false, 'message' => 'يجب أن تحتوي على رقم'];
    }

    if (!preg_match('/[@$!%*?&]/', $password)) {
        return ['success' => false, 'message' => 'يجب أن تحتوي على رمز خاص'];
    }

    return ['success' => true, 'message' => 'كلمة المرور قوية'];
}

// Enhanced error handler with user-friendly messages
function handle_error_enhanced($error_type, $error_message, $context = []) {
    $error_codes = [
        'database_error' => 'حدث خطأ في قاعدة البيانات',
        'validation_error' => 'يوجد خطأ في البيانات المدخلة',
        'authorization_error' => 'ليس لديك صلاحية لهذا الإجراء',
        'system_error' => 'حدث خطأ في النظام',
        'network_error' => 'خطأ في الاتصال بالشبكة'
    ];

    $user_message = $error_codes[$error_type] ?? 'حدث خطأ غير متوقع';

    log_error($error_type, $error_message, $context);

    return [
        'success' => false,
        'message' => $user_message,
        'error_code' => $error_type
    ];
}

// تسجيل الأخطاء مع معلومات إضافية
function log_error($message, $context = []) {
    $log_message = $message;

    if (!empty($context)) {
        $log_message .= ' | Context: ' . json_encode($context);
    }

    $log_message .= ' | User: ' . ($_SESSION['user_id'] ?? 'Guest');
    $log_message .= ' | IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown');
    $log_message .= ' | Time: ' . date('Y-m-d H:i:s');

    error_log($log_message);
}

// Validate appointment booking to prevent double booking
function validate_appointment_booking($doctor_id, $clinic_id, $date, $time, $user_id) {
    global $conn;

    try {
        // Check if slot is available
        $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
        $stmt->execute([$doctor_id, $date, $time]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return ['success' => false, 'message' => 'هذا الوقت محجوز بالفعل'];
        }

        // Check if user already has appointment at this time
        $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE user_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
        $stmt->execute([$user_id, $date, $time]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return ['success' => false, 'message' => 'لديك موعد آخر في نفس الوقت'];
        }

        // Check if appointment is in the past
        $appointment_datetime = new DateTime($date . ' ' . $time);
        $now = new DateTime();

        if ($appointment_datetime < $now) {
            return ['success' => false, 'message' => 'لا يمكن حجز موعد في الماضي'];
        }

        return ['success' => true, 'message' => 'الموعد متاح'];

    } catch (PDOException $e) {
        log_error('Appointment validation failed', $e->getMessage(), ['doctor_id' => $doctor_id, 'date' => $date, 'time' => $time]);
        return ['success' => false, 'message' => 'حدث خطأ أثناء التحقق من الموعد'];
    }
}

// إنشاء رسالة تنبيه HTML
function create_alert($message, $type = 'info') {
    $icons = [
        'success' => 'fas fa-check-circle',
        'error' => 'fas fa-exclamation-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'info' => 'fas fa-info-circle'
    ];

    $icon = $icons[$type] ?? $icons['info'];

    return "
    <div class=\"message-{$type}\">
        <i class=\"{$icon}\"></i>
        <span>{$message}</span>
    </div>";
}

// إعادة توجيه مع رسالة
function redirect_with_message($url, $message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: {$url}");
    exit();
}

// عرض رسالة الفلاش
function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);

        return create_alert($message, $type);
    }

    return '';
}

// التحقق من صلاحية المستخدم
function check_user_permission($required_type) {
    // Bypass check for designated public pages
    global $public_page;
    if (isset($public_page) && $public_page === true) {
        return;
    }

    // أولاً، تحقق مما إذا كان المستخدم قد سجل دخوله
    if (!is_logged_in()) {
        return false;
    }

    $user_type = $_SESSION['user_type'] ?? '';

    // الأدمن لديه جميع الصلاحيات
    if ($user_type === 'admin') {
        return true;
    }

    return $user_type === $required_type;
}

// التحقق من انتهاء صلاحية الجلسة
function is_session_expired($timeout_minutes = 30) {
    if (!isset($_SESSION['login_time'])) {
        return true;
    }

    $timeout_seconds = $timeout_minutes * 60;
    return (time() - $_SESSION['login_time']) > $timeout_seconds;
}

// تسجيل دخول المستخدم - محسن للأمان
function login_user($email, $password) {
    // التحقق من محاولات تسجيل الدخول المتكررة
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $attempt_key = 'login_attempts_' . md5($ip_address);

    if (!isset($_SESSION[$attempt_key])) {
        $_SESSION[$attempt_key] = ['count' => 0, 'last_attempt' => time()];
    }

    // إعادة تعيين العداد بعد 15 دقيقة
    if (time() - $_SESSION[$attempt_key]['last_attempt'] > 900) {
        $_SESSION[$attempt_key] = ['count' => 0, 'last_attempt' => time()];
    }

    // منع بعد 5 محاولات فاشلة
    if ($_SESSION[$attempt_key]['count'] >= 5) {
        return ['success' => false, 'message' => 'تم حظرك مؤقتاً بسبب محاولات تسجيل دخول متكررة. حاول بعد 15 دقيقة.'];
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        if (!$conn) {
            return ['success' => false, 'message' => 'خطأ في الاتصال بقاعدة البيانات'];
        }

        // التحقق من صحة البيانات
        if (!validate_email($email)) {
            $_SESSION[$attempt_key]['count']++;
            $_SESSION[$attempt_key]['last_attempt'] = time();
            return ['success' => false, 'message' => 'البريد الإلكتروني غير صحيح'];
        }

        if (empty($password)) {
            $_SESSION[$attempt_key]['count']++;
            $_SESSION[$attempt_key]['last_attempt'] = time();
            return ['success' => false, 'message' => 'كلمة المرور مطلوبة'];
        }

        // البحث عن المستخدم
        $stmt = $conn->prepare("SELECT id, full_name, email, password, user_type, is_active, failed_login_attempts, last_failed_login FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION[$attempt_key]['count']++;
            $_SESSION[$attempt_key]['last_attempt'] = time();
            return ['success' => false, 'message' => 'البريد الإلكتروني غير موجود أو الحساب غير مفعل'];
        }

        // التحقق من محاولات تسجيل الدخول الفاشلة
        if ($user['failed_login_attempts'] >= 5 &&
            strtotime($user['last_failed_login']) > strtotime('-15 minutes')) {
            return ['success' => false, 'message' => 'تم حظر الحساب مؤقتاً بسبب محاولات تسجيل دخول متكررة'];
        }

        if (!verify_password($password, $user['password'])) {
            // تحديث عدد المحاولات الفاشلة
            $update_stmt = $conn->prepare("UPDATE users SET failed_login_attempts = failed_login_attempts + 1, last_failed_login = NOW() WHERE id = ?");
            $update_stmt->execute([$user['id']]);

            $_SESSION[$attempt_key]['count']++;
            $_SESSION[$attempt_key]['last_attempt'] = time();
            return ['success' => false, 'message' => 'كلمة المرور غير صحيحة'];
        }

        // إعادة تعيين عدد المحاولات الفاشلة
        $update_stmt = $conn->prepare("UPDATE users SET failed_login_attempts = 0, last_login = NOW() WHERE id = ?");
        $update_stmt->execute([$user['id']]);

        // تسجيل الدخول بنجاح - إعادة توليد معرف الجلسة
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // تسجيل نشاط تسجيل الدخول
        $log_stmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, user_agent, login_time) VALUES (?, ?, ?, NOW())");
        $log_stmt->execute([$user['id'], $_SESSION['ip_address'], $_SESSION['user_agent']]);

        // مسح محاولات تسجيل الدخول
        unset($_SESSION[$attempt_key]);

        return ['success' => true, 'message' => 'تم تسجيل الدخول بنجاح'];

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'حدث خطأ أثناء تسجيل الدخول'];
    }
}

// تسجيل مستخدم جديد
function register_user($data) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        if (!$conn) {
            return ['success' => false, 'message' => 'خطأ في الاتصال بقاعدة البيانات'];
        }

        // التحقق من صحة البيانات
        $validation_errors = [];

        if (empty($data['full_name']) || strlen($data['full_name']) < 2) {
            $validation_errors[] = 'الاسم الكامل مطلوب ويجب أن يكون حرفين على الأقل';
        }

        if (!validate_email($data['email'])) {
            $validation_errors[] = 'البريد الإلكتروني غير صحيح';
        }

        if (!validate_phone_enhanced($data['phone'])) {
            $validation_errors[] = 'رقم الهاتف غير صحيح';
        }

        $password_validation = validate_password_enhanced($data['password']);
        if (!$password_validation['success']) {
            $validation_errors = array_merge($validation_errors, $password_validation['messages']);
        }

        if ($data['password'] !== $data['confirm_password']) {
            $validation_errors[] = 'كلمة المرور غير متطابقة';
        }

        if (!validate_date($data['birth_date'])) {
            $validation_errors[] = 'تاريخ الميلاد غير صحيح';
        }

        if (!empty($validation_errors)) {
            return ['success' => false, 'message' => implode('<br>', $validation_errors)];
        }

        // التحقق من عدم وجود المستخدم مسبقاً
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $check_stmt->execute([$data['email'], $data['phone']]);

        if ($check_stmt->fetch()) {
            return ['success' => false, 'message' => 'البريد الإلكتروني أو رقم الهاتف موجود مسبقاً'];
        }

        // إنشاء المستخدم الجديد
        $hashed_password = hash_password($data['password']);

        $stmt = $conn->prepare("
            INSERT INTO users (full_name, email, phone, password, birth_date, gender, user_type, created_at, is_active)
            VALUES (?, ?, ?, ?, ?, ?, 'patient', NOW(), 1)
        ");

        $result = $stmt->execute([
            clean_input($data['full_name']),
            clean_input($data['email']),
            clean_input($data['phone']),
            $hashed_password,
            $data['birth_date'],
            $data['gender'] ?? 'male'
        ]);

        if ($result) {
            return ['success' => true, 'message' => 'تم إنشاء الحساب بنجاح'];
        } else {
            return ['success' => false, 'message' => 'حدث خطأ أثناء إنشاء الحساب'];
        }

    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'حدث خطأ أثناء إنشاء الحساب'];
    }
}

// الحصول على جميع المستشفيات
function get_all_hospitals() {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        if (!$conn) {
            return [];
        }

        $stmt = $conn->prepare("
            SELECT h.*,
                   COUNT(DISTINCT d.id) as doctor_count,
                   AVG(r.rating) as avg_rating,
                   COUNT(DISTINCT r.id) as review_count
            FROM hospitals h
            LEFT JOIN doctors d ON h.id = d.hospital_id
            LEFT JOIN reviews r ON d.id = r.doctor_id
            WHERE h.is_active = 1
            GROUP BY h.id
            ORDER BY h.name
        ");

        $stmt->execute();
        return $stmt->fetchAll();

    } catch (Exception $e) {
        error_log("Get hospitals error: " . $e->getMessage());
        return [];
    }
}

// الحصول على جميع التخصصات
function get_all_specialties() {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        if (!$conn) {
            return [];
        }

        $stmt = $conn->prepare("
            SELECT s.*, COUNT(d.id) as doctor_count
            FROM specialties s
            LEFT JOIN doctors d ON s.id = d.specialty_id
            WHERE s.is_active = 1
            GROUP BY s.id
            ORDER BY s.name
        ");

        $stmt->execute();
        return $stmt->fetchAll();

    } catch (Exception $e) {
        error_log("Get specialties error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all appointments for a specific user.
 * @param int $user_id
 * @return array
 */
function get_user_appointments($user_id)
{
    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            SELECT
                a.*,
                d.full_name as doctor_name,
                h.name as hospital_name,
                s.name as specialty_name
            FROM appointments a
            JOIN doctors d ON a.doctor_id = d.id
            JOIN hospitals h ON d.hospital_id = h.id
            JOIN specialties s ON d.specialty_id = s.id
            WHERE a.user_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ");

        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("Get user appointments error: " . $e->getMessage());
        return []; // Return an empty array on error
    }
}

// ====================================================================
// APPOINTMENT & BOOKING FUNCTIONS
// ====================================================================

/**
 * Get detailed information for a single doctor.
 * @param int $doctor_id
 * @return array|false
 */
function get_doctor_details($doctor_id) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            SELECT
                d.id, d.full_name, d.email, d.phone, d.bio, d.image, d.rating, d.specialty_id, d.clinic_id, d.is_active,
                d.consultation_fee,
                s.name as specialty_name,
                c.name as clinic_name,
                c.address as clinic_address
            FROM doctors d
            LEFT JOIN specialties s ON d.specialty_id = s.id
            LEFT JOIN clinics c ON d.clinic_id = c.id
            WHERE d.id = ?
        ");

        $stmt->execute([$doctor_id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doctor && (empty($doctor['image']) || !file_exists($doctor['image']))) {
            $doctor['image'] = 'assets/images/default-avatar.png';
        }

        return $doctor;

    } catch (Exception $e) {
        error_log("Get doctor details error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if a specific appointment slot is already taken.
 * @param int $doctor_id
 * @param string $appointment_date
 * @param string $appointment_time
 * @return bool
 */
function is_appointment_slot_taken($doctor_id, $appointment_date, $appointment_time) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        // Check for appointments with 'confirmed' or 'pending' status
        $stmt = $conn->prepare("
            SELECT COUNT(*)
            FROM appointments
            WHERE doctor_id = ?
            AND appointment_date = ?
            AND appointment_time = ?
            AND status IN ('confirmed', 'pending')
        ");

        $stmt->execute([$doctor_id, $appointment_date, $appointment_time]);
        return $stmt->fetchColumn() > 0;

    } catch (Exception $e) {
        error_log("Check appointment slot error: " . $e->getMessage());
        return true; // Fail safe: assume it's taken if there's a DB error
    }
}

/**
 * Create a new appointment in the database.
 * @param int $patient_id
 * @param int $doctor_id
 * @param string $appointment_date
 * @param string $appointment_time
 * @param string $reason
 * @return int|false The new appointment ID or false on failure.
 */
function create_appointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $reason) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time, reason, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())
        ");

        if ($stmt->execute([$patient_id, $doctor_id, $appointment_date, $appointment_time, $reason])) {
            return $conn->lastInsertId();
        }

        return false;

    } catch (Exception $e) {
        error_log("Create appointment error: " . $e->getMessage());
        return false;
    }
}

// ====================================================================
// FORMATTING & LOCALIZATION FUNCTIONS
// ====================================================================

/**
 * Format a date into a readable Arabic format.
 * @param string $date_string (e.g., '2025-08-08')
 * @return string
 */
function format_date_arabic($date_string) {
    if (empty($date_string)) return '';
    $timestamp = strtotime($date_string);
    $months = [
        "يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو",
        "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"
    ];
    $day = date('d', $timestamp);
    $month = $months[date('n', $timestamp) - 1];
    $year = date('Y', $timestamp);
    return "$day $month $year";
}

/**
 * Format a time into a readable Arabic format.
 * @param string $time_string (e.g., '14:30:00')
 * @return string
 */
function format_time_arabic($time_string) {
    if (empty($time_string)) return '';
    $timestamp = strtotime($time_string);
    return date('h:i A', $timestamp);
}

/**
 * Translate appointment status to Arabic.
 * @param string $status The status from the database.
 * @return string The translated status.
 */
function translate_status($status) {
    switch ($status) {
        case 'confirmed': return 'مؤكد';
        case 'pending': return 'قيد الانتظار';
        case 'cancelled': return 'ملغي';
        default: return ucfirst($status);
    }
}

/**
 * Get the Bootstrap badge class for a given status.
 * @param string $status The status from the database.
 * @return string The CSS class for the badge.
 */
function get_status_badge_class($status) {
    switch ($status) {
        case 'confirmed': return 'success';
        case 'pending': return 'warning';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

// ====================================================================
// OTHER FUNCTIONS
// ====================================================================

// Function to search for doctors
function search_doctors($search_query, $specialty_id) {
    global $conn;
    if (!$conn) {
        return []; // Return empty array if connection is not available
    }

    $sql = "SELECT
                d.id,
                d.full_name,
                d.image,
                d.rating,
                s.name as specialty_name,
                c.name as clinic_name,
                c.address as clinic_address
            FROM doctors d
            LEFT JOIN specialties s ON d.specialty_id = s.id
            LEFT JOIN clinics c ON d.clinic_id = c.id
            WHERE 1=1";

    $params = [];

    if (!empty($search_query)) {
        $sql .= " AND (d.full_name LIKE ? OR c.name LIKE ?)";
        $params[] = '%' . $search_query . '%';
        $params[] = '%' . $search_query . '%';
    }

    if (!empty($specialty_id)) {
        $sql .= " AND d.specialty_id = ?";
        $params[] = $specialty_id;
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Search doctors error: ' . $e->getMessage());
        return []; // Return empty array on error
    }
}

// Get a single doctor by their ID.
//  * @param int $doctor_id The ID of the doctor.
//  * @return array|false The doctor's data or false if not found.

function get_doctor_by_id($doctor_id) {
    global $conn;
    if (!$conn) {
        return false;
    }

    $sql = "SELECT
                d.id, d.full_name, d.email, d.phone, d.bio, d.image, d.rating, d.specialty_id, d.clinic_id, d.is_active,
                d.consultation_fee,
                s.name as specialty_name,
                c.name as clinic_name,
                c.address as clinic_address
            FROM doctors d
            LEFT JOIN specialties s ON d.specialty_id = s.id
            LEFT JOIN clinics c ON d.clinic_id = c.id
            WHERE d.id = ?";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$doctor_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Get doctor by ID error: ' . $e->getMessage());
        return false;
    }
}

function get_appointments_by_user_id($user_id) {
    global $conn;
    if (!$conn) {
        return [];
    }

    $sql = "SELECT
                a.id, a.appointment_date, a.appointment_time, a.status,
                d.full_name as doctor_name,
                s.name as specialty_name,
                c.name as clinic_name,
                c.address as clinic_address
            FROM appointments a
            JOIN doctors d ON a.doctor_id = d.id
            JOIN specialties s ON d.specialty_id = s.id
            JOIN clinics c ON d.clinic_id = c.id
            WHERE a.user_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Get appointments by user ID error: ' . $e->getMessage());
        return [];
    }
}

function get_all_doctors($limit = 10, $offset = 0) {
    global $conn;
    if (!$conn) {
        return []; // Return empty array if connection is not available
    }

    $sql = "SELECT
                d.id,
                d.full_name,
                d.image,
                d.rating,
                s.name as specialty_name,
                c.name as clinic_name,
                c.address as clinic_address
            FROM doctors d
            LEFT JOIN specialties s ON d.specialty_id = s.id
            LEFT JOIN clinics c ON d.clinic_id = c.id
            WHERE d.is_active = 1
            ORDER BY d.full_name ASC
            LIMIT ? OFFSET ?";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Get all doctors error: ' . $e->getMessage());
        return []; // Return empty array on error
    }
}

// ==========================================================================
// User Authentication Functions
// ==========================================================================

// ... rest of the code remains the same ...

// Get available appointment slots for a doctor on a specific date.
/**
 * @param int $doctor_id The ID of the doctor.
 * @param string $date The date in 'Y-m-d' format.
 * @return array An array of available time slots in 'H:i:s' format.
 */
function get_available_slots($doctor_id, $date) {
    global $conn;
    if (!$conn) {
        return [];
    }

    try {
        // Step 1: Define doctor's general availability (can be moved to a DB table later)
        $work_start_time = new DateTime('09:00:00');
        $work_end_time = new DateTime('17:00:00');
        $slot_duration = new DateInterval('PT30M'); // 30 minutes slots

        // Step 2: Get all booked appointments for the doctor on the given date
        $sql = "SELECT appointment_time FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND status = 'confirmed'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$doctor_id, $date]);
        $booked_slots_raw = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $booked_slots = [];
        foreach ($booked_slots_raw as $slot) {
            $booked_slots[] = (new DateTime($slot))->format('H:i');
        }

        // Step 3: Generate all possible slots for the day
        $all_slots = [];
        $current_slot_time = clone $work_start_time;

        while ($current_slot_time < $work_end_time) {
            $all_slots[] = $current_slot_time->format('H:i');
            $current_slot_time->add($slot_duration);
        }

        // Step 4: Filter out the booked slots
        $available_slots = array_diff($all_slots, $booked_slots);

        // Step 5: Filter out past slots for the current day
        $today_date = new DateTime('now', new DateTimeZone('UTC')); // Use your server's timezone or a specific one
        if ($date === $today_date->format('Y-m-d')) {
            $current_time = $today_date->format('H:i');
            $available_slots = array_filter($available_slots, function($slot) use ($current_time) {
                return $slot > $current_time;
            });
        }

        return array_values($available_slots); // Return re-indexed array

    } catch (Exception $e) {
        error_log("Get available slots error: " . $e->getMessage());
        return []; // Return empty array on error
    }
}

// ==========================================================================
// User Authentication Functions
// ==========================================================================

// ... rest of the code remains the same ...

/**
 * Get all appointments for a specific doctor.
 * @param int $doctor_id The ID of the doctor.
 * @return array An array of appointments.
 */
function get_appointments_by_doctor_id($doctor_id) {
    global $conn;
    if (!$conn) {
        return [];
    }

    try {
        $sql = "SELECT
                    a.id,
                    a.appointment_date,
                    a.appointment_time,
                    a.status,
                    u.full_name as patient_name
                FROM appointments a
                JOIN users u ON a.user_id = u.id
                WHERE a.doctor_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$doctor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Get appointments by doctor ID error: ' . $e->getMessage());
        return [];
    }
}

// ==============================================
// DASHBOARD STATS FUNCTIONS
// ==============================================

function get_total_count($pdo, $table) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table}");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        // In a real app, you'd log this error.
        // error_log('Count query failed: ' . $e->getMessage());
        return 0;
    }
}

function get_user_type_count($pdo, $user_type) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_type = ?");
        $stmt->execute([$user_type]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function get_doctor_appointment_count($pdo, $doctor_id, $status) {
    try {
        $sql = "SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$doctor_id, $status]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function get_doctor_patient_count($pdo, $doctor_id) {
    try {
        // Counts distinct patients who have had appointments with this doctor
        $sql = "SELECT COUNT(DISTINCT user_id) FROM appointments WHERE doctor_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$doctor_id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function get_patient_appointment_count($pdo, $user_id, $status) {
    try {
        $sql = "SELECT COUNT(*) FROM appointments WHERE user_id = ? AND status = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $status]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function get_recent_patients($conn, $limit = 5) {
    try {
        $sql = "SELECT id, full_name, email, created_at, status FROM users WHERE user_type = 'patient' ORDER BY created_at DESC LIMIT :limit";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Get recent patients error: ' . $e->getMessage());
        return [];
    }
}

function get_users_by_type($conn, $user_type) {
    try {
        $stmt = $conn->prepare("SELECT id, full_name, email, created_at, status FROM users WHERE user_type = ?");
        $stmt->execute([$user_type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Get users by type error: ' . $e->getMessage());
        return [];
    }
}

// Function to get all users from the database
function get_all_users($pdo) {
    $sql = "SELECT id, full_name, username, email, user_type, created_at FROM users ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all appointments for the admin dashboard
function get_all_appointments($conn) {
    try {
        $sql = "SELECT
                    a.id,
                    a.appointment_date,
                    a.appointment_time,
                    a.status,
                    p.full_name AS patient_name,
                    d.full_name AS doctor_name
                FROM
                    appointments a
                JOIN
                    users p ON a.user_id = p.id
                JOIN
                    users d ON a.doctor_id = d.id
                ORDER BY
                    a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error in get_all_appointments: ' . $e->getMessage());
        return [];
    }
}

// Function to get a single user by their ID
function get_user_by_id($conn, $user_id) {
    try {
        $stmt = $conn->prepare("SELECT id, full_name, email, user_type FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Get user by ID error: ' . $e->getMessage());
        return false;
    }
}

// Function to update user data
function update_user($conn, $user_id, $full_name, $email, $user_type) {
    try {
        $sql = "UPDATE users SET full_name = ?, email = ?, user_type = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$full_name, $email, $user_type, $user_id]);
    } catch (PDOException $e) {
        error_log('Update user error: ' . $e->getMessage());
        return false;
    }
}

// Function to delete a user by their ID
function delete_user($conn, $user_id) {
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$user_id]);
    } catch (PDOException $e) {
        error_log('Delete user error: ' . $e->getMessage());
        return false;
    }
}

function update_appointment_status($pdo, $appointment_id, $status) {
    $sql = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$status, $appointment_id]);
}

function delete_appointment($pdo, $appointment_id) {
    $sql = "DELETE FROM appointments WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$appointment_id]);
}

// Function to get all appointments with patient and doctor names
function get_all_appointments_with_patient_doctor($pdo) {
    $sql = "SELECT
                a.id,
                a.appointment_date,
                a.appointment_time,
                a.status,
                p.full_name as patient_name,
                d.full_name as doctor_name
            FROM appointments a
            JOIN users p ON a.user_id = p.id
            JOIN doctors doc ON a.doctor_id = doc.id
            JOIN users d ON doc.user_id = d.id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ====================================================================
// DOCTOR MANAGEMENT FUNCTIONS (Admin)
// ====================================================================

/**
 * Get all doctors with their specialization and account status.
 * @param PDO $pdo
 * @return array
 */
function get_all_doctors_with_details($pdo) {
    $sql = "SELECT
                u.id as user_id,
                u.full_name,
                u.email,
                d.id as doctor_id,
                d.specialization,
                d.status
            FROM users u
            JOIN doctors d ON u.id = d.user_id
            WHERE u.user_type = 'doctor'
            ORDER BY d.status ASC, u.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Update the status of a doctor's account (e.g., pending, approved, suspended).
 * @param PDO $pdo
 * @param int $doctor_id
 * @param string $status
 * @return bool
 */
function update_doctor_account_status($pdo, $doctor_id, $status) {
    $sql = "UPDATE doctors SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$status, $doctor_id]);
}

/**
 * Delete a doctor completely from the system (from doctors and users tables).
 * @param PDO $pdo
 * @param int $user_id
 * @return bool
 */
function delete_doctor_by_user_id($pdo, $user_id) {
    $pdo->beginTransaction();
    try {
        // First, delete from the doctors table
        $sql_doctors = "DELETE FROM doctors WHERE user_id = ?";
        $stmt_doctors = $pdo->prepare($sql_doctors);
        $stmt_doctors->execute([$user_id]);

        // Then, delete from the users table
        $sql_users = "DELETE FROM users WHERE id = ?";
        $stmt_users = $pdo->prepare($sql_users);
        $stmt_users->execute([$user_id]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        // You might want to log the error message: $e->getMessage()
        return false;
    }
}


// New public function to bypass the hidden, protected one.
function get_public_available_slots($doctor_id, $date) {
    global $conn;
    if (!$conn) {
        // Attempt to reconnect if the global connection is lost.
        $db = new Database();
        $conn = $db->getConnection();
        if (!$conn) {
            return ['error' => 'Database connection failed.'];
        }
    }

    // Base query to get all potential slots for the doctor on a given day of the week.
    $day_of_week = date('l', strtotime($date));
    $sql = "SELECT start_time, end_time FROM doctor_availability WHERE doctor_id = ? AND day_of_week = ?";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$doctor_id, $day_of_week]);
        $availability = $stmt->fetch();

        if (!$availability) {
            return ['available_times' => []]; // No availability defined for this day.
        }

        // Now, find appointments that are already booked for that specific date.
        $booked_sql = "SELECT appointment_time FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND status NOT IN ('cancelled', 'declined')";
        $booked_stmt = $conn->prepare($booked_sql);
        $booked_stmt->execute([$doctor_id, $date]);
        $booked_times_raw = $booked_stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        $booked_times = array_map(function($time) {
            return date('H:i', strtotime($time));
        }, $booked_times_raw);

        $all_slots = [];
        $start = new DateTime($availability['start_time']);
        $end = new DateTime($availability['end_time']);
        $interval = new DateInterval('PT30M'); // 30-minute slots

        while ($start < $end) {
            $current_slot = $start->format('H:i');
            if (!in_array($current_slot, $booked_times)) {
                $all_slots[] = $current_slot;
            }
            $start->add($interval);
        }

        return ['available_times' => $all_slots];

    } catch (PDOException $e) {
        error_log('Database error in get_public_available_slots: ' . $e->getMessage());
        return ['error' => 'A database error occurred.'];
    }
}

?>
