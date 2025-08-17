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
$pageTitle = 'سجلاتي الطبية';
$page_title = $pageTitle;

$user_id = $_SESSION['user_id'];

// Get medical records (appointments with notes)
$medical_records = [];
try {
    $stmt = $conn->prepare("
        SELECT a.*, d.full_name as doctor_name, h.name as hospital_name, s.name as specialty_name
        FROM appointments a
        LEFT JOIN users d ON a.doctor_id = d.id
        LEFT JOIN hospitals h ON a.hospital_id = h.id
        LEFT JOIN specialties s ON d.specialty_id = s.id
        WHERE a.user_id = ? AND a.status = 'completed'
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute([$user_id]);
    $medical_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching medical records: " . $e->getMessage());
}

require_once '../includes/dashboard_header.php';
?>

<style>
/* Medical Records Page Styles */
.medical-records-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
}

.medical-records-container {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
    min-height: calc(100vh - 40px);
}

.enhanced-sidebar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
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
    color: #4a5568;
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
    color: #4a5568;
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-weight: 600;
}

.nav-link:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateX(-5px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
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
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
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

.records-grid {
    display: grid;
    gap: 25px;
}

.record-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border-left: 5px solid #48bb78;
}

.record-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.record-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f7fafc;
}

.record-info h3 {
    color: #2d3748;
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0 0 10px 0;
}

.record-info p {
    color: #718096;
    margin: 5px 0;
    font-size: 0.95rem;
}

.record-date {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.record-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.detail-item {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f7fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.detail-item i {
    color: #48bb78;
    margin-left: 12px;
    font-size: 18px;
    width: 20px;
    text-align: center;
}

.detail-item span {
    color: #4a5568;
    font-weight: 600;
}

.record-notes {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
}

.record-notes h4 {
    color: #2d3748;
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
}

.record-notes h4 i {
    color: #48bb78;
    margin-left: 10px;
}

.record-notes p {
    color: #4a5568;
    line-height: 1.6;
    margin: 0;
    font-size: 0.95rem;
}

.record-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 2px solid #f7fafc;
}

.action-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #cbd5e0;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #718096;
}

.empty-state i {
    font-size: 5rem;
    margin-bottom: 30px;
    opacity: 0.5;
    color: #48bb78;
}

.empty-state h3 {
    font-size: 1.8rem;
    margin-bottom: 15px;
    color: #4a5568;
}

.empty-state p {
    margin-bottom: 30px;
    font-size: 1.1rem;
}

.stats-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-item {
    background: white;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 24px;
    color: white;
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 5px;
}

.stat-label {
    color: #718096;
    font-weight: 600;
}

@media (max-width: 1024px) {
    .medical-records-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .enhanced-sidebar {
        position: static;
        order: 2;
    }
}

@media (max-width: 768px) {
    .medical-records-page {
        padding: 10px;
    }

    .main-content {
        padding: 20px;
    }

    .record-header {
        flex-direction: column;
        gap: 15px;
    }

    .record-details {
        grid-template-columns: 1fr;
    }

    .record-actions {
        flex-direction: column;
    }

    .stats-summary {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="medical-records-page">
    <div class="medical-records-container">
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
                    <a href="<?php echo BASE_URL; ?>patient/appointments.php" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>مواعيدي</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?php echo BASE_URL; ?>patient/medical_records.php" class="nav-link active">
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
                <h1>سجلاتي الطبية</h1>
                <p>عرض تاريخك الطبي والمواعيد المكتملة</p>
            </div>

            <!-- Stats Summary -->
            <div class="stats-summary">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="stat-number"><?php echo count($medical_records); ?></div>
                    <div class="stat-label">إجمالي السجلات</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-number">
                        <?php
                        $unique_doctors = array_unique(array_column($medical_records, 'doctor_name'));
                        echo count(array_filter($unique_doctors));
                        ?>
                    </div>
                    <div class="stat-label">الأطباء المعالجون</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <div class="stat-number">
                        <?php
                        $unique_hospitals = array_unique(array_column($medical_records, 'hospital_name'));
                        echo count(array_filter($unique_hospitals));
                        ?>
                    </div>
                    <div class="stat-label">المستشفيات</div>
                </div>
            </div>

            <!-- Medical Records List -->
            <div class="records-grid">
                <?php if (!empty($medical_records)): ?>
                    <?php foreach ($medical_records as $record): ?>
                        <div class="record-card">
                            <div class="record-header">
                                <div class="record-info">
                                    <h3>
                                        <?php echo htmlspecialchars($record['doctor_name'] ?? 'طبيب غير محدد'); ?>
                                    </h3>
                                    <p>
                                        <i class="fas fa-hospital"></i>
                                        <?php echo htmlspecialchars($record['hospital_name'] ?? 'مستشفى غير محدد'); ?>
                                    </p>
                                    <?php if (!empty($record['specialty_name'])): ?>
                                        <p>
                                            <i class="fas fa-stethoscope"></i>
                                            <?php echo htmlspecialchars($record['specialty_name']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="record-date">
                                    <?php echo date('d/m/Y', strtotime($record['appointment_date'])); ?>
                                </div>
                            </div>

                            <div class="record-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>التاريخ: <?php echo htmlspecialchars($record['appointment_date']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span>الوقت: <?php echo htmlspecialchars($record['appointment_time']); ?></span>
                                </div>
                                <?php if (!empty($record['specialty_name'])): ?>
                                    <div class="detail-item">
                                        <i class="fas fa-stethoscope"></i>
                                        <span>التخصص: <?php echo htmlspecialchars($record['specialty_name']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($record['notes'])): ?>
                                <div class="record-notes">
                                    <h4>
                                        <i class="fas fa-notes-medical"></i>
                                        ملاحظات الطبيب
                                    </h4>
                                    <p><?php echo nl2br(htmlspecialchars($record['notes'])); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="record-actions">
                                <a href="#" class="action-btn btn-primary">
                                    <i class="fas fa-eye"></i> عرض التفاصيل الكاملة
                                </a>
                                <a href="#" class="action-btn btn-secondary">
                                    <i class="fas fa-download"></i> تحميل التقرير
                                </a>
                                <a href="#" class="action-btn btn-secondary">
                                    <i class="fas fa-share"></i> مشاركة مع طبيب آخر
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-file-medical"></i>
                        <h3>لا توجد سجلات طبية</h3>
                        <p>لم تكتمل أي مواعيد طبية بعد. ستظهر السجلات هنا بعد إكمال مواعيدك.</p>
                        <a href="<?php echo BASE_URL; ?>#doctors" class="action-btn btn-primary">
                            <i class="fas fa-plus"></i> حجز موعد جديد
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth animations to record cards
    const recordCards = document.querySelectorAll('.record-card');

    recordCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Add click handlers for action buttons
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.href === '#') {
                e.preventDefault();
                // Show a message that this feature is coming soon
                alert('هذه الميزة ستكون متاحة قريباً');
            }
        });
    });
});
</script>

<?php
require_once '../includes/dashboard_footer.php';
?>
