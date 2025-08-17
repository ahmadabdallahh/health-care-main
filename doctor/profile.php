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
$doctor_id = $_SESSION['user_id'];
$pageTitle = 'لوحة التحكم الطبية';
$page_title = $pageTitle;

// Get doctor statistics
$stats = get_doctor_dashboard_stats($conn, $doctor_id);
$upcoming_appointments = get_doctor_upcoming_appointments($conn, $doctor_id, 10);
$recent_patients = get_doctor_recent_patients($conn, $doctor_id, 5);

// Handle on-call status toggle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_oncall'])) {
    $new_status = $_POST['oncall_status'] === 'on' ? 'off' : 'on';
    $stmt = $conn->prepare("UPDATE users SET oncall_status = ? WHERE id = ?");
    $stmt->execute([$new_status, $doctor_id]);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

require_once '../includes/dashboard_header.php';
?>

<!-- Include the doctor theme CSS -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/doctor-theme.php">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Medical Dashboard Styles */
.medical-dashboard {
    min-height: 100vh;
    background: var(--gradient-primary);
    padding: 20px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 320px 1fr 350px;
    gap: 25px;
    max-width: 1800px;
    margin: 0 auto;
    min-height: calc(100vh - 40px);
}

/* Left Sidebar - Doctor Profile & Stats */
.doctor-sidebar {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    border-radius: 25px;
    padding: 30px;
    box-shadow: var(--glass-shadow);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.doctor-profile {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 2px solid var(--border-light);
}

.doctor-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--primary-color);
    box-shadow: 0 10px 30px var(--shadow-primary);
    margin: 0 auto 15px;
}

.doctor-name {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 5px;
}

.doctor-specialty {
    color: var(--text-secondary);
    font-size: 0.95rem;
    margin-bottom: 15px;
}

.oncall-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 20px;
}

.toggle-switch {
    position: relative;
    width: 60px;
    height: 30px;
    background: var(--border-medium);
    border-radius: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.toggle-switch.active {
    background: var(--success-color);
}

.toggle-slider {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 24px;
    height: 24px;
    background: white;
    border-radius: 50%;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.toggle-switch.active .toggle-slider {
    transform: translateX(30px);
}

.stats-grid {
    display: grid;
    gap: 15px;
}

.stat-card {
    background: var(--bg-primary);
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px var(--shadow-primary);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.stat-label {
    color: var(--text-secondary);
    font-size: 0.9rem;
    font-weight: 600;
}

/* Main Content - Appointment Calendar */
.main-content {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    border-radius: 25px;
    padding: 30px;
    box-shadow: var(--glass-shadow);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--border-light);
}

.calendar-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-primary);
}

.calendar-nav {
    display: flex;
    gap: 10px;
}

