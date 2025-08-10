<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

// Ensure only admins can access this page
if (!check_user_role('admin')) {
    redirect('../login.php');
}

$db = new Database();
$conn = $db->getConnection();

// Handle POST requests for updating status or deleting a doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $doctor_id = $_POST['doctor_id'];
        $new_status = $_POST['new_status'];
        update_doctor_account_status($conn, $doctor_id, $new_status);
    } elseif (isset($_POST['delete_doctor'])) {
        $user_id = $_POST['user_id'];
        delete_doctor_by_user_id($conn, $user_id);
    }
    // Redirect to the same page to see changes and prevent form resubmission
    redirect('manage_doctors.php');
}

// Fetch all doctors for display
$doctors = get_all_doctors_with_details($conn);

$page_title = "إدارة الأطباء";
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
    <?php include '../includes/dashboard_sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden lg:mr-64">
        <?php include '../includes/dashboard_header.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            <div class="container mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4"><?php echo $page_title; ?></h3>

                    <!-- Doctors Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">اسم الطبيب</th>
                                    <th scope="col" class="px-6 py-3">التخصص</th>
                                    <th scope="col" class="px-6 py-3">البريد الإلكتروني</th>
                                    <th scope="col" class="px-6 py-3">الحالة</th>
                                    <th scope="col" class="px-6 py-3">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($doctors)):
                                    foreach ($doctors as $doctor):
                                        $status_classes = [
                                            'approved' => 'text-green-700 bg-green-100',
                                            'pending' => 'text-yellow-700 bg-yellow-100',
                                            'suspended' => 'text-red-700 bg-red-100',
                                        ];
                                        $status_class = $status_classes[$doctor['status']] ?? 'text-gray-700 bg-gray-100';
                                ?>
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium text-gray-900">
                                                <?php echo htmlspecialchars($doctor['full_name']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo htmlspecialchars($doctor['specialization']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo htmlspecialchars($doctor['email']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 font-semibold leading-tight rounded-full <?php echo $status_class; ?>">
                                                    <?php echo htmlspecialchars($doctor['status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 flex items-center space-x-2">
                                                <form action="manage_doctors.php" method="POST" class="inline-block">
                                                    <input type="hidden" name="doctor_id" value="<?php echo $doctor['doctor_id']; ?>">
                                                    <select name="new_status" class="p-1 border border-gray-300 rounded-md" onchange="this.form.submit()">
                                                        <option value="pending" <?php echo $doctor['status'] === 'pending' ? 'selected' : ''; ?>>قيد الانتظار</option>
                                                        <option value="approved" <?php echo $doctor['status'] === 'approved' ? 'selected' : ''; ?>>مقبول</option>
                                                        <option value="suspended" <?php echo $doctor['status'] === 'suspended' ? 'selected' : ''; ?>>معلق</option>
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                                <form action="manage_doctors.php" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الطبيب؟ سيتم حذف جميع بياناته بشكل نهائي.');" class="inline-block">
                                                    <input type="hidden" name="user_id" value="<?php echo $doctor['user_id']; ?>">
                                                    <input type="hidden" name="delete_doctor" value="1">
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                <?php
                                    endforeach;
                                else:
                                ?>
                                    <tr class="bg-white border-b">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            لا يوجد أطباء لعرضهم.
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
            sidebarToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.toggle('hidden');
            });

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
