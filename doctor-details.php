<?php
require_once 'includes/functions.php';

// التحقق من وجود معرف الطبيب
$doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$doctor_id) {
    header("Location: hospitals.php");
    exit();
}

// الحصول على معلومات الطبيب
$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    $doctor = null;
    $clinic = null;
    $hospital = null;
    $schedule = [];
} else {
    try {
        // الحصول على معلومات الطبيب والعيادة والمستشفى
        $stmt = $conn->prepare("
            SELECT d.*, c.name as clinic_name, c.description as clinic_description,
                   h.name as hospital_name, h.address as hospital_address,
                   s.name as specialty_name, s.description as specialty_description
            FROM doctors d
            LEFT JOIN clinics c ON d.clinic_id = c.id
            LEFT JOIN hospitals h ON c.hospital_id = h.id
            LEFT JOIN specialties s ON d.specialty_id = s.id
            WHERE d.id = ?
        ");
        $stmt->execute([$doctor_id]);
        $doctor = $stmt->fetch();

        if ($doctor) {
            $clinic = [
                'name' => $doctor['clinic_name'],
                'description' => $doctor['clinic_description'],
                'id' => $doctor['clinic_id']
            ];
            $hospital = [
                'name' => $doctor['hospital_name'],
                'address' => $doctor['hospital_address']
            ];
        }

        // الحصول على جدول عمل الطبيب
        $stmt = $conn->prepare("
            SELECT * FROM doctor_schedules
            WHERE doctor_id = ?
            ORDER BY FIELD(day_of_week, 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')
        ");
        $stmt->execute([$doctor_id]);
        $schedule = $stmt->fetchAll();
    } catch (PDOException $e) {
        $doctor = null;
        $clinic = null;
        $hospital = null;
        $schedule = [];
    }
}

// إذا لم يتم العثور على الطبيب
if (!$doctor) {
    header("Location: hospitals.php");
    exit();
}

// تحويل أيام الأسبوع إلى العربية
$days_arabic = [
    'sunday' => 'الأحد',
    'monday' => 'الاثنين',
    'tuesday' => 'الثلاثاء',
    'wednesday' => 'الأربعاء',
    'thursday' => 'الخميس',
    'friday' => 'الجمعة',
    'saturday' => 'السبت'
];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د. <?php echo htmlspecialchars($doctor['full_name']); ?> - نظام حجز المواعيد الطبية</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php">الرئيسية</a>
            <i class="fas fa-chevron-left"></i>
            <a href="hospitals.php">المستشفيات</a>
            <i class="fas fa-chevron-left"></i>
            <a href="clinics.php?hospital=<?php echo $doctor['hospital_id']; ?>">عيادات <?php echo htmlspecialchars($hospital['name']); ?></a>
            <i class="fas fa-chevron-left"></i>
            <a href="doctors.php?clinic=<?php echo $doctor['clinic_id']; ?>">أطباء <?php echo htmlspecialchars($clinic['name']); ?></a>
            <i class="fas fa-chevron-left"></i>
            <span>د. <?php echo htmlspecialchars($doctor['full_name']); ?></span>
        </div>

        <!-- Doctor Profile -->
        <div class="doctor-profile">
            <div class="doctor-header">
                <div class="doctor-avatar">
                    <?php if (isset($doctor['image']) && $doctor['image']): ?>
                        <img src="<?php echo htmlspecialchars($doctor['image']); ?>" alt="<?php echo htmlspecialchars($doctor['full_name']); ?>">
                    <?php else: ?>
                        <i class="fas fa-user-md"></i>
                    <?php endif; ?>
                </div>

                <div class="doctor-info">
                    <h1>د. <?php echo htmlspecialchars($doctor['full_name']); ?></h1>
                    <?php if (isset($doctor['specialty_name']) && $doctor['specialty_name']): ?>
                        <span class="specialty-tag"><?php echo htmlspecialchars($doctor['specialty_name']); ?></span>
                    <?php endif; ?>
                    <?php if (isset($doctor['experience_years']) && $doctor['experience_years']): ?>
                        <span class="experience-tag"><?php echo $doctor['experience_years']; ?> سنوات خبرة</span>
                    <?php endif; ?>

                    <div class="doctor-location">
                        <p><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($hospital['name']); ?></p>
                        <p><i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($clinic['name']); ?></p>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hospital['address']); ?></p>
                    </div>
                </div>

                <div class="doctor-rating">
                    <div class="rating-stars">
                        <?php
                        $rating = isset($doctor['rating']) ? $doctor['rating'] : 0;
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo '<i class="fas fa-star"></i>';
                            } elseif ($i - $rating < 1) {
                                echo '<i class="fas fa-star-half-alt"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                    </div>
                    <span class="rating-text"><?php echo number_format($rating, 1); ?></span>
                    <span class="rating-label">تقييم المرضى</span>
                </div>
            </div>

            <div class="doctor-actions">
                <a href="book.php?doctor=<?php echo $doctor['id']; ?>&clinic=<?php echo $doctor['clinic_id']; ?>" class="btn-book">
                    <i class="fas fa-calendar-plus"></i>
                    حجز موعد
                </a>
                <a href="tel:<?php echo htmlspecialchars($doctor['phone']); ?>" class="btn-call">
                    <i class="fas fa-phone"></i>
                    اتصل الآن
                </a>
            </div>
        </div>

        <div class="doctor-details-grid">
            <!-- Education & Experience -->
            <div class="detail-section">
                <h3>المؤهلات والخبرة</h3>
                <?php if (isset($doctor['education']) && $doctor['education']): ?>
                    <div class="info-item">
                        <h4>المؤهلات العلمية:</h4>
                        <p><?php echo htmlspecialchars($doctor['education']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (isset($doctor['specialty_description']) && $doctor['specialty_description']): ?>
                    <div class="info-item">
                        <h4>التخصص:</h4>
                        <p><?php echo htmlspecialchars($doctor['specialty_description']); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Contact Information -->
            <div class="detail-section">
                <h3>معلومات التواصل</h3>
                <div class="contact-info">
                    <?php if (isset($doctor['phone']) && $doctor['phone']): ?>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <span class="label">الهاتف:</span>
                                <span class="value"><?php echo htmlspecialchars($doctor['phone']); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($doctor['email']) && $doctor['email']): ?>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <span class="label">البريد الإلكتروني:</span>
                                <span class="value"><?php echo htmlspecialchars($doctor['email']); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Working Schedule -->
            <div class="detail-section">
                <h3>جدول العمل</h3>
                <?php if (empty($schedule)): ?>
                    <p class="no-schedule">لم يتم تحديد جدول عمل بعد</p>
                <?php else: ?>
                    <div class="schedule-grid">
                        <?php foreach ($schedule as $day): ?>
                            <div class="schedule-day">
                                <div class="day-name"><?php echo $days_arabic[$day['day_of_week']]; ?></div>
                                <div class="day-time">
                                    <?php echo date('H:i', strtotime($day['start_time'])); ?> -
                                    <?php echo date('H:i', strtotime($day['end_time'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Clinic Information -->
            <div class="detail-section">
                <h3>معلومات العيادة</h3>
                <div class="clinic-info">
                    <h4><?php echo htmlspecialchars($clinic['name']); ?></h4>
                    <?php if (isset($clinic['description']) && $clinic['description']): ?>
                        <p><?php echo htmlspecialchars($clinic['description']); ?></p>
                    <?php endif; ?>
                    <p><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($hospital['name']); ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hospital['address']); ?></p>
                </div>
            </div>
        </div>

        <!-- Book Appointment Section -->
        <div class="book-appointment-section">
            <div class="section-header">
                <h2>حجز موعد مع د. <?php echo htmlspecialchars($doctor['full_name']); ?></h2>
                <p>احجز موعدك الطبي بسهولة وأمان</p>
            </div>

            <div class="booking-options">
                <a href="book.php?doctor=<?php echo $doctor['id']; ?>&clinic=<?php echo $doctor['clinic_id']; ?>" class="btn-book-large">
                    <i class="fas fa-calendar-plus"></i>
                    حجز موعد جديد
                </a>

                <?php if (is_logged_in()): ?>
                    <a href="appointments.php" class="btn-view-appointments">
                        <i class="fas fa-list"></i>
                        عرض مواعيدي
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        تسجيل الدخول لحجز موعد
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>
</body>
</html>
