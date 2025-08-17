<?php
session_start();
require_once 'config.php';

// Handle theme switching for testing
if (isset($_GET['theme'])) {
    $_SESSION['doctor_theme'] = $_GET['theme'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معاينة ألوان لوحة التحكم الطبية</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/doctor-theme.php">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">معاينة ألوان لوحة التحكم الطبية</h1>

        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-xl font-semibold mb-6">اختر نمط الألوان للتجربة</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="?theme=medical-blue" class="theme-preview bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-lg text-center hover:scale-105 transition-transform">
                    <i class="fas fa-palette text-2xl mb-2"></i>
                    <div class="font-semibold">الأزرق الطبي</div>
                </a>
                <a href="?theme=medical-green" class="theme-preview bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg text-center hover:scale-105 transition-transform">
                    <i class="fas fa-palette text-2xl mb-2"></i>
                    <div class="font-semibold">الأخضر الطبي</div>
                </a>
                <a href="?theme=warm-orange" class="theme-preview bg-gradient-to-br from-orange-500 to-orange-600 text-white p-4 rounded-lg text-center hover:scale-105 transition-transform">
                    <i class="fas fa-palette text-2xl mb-2"></i>
                    <div class="font-semibold">البرتقالي الدافئ</div>
                </a>
                <a href="?theme=professional-gray" class="theme-preview bg-gradient-to-br from-gray-500 to-gray-600 text-white p-4 rounded-lg text-center hover:scale-105 transition-transform">
                    <i class="fas fa-palette text-2xl mb-2"></i>
                    <div class="font-semibold">الرمادي المهني</div>
                </a>
                <a href="?theme=royal-purple" class="theme-preview bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-lg text-center hover:scale-105 transition-transform">
                    <i class="fas fa-palette text-2xl mb-2"></i>
                    <div class="font-semibold">البنفسجي الملكي</div>
                </a>
                <a href="?theme=emergency-red" class="theme-preview bg-gradient-to-br from-red-500 to-red-600 text-white p-4 rounded-lg text-center hover:scale-105 transition-transform">
                    <i class="fas fa-palette text-2xl mb-2"></i>
                    <div class="font-semibold">الأحمر الطوارئ</div>
                </a>
            </div>
        </div>

        <!-- Current Theme Display -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-xl font-semibold mb-6">النمط الحالي:
                <span style="color: var(--primary-color);">
                    <?php
                    $theme_names = [
                        'medical-blue' => 'الأزرق الطبي',
                        'medical-green' => 'الأخضر الطبي',
                        'warm-orange' => 'البرتقالي الدافئ',
                        'professional-gray' => 'الرمادي المهني',
                        'royal-purple' => 'البنفسجي الملكي',
                        'emergency-red' => 'الأحمر الطوارئ'
                    ];
                    echo $theme_names[$_SESSION['doctor_theme'] ?? 'medical-blue'] ?? 'الأزرق الطبي';
                    ?>
                </span>
            </h2>

            <!-- Sample Dashboard Elements -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Stat Card -->
                <div class="stat-card rounded-lg p-6 border border-gray-200">
                    <div class="stat-number text-3xl font-bold mb-2">25</div>
                    <div class="stat-label text-gray-600">مواعيد اليوم</div>
                </div>

                <!-- Appointment Card -->
                <div class="appointment-card rounded-lg p-6 border-l-4">
                    <div class="appointment-time text-lg font-bold mb-2">14:30</div>
                    <div class="patient-name text-xl font-semibold mb-1">أحمد محمد</div>
                    <div class="appointment-type text-gray-600 mb-4">استشارة عامة</div>
                    <div class="flex gap-2">
                        <button class="btn-start text-white px-4 py-2 rounded-lg text-sm">بدء الموعد</button>
                        <button class="btn-reschedule text-white px-4 py-2 rounded-lg text-sm">إعادة جدولة</button>
                    </div>
                </div>

                <!-- Notification Item -->
                <div class="notification-item rounded-lg p-4 border-l-4">
                    <div class="notification-time text-sm text-gray-500 mb-2">منذ 5 دقائق</div>
                    <div class="notification-text font-semibold">تم إلغاء موعد الساعة 14:00</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-xl font-semibold mb-6">روابط سريعة</h2>
            <div class="flex flex-wrap gap-4">
                <a href="doctor/color-customizer.php" class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-palette mr-2"></i>تخصيص الألوان
                </a>
                <a href="doctor/profile.php" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-user-md mr-2"></i>لوحة التحكم الطبية
                </a>
                <a href="add_doctor_features.php" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-cog mr-2"></i>إعدادات قاعدة البيانات
                </a>
            </div>
        </div>
    </div>

    <script>
    // Add visual feedback for theme selection
    document.querySelectorAll('.theme-preview').forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading effect
            this.style.opacity = '0.7';
            this.style.transform = 'scale(0.95)';

            // Remove loading effect after navigation
            setTimeout(() => {
                this.style.opacity = '1';
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });
    </script>
</body>
</html>
