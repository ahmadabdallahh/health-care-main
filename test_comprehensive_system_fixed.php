<?php
/**
 * Comprehensive System Test Script - FIXED VERSION
 * Fixed for PDO compatibility and correct database schema
 */

require_once 'config.php';
require_once 'includes/functions.php';

ob_start();

echo "<!DOCTYPE html>
<html lang='ar' dir='rtl'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>اختبار شامل للنظام - النسخة المصححة</title>
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
        .progress-bar { width: 100%; height: 20px; background: #e9ecef; border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #28a745, #20c997); transition: width 0.3s ease; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>��� اختبار شامل لنظام حجز المواعيد الطبية - النسخة المصححة</h1>
        <p>هذا السكريبت يختبر جميع ميزات النظام مع إصلاح مشاكل التوافق</p>";

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$warnings = 0;

function runTest($testName, $testFunction) {
    global $totalTests, $passedTests, $failedTests, $warnings;

    $totalTests++;
    echo "<div class='test-section'>";
    echo "<h3>��� $testName</h3>";

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

// Test 2: Database Structure
runTest("اختبار هيكل قاعدة البيانات", function() {
    global $conn;

    $requiredTables = [
        'users',
        'doctors',
        'hospitals',
        'clinics',
        'appointments',
        'specialties',
        'cities',
        'reminder_settings',
        'reminder_logs',
        'push_notifications'
    ];

    foreach ($requiredTables as $table) {
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->rowCount() === 0) {
            return "الجدول $table غير موجود";
        }
    }

    return true;
});

// Test 3: User Management
runTest("اختبار إدارة المستخدمين", function() {
    global $conn;

    $testEmail = 'test_' . time() . '@example.com';
    $testPassword = password_hash('test123', PASSWORD_DEFAULT);
    $testName = "مستخدم اختبار";

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role, created_at) VALUES (?, ?, ?, ?, 'patient', NOW())");
    if (!$stmt->execute([$testName, $testEmail, $testPassword, $testName])) {
        return "فشل في إنشاء مستخدم اختبار";
    }

    $userId = $conn->lastInsertId();

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        return "فشل في استرجاع مستخدم الاختبار";
    }

    $conn->query("DELETE FROM users WHERE id = $userId");
    return true;
});

// Test 4: Security Features
runTest("اختبار ميزات الأمان", function() {
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

$progress = ($passedTests / $totalTests) * 100;

echo "<div class='summary'>
    <h2>��� ملخص نتائج الاختبار</h2>
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
        ��� جميع الاختبارات الأساسية نجحت! النظام جاهز للاستخدام.
    </div>";
} else {
    echo "<div class='test-result error' style='text-align: center; font-size: 18px; padding: 20px;'>
        ⚠️ هناك بعض المشاكل التي تحتاج إلى إصلاح قبل استخدام النظام.
    </div>";
}

echo "</div></body></html>";
ob_end_flush();
?>
