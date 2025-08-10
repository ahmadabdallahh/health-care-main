<?php
/**
 * Test Doctor Dashboard - Medical Booking System
 * اختبار لوحة تحكم الطبيب - نظام حجز المواعيد الطبية
 */

// Start session for testing
session_start();

// Mock session data for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Dr. Test Doctor';
$_SESSION['user_type'] = 'doctor';

// Include necessary files
require_once 'config/database.php';
require_once 'includes/functions.php';

echo "<h1>🔍 اختبار لوحة تحكم الطبيب</h1>\n";
echo "<h2>Test Doctor Dashboard</h2>\n";

// Test 1: Check if required functions exist
echo "<h3>1. اختبار وجود الدوال المطلوبة</h3>\n";
echo "<h3>1. Test Required Functions</h3>\n";

$required_functions = [
    'get_doctor_dashboard_stats',
    'get_doctor_upcoming_appointments',
    'is_doctor'
];

foreach ($required_functions as $function) {
    if (function_exists($function)) {
        echo "✅ دالة $function موجودة<br>\n";
        echo "✅ Function $function exists<br>\n";
    } else {
        echo "❌ دالة $function غير موجودة<br>\n";
        echo "❌ Function $function does not exist<br>\n";
    }
}

// Test 2: Check if required include files exist
echo "<h3>2. اختبار وجود ملفات التضمين المطلوبة</h3>\n";
echo "<h3>2. Test Required Include Files</h3>\n";

$required_files = [
    'includes/doctor_sidebar.php',
    'includes/dashboard_header.php',
    'includes/dashboard_navbar.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ ملف $file موجود<br>\n";
        echo "✅ File $file exists<br>\n";
    } else {
        echo "❌ ملف $file غير موجود<br>\n";
        echo "❌ File $file does not exist<br>\n";
    }
}

// Test 3: Test database connection and functions
echo "<h3>3. اختبار قاعدة البيانات والدوال</h3>\n";
echo "<h3>3. Test Database and Functions</h3>\n";

try {
    $db = new Database();
    $conn = $db->getConnection();

    if ($conn) {
        echo "✅ الاتصال بقاعدة البيانات ناجح<br>\n";
        echo "✅ Database connection successful<br>\n";

        // Test doctor functions
        $test_doctor_id = 1;

        // Test get_doctor_dashboard_stats
        $stats = get_doctor_dashboard_stats($conn, $test_doctor_id);
        if (is_array($stats)) {
            echo "✅ دالة get_doctor_dashboard_stats تعمل بشكل صحيح<br>\n";
            echo "✅ Function get_doctor_dashboard_stats works correctly<br>\n";
        } else {
            echo "❌ دالة get_doctor_dashboard_stats لا تعمل بشكل صحيح<br>\n";
            echo "❌ Function get_doctor_dashboard_stats does not work correctly<br>\n";
        }

        // Test get_doctor_upcoming_appointments
        $appointments = get_doctor_upcoming_appointments($conn, $test_doctor_id, 3);
        if (is_array($appointments)) {
            echo "✅ دالة get_doctor_upcoming_appointments تعمل بشكل صحيح<br>\n";
            echo "✅ Function get_doctor_upcoming_appointments works correctly<br>\n";
        } else {
            echo "❌ دالة get_doctor_upcoming_appointments لا تعمل بشكل صحيح<br>\n";
            echo "❌ Function get_doctor_upcoming_appointments does not work correctly<br>\n";
        }

    } else {
        echo "❌ فشل الاتصال بقاعدة البيانات<br>\n";
        echo "❌ Database connection failed<br>\n";
    }
} catch (Exception $e) {
    echo "❌ خطأ في الاتصال: " . $e->getMessage() . "<br>\n";
    echo "❌ Connection error: " . $e->getMessage() . "<br>\n";
}

// Test 4: Check if doctor dashboard can be included without fatal errors
echo "<h3>4. اختبار تضمين لوحة تحكم الطبيب</h3>\n";
echo "<h3>4. Test Including Doctor Dashboard</h3>\n";

try {
    // Test if we can include the sidebar without errors
    ob_start();
    include 'includes/doctor_sidebar.php';
    $sidebar_content = ob_get_clean();

    if (!empty($sidebar_content)) {
        echo "✅ تم تضمين شريط جانبي الطبيب بنجاح<br>\n";
        echo "✅ Doctor sidebar included successfully<br>\n";
    } else {
        echo "❌ فشل في تضمين شريط جانبي الطبيب<br>\n";
        echo "❌ Failed to include doctor sidebar<br>\n";
    }
} catch (Exception $e) {
    echo "❌ خطأ في تضمين شريط جانبي الطبيب: " . $e->getMessage() . "<br>\n";
    echo "❌ Error including doctor sidebar: " . $e->getMessage() . "<br>\n";
}

echo "<hr>\n";
echo "<h3>✅ تم الانتهاء من الاختبار</h3>\n";
echo "<h3>✅ Test Completed</h3>\n";

// Clean up session
session_destroy();
?>
