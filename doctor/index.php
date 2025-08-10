<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Ensure user is logged in as a doctor
if (!is_logged_in() || $_SESSION['user_type'] !== 'doctor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$user = get_logged_in_user();
$pageTitle = 'لوحة تحكم الطبيب';

// Use the global $conn variable directly
global $conn;

// Fetch doctor-specific statistics
$doctor_id = $_SESSION['user_id'];
$upcoming_appointments = get_doctor_appointment_count($conn, $doctor_id, 'confirmed');
$total_patients = get_doctor_patient_count($conn, $doctor_id);
$completed_appointments = get_doctor_appointment_count($conn, $doctor_id, 'completed');

require_once '../includes/dashboard_header.php';
?>

<div class="dashboard-container">
    <?php require_once '../includes/dashboard_sidebar.php'; ?>

    <main class="dashboard-main-content">
        <div class="dashboard-header">
            <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
            <p>مرحباً بك، د. <?php echo htmlspecialchars($user['full_name']); ?>!</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="stat-card bg-white p-6 rounded-lg shadow-md flex items-center">
                <div class="stat-icon bg-blue-100 text-blue-500 rounded-full p-4 mr-4">
                    <i class="fas fa-calendar-check fa-2x"></i>
                </div>
                <div>
                    <h4 class="text-gray-500 font-semibold">المواعيد القادمة</h4>
                    <p class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($upcoming_appointments); ?></p>
                </div>
            </div>
            <div class="stat-card bg-white p-6 rounded-lg shadow-md flex items-center">
                <div class="stat-icon bg-green-100 text-green-500 rounded-full p-4 mr-4">
                    <i class="fas fa-user-injured fa-2x"></i>
                </div>
                <div>
                    <h4 class="text-gray-500 font-semibold">إجمالي المرضى</h4>
                    <p class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($total_patients); ?></p>
                </div>
            </div>
            <div class="stat-card bg-white p-6 rounded-lg shadow-md flex items-center">
                <div class="stat-icon bg-orange-100 text-orange-500 rounded-full p-4 mr-4">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <div>
                    <h4 class="text-gray-500 font-semibold">المواعيد المكتملة</h4>
                    <p class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($completed_appointments); ?></p>
                </div>
            </div>
        </div>

        <div class="dashboard-content bg-white p-6 rounded-lg shadow-md">
            <h3 class="font-bold text-xl mb-4">إدارة العيادة</h3>
            <p>من هنا يمكنك إدارة مواعيدك، تحديث ملفك الشخصي، وتحديد أوقات توافرك.</p>
            <!-- Quick access links can go here -->
        </div>
    </main>
</div>

<?php
require_once '../includes/dashboard_footer.php';
?>
