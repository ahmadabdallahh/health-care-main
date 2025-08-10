<?php
require_once 'includes/functions.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user = get_logged_in_user();

// التحقق من أن المستخدم موجود
if (!$user) {
    // إذا لم يتم العثور على المستخدم، إعادة توجيه إلى صفحة تسجيل الدخول
    session_destroy();
    header("Location: login.php");
    exit();
}

$appointments = get_user_appointments($user['id']);

// التأكد من أن المصفوفة صحيحة
if (!is_array($appointments)) {
    $appointments = [];
}

$upcoming_appointments = array_filter($appointments, function($app) {
    return $app['status'] == 'confirmed' && $app['appointment_date'] >= date('Y-m-d');
});
$past_appointments = array_filter($appointments, function($app) {
    return $app['appointment_date'] < date('Y-m-d');
});
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - موقع حجز المواعيد الطبية</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard {
            padding-top: 80px;
            min-height: 100vh;
            background: var(--bg-secondary);
        }

        .dashboard-header {
            background: white;
            padding: 2rem 0;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
        }

        .sidebar {
            background: white;
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
            height: fit-content;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: var(--radius);
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--primary-color);
            color: pink;
        }

        .main-content {
            background: white;
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }

        .appointments-section {
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .appointments-grid {
            display: grid;
            gap: 1rem;
        }

        .appointment-card {
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .appointment-card:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .appointment-date {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-weight: 500;
        }

        .appointment-status {
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-completed {
            background: #dbeafe;
            color: #1e40af;
        }

        .appointment-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-item i {
            color: var(--primary-color);
            width: 20px;
        }

        .appointment-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: var(--radius-lg);
            margin-bottom: 2rem;
            text-align: center;
        }

        .welcome-section h1 {
            margin-bottom: 0.5rem;
        }

        .welcome-section p {
            opacity: 0.9;
            margin: 0;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            text-align: center;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .quick-action:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .quick-action i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .sidebar {
                order: 2;
            }

            .main-content {
                order: 1;
            }

            .appointment-details {
                grid-template-columns: 1fr;
            }

            .appointment-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <i class="fas fa-heartbeat"></i>
                    <span>صحة</span>
                </div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a href="search.php" class="nav-link">البحث عن طبيب</a>
                    </li>
                    <li class="nav-item">
                        <a href="appointments.php" class="nav-link">مواعيدي</a>
                    </li>
                </ul>
                <div class="nav-auth">
                    <span class="user-name">مرحباً، <?php echo $user['full_name']; ?></span>
                    <a href="logout.php" class="btn btn-outline">تسجيل الخروج</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Dashboard -->
    <section class="dashboard">
        <div class="dashboard-container">
            <div class="dashboard-grid">
                <!-- Sidebar -->
                <aside class="sidebar">
                    <h3>القائمة الرئيسية</h3>
                    <ul class="sidebar-menu">
                        <li>
                            <a href="dashboard.php" class="active">
                                <i class="fas fa-tachometer-alt"></i>
                                لوحة التحكم
                            </a>
                        </li>
                        <li>
                            <a href="appointments.php">
                                <i class="fas fa-calendar-alt"></i>
                                مواعيدي
                            </a>
                        </li>
                        <li>
                            <a href="profile.php">
                                <i class="fas fa-user"></i>
                                الملف الشخصي
                            </a>
                        </li>
                        <li>
                            <a href="search.php">
                                <i class="fas fa-search"></i>
                                البحث عن طبيب
                            </a>
                        </li>
                        <li>
                            <a href="hospitals.php">
                                <i class="fas fa-hospital"></i>
                                المستشفيات
                            </a>
                        </li>
                        <li>
                            <a href="index.php">
                                <i class="fas fa-home"></i>
                                العودة للرئيسية
                            </a>
                        </li>
                    </ul>
                </aside>

                <!-- Main Content -->
                <main class="main-content">
                    <!-- Welcome Section -->
                    <div class="welcome-section">
                        <h1>مرحباً، <?php echo isset($user['full_name']) ? $user['full_name'] : 'المستخدم'; ?>! 👋</h1>
                        <p>إليك نظرة عامة على مواعيدك الطبية</p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <a href="search.php" class="quick-action">
                            <i class="fas fa-search"></i>
                            <h3>البحث عن طبيب</h3>
                            <p>ابحث عن أفضل الأطباء</p>
                        </a>
                        <a href="appointments.php" class="quick-action">
                            <i class="fas fa-calendar-plus"></i>
                            <h3>حجز موعد جديد</h3>
                            <p>احجز موعدك بسهولة</p>
                        </a>
                        <a href="profile.php" class="quick-action">
                            <i class="fas fa-user-edit"></i>
                            <h3>تعديل الملف الشخصي</h3>
                            <p>حدث بياناتك الشخصية</p>
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3><?php echo count($upcoming_appointments); ?></h3>
                            <p>مواعيد قادمة</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo count($past_appointments); ?></h3>
                            <p>مواعيد سابقة</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo count(array_filter($appointments, function($app) { return $app['status'] == 'pending'; })); ?></h3>
                            <p>في الانتظار</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo count(array_filter($appointments, function($app) { return $app['status'] == 'completed'; })); ?></h3>
                            <p>مكتملة</p>
                        </div>
                    </div>

                    <!-- Upcoming Appointments -->
                    <div class="appointments-section">
                        <div class="section-header">
                            <h2>المواعيد القادمة</h2>
                            <a href="appointments.php" class="btn btn-outline">عرض الكل</a>
                        </div>

                        <?php if (empty($upcoming_appointments)): ?>
                            <div class="no-appointments">
                                <p>لا توجد مواعيد قادمة</p>
                                <a href="search.php" class="btn btn-primary">احجز موعد جديد</a>
                            </div>
                        <?php else: ?>
                            <div class="appointments-grid">
                                <?php
                                $count = 0;
                                foreach ($upcoming_appointments as $appointment):
                                    if ($count >= 3) break; // عرض أول 3 مواعيد فقط
                                ?>
                                    <div class="appointment-card">
                                        <div class="appointment-header">
                                            <div class="appointment-date">
                                                <?php echo format_date_arabic($appointment['appointment_date']); ?>
                                                <br>
                                                <?php echo format_time_arabic($appointment['appointment_time']); ?>
                                            </div>
                                            <span class="appointment-status status-<?php echo $appointment['status']; ?>">
                                                <?php echo get_status_arabic($appointment['status']); ?>
                                            </span>
                                        </div>

                                        <div class="appointment-details">
                                            <div class="detail-item">
                                                <i class="fas fa-user-md"></i>
                                                <span><?php echo $appointment['doctor_name']; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-hospital"></i>
                                                <span><?php echo $appointment['hospital_name']; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-stethoscope"></i>
                                                <span><?php echo $appointment['clinic_name']; ?></span>
                                            </div>
                                        </div>

                                        <div class="appointment-actions">
                                            <a href="appointment-details.php?id=<?php echo $appointment['id']; ?>" class="btn btn-outline btn-small">
                                                <i class="fas fa-eye"></i>
                                                التفاصيل
                                            </a>
                                            <?php if ($appointment['status'] == 'confirmed'): ?>
                                                <a href="cancel-appointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-outline btn-small" onclick="return confirm('هل أنت متأكد من إلغاء الموعد؟')">
                                                    <i class="fas fa-times"></i>
                                                    إلغاء
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php
                                    $count++;
                                endforeach;
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Recent Appointments -->
                    <div class="appointments-section">
                        <div class="section-header">
                            <h2>آخر المواعيد</h2>
                            <a href="appointments.php" class="btn btn-outline">عرض الكل</a>
                        </div>

                        <?php if (empty($past_appointments)): ?>
                            <div class="no-appointments">
                                <p>لا توجد مواعيد سابقة</p>
                            </div>
                        <?php else: ?>
                            <div class="appointments-grid">
                                <?php
                                $count = 0;
                                foreach (array_slice($past_appointments, 0, 3) as $appointment):
                                ?>
                                    <div class="appointment-card">
                                        <div class="appointment-header">
                                            <div class="appointment-date">
                                                <?php echo format_date_arabic($appointment['appointment_date']); ?>
                                                <br>
                                                <?php echo format_time_arabic($appointment['appointment_time']); ?>
                                            </div>
                                            <span class="appointment-status status-<?php echo $appointment['status']; ?>">
                                                <?php echo get_status_arabic($appointment['status']); ?>
                                            </span>
                                        </div>

                                        <div class="appointment-details">
                                            <div class="detail-item">
                                                <i class="fas fa-user-md"></i>
                                                <span><?php echo $appointment['doctor_name']; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-hospital"></i>
                                                <span><?php echo $appointment['hospital_name']; ?></span>
                                            </div>
                                        </div>

                                        <div class="appointment-actions">
                                            <a href="appointment-details.php?id=<?php echo $appointment['id']; ?>" class="btn btn-outline btn-small">
                                                <i class="fas fa-eye"></i>
                                                التفاصيل
                                            </a>
                                        </div>
                                    </div>
                                <?php
                                    $count++;
                                endforeach;
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </main>
            </div>
        </div>
    </section>

    <script src="assets/js/script.js"></script>
</body>
</html>
