<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Ensure user is logged in as an admin (use unified role check)
if (!check_user_role('admin')) {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

require_once '../includes/functions.php';
require_once '../config/database.php';

// Fetch all users for display
$db = new Database();
$conn = $db->getConnection();
$users = get_all_users($conn);

$page_title = "إدارة المستخدمين";
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
    </style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen">
    <!-- Sidebar -->
    <?php require_once '../includes/dashboard_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden lg:mr-64">
        <?php require_once '../includes/dashboard_header.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 md:p-6">
            <div class="container mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4"><?php echo $page_title; ?></h3>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white text-center">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3">الاسم الكامل</th>
                                    <th scope="col" class="px-6 py-3">البريد الإلكتروني</th>
                                    <th scope="col" class="px-6 py-3">نوع المستخدم</th>
                                    <th scope="col" class="px-6 py-3">تاريخ التسجيل</th>
                                    <th scope="col" class="px-6 py-3">الإجراءات</th>
                                </tr>

                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium text-gray-900">
                                                <?php echo htmlspecialchars($user['full_name']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo htmlspecialchars($user['email']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo htmlspecialchars($user['user_type']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo date('d-m-Y', strtotime($user['created_at'])); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="font-medium text-blue-600 hover:underline">تعديل</a>
                                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="font-medium text-red-600 hover:underline ml-4">حذف</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="bg-white border-b">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            لا يوجد مستخدمين لعرضهم.
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

<?php
require_once '../includes/dashboard_footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');

        if (sidebarToggle && sidebar) {
            // Event to toggle sidebar visibility
            sidebarToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.toggle('hidden');
            });

            // Event to hide sidebar when clicking outside
            document.addEventListener('click', function (e) {
                const isClickInsideSidebar = sidebar.contains(e.target);
                const isClickOnToggle = sidebarToggle.contains(e.target);

                if (!sidebar.classList.contains('hidden') && !isClickInsideSidebar && !isClickOnToggle) {
                    sidebar.classList.add('hidden');
                }
            });
        }
    });
</script>

</body>
</html>

<!--

feat(admin): Implement full-stack doctor management page

Implements a complete feature for administrators to manage doctor accounts. This includes backend logic, database schema corrections, and a new user interface.

- **New Admin Page:** Creates the [admin/manage_doctors.php](cci:7://file:///c:/xampp/htdocs/app-demo/admin/manage_doctors.php:0:0-0:0) page, allowing admins to view all doctors, approve pending accounts, suspend accounts, and delete doctors.

- **Backend Functions:** Adds three new functions
    - [get_all_doctors_with_details()]
    - [update_doctor_account_status()]
    - [delete_doctor_by_user_id()]

- **Database Schema Fix:** Corrects the [doctors](cci:1://file:///c:/xampp/htdocs/app-demo/includes/functions.php:872:0-913:1) table schema by adding the essential [user_id](cci:1://file:///c:/xampp/htdocs/app-demo/includes/functions.php:1334:0-1360:1), [status](cci:1://file:///c:/xampp/htdocs/app-demo/includes/functions.php:840:0-852:1), and `specialization` columns. This resolves critical fatal errors caused by the schema mismatch and enables the new functionality.-->
