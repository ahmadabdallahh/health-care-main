<?php
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user = get_logged_in_user();
$appointments = get_user_appointments($user['id']);

// تصنيف المواعيد
$upcoming_appointments = array_filter($appointments, function($app) {
    return $app['status'] == 'confirmed' && $app['appointment_date'] >= date('Y-m-d');
});

$pending_appointments = array_filter($appointments, function($app) {
    return $app['status'] == 'pending';
});

$past_appointments = array_filter($appointments, function($app) {
    return $app['appointment_date'] < date('Y-m-d');
});

$cancelled_appointments = array_filter($appointments, function($app) {
    return $app['status'] == 'cancelled';
});

// معالجة إلغاء الموعد
if (isset($_POST['cancel_appointment'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    if (cancel_appointment($appointment_id, $user['id'])) {
        header("Location: appointments.php?success=cancelled");
        exit();
    } else {
        header("Location: appointments.php?error=cancel_failed");
        exit();
    }
}

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] == 'cancelled') {
        $success_message = 'تم إلغاء الموعد بنجاح';
    }
}

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'cancel_failed') {
        $error_message = 'حدث خطأ أثناء إلغاء الموعد';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مواعيدي - موقع حجز المواعيد الطبية</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .appointments-page {
            padding-top: 80px;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-primary) 100%);
        }

        .appointments-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--medical-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--bg-primary);
            padding: 2rem;
            border-radius: var(--radius-xl);
            text-align: center;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
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
            background: linear-gradient(135deg, var(--primary-blue), var(--medical-green));
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--medical-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .tabs-container {
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .tabs-header {
            display: flex;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
        }

        .tab-button {
            flex: 1;
            padding: 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-secondary);
            transition: var(--transition);
            position: relative;
        }

        .tab-button:hover {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-blue);
        }

        .tab-button.active {
            background: var(--primary-blue);
            color: white;
        }

        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--medical-green);
        }

        .tab-content {
            display: none;
            padding: 2rem;
        }

        .tab-content.active {
            display: block;
        }

        .appointments-grid {
            display: grid;
            gap: 1.5rem;
        }

        .appointment-card {
            background: var(--bg-primary);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 2rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .appointment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-blue);
            transition: var(--transition);
        }

        .appointment-card:hover {
            border-color: var(--primary-blue);
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
        }

        .appointment-card:hover::before {
            background: var(--medical-green);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .appointment-date-time {
            background: linear-gradient(135deg, var(--primary-blue), var(--medical-green));
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-lg);
            text-align: center;
            min-width: 150px;
        }

        .appointment-date {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .appointment-time {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .appointment-status {
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-confirmed {
            background: linear-gradient(135deg, var(--success), #34d399);
            color: white;
        }

        .status-pending {
            background: linear-gradient(135deg, var(--warning), #fbbf24);
            color: white;
        }

        .status-cancelled {
            background: linear-gradient(135deg, var(--error), #f87171);
            color: white;
        }

        .status-completed {
            background: linear-gradient(135deg, var(--info), var(--primary-light));
            color: white;
        }

        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: var(--bg-secondary);
            border-radius: var(--radius);
            transition: var(--transition);
        }

        .detail-item:hover {
            background: var(--primary-blue);
            color: white;
        }

        .detail-item i {
            width: 20px;
            color: var(--primary-blue);
            transition: var(--transition);
        }

        .detail-item:hover i {
            color: white;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .detail-item:hover .detail-label {
            color: white;
        }

        .detail-value {
            font-weight: 700;
            color: var(--text-primary);
            transition: var(--transition);
        }

        .detail-item:hover .detail-value {
            color: white;
        }

        .appointment-notes {
            background: var(--bg-secondary);
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-blue);
        }

        .notes-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .notes-content {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .appointment-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-view {
            background: var(--primary-blue);
            color: white;
        }

        .btn-view:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: var(--error);
            color: white;
        }

        .btn-cancel:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .btn-reschedule {
            background: var(--warning);
            color: white;
        }

        .btn-reschedule:hover {
            background: #d97706;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .empty-state p {
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .quick-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .tabs-header {
                flex-direction: column;
            }

            .tab-button {
                border-bottom: 1px solid var(--border-color);
            }

            .appointment-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .appointment-date-time {
                min-width: auto;
            }

            .appointment-details {
                grid-template-columns: 1fr;
            }

            .appointment-actions {
                flex-direction: column;
            }

            .btn-action {
                justify-content: center;
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
                        <a href="dashboard.php" class="nav-link">لوحة التحكم</a>
                    </li>
                </ul>
                <div class="nav-auth">
                    <span class="user-name">مرحباً، <?php echo $user['full_name']; ?></span>
                    <a href="logout.php" class="btn btn-outline">تسجيل الخروج</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Appointments Page -->
    <section class="appointments-page">
        <div class="appointments-container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>مواعيدي الطبية</h1>
                <p>إدارة جميع مواعيدك الطبية في مكان واحد</p>
            </div>

            <!-- Messages -->
            <?php if ($success_message): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $success_message; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($upcoming_appointments); ?></div>
                    <div class="stat-label">مواعيد قادمة</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($pending_appointments); ?></div>
                    <div class="stat-label">في الانتظار</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($past_appointments); ?></div>
                    <div class="stat-label">مواعيد سابقة</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($cancelled_appointments); ?></div>
                    <div class="stat-label">ملغية</div>
                </div>
            </div>

            <!-- Tabs Container -->
            <div class="tabs-container">
                <div class="tabs-header">
                    <button class="tab-button active" data-tab="upcoming">
                        <i class="fas fa-calendar-check"></i>
                        قادمة (<?php echo count($upcoming_appointments); ?>)
                    </button>
                    <button class="tab-button" data-tab="pending">
                        <i class="fas fa-clock"></i>
                        في الانتظار (<?php echo count($pending_appointments); ?>)
                    </button>
                    <button class="tab-button" data-tab="past">
                        <i class="fas fa-history"></i>
                        سابقة (<?php echo count($past_appointments); ?>)
                    </button>
                    <button class="tab-button" data-tab="cancelled">
                        <i class="fas fa-times-circle"></i>
                        ملغية (<?php echo count($cancelled_appointments); ?>)
                    </button>
                </div>

                <!-- Upcoming Appointments Tab -->
                <div class="tab-content active" id="upcoming">
                    <?php if (empty($upcoming_appointments)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-plus"></i>
                            <h3>لا توجد مواعيد قادمة</h3>
                            <p>احجز موعدك الأول مع أفضل الأطباء</p>
                            <div class="quick-actions">
                                <a href="search.php" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                    البحث عن طبيب
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="appointments-grid">
                            <?php foreach ($upcoming_appointments as $appointment): ?>
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-date-time">
                                            <div class="appointment-date">
                                                <?php echo format_date_arabic($appointment['appointment_date']); ?>
                                            </div>
                                            <div class="appointment-time">
                                                <?php echo format_time_arabic($appointment['appointment_time']); ?>
                                            </div>
                                        </div>
                                        <span class="appointment-status status-<?php echo $appointment['status']; ?>">
                                            <?php echo get_status_arabic($appointment['status']); ?>
                                        </span>
                                    </div>

                                    <div class="appointment-details">
                                        <div class="detail-item">
                                            <i class="fas fa-user-md"></i>
                                            <div>
                                                <div class="detail-label">الطبيب</div>
                                                <div class="detail-value"><?php echo $appointment['doctor_name']; ?></div>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-hospital"></i>
                                            <div>
                                                <div class="detail-label">المستشفى</div>
                                                <div class="detail-value"><?php echo $appointment['hospital_name']; ?></div>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-stethoscope"></i>
                                            <div>
                                                <div class="detail-label">العيادة</div>
                                                <div class="detail-value"><?php echo $appointment['clinic_name']; ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!empty($appointment['notes'])): ?>
                                        <div class="appointment-notes">
                                            <div class="notes-label">ملاحظات:</div>
                                            <div class="notes-content"><?php echo $appointment['notes']; ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="appointment-actions">
                                        <a href="appointment-details.php?id=<?php echo $appointment['id']; ?>" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i>
                                            عرض التفاصيل
                                        </a>
                                        <?php if (can_cancel_appointment($appointment['id'], $user['id'])): ?>
                                            <a href="reschedule.php?id=<?php echo $appointment['id']; ?>" class="btn-action btn-reschedule">
                                                <i class="fas fa-calendar-alt"></i>
                                                إعادة جدولة
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الموعد؟')">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                <button type="submit" name="cancel_appointment" class="btn-action btn-cancel">
                                                    <i class="fas fa-times"></i>
                                                    إلغاء الموعد
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pending Appointments Tab -->
                <div class="tab-content" id="pending">
                    <?php if (empty($pending_appointments)): ?>
                        <div class="empty-state">
                            <i class="fas fa-clock"></i>
                            <h3>لا توجد مواعيد في الانتظار</h3>
                            <p>جميع مواعيدك مؤكدة أو مكتملة</p>
                        </div>
                    <?php else: ?>
                        <div class="appointments-grid">
                            <?php foreach ($pending_appointments as $appointment): ?>
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-date-time">
                                            <div class="appointment-date">
                                                <?php echo format_date_arabic($appointment['appointment_date']); ?>
                                            </div>
                                            <div class="appointment-time">
                                                <?php echo format_time_arabic($appointment['appointment_time']); ?>
                                            </div>
                                        </div>
                                        <span class="appointment-status status-<?php echo $appointment['status']; ?>">
                                            <?php echo get_status_arabic($appointment['status']); ?>
                                        </span>
                                    </div>

                                    <div class="appointment-details">
                                        <div class="detail-item">
                                            <i class="fas fa-user-md"></i>
                                            <div>
                                                <div class="detail-label">الطبيب</div>
                                                <div class="detail-value"><?php echo $appointment['doctor_name']; ?></div>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-hospital"></i>
                                            <div>
                                                <div class="detail-label">المستشفى</div>
                                                <div class="detail-value"><?php echo $appointment['hospital_name']; ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="appointment-actions">
                                        <a href="appointment-details.php?id=<?php echo $appointment['id']; ?>" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i>
                                            عرض التفاصيل
                                        </a>
                                        <?php if (can_cancel_appointment($appointment['id'], $user['id'])): ?>
                                            <a href="reschedule.php?id=<?php echo $appointment['id']; ?>" class="btn-action btn-reschedule">
                                                <i class="fas fa-calendar-alt"></i>
                                                إعادة جدولة
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الموعد؟')">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                <button type="submit" name="cancel_appointment" class="btn-action btn-cancel">
                                                    <i class="fas fa-times"></i>
                                                    إلغاء الموعد
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Past Appointments Tab -->
                <div class="tab-content" id="past">
                    <?php if (empty($past_appointments)): ?>
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>لا توجد مواعيد سابقة</h3>
                            <p>ستظهر هنا جميع مواعيدك المكتملة</p>
                        </div>
                    <?php else: ?>
                        <div class="appointments-grid">
                            <?php foreach (array_slice($past_appointments, 0, 10) as $appointment): ?>
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-date-time">
                                            <div class="appointment-date">
                                                <?php echo format_date_arabic($appointment['appointment_date']); ?>
                                            </div>
                                            <div class="appointment-time">
                                                <?php echo format_time_arabic($appointment['appointment_time']); ?>
                                            </div>
                                        </div>
                                        <span class="appointment-status status-<?php echo $appointment['status']; ?>">
                                            <?php echo get_status_arabic($appointment['status']); ?>
                                        </span>
                                    </div>

                                    <div class="appointment-details">
                                        <div class="detail-item">
                                            <i class="fas fa-user-md"></i>
                                            <div>
                                                <div class="detail-label">الطبيب</div>
                                                <div class="detail-value"><?php echo $appointment['doctor_name']; ?></div>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-hospital"></i>
                                            <div>
                                                <div class="detail-label">المستشفى</div>
                                                <div class="detail-value"><?php echo $appointment['hospital_name']; ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="appointment-actions">
                                        <a href="appointment-details.php?id=<?php echo $appointment['id']; ?>" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i>
                                            عرض التفاصيل
                                        </a>
                                        <?php if ($appointment['status'] == 'completed'): ?>
                                            <a href="review_appointment.php?id=<?php echo $appointment['id']; ?>" class="btn-action btn-review">
                                                <i class="fas fa-star"></i>
                                                تقييم الموعد
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Cancelled Appointments Tab -->
                <div class="tab-content" id="cancelled">
                    <?php if (empty($cancelled_appointments)): ?>
                        <div class="empty-state">
                            <i class="fas fa-times-circle"></i>
                            <h3>لا توجد مواعيد ملغية</h3>
                            <p>لم تقم بإلغاء أي مواعيد بعد</p>
                        </div>
                    <?php else: ?>
                        <div class="appointments-grid">
                            <?php foreach (array_slice($cancelled_appointments, 0, 10) as $appointment): ?>
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-date-time">
                                            <div class="appointment-date">
                                                <?php echo format_date_arabic($appointment['appointment_date']); ?>
                                            </div>
                                            <div class="appointment-time">
                                                <?php echo format_time_arabic($appointment['appointment_time']); ?>
                                            </div>
                                        </div>
                                        <span class="appointment-status status-<?php echo $appointment['status']; ?>">
                                            <?php echo get_status_arabic($appointment['status']); ?>
                                        </span>
                                    </div>

                                    <div class="appointment-details">
                                        <div class="detail-item">
                                            <i class="fas fa-user-md"></i>
                                            <div>
                                                <div class="detail-label">الطبيب</div>
                                                <div class="detail-value"><?php echo $appointment['doctor_name']; ?></div>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-hospital"></i>
                                            <div>
                                                <div class="detail-label">المستشفى</div>
                                                <div class="detail-value"><?php echo $appointment['hospital_name']; ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="appointment-actions">
                                        <a href="appointment-details.php?id=<?php echo $appointment['id']; ?>" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i>
                                            عرض التفاصيل
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <script src="assets/js/script.js"></script>
    <script>
        // Tab Switching Logic
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.dataset.tab;

                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    // Add active class to clicked button and corresponding content
                    this.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>
