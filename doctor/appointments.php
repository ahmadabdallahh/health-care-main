<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Ensure user is logged in as a doctor
if (!is_logged_in() || $_SESSION['user_type'] !== 'doctor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

// Get doctor's appointments
$doctor_id = $_SESSION['user_id'];
$appointments = get_appointments_by_doctor_id($doctor_id);

$pageTitle = 'جدول المواعيد';
require_once '../includes/dashboard_header.php';
?>

<div class="dashboard-container">
    <?php require_once '../includes/dashboard_sidebar.php'; ?>

    <main class="dashboard-main-content">
        <div class="dashboard-header">
            <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
        </div>

        <div class="dashboard-content">
            <?php if (empty($appointments)): ?>
                <div class="alert alert-info">لا يوجد لديك أي مواعيد محجوزة حاليًا.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>اسم المريض</th>
                                <th>تاريخ الموعد</th>
                                <th>وقت الموعد</th>
                                <th>الحالة</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                    <td><?php echo htmlspecialchars(date('h:i A', strtotime($appointment['appointment_time']))); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo get_status_badge_class($appointment['status']); ?>">
                                            <?php echo htmlspecialchars(translate_status($appointment['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info">تفاصيل</a>
                                        <!-- Add more actions like cancel/confirm later -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php
require_once '../includes/dashboard_footer.php';
?>
