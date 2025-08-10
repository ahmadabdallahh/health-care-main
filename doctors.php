<?php
require_once 'includes/functions.php';

// التحقق من وجود معرف العيادة
$clinic_id = isset($_GET['clinic']) ? (int)$_GET['clinic'] : 0;

if (!$clinic_id) {
    header("Location: hospitals.php");
    exit();
}

// الحصول على معلومات العيادة والمستشفى
$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    $clinic = null;
    $hospital = null;
    $doctors = [];
} else {
    try {
        // الحصول على معلومات العيادة والمستشفى
        $stmt = $conn->prepare("
            SELECT c.*, h.name as hospital_name, h.address as hospital_address, s.name as specialty_name
            FROM clinics c
            LEFT JOIN hospitals h ON c.hospital_id = h.id
            LEFT JOIN specialties s ON c.specialty_id = s.id
            WHERE c.id = ?
        ");
        $stmt->execute([$clinic_id]);
        $clinic = $stmt->fetch();

        if ($clinic) {
            $hospital = [
                'name' => $clinic['hospital_name'],
                'address' => $clinic['hospital_address']
            ];
        }

        // الحصول على الأطباء
        $stmt = $conn->prepare("
            SELECT d.*, s.name as specialty_name
            FROM doctors d
            LEFT JOIN specialties s ON d.specialty_id = s.id
            WHERE d.clinic_id = ?
            ORDER BY d.full_name
        ");
        $stmt->execute([$clinic_id]);
        $doctors = $stmt->fetchAll();
    } catch (PDOException $e) {
        $clinic = null;
        $hospital = null;
        $doctors = [];
    }
}

// إذا لم يتم العثور على العيادة
if (!$clinic) {
    header("Location: hospitals.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أطباء <?php echo htmlspecialchars($clinic['name']); ?> - نظام حجز المواعيد الطبية</title>
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
            <a href="clinics.php?hospital=<?php echo $clinic['hospital_id']; ?>">عيادات <?php echo htmlspecialchars($hospital['name']); ?></a>
            <i class="fas fa-chevron-left"></i>
            <span>أطباء <?php echo htmlspecialchars($clinic['name']); ?></span>
        </div>

        <!-- Clinic Info -->
        <div class="clinic-info-section">
            <div class="clinic-header">
                <div class="clinic-basic-info">
                    <h1><?php echo htmlspecialchars($clinic['name']); ?></h1>
                    <p class="clinic-hospital">
                        <i class="fas fa-hospital"></i>
                        <?php echo htmlspecialchars($hospital['name']); ?>
                    </p>
                    <p class="clinic-address">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($hospital['address']); ?>
                    </p>
                    <?php if (isset($clinic['specialty_name']) && $clinic['specialty_name']): ?>
                        <span class="specialty-tag"><?php echo htmlspecialchars($clinic['specialty_name']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="clinic-rating">
                    <div class="rating-stars">
                        <?php
                        $rating = isset($clinic['rating']) ? $clinic['rating'] : 0;
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
                </div>
            </div>

            <?php if (isset($clinic['description']) && $clinic['description']): ?>
                <div class="clinic-description">
                    <p><?php echo htmlspecialchars($clinic['description']); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Doctors Section -->
        <div class="doctors-section">
            <div class="section-header">
                <h2>الأطباء المتاحون</h2>
                <p>اختر الطبيب المناسب لحجز موعدك الطبي</p>
            </div>

            <?php if (empty($doctors)): ?>
                <div class="no-doctors">
                    <i class="fas fa-user-md"></i>
                    <h3>لا يوجد أطباء</h3>
                    <p>لم يتم العثور على أطباء في هذه العيادة حالياً.</p>
                    <a href="clinics.php?hospital=<?php echo $clinic['hospital_id']; ?>" class="btn btn-primary">العودة للعيادات</a>
                </div>
            <?php else: ?>
                <div class="doctors-grid">
                    <?php foreach ($doctors as $doctor): ?>
                        <div class="doctor-card">
                            <div class="doctor-header">
                                <div class="doctor-avatar">
                                    <?php if (isset($doctor['image']) && $doctor['image']): ?>
                                        <img src="<?php echo htmlspecialchars($doctor['image']); ?>" alt="<?php echo htmlspecialchars($doctor['full_name']); ?>">
                                    <?php else: ?>
                                        <i class="fas fa-user-md"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="doctor-info">
                                    <h3 class="doctor-name"><?php echo htmlspecialchars($doctor['full_name']); ?></h3>
                                    <?php if (isset($doctor['specialty_name']) && $doctor['specialty_name']): ?>
                                        <span class="specialty-tag"><?php echo htmlspecialchars($doctor['specialty_name']); ?></span>
                                    <?php endif; ?>
                                    <?php if (isset($doctor['experience_years']) && $doctor['experience_years']): ?>
                                        <span class="experience-tag"><?php echo $doctor['experience_years']; ?> سنوات خبرة</span>
                                    <?php endif; ?>
                                </div>
                                <div class="doctor-rating">
                                    <div class="rating-stars">
                                        <?php
                                        $doctor_rating = isset($doctor['rating']) ? $doctor['rating'] : 0;
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $doctor_rating) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif ($i - $doctor_rating < 1) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <span class="rating-text"><?php echo number_format($doctor_rating, 1); ?></span>
                                </div>
                            </div>

                            <div class="doctor-content">
                                <?php if (isset($doctor['education']) && $doctor['education']): ?>
                                    <div class="education-info">
                                        <h4>المؤهلات العلمية:</h4>
                                        <p><?php echo htmlspecialchars($doctor['education']); ?></p>
                                    </div>
                                <?php endif; ?>

                                <div class="doctor-details">
                                    <?php if (isset($doctor['phone']) && $doctor['phone']): ?>
                                        <div class="detail-item">
                                            <i class="fas fa-phone"></i>
                                            <span><?php echo htmlspecialchars($doctor['phone']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($doctor['email']) && $doctor['email']): ?>
                                        <div class="detail-item">
                                            <i class="fas fa-envelope"></i>
                                            <span><?php echo htmlspecialchars($doctor['email']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="doctor-actions">
                                <a href="doctor-details.php?id=<?php echo $doctor['id']; ?>" class="btn-view">
                                    <i class="fas fa-eye"></i>
                                    عرض التفاصيل
                                </a>
                                <a href="book.php?doctor=<?php echo $doctor['id']; ?>&clinic=<?php echo $clinic_id; ?>" class="btn-book">
                                    <i class="fas fa-calendar-plus"></i>
                                    حجز موعد
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>
</body>
</html>
