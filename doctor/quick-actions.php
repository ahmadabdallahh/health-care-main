<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Ensure user is logged in as a doctor
if (!is_logged_in() || $_SESSION['user_type'] !== 'doctor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$doctor_id = $_SESSION['user_id'];
$user = get_logged_in_user();
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_appointment':
                $patient_id = clean_input($_POST['patient_id']);
                $appointment_time = clean_input($_POST['appointment_time']);
                $appointment_type = clean_input($_POST['appointment_type']);
                $notes = clean_input($_POST['notes'] ?? '');

                                if (empty($patient_id) || empty($appointment_time)) {
                    $error_message = 'يرجى ملء جميع الحقول المطلوبة';
                } else {
                    try {
                        // Check if required columns exist
                        $stmt = $conn->query("SHOW COLUMNS FROM appointments LIKE 'appointment_type'");
                        $appointment_type_exists = $stmt->fetch(PDO::FETCH_ASSOC);

                        $stmt = $conn->query("SHOW COLUMNS FROM appointments LIKE 'notes'");
                        $notes_exists = $stmt->fetch(PDO::FETCH_ASSOC);

                                                 if (!$appointment_type_exists || !$notes_exists) {
                             $error_message = 'يرجى تشغيل fix_appointments_table.php أولاً لإصلاح هيكل قاعدة البيانات';
                         } else {
                             // Get default clinic ID
                             $stmt = $conn->query("SELECT id FROM clinics LIMIT 1");
                             $default_clinic = $stmt->fetch(PDO::FETCH_ASSOC);
                             $clinic_id = $default_clinic ? $default_clinic['id'] : 1;

                             // Get the doctor ID from doctors table
                             $stmt = $conn->prepare("SELECT id FROM doctors WHERE email = ?");
                             $stmt->execute([$user['email']]);
                             $doctor_record = $stmt->fetch(PDO::FETCH_ASSOC);

                             if ($doctor_record) {
                                 $actual_doctor_id = $doctor_record['id'];
                             } else {
                                 // If doctor not found in doctors table, create one
                                 $stmt = $conn->prepare("
                                     INSERT INTO doctors (id, name, email, phone, specialty)
                                     VALUES (?, ?, ?, ?, ?)
                                 ");
                                 $stmt->execute([
                                     $doctor_id,
                                     $user['full_name'],
                                     $user['email'],
                                     $user['phone'] ?? '',
                                     $user['specialty'] ?? 'طب عام'
                                 ]);
                                 $actual_doctor_id = $doctor_id;
                             }

                             $stmt = $conn->prepare("
                                 INSERT INTO appointments (user_id, doctor_id, clinic_id, patient_id, appointment_time, appointment_type, notes, status, created_at)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed', NOW())
                             ");
                             $stmt->execute([$doctor_id, $actual_doctor_id, $clinic_id, $patient_id, $appointment_time, $appointment_type, $notes]);

                            $success_message = 'تم إضافة الموعد بنجاح';
                        }
                                         } catch (Exception $e) {
                         $error_message = 'حدث خطأ أثناء إضافة الموعد: ' . $e->getMessage();
                         // Check if it's a constraint error
                         if (strpos($e->getMessage(), 'clinic_id') !== false || strpos($e->getMessage(), 'clinics') !== false) {
                             $error_message .= '<br><br>يرجى تشغيل fix_appointments_constraints.php أولاً لإصلاح مشكلة العيادة';
                         } elseif (strpos($e->getMessage(), 'user_id') !== false) {
                             $error_message .= '<br><br>يرجى تشغيل fix_appointments_constraints.php أولاً لإصلاح مشكلة المستخدم';
                         } elseif (strpos($e->getMessage(), 'doctor_id') !== false || strpos($e->getMessage(), 'doctors') !== false) {
                             $error_message .= '<br><br>يرجى تشغيل fix_all_constraints.php أولاً لإصلاح مشكلة الأطباء';
                         } elseif (strpos($e->getMessage(), 'clinic_id') !== false || strpos($e->getMessage(), 'clinics') !== false) {
                             $error_message .= '<br><br>يرجى تشغيل fix_all_constraints.php أولاً لإصلاح مشكلة العيادة';
                         }
                     }
                }
                break;

            case 'update_settings':
                $full_name = clean_input($_POST['full_name']);
                $email = clean_input($_POST['email']);
                $phone = clean_input($_POST['phone']);
                $specialty = clean_input($_POST['specialty']);

                if (empty($full_name) || empty($email)) {
                    $error_message = 'يرجى ملء الحقول المطلوبة';
                } else {
                    try {
                        // Check if updated_at column exists
                        $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'updated_at'");
                        $updated_at_exists = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($updated_at_exists) {
                            $stmt = $conn->prepare("
                                UPDATE users SET
                                full_name = ?,
                                email = ?,
                                phone = ?,
                                specialty = ?,
                                updated_at = NOW()
                                WHERE id = ?
                            ");
                            $stmt->execute([$full_name, $email, $phone, $specialty, $doctor_id]);
                        } else {
                            $stmt = $conn->prepare("
                                UPDATE users SET
                                full_name = ?,
                                email = ?,
                                phone = ?,
                                specialty = ?
                                WHERE id = ?
                            ");
                            $stmt->execute([$full_name, $email, $phone, $specialty, $doctor_id]);
                        }

                        $success_message = 'تم تحديث الإعدادات بنجاح';
                        $user = get_logged_in_user(); // Refresh user data
                                         } catch (Exception $e) {
                                                   $error_message = 'حدث خطأ أثناء تحديث الإعدادات: ' . $e->getMessage();
                          // Check if it's a specialty column error
                          if (strpos($e->getMessage(), 'specialty') !== false) {
                              $error_message .= '<br><br>يرجى تشغيل fix_all_constraints.php أولاً لإصلاح هيكل قاعدة البيانات';
                          } elseif (strpos($e->getMessage(), 'updated_at') !== false) {
                              $error_message .= '<br><br>يرجى تشغيل fix_all_constraints.php أولاً لإصلاح هيكل قاعدة البيانات';
                          }
                     }
                }
                break;
        }
    }
}

// Get patients for appointment form
$patients = [];
try {
    $stmt = $conn->prepare("SELECT id, full_name, email FROM users WHERE user_type = 'patient' ORDER BY full_name");
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = 'خطأ في تحميل قائمة المرضى: ' . $e->getMessage();
}

// Get medical records
$medical_records = [];
try {
    // First check if the required columns exist
    $stmt = $conn->query("SHOW COLUMNS FROM appointments LIKE 'appointment_type'");
    $appointment_type_exists = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SHOW COLUMNS FROM appointments LIKE 'notes'");
    $notes_exists = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment_type_exists || !$notes_exists) {
        $error_message = 'يرجى تشغيل fix_appointments_table.php أولاً لإصلاح هيكل قاعدة البيانات';
    } else {
        $stmt = $conn->prepare("
            SELECT
                a.id,
                a.appointment_time,
                COALESCE(a.appointment_type, 'استشارة عامة') as appointment_type,
                a.status,
                COALESCE(a.notes, '') as notes,
                u.full_name as patient_name,
                u.email as patient_email
            FROM appointments a
            LEFT JOIN users u ON a.patient_id = u.id
            WHERE a.doctor_id = ?
            ORDER BY a.appointment_time DESC
            LIMIT 20
        ");
        $stmt->execute([$doctor_id]);
        $medical_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $error_message = 'خطأ في تحميل السجلات الطبية: ' . $e->getMessage();
}

$pageTitle = 'الإجراءات السريعة';
require_once '../includes/dashboard_header.php';
?>

<!-- Include the doctor theme CSS -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/doctor-theme.php">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.quick-actions-page {
    min-height: 100vh;
    background: var(--gradient-primary);
    padding: 20px;
}

.actions-container {
    max-width: 1200px;
    margin: 0 auto;
}

.action-section {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    border-radius: 25px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: var(--glass-shadow);
}

.section-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--border-light);
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 10px;
}

