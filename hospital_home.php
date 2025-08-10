<?php
require_once 'includes/functions.php';
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}
$user = get_logged_in_user();

// التحقق من أن المستخدم مستشفى
if ($user['role'] !== 'hospital') {
    header("Location: patient_home.php");
    exit();
}

// جلب بيانات المستشفى
$hospital_id = $user['id'];
$hospital_data = get_hospital_data($hospital_id);

// جلب إحصائيات المستشفى
$stats = get_hospital_stats($hospital_id);

// جلب الأطباء في المستشفى
$doctors = get_hospital_doctors($hospital_id);

// جلب الأقسام
$departments = get_hospital_departments($hospital_id);

// جلب المواعيد اليوم
$today_appointments = get_hospital_today_appointments($hospital_id);

// جلب التقييمات الأخيرة
$recent_reviews = get_hospital_reviews($hospital_id, 5);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المستشفى | صحة</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); }
        .hero-section {
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            color: #fff;
            padding: 2rem 0;
            text-align: center;
        }
        .hero-section h1 { font-size: 2.5rem; margin-bottom: 0.5rem; }
        .hero-section p { font-size: 1.1rem; opacity: 0.9; }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .dashboard-card {
            background: #fff;
            border-radius: 1.2rem;
            box-shadow: 0 4px 20px rgba(22,163,74,0.1);
            padding: 1.5rem;
            border: 1px solid #dcfce7;
        }
        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0fdf4;
        }
        .card-header i {
            font-size: 1.8rem;
            color: #16a34a;
            background: #f0fdf4;
            padding: 0.8rem;
            border-radius: 50%;
        }
        .card-header h3 {
            margin: 0;
            color: #14532d;
            font-size: 1.3rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .stat-item {
            text-align: center;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.8rem;
            border: 1px solid #e0f2fe;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 0.3rem;
        }
        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
        }
        .doctor-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.8rem;
            margin-bottom: 1rem;
            border-right: 4px solid #16a34a;
        }
        .doctor-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #16a34a;
        }
        .doctor-info {
            flex: 1;
        }
        .doctor-info h4 {
            margin: 0 0 0.3rem 0;
            color: #14532d;
        }
        .doctor-info p {
            margin: 0;
            color: #64748b;
            font-size: 0.9rem;
        }
        .department-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #e0f2fe;
        }
        .department-info h4 {
            margin: 0 0 0.3rem 0;
            color: #14532d;
        }
        .department-info p {
            margin: 0;
            color: #64748b;
            font-size: 0.9rem;
        }
        .department-stats {
            text-align: center;
        }
        .department-count {
            font-size: 1.5rem;
            font-weight: bold;
            color: #16a34a;
        }
        .appointment-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.8rem;
            margin-bottom: 1rem;
            border-left: 4px solid #16a34a;
        }
        .appointment-time {
            background: #16a34a;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: bold;
            min-width: 100px;
            text-align: center;
        }
        .appointment-info {
            flex: 1;
        }
        .appointment-info h4 {
            margin: 0 0 0.3rem 0;
            color: #14532d;
        }
        .appointment-info p {
            margin: 0;
            color: #64748b;
            font-size: 0.9rem;
        }
        .review-item {
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.8rem;
            margin-bottom: 1rem;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .review-stars {
            color: #fbbf24;
        }
        .review-date {
            color: #64748b;
            font-size: 0.9rem;
        }
        .review-text {
            color: #374151;
            line-height: 1.5;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.2rem;
            background: #16a34a;
            color: #fff;
            text-decoration: none;
            border-radius: 0.7rem;
            font-weight: bold;
            transition: background 0.2s;
        }
        .action-btn:hover {
            background: #15803d;
            color: #fff;
        }
        .action-btn.secondary {
            background: #f1f5f9;
            color: #16a34a;
            border: 2px solid #16a34a;
        }
        .action-btn.secondary:hover {
            background: #f0fdf4;
        }
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-active {
            background: #dcfce7;
            color: #16a34a;
        }
        .status-inactive {
            background: #fef2f2;
            color: #dc2626;
        }
        @media (max-width: 768px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="hero-section">
    <h1>مرحباً <?php echo htmlspecialchars($hospital_data['name'] ?? $user['full_name']); ?></h1>
    <p>لوحة تحكم المستشفى - إدارة الأطباء والمواعيد والأقسام</p>
</div>

<div class="dashboard-grid">
    <!-- إحصائيات المستشفى -->
    <div class="dashboard-card">
        <div class="card-header">
            <i class="fas fa-chart-line"></i>
            <h3>إحصائيات المستشفى</h3>
        </div>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['total_doctors'] ?? 0; ?></div>
                <div class="stat-label">إجمالي الأطباء</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['total_departments'] ?? 0; ?></div>
                <div class="stat-label">الأقسام</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['today_appointments'] ?? 0; ?></div>
                <div class="stat-label">مواعيد اليوم</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo number_format($stats['avg_rating'] ?? 0, 1); ?></div>
                <div class="stat-label">متوسط التقييم</div>
            </div>
        </div>
    </div>

    <!-- الأطباء في المستشفى -->
    <div class="dashboard-card">
        <div class="card-header">
            <i class="fas fa-user-md"></i>
            <h3>الأطباء في المستشفى</h3>
        </div>
        <?php if (empty($doctors)): ?>
            <p style="text-align:center; color:#64748b;">لا يوجد أطباء مسجلين</p>
        <?php else: ?>
            <?php foreach (array_slice($doctors, 0, 5) as $doctor): ?>
                <div class="doctor-item">
                    <img src="assets/images/doctor.png" class="doctor-img" alt="صورة الطبيب">
                    <div class="doctor-info">
                        <h4><?php echo htmlspecialchars($doctor['full_name']); ?></h4>
                        <p><?php echo htmlspecialchars($doctor['specialty_name'] ?? ''); ?></p>
                    </div>
                    <span class="status-badge <?php echo $doctor['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo $doctor['is_active'] ? 'نشط' : 'غير نشط'; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="quick-actions">
            <a href="hospital-doctors.php" class="action-btn">
                <i class="fas fa-users"></i>
                إدارة الأطباء
            </a>
            <a href="add-doctor.php" class="action-btn secondary">
                <i class="fas fa-plus"></i>
                إضافة طبيب
            </a>
        </div>
    </div>

    <!-- الأقسام -->
    <div class="dashboard-card">
        <div class="card-header">
            <i class="fas fa-hospital"></i>
            <h3>أقسام المستشفى</h3>
        </div>
        <?php if (empty($departments)): ?>
            <p style="text-align:center; color:#64748b;">لا توجد أقسام مسجلة</p>
        <?php else: ?>
            <?php foreach ($departments as $dept): ?>
                <div class="department-item">
                    <div class="department-info">
                        <h4><?php echo htmlspecialchars($dept['name']); ?></h4>
                        <p><?php echo htmlspecialchars($dept['description'] ?? ''); ?></p>
                    </div>
                    <div class="department-stats">
                        <div class="department-count"><?php echo $dept['doctor_count'] ?? 0; ?></div>
                        <div style="font-size:0.8rem; color:#64748b;">طبيب</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="quick-actions">
            <a href="hospital-departments.php" class="action-btn">
                <i class="fas fa-building"></i>
                إدارة الأقسام
            </a>
        </div>
    </div>

    <!-- مواعيد اليوم -->
    <div class="dashboard-card">
        <div class="card-header">
            <i class="fas fa-calendar-day"></i>
            <h3>مواعيد اليوم</h3>
        </div>
        <?php if (empty($today_appointments)): ?>
            <p style="text-align:center; color:#64748b;">لا توجد مواعيد اليوم</p>
        <?php else: ?>
            <?php foreach (array_slice($today_appointments, 0, 5) as $appointment): ?>
                <div class="appointment-item">
                    <div class="appointment-time">
                        <?php echo date('H:i', strtotime($appointment['appointment_time'])); ?>
                    </div>
                    <div class="appointment-info">
                        <h4><?php echo htmlspecialchars($appointment['patient_name']); ?></h4>
                        <p>د. <?php echo htmlspecialchars($appointment['doctor_name']); ?> - <?php echo htmlspecialchars($appointment['specialty_name'] ?? ''); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="quick-actions">
            <a href="hospital-appointments.php" class="action-btn">
                <i class="fas fa-calendar-alt"></i>
                عرض جميع المواعيد
            </a>
        </div>
    </div>

    <!-- التقييمات الأخيرة -->
    <div class="dashboard-card">
        <div class="card-header">
            <i class="fas fa-star"></i>
            <h3>التقييمات الأخيرة</h3>
        </div>
        <?php if (empty($recent_reviews)): ?>
            <p style="text-align:center; color:#64748b;">لا توجد تقييمات بعد</p>
        <?php else: ?>
            <?php foreach ($recent_reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="review-stars">
                            <?php
                            $rating = $review['rating'];
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <div class="review-date">
                            <?php echo date('Y-m-d', strtotime($review['created_at'])); ?>
                        </div>
                    </div>
                    <div class="review-text">
                        <?php echo htmlspecialchars($review['comment']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- إجراءات سريعة -->
    <div class="dashboard-card">
        <div class="card-header">
            <i class="fas fa-tools"></i>
            <h3>إجراءات سريعة</h3>
        </div>
        <div class="quick-actions">
            <a href="hospital-profile.php" class="action-btn">
                <i class="fas fa-hospital-user"></i>
                إعدادات المستشفى
            </a>
            <a href="hospital-reports.php" class="action-btn">
                <i class="fas fa-chart-bar"></i>
                التقارير والإحصائيات
            </a>
            <a href="hospital-schedule.php" class="action-btn">
                <i class="fas fa-clock"></i>
                إدارة الجداول
            </a>
            <a href="hospital-notifications.php" class="action-btn secondary">
                <i class="fas fa-bell"></i>
                الإشعارات
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/script.js"></script>
</body>
</html>
