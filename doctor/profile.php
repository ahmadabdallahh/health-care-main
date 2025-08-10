<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Ensure user is logged in as a doctor
if (!is_logged_in() || $_SESSION['user_type'] !== 'doctor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'إعدادات حسابي';
require_once '../includes/dashboard_header.php';
?>

<div class="dashboard-container">
    <?php require_once '../includes/dashboard_sidebar.php'; ?>

    <main class="dashboard-main-content">
        <div class="dashboard-header">
            <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
        </div>

        <div class="dashboard-content">
            <p>هنا سيتم وضع نموذج لتعديل معلومات الملف الشخصي للطبيب.</p>
            <!-- Profile form will go here -->
        </div>
    </main>
</div>

<?php
require_once '../includes/dashboard_footer.php';
?>
