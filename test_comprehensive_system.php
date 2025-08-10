<?php
/**
 * Comprehensive System Test Script
 * Tests all features and functionality of the medical appointment system
 */

// Include configuration and functions
require_once 'config.php';
require_once 'includes/functions.php';

// Start output buffering
ob_start();

echo "<!DOCTYPE html>
<html lang='ar' dir='rtl'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>اختبار شامل للنظام</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .test-section h3 { color: #333; margin-top: 0; }
        .test-result { padding: 10px; margin: 5px 0; border-radius: 3px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .test-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin: 15px 0; }
        .summary { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .progress-bar { width: 100%; height: 20px; background: #e9ecef; border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #28a745, #20c997); transition: width 0.3s ease; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 اختبار شامل لنظام حجز المواعيد الطبية</h1>
        <p>هذا السكريبت يختبر جميع ميزات النظام الجديدة</p>";

// Initialize test results
$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$warnings = 0;

function runTest($testName, $testFunction) {
    global $totalTests, $passedTests, $failedTests, $warnings;

    $totalTests++;
    echo "<div class='test-section'>";
    echo "<h3>🔍 $testName</h3>";

    try {
        $result = $testFunction();
        if ($result === true) {
            echo "<div class='test-result success'>✅ نجح الاختبار</div>";
            $passedTests++;
        } elseif ($result === false) {
            echo "<div class='test-result error'>❌ فشل الاختبار</div>";
            $failedTests++;
        } else {
            echo "<div class='test-result warning'>⚠️ تحذير: $result</div>";
            $warnings++;
        }
    } catch (Exception $e) {
        echo "<div class='test-result error'>❌ خطأ في الاختبار: " . htmlspecialchars($e->getMessage()) . "</div>";
        $failedTests++;
    }

    echo "</div>";
}

// Test 1: Database Connection
runTest("اختبار الاتصال بقاعدة البيانات", function() {
    global $conn;
    if (!$conn) {
        return false;
    }

    $result = $conn->query("SELECT 1");
    return $result !== false;
});

// Test 2: Core Functions
runTest("اختبار الدوال الأساسية", function() {
    // Test get_logged_in_user function
    if (!function_exists('get_logged_in_user')) {
        return false;
    }

    // Test other core functions
    $requiredFunctions = [
        'get_user_by_id',
        'get_doctor_by_id',
        'get_hospital_by_id',
        'get_appointments_by_user',
        'get_available_slots'
    ];

    foreach ($requiredFunctions as $func) {
        if (!function_exists($func)) {
            return "الدالة $func غير موجودة";
        }
    }

    return true;
});

/*
// Test 3: Database Schema
runTest("اختبار هيكل قاعدة البيانات", function() {
    global $conn;

    $requiredTables = [
        'users',
        'doctors',
        'hospitals',
        'appointments',
        'notifications'
    ];

    foreach ($requiredTables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() === 0) {
            return "الجدول $table غير موجود";
        }
    }

    return true;
});
*/

// Test 4: User Management
runTest("اختبار إدارة المستخدمين", function() {
    global $conn;

    // Test user creation
    $testEmail = 'test_' . time() . '@example.com';
    $testPassword = password_hash('test123', PASSWORD_DEFAULT);
    $testName = "مستخدم اختبار";

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'patient', NOW())");

    if (!$stmt->execute([$testName, $testEmail, $testPassword])) {
        return "فشل في إنشاء مستخدم اختبار";
    }

    $userId = $conn->lastInsertId();

    // Test user retrieval
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetchAll();

    if (count($result) === 0) {
        return "فشل في استرجاع مستخدم الاختبار";
    }

    // Clean up
    $conn->query("DELETE FROM users WHERE id = $userId");

    return true;
});

// Test 5: Doctor Management
runTest("اختبار إدارة الأطباء", function() {
    global $conn;

    // Test doctor creation
    $testEmail = 'doctor_' . time() . '@example.com';
    $testPassword = password_hash('test123', PASSWORD_DEFAULT);

    // First create user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'doctor')");
    $testName = "طبيب اختبار" . time();

    if (!$stmt->execute([$testName, $testEmail, $testPassword])) {
        return "فشل في إنشاء مستخدم طبيب";
    }

    $userId = $conn->lastInsertId();

    // Then create doctor record
    $stmt = $conn->prepare("INSERT INTO doctors (user_id, experience_years, consultation_fee, bio) VALUES (?, ?, ?, ?)");
    $experience = 5;
    $fee = 100.00;
    $bio = "طبيب اختبار للتجربة";

    if (!$stmt->execute([$userId, $experience, $fee, $bio])) {
        $conn->query("DELETE FROM users WHERE id = $userId");
        return "فشل في إنشاء سجل الطبيب";
    }

    $doctorId = $conn->lastInsertId();

    // Test doctor retrieval
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->execute([$doctorId]);
    $result = $stmt->fetchAll();

    if (count($result) === 0) {
        return "فشل في استرجاع سجل الطبيب";
    }

    // Clean up
    $conn->query("DELETE FROM doctors WHERE id = $doctorId");
    $conn->query("DELETE FROM users WHERE id = $userId");

    return true;
});

// Test 6: Appointment System
runTest("اختبار نظام المواعيد", function() {
    global $conn;

    // Test appointment creation
    $testDate = date('Y-m-d', strtotime('+1 day'));
    $testTime = '10:00:00';

    $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, clinic_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, ?)");
    $userId = 1; // Assuming user ID 1 exists
    $doctorId = 1;  // Assuming doctor ID 1 exists
    $clinicId = 1; // Assuming clinic ID 1 exists
    $status = "scheduled";

    if (!$stmt->execute([$userId, $doctorId, $clinicId, $testDate, '10:00:00', $status])) {
        return "فشل في إنشاء موعد اختبار";
    }

    $appointmentId = $conn->lastInsertId();

    // Test appointment retrieval
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
    $stmt->execute([$appointmentId]);
    $result = $stmt->fetchAll();

    if (count($result) === 0) {
        return "فشل في استرجاع الموعد";
    }

    // Clean up
    $conn->query("DELETE FROM appointments WHERE id = $appointmentId");

    return true;
});

// Test 7: Search Functionality
runTest("اختبار وظائف البحث", function() {
    global $conn;

    // Test doctor search
    $searchTerm = "طب";
    $stmt = $conn->prepare("SELECT d.id, u.username as doctor_name, h.name as hospital_name FROM doctors d JOIN users u ON d.user_id = u.id JOIN hospitals h ON d.hospital_id = h.id WHERE u.username LIKE ? OR h.name LIKE ?");
    $searchPattern = "%$searchTerm%";

    if (!$stmt->execute([$searchPattern, $searchPattern])) {
        return "فشل في البحث عن الأطباء";
    }

    return true;
});

// Test 8: File System
runTest("اختبار نظام الملفات", function() {
    // Check if upload directories exist
    $uploadDirs = [
        'uploads/avatars',
        'uploads/profile_images'
    ];

    foreach ($uploadDirs as $dir) {
        if (!is_dir($dir)) {
            return "المجلد $dir غير موجود";
        }

        if (!is_writable($dir)) {
            return "المجلد $dir غير قابل للكتابة";
        }
    }

    return true;
});

// Test 10: Security Features
runTest("اختبار ميزات الأمان", function() {
    // Check if security files exist
    $securityFiles = [
        'includes/security.php',
        'includes/session_manager.php',
        'includes/security_fixes.php'
    ];

    foreach ($securityFiles as $file) {
        if (!file_exists($file)) {
            return "ملف الأمان $file غير موجود";
        }
    }

    return true;
});

// Calculate progress
$progress = ($passedTests / $totalTests) * 100;

echo "<div class='summary'>
    <h2>📊 ملخص نتائج الاختبار</h2>
    <div class='progress-bar'>
        <div class='progress-fill' style='width: {$progress}%'></div>
    </div>
    <p><strong>التقدم:</strong> " . number_format($progress, 1) . "%</p>
    <p><strong>إجمالي الاختبارات:</strong> $totalTests</p>
    <p><strong>الاختبارات الناجحة:</strong> <span style='color: #28a745;'>$passedTests</span></p>
    <p><strong>الاختبارات الفاشلة:</strong> <span style='color: #dc3545;'>$failedTests</span></p>
    <p><strong>التحذيرات:</strong> <span style='color: #ffc107;'>$warnings</span></p>
</div>";

if ($failedTests === 0) {
    echo "<div class='test-result success' style='text-align: center; font-size: 18px; padding: 20px;'>
        🎉 جميع الاختبارات الأساسية نجحت! النظام جاهز للاستخدام.
    </div>";
} else {
    echo "<div class='test-result error' style='text-align: center; font-size: 18px; padding: 20px;'>
        ⚠️ هناك بعض المشاكل التي تحتاج إلى إصلاح قبل استخدام النظام.
    </div>";
}

echo "<div class='test-section'>
    <h3>🚀 الخطوات التالية</h3>
    <ul>
        <li>إذا نجحت جميع الاختبارات: النظام جاهز للاستخدام</li>
        <li>إذا فشلت بعض الاختبارات: راجع الأخطاء وأصلحها</li>
        <li>اختبر النظام يدوياً للتأكد من عمل جميع الميزات</li>
        <li>أضف بيانات تجريبية حقيقية للنظام</li>
    </ul>
</div>";

echo "</div></body></html>";

// Flush output buffer
ob_end_flush();
?>
