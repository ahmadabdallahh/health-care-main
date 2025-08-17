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
$pageTitle = 'الملف الشخصي';
$page_title = $pageTitle;

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = clean_input($_POST['full_name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $date_of_birth = clean_input($_POST['date_of_birth']);
    $gender = clean_input($_POST['gender']);
    $insurance_provider = clean_input($_POST['insurance_provider'] ?? '');
    $insurance_number = clean_input($_POST['insurance_number'] ?? '');

    // Validate required fields
    if (empty($full_name) || empty($email)) {
        $error_message = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        try {
            // Check if email already exists for another user
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $error_message = 'البريد الإلكتروني مستخدم بالفعل';
            } else {
                // Update user profile - only update fields that exist in the database
                $stmt = $conn->prepare("
                    UPDATE users SET
                    full_name = ?,
                    email = ?,
                    phone = ?,
                    date_of_birth = ?,
                    gender = ?,
                    insurance_provider = ?,
                    insurance_number = ?,
                    updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$full_name, $email, $phone, $date_of_birth, $gender, $insurance_provider, $insurance_number, $user_id]);

                $success_message = 'تم تحديث الملف الشخصي بنجاح';

                // Update session
                $_SESSION['user_name'] = $full_name;

                // Refresh user data
                $user = get_logged_in_user();
            }
        } catch (Exception $e) {
            error_log("Error updating profile: " . $e->getMessage());
            $error_message = 'حدث خطأ أثناء تحديث الملف الشخصي: ' . $e->getMessage();
        }
    }
}

require_once '../includes/dashboard_header.php';
?>

<!-- Include the color scheme CSS -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/color-scheme.php">

<style>
/* Profile Page Styles */
.profile-page {
    /* background: var(--gradient-primary); */
    min-height: 100vh;
    padding: 20px;
}

.profile-container {
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
    background:#f1f1f1 !important;
    color: black !important;
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
    border: 2px solid #dcdcdc;
    border-radius: 20px;
    color: black;
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

.profile-form {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.form-section {
    margin-bottom: 30px;
}

.form-section h3 {
    color: var(--text-primary);
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--border-light);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: var(--text-secondary);
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-light);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--bg-secondary);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    background: var(--bg-primary);
    box-shadow: 0 0 0 3px var(--shadow-primary);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid var(--border-light);
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
    background: var(--gradient-primary);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px var(--shadow-primary);
}

.btn-secondary {
    background: var(--bg-tertiary);
    color: var(--text-secondary);
}

.btn-secondary:hover {
    background: var(--border-medium);
}

.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 600;
}

.alert-success {
    background: var(--status-completed-bg);
    color: var(--status-completed-text);
    border: 1px solid var(--success-color);
}

.alert-error {
    background: var(--status-cancelled-bg);
    color: var(--status-cancelled-text);
    border: 1px solid var(--danger-color);
}

.profile-avatar {
    text-align: center;
    margin-bottom: 30px;
}

.avatar-container {
    position: relative;
    display: inline-block;
}

.avatar-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--primary-color);
    box-shadow: 0 10px 30px var(--shadow-primary);
}

