<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Ensure user is logged in as a patient
if (!is_logged_in() || ($_SESSION['role'] !== 'patient' && $_SESSION['user_type'] !== 'patient')) {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$user = get_logged_in_user();
$pageTitle = 'مواعيدي';
$page_title = $pageTitle;

$user_id = $_SESSION['user_id'];

// Get all appointments for the patient
$appointments = [];
try {
    $stmt = $conn->prepare("
        SELECT a.*, d.full_name as doctor_name, h.name as hospital_name, s.name as specialty_name
        FROM appointments a
        LEFT JOIN users d ON a.doctor_id = d.id
        LEFT JOIN hospitals h ON a.hospital_id = h.id
        LEFT JOIN specialties s ON d.specialty_id = s.id
        WHERE a.user_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute([$user_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching appointments: " . $e->getMessage());
}

require_once '../includes/dashboard_header.php';
?>

<!-- Include the color scheme CSS -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/color-scheme.php">

<style>
/* Appointments Page Styles */
.appointments-page {
    /* background: var(--gradient-primary); */
    min-height: 100vh;
    padding: 20px;
}

.appointments-container {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
    min-height: calc(100vh - 40px);
}

.enhanced-sidebar {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px 20px;
    box-shadow: var(--glass-shadow);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.sidebar-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.sidebar-header h3 {
    color: var(--text-secondary);
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.sidebar-nav {
    margin-bottom: 30px;
}

.nav-item {
    margin-bottom: 8px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-weight: 600;
}

.nav-link:hover {
    background:#dcdcdc;
    color: black;
    transform: translateX(-5px);
    box-shadow: 0 10px 20px var(--shadow-primary);
}

.nav-link.active {
    /* background: var(--gradient-primary); */
    background: #dcdcdc !important;
    color: black !important;
    box-shadow: 0 10px 20px var(--shadow-primary);
}

.nav-link i {
    margin-left: 15px;
    font-size: 18px;
    width: 20px;
    text-align: center;
}

.nav-link span {
    flex: 1;
}

.main-content {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: var(--glass-shadow);
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px;
    background: #fefefe !important;
    color: black !important;
    border-radius: 20px;
    border: 2px solid #dcdcdc;
}

.page-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.page-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.appointments-grid {
    display: grid;
    gap: 20px;
}

.appointment-card {
    background: var(--bg-primary);
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 30px var(--shadow-medium);
    transition: all 0.3s ease;
    border-left: 5px solid var(--primary-color);
}

.appointment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px var(--shadow-dark);
}

.appointment-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.appointment-info h3 {
    color: var(--text-primary);
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0 0 10px 0;
}

.appointment-info p {
    color: var(--text-muted);
    margin: 5px 0;
    font-size: 0.95rem;
}

.appointment-status {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-confirmed {
    background: var(--status-confirmed-bg);
    color: var(--status-confirmed-text);
}

.status-completed {
    background: var(--status-completed-bg);
    color: var(--status-completed-text);
}

.status-cancelled {
    background: var(--status-cancelled-bg);
    color: var(--status-cancelled-text);
}

.status-pending {
    background: var(--status-pending-bg);
    color: var(--status-pending-text);
}

.appointment-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.detail-item {
    display: flex;
    align-items: center;
}

.detail-item i {
    color: var(--primary-color);
    margin-left: 10px;
    width: 20px;
    text-align: center;
}

.detail-item span {
    color: var(--text-secondary);
    font-weight: 600;
}

.appointment-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.action-btn {
    padding: 15px 25px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 0.9rem;
    box-shadow: 0 10px 20px var(--shadow-primary);
    background: #dcdcdc !important;
    color: black !important;
}

.btn-primary {
    background: var(--gradient-primary);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px var(--shadow-primary);
}

.btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #cbd5e0;
}

.btn-danger {
    background: #fed7d7;
    color: #742a2a;
}

.btn-danger:hover {
    background: #feb2b2;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #718096;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #4a5568;
}

.empty-state p {
    margin-bottom: 30px;
}

@media (max-width: 1024px) {
    .appointments-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .enhanced-sidebar {
        position: static;
        order: 2;
    }
}

@media (max-width: 768px) {
    .appointments-page {
        padding: 10px;
    }

    .main-content {
        padding: 20px;
    }

    .appointment-header {
        flex-direction: column;
        gap: 15px;
    }

    .appointment-details {
        grid-template-columns: 1fr;
    }

    .appointment-actions {
        flex-direction: column;
    }
}
</style>

<div class="appointments-page">
    <div class="appointments-container">
        <!-- Enhanced Sidebar -->
        <div class="enhanced-sidebar">
            <div class="sidebar-header">
                <h3>شفاء</h3>
                <p style="color: #718096; margin: 10px 0 0 0; font-size: 0.9rem;">نظام الحجوزات الطبية</p>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="<?php echo BASE_URL; ?>patient/index.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>لوحة التحكم</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?php echo BASE_URL; ?>patient/appointments.php" class="nav-link active">
                        <i class="fas fa-calendar-check"></i>
                        <span>مواعيدي</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?php echo BASE_URL; ?>patient/medical_records.php" class="nav-link">
                        <i class="fas fa-file-medical"></i>
                        <span>سجلاتي الطبية</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?php echo BASE_URL; ?>patient/profile.php" class="nav-link">
                        <i class="fas fa-user-edit"></i>
                        <span>تعديل الملف الشخصي</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?php echo BASE_URL; ?>#doctors" class="nav-link">
                        <i class="fas fa-search"></i>
                        <span>حجز موعد جديد</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="nav-item">
                    <a href="<?php echo BASE_URL; ?>patient/settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>الإعدادات</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?php echo BASE_URL; ?>logout.php" class="nav-link" style="color: #e53e3e;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>تسجيل الخروج</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1>مواعيدي الطبية</h1>
                <p>عرض وإدارة جميع مواعيدك الطبية</p>
            </div>

            <!-- Appointments List -->
            <div class="appointments-grid">
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="appointment-info">
                                    <h3>
                                        <?php echo htmlspecialchars($appointment['doctor_name'] ?? 'طبيب غير محدد'); ?>
                                    </h3>
                                    <p>
                                        <i class="fas fa-hospital"></i>
                                        <?php echo htmlspecialchars($appointment['hospital_name'] ?? 'مستشفى غير محدد'); ?>
                                    </p>
                                    <?php if (!empty($appointment['specialty_name'])): ?>
                                        <p>
                                            <i class="fas fa-stethoscope"></i>
                                            <?php echo htmlspecialchars($appointment['specialty_name']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="appointment-status status-<?php echo strtolower($appointment['status']); ?>">
                                    <?php
                                    $status_text = [
                                        'confirmed' => 'مؤكد',
                                        'completed' => 'مكتمل',
                                        'cancelled' => 'ملغي',
                                        'pending' => 'في الانتظار'
                                    ];
                                    echo $status_text[$appointment['status']] ?? $appointment['status'];
                                    ?>
                                </div>
                            </div>

                            <div class="appointment-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>التاريخ: <?php echo htmlspecialchars($appointment['appointment_date']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span>الوقت: <?php echo htmlspecialchars($appointment['appointment_time']); ?></span>
                                </div>
                                <?php if (!empty($appointment['notes'])): ?>
                                    <div class="detail-item">
                                        <i class="fas fa-sticky-note"></i>
                                        <span>ملاحظات: <?php echo htmlspecialchars($appointment['notes']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="appointment-actions">
                                <?php if ($appointment['status'] === 'confirmed'): ?>
                                    <a href="#" class="action-btn btn-primary">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </a>
                                    <a href="#" class="action-btn btn-danger">
                                        <i class="fas fa-times"></i> إلغاء الموعد
                                    </a>
                                <?php elseif ($appointment['status'] === 'completed'): ?>
                                    <a href="#" class="action-btn btn-primary">
                                        <i class="fas fa-eye"></i> عرض التقرير
                                    </a>
                                    <a href="#" class="action-btn btn-secondary">
                                        <i class="fas fa-star"></i> تقييم الطبيب
                                    </a>
            <?php else: ?>
                                    <a href="#" class="action-btn btn-secondary">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </a>
                                        <?php endif; ?>
                            </div>
                        </div>
                            <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>لا توجد مواعيد</h3>
                        <p>لم تقم بحجز أي مواعيد طبية بعد</p>
                        <a href="<?php echo BASE_URL; ?>#doctors" class="action-btn btn-primary">
                            <i class="fas fa-plus"></i> حجز موعد جديد
                        </a>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/dashboard_footer.php';
?>