.section-subtitle {
    color: var(--text-secondary);
    font-size: 1.1rem;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.action-card {
    background: var(--bg-primary);
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    border: 2px solid var(--border-light);
    transition: all 0.3s ease;
    cursor: pointer;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px var(--shadow-primary);
    border-color: var(--primary-color);
}

.action-icon {
    font-size: 3rem;
    margin-bottom: 15px;
    color: var(--primary-color);
}

.action-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 10px;
}

.action-description {
    color: var(--text-secondary);
    font-size: 0.95rem;
    line-height: 1.5;
}

.form-container {
    background: var(--bg-primary);
    border-radius: 15px;
    padding: 30px;
    margin-top: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

.btn-submit {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px var(--shadow-primary);
}

.records-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.records-table th,
.records-table td {
    padding: 12px;
    text-align: right;
    border-bottom: 1px solid var(--border-light);
}

.records-table th {
    background: var(--bg-secondary);
    color: var(--text-primary);
    font-weight: 600;
}

.records-table tr:hover {
    background: var(--bg-secondary);
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-confirmed {
    background: var(--status-completed-bg);
    color: var(--status-completed-text);
}

.status-pending {
    background: var(--status-pending-bg);
    color: var(--status-pending-text);
}

.status-cancelled {
    background: var(--status-cancelled-bg);
    color: var(--status-cancelled-text);
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

@media (max-width: 768px) {
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .records-table {
        font-size: 0.9rem;
    }

    .records-table th,
    .records-table td {
        padding: 8px;
    }
}
</style>

<div class="quick-actions-page">
    <div class="actions-container">
        <!-- Page Header -->
        <div class="action-section">
            <div class="section-header">
                <h1 class="section-title">الإجراءات السريعة</h1>
                <p class="section-subtitle">إدارة المواعيد والسجلات الطبية والإعدادات</p>
            </div>

            <!-- Alerts -->
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                                         <?php if (strpos($error_message, 'fix_appointments_table.php') !== false): ?>
                         <br><br>
                         <a href="../fix_appointments_table.php" class="btn-submit" style="text-decoration: none; display: inline-block; font-size: 0.9rem; padding: 8px 16px;">
                             <i class="fas fa-wrench mr-2"></i>إصلاح قاعدة البيانات
                         </a>
                     <?php endif; ?>
                                           <?php if (strpos($error_message, 'fix_specialty_column.php') !== false): ?>
                          <br><br>
                          <a href="../fix_all_constraints.php" class="btn-submit" style="text-decoration: none; display: inline-block; font-size: 0.9rem; padding: 8px 16px;">
                              <i class="fas fa-wrench mr-2"></i>إصلاح هيكل قاعدة البيانات
                          </a>
                      <?php endif; ?>
                     <?php if (strpos($error_message, 'fix_appointments_constraints.php') !== false): ?>
                         <br><br>
                         <a href="../fix_appointments_constraints.php" class="btn-submit" style="text-decoration: none; display: inline-block; font-size: 0.9rem; padding: 8px 16px;">
                             <i class="fas fa-database mr-2"></i>إصلاح جميع مشاكل قاعدة البيانات
                         </a>
                     <?php endif; ?>
                     <?php if (strpos($error_message, 'fix_all_constraints.php') !== false): ?>
                         <br><br>
                         <a href="../fix_all_constraints.php" class="btn-submit" style="text-decoration: none; display: inline-block; font-size: 0.9rem; padding: 8px 16px;">
                             <i class="fas fa-database mr-2"></i>إصلاح جميع مشاكل قاعدة البيانات
                         </a>
                     <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Quick Actions Grid -->
            <div class="quick-actions-grid">
                <!-- Add Appointment Card -->
                <div class="action-card" onclick="showForm('appointment-form')">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="action-title">إضافة موعد جديد</div>
                    <div class="action-description">إضافة موعد جديد لمريض مع تحديد الوقت والنوع</div>
                </div>

                <!-- View Medical Records Card -->
                <div class="action-card" onclick="showForm('records-section')">
                    <div class="action-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="action-title">عرض السجلات الطبية</div>
                    <div class="action-description">عرض وتصفح جميع السجلات الطبية والمواعيد</div>
                </div>

                <!-- Account Settings Card -->
                <div class="action-card" onclick="showForm('settings-form')">
                    <div class="action-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="action-title">إعدادات الحساب</div>
                    <div class="action-description">تحديث معلومات الحساب الشخصية والتخصص</div>
                </div>
            </div>
        </div>

        <!-- Add Appointment Form -->
        <div id="appointment-form" class="action-section" style="display: none;">
            <h2 class="section-title">إضافة موعد جديد</h2>
            <form method="POST" class="form-container">
                <input type="hidden" name="action" value="add_appointment">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="patient_id">المريض *</label>
                        <select id="patient_id" name="patient_id" required>
                            <option value="">اختر المريض</option>
                            <?php foreach ($patients as $patient): ?>
                                <option value="<?php echo $patient['id']; ?>">
                                    <?php echo htmlspecialchars($patient['full_name']); ?> - <?php echo htmlspecialchars($patient['email']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="appointment_time">وقت الموعد *</label>
                        <input type="datetime-local" id="appointment_time" name="appointment_time" required>
                    </div>

                    <div class="form-group">
                        <label for="appointment_type">نوع الموعد</label>
                        <select id="appointment_type" name="appointment_type">
                            <option value="استشارة عامة">استشارة عامة</option>
                            <option value="فحص دوري">فحص دوري</option>
                            <option value="متابعة">متابعة</option>
                            <option value="استشارة عاجلة">استشارة عاجلة</option>
                            <option value="فحص مختبر">فحص مختبر</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">ملاحظات</label>
                    <textarea id="notes" name="notes" placeholder="أي ملاحظات إضافية حول الموعد..."></textarea>
                </div>

                <div style="text-align: center;">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-plus mr-2"></i>إضافة الموعد
                    </button>
                </div>
            </form>
        </div>

        <!-- Medical Records Section -->
        <div id="records-section" class="action-section" style="display: none;">
            <h2 class="section-title">السجلات الطبية</h2>
            <div class="form-container">
                <?php if (!empty($medical_records)): ?>
                    <table class="records-table">
                        <thead>
                            <tr>
                                <th>المريض</th>
                                <th>الوقت</th>
                                <th>النوع</th>
                                <th>الحالة</th>
                                <th>الملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medical_records as $record): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($record['appointment_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['appointment_type']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $record['status']; ?>">
                                            <?php
                                            $status_names = [
                                                'confirmed' => 'مؤكد',
                                                'pending' => 'في الانتظار',
                                                'cancelled' => 'ملغي',
                                                'completed' => 'مكتمل'
                                            ];
                                            echo $status_names[$record['status']] ?? $record['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($record['notes'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-muted); padding: 40px;">
                        لا توجد سجلات طبية متاحة حالياً
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Account Settings Form -->
        <div id="settings-form" class="action-section" style="display: none;">
            <h2 class="section-title">إعدادات الحساب</h2>
            <form method="POST" class="form-container">
                <input type="hidden" name="action" value="update_settings">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="full_name">الاسم الكامل *</label>
                        <input type="text" id="full_name" name="full_name"
                               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني *</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">رقم الهاتف</label>
                        <input type="tel" id="phone" name="phone"
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="specialty">التخصص</label>
                        <select id="specialty" name="specialty">
                            <option value="طب عام" <?php echo ($user['specialty'] ?? '') === 'طب عام' ? 'selected' : ''; ?>>طب عام</option>
                            <option value="طب القلب" <?php echo ($user['specialty'] ?? '') === 'طب القلب' ? 'selected' : ''; ?>>طب القلب</option>
                            <option value="طب الأطفال" <?php echo ($user['specialty'] ?? '') === 'طب الأطفال' ? 'selected' : ''; ?>>طب الأطفال</option>
                            <option value="طب النساء" <?php echo ($user['specialty'] ?? '') === 'طب النساء' ? 'selected' : ''; ?>>طب النساء</option>
                            <option value="طب العظام" <?php echo ($user['specialty'] ?? '') === 'طب العظام' ? 'selected' : ''; ?>>طب العظام</option>
                            <option value="طب الجلد" <?php echo ($user['specialty'] ?? '') === 'طب الجلد' ? 'selected' : ''; ?>>طب الجلد</option>
                            <option value="طب العيون" <?php echo ($user['specialty'] ?? '') === 'طب العيون' ? 'selected' : ''; ?>>طب العيون</option>
                            <option value="طب الأسنان" <?php echo ($user['specialty'] ?? '') === 'طب الأسنان' ? 'selected' : ''; ?>>طب الأسنان</option>
                        </select>
                    </div>
                </div>

                <div style="text-align: center;">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save mr-2"></i>حفظ الإعدادات
                    </button>
                </div>
            </form>
        </div>

        <!-- Back to Dashboard -->
        <div class="action-section" style="text-align: center;">
            <a href="profile.php" class="btn-submit" style="text-decoration: none; display: inline-block;">
                <i class="fas fa-arrow-right mr-2"></i>العودة للوحة التحكم
            </a>
        </div>
    </div>
</div>

<script>
function showForm(formId) {
    // Hide all forms first
    document.getElementById('appointment-form').style.display = 'none';
    document.getElementById('records-section').style.display = 'none';
    document.getElementById('settings-form').style.display = 'none';

    // Show the selected form
    document.getElementById(formId).style.display = 'block';

    // Scroll to the form
    document.getElementById(formId).scrollIntoView({ behavior: 'smooth' });
}

// Set minimum date for appointment time to today
document.addEventListener('DOMContentLoaded', function() {
    const appointmentTimeInput = document.getElementById('appointment_time');
    if (appointmentTimeInput) {
        const today = new Date().toISOString().slice(0, 16);
        appointmentTimeInput.min = today;
    }
});
</script>

<?php
require_once '../includes/dashboard_footer.php';
?>
