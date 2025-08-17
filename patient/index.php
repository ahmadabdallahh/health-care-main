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
$pageTitle = 'لوحة تحكم المريض';
$page_title = $pageTitle; // Set both for compatibility

// Fetch patient-specific statistics
$user_id = $_SESSION['user_id'];
$upcoming_appointments = get_patient_appointment_count($conn, $user_id, 'confirmed');
$completed_appointments = get_patient_appointment_count($conn, $user_id, 'completed');
$total_appointments = get_patient_appointment_count($conn, $user_id, 'all');

// Get recent appointments
$recent_appointments = [];
try {
    $stmt = $conn->prepare("
        SELECT a.*, d.full_name as doctor_name, h.name as hospital_name
        FROM appointments a
        LEFT JOIN users d ON a.doctor_id = d.id
        LEFT JOIN hospitals h ON a.hospital_id = h.id
        WHERE a.user_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $recent_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching recent appointments: " . $e->getMessage());
}

require_once '../includes/dashboard_header.php';
?>

<!-- Include the color scheme CSS -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/color-scheme.php">

<style>
/* Enhanced Patient Dashboard Styles */
.patient-dashboard {
    /* background: var(--gradient-primary); */
    min-height: 100vh;
    padding: 20px;
}

.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
    min-height: calc(100vh - 40px);
}

/* Enhanced Sidebar */
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
    position: relative;
    overflow: hidden;
}

.nav-link:hover {
    background: #dcdcdc;
    color: black;
    transform: translateX(-5px);
    box-shadow: 0 10px 20px var(--shadow-primary);
}

.nav-link.active {
    background:#dcdcdc !important;
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

/* Main Content Area */
.main-content {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: var(--glass-shadow);
}

.welcome-section {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px;
    background: #fefefe;
    border-radius: 20px;
    color: white;
    box-shadow: 0 10px 20px var(--shadow-primary);
    border: 2px solid #dcdcdc;
}

.welcome-section h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.welcome-section p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: var(--bg-primary);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px var(--shadow-medium);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #dcdcdc;
}

.stat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px var(--shadow-dark);
}

.stat-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 15px;
    font-size: 24px;
    color: white;
}

.stat-icon.upcoming {
    background: var(--gradient-primary);
}

.stat-icon.completed {
    background: var(--gradient-success);
}

.stat-icon.total {
    background: var(--gradient-warning);
}

.stat-info h3 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    color: var(--text-primary);
}

.stat-info p {
    color: var(--text-muted);
    margin: 5px 0 0 0;
    font-weight: 600;
}

/* Quick Actions */
.quick-actions {
    background: var(--bg-primary);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px var(--shadow-medium);
    margin-bottom: 40px;
}

.quick-actions h3 {
    color: var(--text-primary);
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 25px;
    text-align: center;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 25px 20px;
    background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
    border: 2px solid var(--border-light);
    border-radius: 15px;
    text-decoration: none;
    color: var(--text-secondary);
    transition: all 0.3s ease;
    text-align: center;
}

.action-btn:hover {
    background: #dcdcdc;
    color: black;
    transform: translateY(-5px);
    box-shadow: 0 15px 30px var(--shadow-primary);
    border-color: transparent;

}

.action-btn i {
    font-size: 2rem;
    margin-bottom: 15px;
}

.action-btn span {
    font-weight: 600;
    font-size: 1rem;
}

/* Recent Appointments */
.recent-appointments {
    background: var(--bg-primary);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px var(--shadow-medium);
}

.recent-appointments h3 {
    color: var(--text-primary);
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 25px;
    text-align: center;
}

.appointment-item {
    display: flex;
    align-items: center;
    padding: 20px;
    border: 1px solid var(--border-light);
    border-radius: 12px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.appointment-item:hover {
    border-color: var(--primary-color);
    box-shadow: 0 5px 15px var(--shadow-primary);
}

.appointment-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 15px;
    font-size: 20px;
    color: white;
    background: var(--gradient-primary);
}

.appointment-info {
    flex: 1;
}

