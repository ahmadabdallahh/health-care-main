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
$pageTitle = 'الإعدادات';
$page_title = $pageTitle;

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'يرجى ملء جميع الحقول المطلوبة';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'كلمة المرور الجديدة غير متطابقة';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    } else {
        try {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_data = $stmt->fetch();

            if ($user_data && $current_password === $user_data['password']) {
                // Update password
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$new_password, $user_id]);

                $success_message = 'تم تغيير كلمة المرور بنجاح';
            } else {
                $error_message = 'كلمة المرور الحالية غير صحيحة';
            }
        } catch (Exception $e) {
            error_log("Error changing password: " . $e->getMessage());
            $error_message = 'حدث خطأ أثناء تغيير كلمة المرور';
        }
    }
}

require_once '../includes/dashboard_header.php';
?>

<!-- Include the color scheme CSS -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/color-scheme.php">

<style>
/* Settings Page Styles */
.settings-page {
    /* background: var(--gradient-primary); */
    min-height: 100vh;
    padding: 20px;
}

.settings-container {
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
    /* background: var(--gradient-primary); */
    background: #dcdcdc;
    color: black;
    transform: translateX(-5px);
    box-shadow: 0 10px 20px var(--shadow-primary);
}

.nav-link.active {
    /* background: var(--gradient-primary); */
    background: #dcdcdc;
    color: black;
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
    border-radius: 20px;
    color: black !important;
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

.settings-grid {
    display: grid;
    gap: 30px;
}

.setting-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.setting-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.setting-header {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f7fafc;
}

.setting-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 15px;
    font-size: 20px;
    color: black !important;
    background: #dcdcdc !important;
    box-shadow: 0 10px 20px var(--shadow-primary);
}

.setting-title h3 {
    color: #2d3748;
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0 0 5px 0;
}

.setting-title p {
    color: #718096;
    margin: 0;
    font-size: 0.9rem;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f7fafc;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 2px solid #f7fafc;
}

.btn {
    padding: 12px 30px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #dcdcdc !important;
    color: black !important;
    box-shadow: 0 10px 20px var(--shadow-primary);
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

.btn-danger {
    background: #fed7d7;
    color: #742a2a;
}

.btn-danger:hover {
    background: #feb2b2;
}

.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 600;
}

.alert-success {
    background: #c6f6d5;
    color: #22543d;
    border: 1px solid #9ae6b4;
}

.alert-error {
    background: #fed7d7;
    color: #742a2a;
    border: 1px solid #feb2b2;
}

.notification-settings {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.notification-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    background: #f7fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.notification-info h4 {
    color: #2d3748;
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 5px 0;
}

.notification-info p {
    color: #718096;
    margin: 0;
    font-size: 0.85rem;
}

.toggle-switch {
    position: relative;
    width: 50px;
    height: 24px;
    background: #cbd5e0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.toggle-switch.active {
    background: #48bb78;
}

.toggle-switch::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.toggle-switch.active::after {
    transform: translateX(26px);
}

@media (max-width: 1024px) {
    .settings-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .enhanced-sidebar {
        position: static;
        order: 2;
    }
}

