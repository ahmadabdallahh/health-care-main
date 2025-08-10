<?php
// Get the current page to set the 'active' class
$current_page = basename($_SERVER['PHP_SELF']);

// Get user role from session - FIX: Changed from user_role to user_type for consistency
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

$is_admin = ($user_type === 'admin');
$is_doctor = ($user_type === 'doctor');
$is_patient = ($user_type === 'patient');
$is_hospital = ($user_type === 'hospital');

function get_active_class($page_name) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($page_name == $current_page) ? 'bg-gray-100' : '';
}

function is_active($page_name, $current_page) {
    return ($page_name == $current_page) ? 'bg-gradient-to-tr from-blue-600 to-blue-400 text-white shadow-md' : 'text-gray-600';
}
?>

<div id="sidebar" class="fixed top-0 right-0 h-full bg-white shadow-lg w-64 z-50 hidden lg:block flex flex-col">
    <div class="px-6 py-4">
        <a href="/app-demo/" class="text-2xl font-bold text-gray-800 hover:text-gray-700">شفاء</a>
    </div>

    <nav class="flex-1 overflow-y-auto px-4">
        <ul class="space-y-2">
            <?php if ($is_admin): ?>
                <li>
                    <a href="/app-demo/admin/index.php" class="flex items-center justify-between py-3 px-4 rounded-lg <?php echo get_active_class('index.php'); ?>">
                        <span>لوحة التحكم</span>
                        <i class="fas fa-tachometer-alt"></i>
                    </a>
                </li>
                <li>
                    <a href="/app-demo/admin/manage_users.php" class="flex items-center justify-between py-3 px-4 rounded-lg <?php echo get_active_class('manage_users.php'); ?>">
                        <span>إدارة المستخدمين</span>
                        <i class="fas fa-users"></i>
                    </a>
                </li>
                <li>
                    <a href="/app-demo/admin/manage_doctors.php" class="flex items-center justify-between py-3 px-4 rounded-lg <?php echo get_active_class('manage_doctors.php'); ?>">
                        <span>إدارة الأطباء</span>
                        <i class="fas fa-user-md"></i>
                    </a>
                </li>
                <li>
                    <a href="/app-demo/admin/manage_bookings.php" class="flex items-center justify-between py-3 px-4 rounded-lg <?php echo get_active_class('manage_bookings.php'); ?>">
                        <span>إدارة الحجوزات</span>
                        <i class="fas fa-calendar-check"></i>
                    </a>
                </li>
            <?php elseif ($is_doctor): ?>
                <!-- Doctor Links -->
                <li>
                    <a href="/app-demo/doctor/index.php" class="flex items-center justify-between py-3 px-4 rounded-lg <?php echo get_active_class('index.php'); ?>">
                        <span>لوحة التحكم</span>
                        <i class="fas fa-tachometer-alt"></i>
                    </a>
                </li>
                <li>
                    <a href="/app-demo/doctor/appointments.php" class="flex items-center justify-between py-3 px-4 rounded-lg <?php echo get_active_class('appointments.php'); ?>">
                        <span>جدول المواعيد</span>
                        <i class="fas fa-calendar-alt"></i>
                    </a>
                </li>
                <li>
                    <a href="/app-demo/doctor/profile.php" class="flex items-center justify-between py-3 px-4 rounded-lg <?php echo get_active_class('profile.php'); ?>">
                        <span>إعدادات حسابي</span>
                        <i class="fas fa-user-cog"></i>
                    </a>
                </li>
                <li>
                    <a href="/app-demo/doctor/availability.php" class="flex items-center justify-between py-3 px-4 rounded-lg <?php echo get_active_class('availability.php'); ?>">
                        <span>إدارة التوافر</span>
                        <i class="fas fa-clock"></i>
                    </a>
                </li>
            <?php elseif ($is_patient): ?>
                <!-- Patient Links -->
                <li>
                    <a href="/app-demo/patient/index.php" class="flex items-center justify-between py-3 px-4 rounded-lg <?php echo get_active_class('index.php'); ?>">
                        <span>لوحة التحكم</span>
                        <i class="fas fa-tachometer-alt"></i>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg text-gray-600">
                        <span>مواعيدي</span>
                        <i class="fas fa-calendar-check"></i>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg text-gray-600">
                        <span>سجلاتي الطبية</span>
                        <i class="fas fa-file-medical"></i>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg text-gray-600">
                        <span>تعديل الملف الشخصي</span>
                        <i class="fas fa-user-edit"></i>
                    </a>
                </li>
                <li>
                    <a href="/app-demo/#doctors" class="flex items-center justify-between py-3 px-4 rounded-lg text-gray-600">
                        <span>حجز موعد جديد</span>
                        <i class="fas fa-search"></i>
                    </a>
                </li>
            <?php elseif ($is_hospital): ?>
                <!-- Hospital Links -->
                <li>
                    <a href="/app-demo/hospital/index.php" class="flex items-center justify-between py-3 px-4 rounded-lg <?php echo get_active_class('index.php'); ?>">
                        <span>لوحة التحكم</span>
                        <i class="fas fa-tachometer-alt"></i>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg text-gray-600">
                        <span>إدارة الأطباء</span>
                        <i class="fas fa-user-md"></i>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg text-gray-600">
                        <span>ملف المستشفى</span>
                        <i class="fas fa-building"></i>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center justify-between py-3 px-4 rounded-lg text-gray-600">
                        <span>التقارير والإحصائيات</span>
                        <i class="fas fa-chart-line"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Footer Links -->
    <div class="p-4 mt-auto">
        <ul class="space-y-2">
            <li>
                <a href="/app-demo/<?php echo $user_type; ?>/profile.php" class="flex items-center justify-between py-2 px-4 rounded-lg <?php echo get_active_class('profile.php'); ?> hover:bg-gray-100 transition-colors duration-200">
                    <span class="font-semibold">الإعدادات</span>
                    <i class="fas fa-cog"></i>
                </a>
            </li>
            <li>
                <a href="/app-demo/logout.php" class="flex items-center justify-between py-2 px-4 rounded-lg text-red-500 hover:bg-red-50 transition-colors duration-200">
                    <span class="font-semibold">تسجيل الخروج</span>
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
        </ul>
    </div>
</div>