.calendar-nav button {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.calendar-nav button:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.appointment-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.appointment-card {
    background: var(--bg-primary);
    border-radius: 15px;
    padding: 20px;
    border-left: 4px solid var(--primary-color);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.appointment-card.urgent {
    border-left-color: var(--danger-color);
    background: linear-gradient(135deg, var(--status-cancelled-bg), var(--bg-primary));
}

.appointment-card.regular {
    border-left-color: var(--success-color);
}

.appointment-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px var(--shadow-primary);
}

.appointment-time {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.patient-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 5px;
}

.appointment-type {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.appointment-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-action {
    padding: 8px 15px;
    border: none;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-start {
    background: var(--success-color);
    color: white;
}

.btn-start:hover {
    background: var(--success-dark);
}

.btn-reschedule {
    background: var(--warning-color);
    color: white;
}

.btn-reschedule:hover {
    background: var(--warning-dark);
}

/* Right Panel - Notifications & Alerts */
.notifications-panel {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    border-radius: 25px;
    padding: 30px;
    box-shadow: var(--glass-shadow);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.panel-header {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border-light);
}

.emergency-alert {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 20px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.notification-item {
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    border-left: 3px solid var(--primary-color);
    transition: all 0.3s ease;
}

.notification-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px var(--shadow-primary);
}

.notification-time {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 5px;
}

.notification-text {
    color: var(--text-primary);
    font-weight: 600;
}

/* Patient Queue */
.patient-queue {
    margin-top: 30px;
}

.queue-item {
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s ease;
    border-left: 3px solid var(--border-light);
}

.queue-item.priority-high {
    border-left-color: var(--danger-color);
    background: linear-gradient(135deg, var(--status-cancelled-bg), var(--bg-primary));
}

.queue-item.priority-medium {
    border-left-color: var(--warning-color);
}

.queue-item.priority-low {
    border-left-color: var(--success-color);
}

.queue-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px var(--shadow-primary);
}

.queue-number {
    background: var(--primary-color);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
}

.queue-info {
    flex: 1;
}

.queue-patient {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 3px;
}

.queue-time {
    font-size: 0.85rem;
    color: var(--text-secondary);
}

/* Responsive Design */
@media (max-width: 1400px) {
    .dashboard-grid {
        grid-template-columns: 280px 1fr 300px;
        gap: 20px;
    }
}

@media (max-width: 1200px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .doctor-sidebar,
    .notifications-panel {
        position: static;
        order: 2;
    }

    .main-content {
        order: 1;
    }
}

@media (max-width: 768px) {
    .medical-dashboard {
        padding: 10px;
    }

    .main-content,
    .doctor-sidebar,
    .notifications-panel {
        padding: 20px;
    }

    .appointment-grid {
        grid-template-columns: 1fr;
    }

    .calendar-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }

    .appointment-actions {
        flex-direction: column;
    }
}

/* Loading Animation */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid var(--border-light);
    border-radius: 50%;
    border-top-color: var(--primary-color);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Status Indicators */
.status-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 8px;
}

.status-online {
    background: var(--success-color);
    box-shadow: 0 0 10px var(--success-color);
}

.status-busy {
    background: var(--warning-color);
    box-shadow: 0 0 10px var(--warning-color);
}

.status-offline {
    background: var(--text-muted);
}
</style>

