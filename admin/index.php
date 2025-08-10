<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

// Ensure only admins can access this page
if (!check_user_role('admin')) {
    redirect('../login.php');
}

// Get database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch statistics
$total_patients = get_user_type_count($conn, 'patient');
$total_doctors = get_user_type_count($conn, 'doctor');
$total_appointments = get_total_count($conn, 'appointments');
$estimated_revenue = get_total_count($conn, 'appointments') * 50; // Example calculation

// Fetch recent patients
$recent_patients = get_recent_patients($conn, 5);

$page_title = "لوحة تحكم المسؤول";
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - شفاء</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
        .content-area {
            transition: margin-right 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

<div class="flex h-screen">
    <!-- Sidebar -->
    <?php include '../includes/dashboard_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden lg:mr-64">
        <?php include '../includes/dashboard_header.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 md:p-6">
            <div class="container mx-auto">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">لوحة التحكم الرئيسية</h3>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
                    <!-- Total Patients -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">إجمالي المرضى</h3>
                            <p class="text-4xl font-bold"><?php echo $total_patients; ?></p>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                    <!-- Total Doctors -->
                    <div class="bg-gradient-to-br from-green-400 to-green-600 text-white rounded-xl shadow-lg p-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">إجمالي الأطباء</h3>
                            <p class="text-4xl font-bold"><?php echo $total_doctors; ?></p>
                        </div>
                        <i class="fas fa-stethoscope fa-3x opacity-50"></i>
                    </div>
                    <!-- Total Appointments -->
                    <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 text-white rounded-xl shadow-lg p-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">إجمالي الحجوزات</h3>
                            <p class="text-4xl font-bold"><?php echo $total_appointments; ?></p>
                        </div>
                        <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                    </div>
                    <!-- Estimated Revenue -->
                    <div class="bg-gradient-to-br from-red-400 to-red-600 text-white rounded-xl shadow-lg p-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">الأرباح المقدرة</h3>
                            <p class="text-4xl font-bold">$<?php echo number_format($estimated_revenue); ?></p>
                        </div>
                        <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                    </div>
                </div>

                <!-- Recent Patients Table -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="text-xl font-bold text-gray-800 mb-4">أحدث المرضى المسجلين</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white text-center">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3">اسم المريض</th>
                                    <th scope="col" class="px-6 py-3">تاريخ التسجيل</th>
                                    <th scope="col" class="px-6 py-3">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_patients)): ?>
                                    <?php foreach ($recent_patients as $patient): ?>
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                <?php echo htmlspecialchars($patient['full_name']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo date('d-m-Y', strtotime($patient['created_at'])); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                                                    نشط
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="bg-white border-b">
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                            لا يوجد مرضى لعرضهم حالياً.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');

        if (sidebarToggle && sidebar) {
            // Event to toggle sidebar visibility
            sidebarToggle.addEventListener('click', function (e) {
                e.stopPropagation(); // Prevents the click from bubbling up to the document
                sidebar.classList.toggle('hidden');
            });

            // Event to hide sidebar when clicking outside
            document.addEventListener('click', function (e) {
                const isClickInsideSidebar = sidebar.contains(e.target);
                const isClickOnToggle = sidebarToggle.contains(e.target);

                // If sidebar is visible and click is outside both sidebar and toggle button
                if (!sidebar.classList.contains('hidden') && !isClickInsideSidebar && !isClickOnToggle) {
                    sidebar.classList.add('hidden');
                }
            });
        }
    });
</script>

</body>
</html>
