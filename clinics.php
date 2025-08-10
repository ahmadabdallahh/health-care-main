<?php
require_once 'includes/functions.php';

// التحقق من وجود معرف المستشفى
$hospital_id = isset($_GET['hospital']) ? (int)$_GET['hospital'] : 0;

if (!$hospital_id) {
    header("Location: hospitals.php");
    exit();
}

// الحصول على معلومات المستشفى
$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    $hospital = null;
    $clinics = [];
} else {
    try {
        // الحصول على معلومات المستشفى
        $stmt = $conn->prepare("SELECT * FROM hospitals WHERE id = ?");
        $stmt->execute([$hospital_id]);
        $hospital = $stmt->fetch();

        // الحصول على العيادات
        $stmt = $conn->prepare("
            SELECT c.*, s.name as specialty_name, s.description as specialty_description
            FROM clinics c
            LEFT JOIN specialties s ON c.specialty_id = s.id
            WHERE c.hospital_id = ?
            ORDER BY c.name
        ");
        $stmt->execute([$hospital_id]);
        $clinics = $stmt->fetchAll();
    } catch (PDOException $e) {
        $hospital = null;
        $clinics = [];
    }
}

// إذا لم يتم العثور على المستشفى
if (!$hospital) {
    header("Location: hospitals.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عيادات <?php echo htmlspecialchars($hospital['name']); ?> - نظام حجز المواعيد الطبية</title>
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
            <span>عيادات <?php echo htmlspecialchars($hospital['name']); ?></span>
        </div>

        <!-- Hospital Info -->
        <div class="hospital-info-section">
            <div class="hospital-header">
                <div class="hospital-basic-info">
                    <h1><?php echo htmlspecialchars($hospital['name']); ?></h1>
                    <p class="hospital-address">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($hospital['address']); ?>
                    </p>
                    <div class="hospital-contact">
                        <?php if (isset($hospital['phone']) && $hospital['phone']): ?>
                            <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($hospital['phone']); ?></span>
                        <?php endif; ?>
                        <?php if (isset($hospital['email']) && $hospital['email']): ?>
                            <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($hospital['email']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hospital-rating">
                    <div class="rating-stars">
                        <?php
                        $rating = isset($hospital['rating']) ? $hospital['rating'] : 0;
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
        </div>

        <!-- Clinics Section -->
        <div class="clinics-section">
            <div class="section-header">
                <h2>العيادات المتاحة</h2>
                <p>اختر العيادة المناسبة لحجز موعدك الطبي</p>
            </div>

            <?php if (empty($clinics)): ?>
                <div class="no-clinics">
                    <i class="fas fa-stethoscope"></i>
                    <h3>لا توجد عيادات</h3>
                    <p>لم يتم العثور على عيادات في هذا المستشفى حالياً.</p>
                    <a href="hospitals.php" class="btn btn-primary">العودة للمستشفيات</a>
                </div>
            <?php else: ?>
                <div class="clinics-grid">
                    <?php foreach ($clinics as $clinic): ?>
                        <div class="clinic-card">
                            <div class="clinic-header">
                                <div class="clinic-info">
                                    <h3 class="clinic-name"><?php echo htmlspecialchars($clinic['name']); ?></h3>
                                    <?php if (isset($clinic['specialty_name']) && $clinic['specialty_name']): ?>
                                        <span class="specialty-tag"><?php echo htmlspecialchars($clinic['specialty_name']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="clinic-rating">
                                    <div class="rating-stars">
                                        <?php
                                        $clinic_rating = isset($clinic['rating']) ? $clinic['rating'] : 0;
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $clinic_rating) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif ($i - $clinic_rating < 1) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <span class="rating-text"><?php echo number_format($clinic_rating, 1); ?></span>
                                </div>
                            </div>

                            <div class="clinic-content">
                                <?php if (isset($clinic['description']) && $clinic['description']): ?>
                                    <p class="clinic-description"><?php echo htmlspecialchars($clinic['description']); ?></p>
                                <?php endif; ?>

                                <div class="clinic-details">
                                    <?php if (isset($clinic['phone']) && $clinic['phone']): ?>
                                        <div class="detail-item">
                                            <i class="fas fa-phone"></i>
                                            <span><?php echo htmlspecialchars($clinic['phone']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($clinic['email']) && $clinic['email']): ?>
                                        <div class="detail-item">
                                            <i class="fas fa-envelope"></i>
                                            <span><?php echo htmlspecialchars($clinic['email']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($clinic['consultation_fee']) && $clinic['consultation_fee']): ?>
                                        <div class="detail-item">
                                            <i class="fas fa-money-bill-wave"></i>
                                            <span>رسوم الاستشارة: <?php echo number_format($clinic['consultation_fee']); ?> جنيه</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (isset($clinic['specialty_description']) && $clinic['specialty_description']): ?>
                                    <div class="specialty-info">
                                        <h4>التخصص:</h4>
                                        <p><?php echo htmlspecialchars($clinic['specialty_description']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="clinic-actions">
                                <a href="doctors.php?clinic=<?php echo $clinic['id']; ?>" class="btn-view">
                                    <i class="fas fa-user-md"></i>
                                    عرض الأطباء
                                </a>
                                <a href="book.php?clinic=<?php echo $clinic['id']; ?>" class="btn-book">
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