.avatar-upload {
    position: absolute;
    bottom: 0;
    right: 0;
    background: var(--primary-color);
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.avatar-upload:hover {
    background: var(--primary-dark);
    transform: scale(1.1);
}

@media (max-width: 1024px) {
    .profile-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .enhanced-sidebar {
        position: static;
        order: 2;
    }
}

@media (max-width: 768px) {
    .profile-page {
        padding: 10px;
    }

    .main-content {
        padding: 20px;
    }

    .profile-form {
        padding: 20px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>

<div class="profile-page">
    <div class="profile-container">
        <!-- Enhanced Sidebar -->
        <div class="enhanced-sidebar">
            <div class="sidebar-header">
                <h3>شفاء</h3>
                <p style="color: var(--text-muted); margin: 10px 0 0 0; font-size: 0.9rem;">نظام الحجوزات الطبية</p>
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
                    <a href="<?php echo BASE_URL; ?>patient/profile.php" class="nav-link active">
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
            <!-- Page Header -->
            <div class="page-header">
                <h1>الملف الشخصي</h1>
                <p>تعديل معلوماتك الشخصية</p>
            </div>

            <!-- Profile Form -->
            <div class="profile-form">
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

                                 <!-- Profile Avatar -->
                 <div class="profile-avatar">
                     <div class="avatar-container">
                         <img src="<?php echo htmlspecialchars($user['profile_image'] ?? 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiByeD0iNjAiIGZpbGw9IiNFNUU3RUIiLz4KPHN2ZyB4PSIzMCIgeT0iMjAiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjOEE5M0E2Ij4KPHBhdGggZD0iTTEyIDJDNi40OCAyIDIgNi40OCAyIDEyczQuNDggMTAgMTAgMTAgMTAtNC40OCAxMC0xMFMxNy41MiAyIDEyIDJ6bTAgM2MyLjY3IDAgNC44MyAyLjE2IDQuODMgNC44M1MxNC42NyAxNC42NiAxMiAxNC42NiA3LjE3IDEyLjUgNy4xNyA5LjgzIDkuMzMgNS4xNyAxMiA1LjE3em0wIDEyYzQuNDIgMCA4LjE3LTIuMTYgOC4xNy00Ljgzcy0zLjc1LTQuODMtOC4xNy00LjgzLTguMTcgMi4xNi04LjE3IDQuODNTNy41OCAyMC4xNyAxMiAyMC4xN3oiLz4KPC9zdmc+Cjwvc3ZnPgo='); ?>"
                              alt="صورة الملف الشخصي"
                              class="avatar-image"
                              id="profile-image"
                              onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiByeD0iNjAiIGZpbGw9IiNFNUU3RUIiLz4KPHN2ZyB4PSIzMCIgeT0iMjAiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjOEE5M0E2Ij4KPHBhdGggZD0iTTEyIDJDNi40OCAyIDIgNi40OCAyIDEyczQuNDggMTAgMTAgMTAgMTAtNC40OCAxMC0xMFMxNy41MiAyIDEyIDJ6bTAgM2MyLjY3IDAgNC44MyAyLjE2IDQuODMgNC44M1MxNC42NyAxNC42NiAxMiAxNC42NiA3LjE3IDEyLjUgNy4xNyA5LjgzIDkuMzMgNS4xNyAxMiA1LjE3em0wIDEyYzQuNDIgMCA4LjE3LTIuMTYgOC4xNy00Ljgzcy0zLjc1LTQuODMtOC4xNy00LjgzLTguMTcgMi4xNi04LjE3IDQuODNTNy41OCAyMC4xNyAxMiAyMC4xN3oiLz4KPC9zdmc+Cjwvc3ZnPgo=';">
                         <div class="avatar-upload" id="avatar-upload">
                             <i class="fas fa-camera"></i>
                         </div>
                         <input type="file" id="profile-picture-input" accept="image/*" style="display: none;">
                     </div>
                     <p class="text-sm text-gray-600 mt-2">انقر على الكاميرا لتغيير الصورة الشخصية</p>
                 </div>

                <form method="POST" action="">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3>المعلومات الشخصية</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="full_name">الاسم الكامل *</label>
                                <input type="text" id="full_name" name="full_name"
                                       value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="email">البريد الإلكتروني *</label>
                                <input type="email" id="email" name="email"
                                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="phone">رقم الهاتف</label>
                                <input type="tel" id="phone" name="phone"
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="date_of_birth">تاريخ الميلاد</label>
                                <input type="date" id="date_of_birth" name="date_of_birth"
                                       value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="gender">الجنس</label>
                                <select id="gender" name="gender">
                                    <option value="">اختر الجنس</option>
                                    <option value="male" <?php echo ($user['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>ذكر</option>
                                    <option value="female" <?php echo ($user['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>أنثى</option>
                                </select>
                            </div>
                        </div>
                    </div>

                                         <!-- Additional Information -->
                     <div class="form-section">
                         <h3>معلومات إضافية</h3>
                         <div class="form-group">
                             <label for="insurance_provider">شركة التأمين (اختياري)</label>
                             <input type="text" id="insurance_provider" name="insurance_provider"
                                    value="<?php echo htmlspecialchars($user['insurance_provider'] ?? ''); ?>"
                                    placeholder="أدخل اسم شركة التأمين">
                         </div>
                         <div class="form-group">
                             <label for="insurance_number">رقم التأمين (اختياري)</label>
                             <input type="text" id="insurance_number" name="insurance_number"
                                    value="<?php echo htmlspecialchars($user['insurance_number'] ?? ''); ?>"
                                    placeholder="أدخل رقم التأمين">
                         </div>
                     </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ التغييرات
                        </button>
                        <a href="<?php echo BASE_URL; ?>patient/index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avatar upload functionality
    const avatarUpload = document.getElementById('avatar-upload');
    const avatarImage = document.getElementById('profile-image');
    const fileInput = document.getElementById('profile-picture-input');

    if (avatarUpload && fileInput) {
        avatarUpload.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Show loading state
                avatarUpload.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                // Create FormData for upload
                const formData = new FormData();
                formData.append('profile_picture', file);

                                // Upload file to server
                fetch('<?php echo BASE_URL; ?>upload_profile_picture.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update image source
                        avatarImage.src = data.image_url + '?t=' + new Date().getTime();

                        // Show success message
                        showAlert('تم تحديث الصورة الشخصية بنجاح', 'success');
                    } else {
                        showAlert('خطأ في تحديث الصورة: ' + (data.message || 'خطأ غير معروف'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('حدث خطأ أثناء رفع الصورة: ' + error.message, 'error');
                })
                .finally(() => {
                    // Reset upload button
                    avatarUpload.innerHTML = '<i class="fas fa-camera"></i>';
                });
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

            if (!isValid) {
                e.preventDefault();
                showAlert('يرجى ملء جميع الحقول المطلوبة', 'error');
            }
        });
    }

    // Alert function
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'error'}`;
        alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;

        // Insert at the top of the form
        const form = document.querySelector('.profile-form');
        form.insertBefore(alertDiv, form.firstChild);

        // Remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
</script>

<?php
require_once '../includes/dashboard_footer.php';
?>
