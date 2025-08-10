<?php
/**
 * Doctor Dashboard Sidebar
 * شريط جانبي لوحة تحكم الطبيب
 */

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || !is_doctor()) {
    header('Location: ../login.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<div id="sidebar" class="fixed top-0 right-0 h-full bg-white shadow-lg w-64 z-50 hidden lg:block flex flex-col">
    <!-- Sidebar Header -->
    <div class="bg-blue-600 text-white p-4">
        <h2 class="text-lg font-semibold">لوحة تحكم الطبيب</h2>
        <p class="text-sm opacity-90">Doctor Dashboard</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-4">
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="index.php"
                   class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors <?php echo $current_page === 'index.php' ? 'bg-blue-100 text-blue-700' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    الرئيسية
                    <span class="mr-auto">Dashboard</span>
                </a>
            </li>

            <!-- Appointments -->
            <li>
                <a href="appointments.php"
                   class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors <?php echo $current_page === 'appointments.php' ? 'bg-blue-100 text-blue-700' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    المواعيد
                    <span class="mr-auto">Appointments</span>
                </a>
            </li>

            <!-- Patients -->
            <li>
                <a href="patients.php"
                   class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors <?php echo $current_page === 'patients.php' ? 'bg-blue-100 text-blue-700' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    المرضى
                    <span class="mr-auto">Patients</span>
                </a>
            </li>

            <!-- Schedule -->
            <li>
                <a href="schedule.php"
                   class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors <?php echo $current_page === 'schedule.php' ? 'bg-blue-100 text-blue-700' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    الجدول الزمني
                    <span class="mr-auto">Schedule</span>
                </a>
            </li>

            <!-- Availability -->
            <li>
                <a href="availability.php"
                   class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors <?php echo $current_page === 'availability.php' ? 'bg-blue-100 text-blue-700' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    أوقات العمل
                    <span class="mr-auto">Availability</span>
                </a>
            </li>

            <!-- Profile -->
            <li>
                <a href="profile.php"
                   class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors <?php echo $current_page === 'profile.php' ? 'bg-blue-100 text-blue-700' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    الملف الشخصي
                    <span class="mr-auto">Profile</span>
                </a>
            </li>

            <!-- Settings -->
            <li>
                <a href="settings.php"
                   class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-colors <?php echo $current_page === 'settings.php' ? 'bg-blue-100 text-blue-700' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    الإعدادات
                    <span class="mr-auto">Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-gray-200">
        <a href="../logout.php"
           class="flex items-center px-3 py-2 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            تسجيل الخروج
            <span class="mr-auto">Logout</span>
        </a>
    </div>
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

<script>
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('hidden');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('hidden');
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.add('hidden');
            sidebarOverlay.classList.add('hidden');
        });
    }

    // Close sidebar when clicking outside
    document.addEventListener('click', function(e) {
        const isClickInsideSidebar = sidebar && sidebar.contains(e.target);
        const isClickOnToggle = sidebarToggle && sidebarToggle.contains(e.target);

        if (sidebar && !sidebar.classList.contains('hidden') && !isClickInsideSidebar && !isClickOnToggle) {
            sidebar.classList.add('hidden');
            if (sidebarOverlay) {
                sidebarOverlay.classList.add('hidden');
            }
        }
    });
});
</script>
