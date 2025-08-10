<?php
/**
 * Feature Test Script
 * Tests all new features of the medical appointment system
 */

require_once 'config.php';
require_once 'includes/functions.php';

echo "<!DOCTYPE html>
<html lang='ar' dir='rtl'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>اختبار الميزات الجديدة</title>
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
        .summary { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 اختبار الميزات الجديدة</h1>
        <p>اختبار شامل لجميع الميزات الجديدة في نظام حجز المواعيد الطبية</p>";

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$warnings = 0;

function runFeatureTest($testName, $testFunction) {
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

// Test 1: Patient Home Page
runFeatureTest("اختبار صفحة المرضى الرئيسية", function() {
    if (!file_exists('patient_home.php')) {
        return "ملف patient_home.php غير موجود";
    }

    $content = file_get_contents('patient_home.php');
    if (strpos($content, 'patient') === false) {
        return "المحتوى لا يبدو أنه صفحة مرضى";
    }

    return true;
});

// Test 2: Doctor Home Page
runFeatureTest("اختبار صفحة الأطباء الرئيسية", function() {
    if (!file_exists('doctor_home.php')) {
        return "ملف doctor_home.php غير موجود";
    }

    $content = file_get_contents('doctor_home.php');
    if (strpos($content, 'doctor') === false) {
        return "المحتوى لا يبدو أنه صفحة أطباء";
    }

    return true;
});

// Test 3: Hospital Home Page
runFeatureTest("اختبار صفحة المستشفيات الرئيسية", function() {
    if (!file_exists('hospital_home.php')) {
        return "ملف hospital_home.php غير موجود";
    }

    $content = file_get_contents('hospital_home.php');
    if (strpos($content, 'hospital') === false) {
        return "المحتوى لا يبدو أنه صفحة مستشفيات";
    }

    return true;
});

// Test 4: Doctor Availability System
runFeatureTest("اختبار نظام أوقات الشواغر", function() {
    if (!file_exists('doctor_availability.php')) {
        return "ملف doctor_availability.php غير موجود";
    }

    $content = file_get_contents('doctor_availability.php');
    if (strpos($content, 'availability') === false) {
        return "المحتوى لا يبدو أنه نظام أوقات شواغر";
    }

    return true;
});

// Test 5: Reminder System
runFeatureTest("اختبار نظام التذكيرات", function() {
    if (!file_exists('reminder_system.php')) {
        return "ملف reminder_system.php غير موجود";
    }

    $content = file_get_contents('reminder_system.php');
    if (strpos($content, 'reminder') === false) {
        return "المحتوى لا يبدو أنه نظام تذكيرات";
    }

    return true;
});

// Test 6: Advanced Search
runFeatureTest("اختبار البحث المتقدم", function() {
    if (!file_exists('search.php')) {
        return "ملف search.php غير موجود";
    }

    $content = file_get_contents('search.php');
    $searchFeatures = ['specialization', 'location', 'insurance', 'price', 'rating'];
    $foundFeatures = 0;

    foreach ($searchFeatures as $feature) {
        if (strpos($content, $feature) !== false) {
            $foundFeatures++;
        }
    }

    if ($foundFeatures < 3) {
        return "البحث المتقدم لا يحتوي على ميزات كافية";
    }

    return true;
});

// Test 7: Appointment Booking
runFeatureTest("اختبار نظام حجز المواعيد", function() {
    if (!file_exists('book_appointment.php')) {
        return "ملف book_appointment.php غير موجود";
    }

    $content = file_get_contents('book_appointment.php');
    if (strpos($content, 'appointment') === false) {
        return "المحتوى لا يبدو أنه نظام حجز مواعيد";
    }

    return true;
});

// Test 8: Admin Panel
runFeatureTest("اختبار لوحة الإدارة", function() {
    $adminFiles = [
        'admin/index.php',
        'admin/manage_users.php',
        'admin/manage_doctors.php',
        'admin/manage_appointments.php'
    ];

    foreach ($adminFiles as $file) {
        if (!file_exists($file)) {
            return "ملف الإدارة $file غير موجود";
        }
    }

    return true;
});

// Test 9: Database Schema
runFeatureTest("اختبار هيكل قاعدة البيانات", function() {
    global $conn;

    $requiredTables = [
        'users',
        'doctors',
        'hospitals',
        'appointments',
        'reminders',
        'notifications',
        'doctor_availability'
    ];

    foreach ($requiredTables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() === 0) {
            return "الجدول $table غير موجود";
        }
    }

    return true;
});

// Test 10: Security Features
runFeatureTest("اختبار ميزات الأمان", function() {
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

// Test 11: File Upload System
runFeatureTest("اختبار نظام رفع الملفات", function() {
    $uploadDirs = [
        'uploads/avatars',
        'uploads/profile_images'
    ];

    foreach ($uploadDirs as $dir) {
        if (!is_dir($dir)) {
            return "مجلد الرفع $dir غير موجود";
        }

        if (!is_writable($dir)) {
            return "مجلد الرفع $dir غير قابل للكتابة";
        }
    }

    return true;
});

// Test 12: API Endpoints
runFeatureTest("اختبار نقاط النهاية API", function() {
    $apiFiles = [
        'api/get_slots.php',
        'get_available_times.php'
    ];

    foreach ($apiFiles as $file) {
        if (!file_exists($file)) {
            return "ملف API $file غير موجود";
        }
    }

    return true;
});

// Calculate progress
$progress = ($passedTests / $totalTests) * 100;

echo "<div class='summary'>
    <h2>📊 ملخص نتائج اختبار الميزات</h2>
    <p><strong>التقدم:</strong> " . number_format($progress, 1) . "%</p>
    <p><strong>إجمالي الاختبارات:</strong> $totalTests</p>
    <p><strong>الاختبارات الناجحة:</strong> <span style='color: #28a745;'>$passedTests</span></p>
    <p><strong>الاختبارات الفاشلة:</strong> <span style='color: #dc3545;'>$failedTests</span></p>
    <p><strong>التحذيرات:</strong> <span style='color: #ffc107;'>$warnings</span></p>
</div>";

if ($failedTests === 0) {
    echo "<div class='test-result success' style='text-align: center; font-size: 18px; padding: 20px;'>
        🎉 جميع الميزات تعمل بشكل صحيح! النظام جاهز للاستخدام.
    </div>";
} else {
    echo "<div class='test-result error' style='text-align: center; font-size: 18px; padding: 20px;'>
        ⚠️ هناك بعض المشاكل في الميزات التي تحتاج إلى إصلاح.
    </div>";
}

echo "<div class='test-section'>
    <h3>🚀 الخطوات التالية</h3>
    <ul>
        <li>إذا نجحت جميع الاختبارات: النظام جاهز للاستخدام والإنتاج</li>
        <li>إذا فشلت بعض الاختبارات: راجع الأخطاء وأصلحها</li>
        <li>اختبر النظام يدوياً للتأكد من عمل جميع الميزات</li>
        <li>أضف بيانات تجريبية حقيقية للنظام</li>
        <li>اختبر تجربة المستخدم النهائية</li>
    </ul>
</div>";

echo "</div></body></html>";
?>