.appointment-info h4 {
    color: var(--text-primary);
    font-weight: 600;
    margin: 0 0 5px 0;
}

.appointment-info p {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.9rem;
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

/* Responsive Design */
@media (max-width: 1024px) {
    .dashboard-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .enhanced-sidebar {
        position: static;
        order: 2;
    }
}

@media (max-width: 768px) {
    .patient-dashboard {
        padding: 10px;
    }

    .main-content {
        padding: 20px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .actions-grid {
        grid-template-columns: 1fr;
    }

    .welcome-section h1 {
        font-size: 2rem;
    }
}

/* Loading Animation */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--primary-color);
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="patient-dashboard">
<div class="dashboard-container">
        <!-- NOTE: Enhanced Sidebar -->
        <div class="enhanced-sidebar">
            <div class="sidebar-header">
                <h3>شفاء</h3>
                <p style="color: var(--text-muted); margin: 10px 0 0 0; font-size: 0.9rem;">نظام الحجوزات الطبية</p>
        </div>

            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="<?php echo BASE_URL; ?>patient/index.php" class="nav-link active">
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
                    <a href="<?php echo BASE_URL; ?>logout.php" class="nav-link" style="color: var(--danger-color);">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>تسجيل الخروج</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>مرحباً بك، <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
                <p>إدارة مواعيدك الطبية أصبحت أسهل من أي وقت مضى</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon upcoming">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo htmlspecialchars($upcoming_appointments); ?></h3>
                            <p>المواعيد القادمة</p>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo htmlspecialchars($completed_appointments); ?></h3>
                            <p>المواعيد المكتملة</p>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon total">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo htmlspecialchars($upcoming_appointments + $completed_appointments); ?></h3>
                            <p>إجمالي المواعيد</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3>إجراءات سريعة</h3>
                <div class="actions-grid">
                    <a href="<?php echo BASE_URL; ?>#doctors" class="action-btn">
                        <i class="fas fa-search"></i>
                        <span>حجز موعد جديد</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>patient/appointments.php" class="action-btn">
                        <i class="fas fa-calendar-check"></i>
                        <span>عرض مواعيدي</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>patient/profile.php" class="action-btn">
                        <i class="fas fa-user-edit"></i>
                        <span>تعديل الملف الشخصي</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>patient/medical_records.php" class="action-btn">
                        <i class="fas fa-file-medical"></i>
                        <span>السجلات الطبية</span>
                    </a>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="recent-appointments">
                <h3>آخر المواعيد</h3>
                <?php if (!empty($recent_appointments)): ?>
                    <?php foreach ($recent_appointments as $appointment): ?>
                        <div class="appointment-item">
                            <div class="appointment-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="appointment-info">
                                <h4>
                                    <?php echo htmlspecialchars($appointment['doctor_name'] ?? 'طبيب غير محدد'); ?>
                                    <?php if (!empty($appointment['hospital_name'])): ?>
                                        - <?php echo htmlspecialchars($appointment['hospital_name']); ?>
                                    <?php endif; ?>
                                </h4>
                                <p>
                                    <?php echo htmlspecialchars($appointment['appointment_date']); ?>
                                    في الساعة <?php echo htmlspecialchars($appointment['appointment_time']); ?>
                                </p>
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
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #718096;">
                        <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                        <p>لا توجد مواعيد حديثة</p>
                        <a href="<?php echo BASE_URL; ?>#doctors" class="action-btn" style="display: inline-flex; margin-top: 20px;">
                            <i class="fas fa-plus"></i>
                            <span>حجز موعد جديد</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Add smooth scrolling and enhanced interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to action buttons
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.href.includes('#') || this.href.includes('logout.php')) {
                return; // Don't add loading for anchor links or logout
            }

            this.classList.add('loading');
            this.style.position = 'relative';

            // Remove loading after navigation
            setTimeout(() => {
                this.classList.remove('loading');
            }, 2000);
        });
    });

    // Add hover effects to stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php
require_once '../includes/dashboard_footer.php';
?>