@media (max-width: 768px) {
    .settings-page {
        padding: 10px;
    }

    .main-content {
        padding: 20px;
    }

    .form-actions {
        flex-direction: column;
    }

    .notification-settings {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="settings-page">
    <div class="settings-container">
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
                    <a href="<?php echo BASE_URL; ?>patient/settings.php" class="nav-link active">
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
                <h1>الإعدادات</h1>
                <p>إدارة إعدادات حسابك وتفضيلاتك</p>
            </div>

            <!-- Settings Grid -->
            <div class="settings-grid">
                <!-- Password Change -->
                <div class="setting-card">
                    <div class="setting-header">
                        <div class="setting-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="setting-title">
                            <h3>تغيير كلمة المرور</h3>
                            <p>تحديث كلمة المرور لحماية حسابك</p>
                        </div>
                    </div>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="current_password">كلمة المرور الحالية</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">كلمة المرور الجديدة</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">تأكيد كلمة المرور الجديدة</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="change_password" class="btn btn-primary">
                                <i class="fas fa-save"></i> تغيير كلمة المرور
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Notification Settings -->
                <div class="setting-card">
                    <div class="setting-header">
                        <div class="setting-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="setting-title">
                            <h3>إعدادات الإشعارات</h3>
                            <p>تخصيص الإشعارات التي تريد استلامها</p>
                        </div>
                    </div>

                    <div class="notification-settings">
                        <div class="notification-item">
                            <div class="notification-info">
                                <h4>تذكير المواعيد</h4>
                                <p>استلام تذكيرات قبل المواعيد</p>
                            </div>
                            <div class="toggle-switch active" data-setting="appointment_reminders"></div>
                        </div>

                        <div class="notification-item">
                            <div class="notification-info">
                                <h4>تحديثات الحالة</h4>
                                <p>إشعارات عند تغيير حالة المواعيد</p>
                            </div>
                            <div class="toggle-switch active" data-setting="status_updates"></div>
                        </div>

                        <div class="notification-item">
                            <div class="notification-info">
                                <h4>رسائل الأطباء</h4>
                                <p>إشعارات عند إرسال رسائل من الأطباء</p>
                            </div>
                            <div class="toggle-switch" data-setting="doctor_messages"></div>
                        </div>

                        <div class="notification-item">
                            <div class="notification-info">
                                <h4>العروض والخصومات</h4>
                                <p>إشعارات حول العروض الخاصة</p>
                            </div>
                            <div class="toggle-switch" data-setting="offers"></div>
                        </div>
                    </div>
                </div>

                <!-- Privacy Settings -->
                <div class="setting-card">
                    <div class="setting-header">
                        <div class="setting-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="setting-title">
                            <h3>إعدادات الخصوصية</h3>
                            <p>التحكم في خصوصية بياناتك</p>
                        </div>
                    </div>

                    <div class="notification-settings">
                        <div class="notification-item">
                            <div class="notification-info">
                                <h4>مشاركة البيانات الطبية</h4>
                                <p>السماح للأطباء بمشاركة بياناتك مع أطباء آخرين</p>
                            </div>
                            <div class="toggle-switch active" data-setting="share_medical_data"></div>
                        </div>

                        <div class="notification-item">
                            <div class="notification-info">
                                <h4>التحليلات والبحوث</h4>
                                <p>المساهمة في البحوث الطبية (بيانات مجهولة)</p>
                            </div>
                            <div class="toggle-switch" data-setting="research_analytics"></div>
                        </div>
                    </div>
                </div>

                <!-- Account Actions -->
                <div class="setting-card">
                    <div class="setting-header">
                        <div class="setting-icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div class="setting-title">
                            <h3>إجراءات الحساب</h3>
                            <p>إدارة حسابك وحذفه</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="<?php echo BASE_URL; ?>patient/profile.php" class="btn btn-primary">
                            <i class="fas fa-user-edit"></i> تعديل الملف الشخصي
                        </a>
                        <a href="#" class="btn btn-secondary">
                            <i class="fas fa-download"></i> تصدير بياناتي
                        </a>
                        <a href="#" class="btn btn-danger">
                            <i class="fas fa-trash"></i> حذف الحساب
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle switch functionality
    const toggleSwitches = document.querySelectorAll('.toggle-switch');
    toggleSwitches.forEach(toggle => {
        toggle.addEventListener('click', function() {
            this.classList.toggle('active');
            const setting = this.dataset.setting;

            // Here you would typically save the setting to the database
            console.log(`Setting ${setting} changed to: ${this.classList.contains('active')}`);

            // Show a temporary success message
            const notification = this.closest('.notification-item');
            const info = notification.querySelector('.notification-info h4');
            const originalText = info.textContent;
            info.textContent = 'تم حفظ الإعداد';
            info.style.color = '#48bb78';

            setTimeout(() => {
                info.textContent = originalText;
                info.style.color = '#2d3748';
            }, 2000);
        });
    });

    // Password validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');

    if (newPassword && confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            if (newPassword.value !== this.value) {
                this.style.borderColor = '#e53e3e';
            } else {
                this.style.borderColor = '#48bb78';
            }
        });
    }

    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#e53e3e';
                    isValid = false;
                } else {
                    field.style.borderColor = '#e2e8f0';
                }
            });

            if (newPassword && confirmPassword && newPassword.value !== confirmPassword.value) {
                confirmPassword.style.borderColor = '#e53e3e';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                alert('يرجى التأكد من صحة جميع البيانات');
            }
        });
    }

    // Action button handlers
    const actionButtons = document.querySelectorAll('.btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.href === '#') {
                e.preventDefault();
                if (this.classList.contains('btn-danger')) {
                    if (confirm('هل أنت متأكد من حذف حسابك؟ هذا الإجراء لا يمكن التراجع عنه.')) {
                        alert('سيتم تنفيذ حذف الحساب قريباً');
                    }
                } else {
                    alert('هذه الميزة ستكون متاحة قريباً');
                }
            }
        });
    });
});
</script>

<?php
require_once '../includes/dashboard_footer.php';
?>