<div class="medical-dashboard">
    <div class="dashboard-grid">
        <!-- Left Sidebar - Doctor Profile & Stats -->
        <div class="doctor-sidebar">
            <!-- Doctor Profile -->
            <div class="doctor-profile">
                <img src="<?php echo htmlspecialchars($user['profile_image'] ?? 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiByeD0iNjAiIGZpbGw9IiNFNUU3RUIiLz4KPHN2ZyB4PSIzMCIgeT0iMjAiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjOEE5M0E2Ij4KPHBhdGggZD0iTTEyIDJDNi40OCAyIDIgNi40OCAyIDEyczQuNDggMTAgMTAgMTAgMTAtNC40OCAxMC0xMFMxNy41MiAyIDEyIDJ6bTAgM2MyLjY3IDAgNC44MyAyLjE2IDQuODMgNC44M1MxNC42NyAxNC42NiAxMiAxNC42NiA3LjE3IDEyLjUgNy4xNyA5LjgzIDkuMzMgNS4xNyAxMiA1LjE3em0wIDEyYzQuNDIgMCA4LjE3LTIuMTYgOC4xNy00Ljgzcy0zLjc1LTQuODMtOC4xNy00LjgzLTguMTcgMi4xNi04LjE3IDQuODNTNy41OCAyMC4xNyAxMiAyMC4xN3oiLz4KPC9zdmc+Cjwvc3ZnPgo='); ?>"
                     alt="صورة الطبيب" class="doctor-avatar">
                <div class="doctor-name">د. <?php echo htmlspecialchars($user['full_name'] ?? 'اسم الطبيب'); ?></div>
                <div class="doctor-specialty"><?php echo htmlspecialchars($user['specialty'] ?? 'طب عام'); ?></div>
                <div class="theme-indicator" style="margin-top: 10px; padding: 5px 10px; background: var(--primary-light); color: var(--primary-color); border-radius: 8px; font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-palette mr-1"></i>
                    <?php
                    $theme_names = [
                        'medical-blue' => 'الأزرق الطبي',
                        'medical-green' => 'الأخضر الطبي',
                        'warm-orange' => 'البرتقالي الدافئ',
                        'professional-gray' => 'الرمادي المهني',
                        'royal-purple' => 'البنفسجي الملكي',
                        'emergency-red' => 'الأحمر الطوارئ'
                    ];
                    echo $theme_names[$_SESSION['doctor_theme'] ?? 'medical-blue'] ?? 'الأزرق الطبي';
                    ?>
                </div>

                <!-- On-Call Toggle -->
                <form method="POST" class="oncall-toggle">
                    <span class="status-indicator <?php echo ($user['oncall_status'] ?? 'off') === 'on' ? 'status-online' : 'status-offline'; ?>"></span>
                    <span><?php echo ($user['oncall_status'] ?? 'off') === 'on' ? 'متاح للاستشارة' : 'غير متاح'; ?></span>
                    <div class="toggle-switch <?php echo ($user['oncall_status'] ?? 'off') === 'on' ? 'active' : ''; ?>"
                         onclick="toggleOnCall()">
                        <div class="toggle-slider"></div>
                    </div>
                    <input type="hidden" name="oncall_status" value="<?php echo $user['oncall_status'] ?? 'off'; ?>">
                    <input type="hidden" name="toggle_oncall" value="1">
                </form>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['today_appointments'] ?? 0; ?></div>
                    <div class="stat-label">مواعيد اليوم</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_patients'] ?? 0; ?></div>
                    <div class="stat-label">إجمالي المرضى</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">4.8</div>
                    <div class="stat-label">التقييم العام</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['upcoming_appointments'] ?? 0; ?></div>
                    <div class="stat-label">المواعيد القادمة</div>
                </div>
            </div>

            <!-- Patient Queue -->
            <div class="patient-queue">
                <h3 class="panel-header">طابور المرضى</h3>
                <?php if (!empty($upcoming_appointments)): ?>
                    <?php foreach (array_slice($upcoming_appointments, 0, 5) as $index => $appointment): ?>
                        <div class="queue-item priority-<?php echo $appointment['priority'] ?? 'medium'; ?>">
                            <div class="queue-number"><?php echo $index + 1; ?></div>
                            <div class="queue-info">
                                <div class="queue-patient"><?php echo htmlspecialchars($appointment['patient_name'] ?? 'مريض'); ?></div>
                                <div class="queue-time"><?php echo date('H:i', strtotime($appointment['appointment_time'] ?? 'now')); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-muted); padding: 20px;">لا توجد مواعيد قادمة</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content - Appointment Calendar -->
        <div class="main-content">
            <div class="calendar-header">
                <h2 class="calendar-title">جدول المواعيد</h2>
                <div class="calendar-nav">
                    <button onclick="previousDay()"><i class="fas fa-chevron-right"></i></button>
                    <button onclick="nextDay()"><i class="fas fa-chevron-left"></i></button>
                    <button onclick="today()">اليوم</button>
                    <a href="color-customizer.php" class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-palette mr-2"></i>تخصيص الألوان
                    </a>
                </div>
            </div>

            <!-- Appointment Grid -->
            <div class="appointment-grid">
                <?php if (!empty($upcoming_appointments)): ?>
                    <?php foreach ($upcoming_appointments as $appointment): ?>
                        <div class="appointment-card <?php echo $appointment['priority'] ?? 'regular'; ?>">
                            <div class="appointment-time">
                                <?php echo date('H:i', strtotime($appointment['appointment_time'] ?? 'now')); ?>
                            </div>
                            <div class="patient-name"><?php echo htmlspecialchars($appointment['patient_name'] ?? 'مريض'); ?></div>
                            <div class="appointment-type"><?php echo htmlspecialchars($appointment['appointment_type'] ?? 'استشارة عامة'); ?></div>
                            <div class="appointment-actions">
                                <button class="btn-action btn-start" onclick="startAppointment(<?php echo $appointment['id'] ?? 0; ?>)">
                                    <i class="fas fa-play"></i> بدء الموعد
                                </button>
                                <button class="btn-action btn-reschedule" onclick="rescheduleAppointment(<?php echo $appointment['id'] ?? 0; ?>)">
                                    <i class="fas fa-clock"></i> إعادة جدولة
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                        <h3>لا توجد مواعيد اليوم</h3>
                        <p>يمكنك إضافة مواعيد جديدة أو مراجعة جدولك</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Charts Section -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--text-primary); margin-bottom: 20px;">إحصائيات الأسبوع</h3>
                <div style="background: var(--bg-primary); border-radius: 15px; padding: 20px;">
                    <canvas id="weeklyChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Panel - Notifications & Alerts -->
        <div class="notifications-panel">
            <h3 class="panel-header">التنبيهات والإشعارات</h3>

            <!-- Emergency Alert -->
            <div class="emergency-alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>تنبيه طبي عاجل</strong><br>
                مريض في حالة طوارئ - غرفة 302
            </div>

            <!-- Notifications -->
            <div class="notification-item">
                <div class="notification-time">منذ 5 دقائق</div>
                <div class="notification-text">تم إلغاء موعد الساعة 14:00</div>
            </div>

            <div class="notification-item">
                <div class="notification-time">منذ 15 دقيقة</div>
                <div class="notification-text">مريض جديد ينتظر في الطابور</div>
            </div>

            <div class="notification-item">
                <div class="notification-time">منذ ساعة</div>
                <div class="notification-text">تم تحديث السجل الطبي للمريض أحمد محمد</div>
            </div>

            <!-- Quick Actions -->
            <div style="margin-top: 30px;">
                <h4 style="font-size: 1.1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 15px;">إجراءات سريعة</h4>
                <div style="display: grid; gap: 10px;">
                    <button class="btn-action btn-start" style="width: 100%;">
                        <i class="fas fa-plus"></i> إضافة موعد جديد
                    </button>
                    <button class="btn-action btn-reschedule" style="width: 100%;">
                        <i class="fas fa-file-medical"></i> عرض السجلات الطبية
                    </button>
                    <button class="btn-action" style="width: 100%; background: var(--primary-color); color: white;">
                        <i class="fas fa-cog"></i> إعدادات الحساب
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// On-Call Toggle Function
function toggleOnCall() {
    const form = document.querySelector('.oncall-toggle form');
    const toggleSwitch = document.querySelector('.toggle-switch');
    const statusInput = document.querySelector('input[name="oncall_status"]');
    const statusIndicator = document.querySelector('.status-indicator');

    // Toggle the switch visually
    toggleSwitch.classList.toggle('active');

    // Update the hidden input
    statusInput.value = statusInput.value === 'on' ? 'off' : 'on';

    // Update status indicator
    if (statusInput.value === 'on') {
        statusIndicator.className = 'status-indicator status-online';
    } else {
        statusIndicator.className = 'status-indicator status-offline';
    }

    // Submit the form
    form.submit();
}

// Calendar Navigation Functions
function previousDay() {
    // Implementation for previous day
    console.log('Previous day');
}

function nextDay() {
    // Implementation for next day
    console.log('Next day');
}

function today() {
    // Implementation for today
    console.log('Today');
}

// Appointment Functions
function startAppointment(appointmentId) {
    if (confirm('هل تريد بدء هذا الموعد؟')) {
        // Implementation for starting appointment
        console.log('Starting appointment:', appointmentId);
    }
}

function rescheduleAppointment(appointmentId) {
    // Implementation for rescheduling appointment
    console.log('Rescheduling appointment:', appointmentId);
}

// Weekly Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    const weeklyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
            datasets: [{
                label: 'عدد المواعيد',
                data: [12, 19, 15, 25, 22, 18, 14],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});

// Auto-refresh notifications every 30 seconds
setInterval(function() {
    // Implementation for refreshing notifications
    console.log('Refreshing notifications...');
}, 30000);

// Emergency alert sound (optional)
function playEmergencySound() {
    // Implementation for emergency sound
    console.log('Playing emergency sound');
}
</script>

<?php
require_once '../includes/dashboard_footer.php';
?>
