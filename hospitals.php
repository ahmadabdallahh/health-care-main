<?php
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user = get_logged_in_user();

// الحصول على معاملات البحث والتصفية
$search_query = isset($_GET['q']) ? clean_input($_GET['q']) : '';
$hospital_type = isset($_GET['type']) ? clean_input($_GET['type']) : '';
$is_24h = isset($_GET['24h']) ? (int)$_GET['24h'] : 0;
$min_rating = isset($_GET['rating']) ? (float)$_GET['rating'] : 0;

// الحصول على المستشفيات
$hospitals = get_all_hospitals();

// تطبيق الفلاتر
if ($search_query || $hospital_type || $is_24h || $min_rating) {
    $filtered_hospitals = [];
    foreach ($hospitals as $hospital) {
        $matches = true;
        
        // فلتر البحث
        if ($search_query && !preg_match("/$search_query/i", $hospital['name'] . ' ' . $hospital['description'])) {
            $matches = false;
        }
        
        // فلتر النوع
        if ($hospital_type && $hospital['type'] !== $hospital_type) {
            $matches = false;
        }
        
        // فلتر 24 ساعة
        if ($is_24h && !$hospital['is_24h']) {
            $matches = false;
        }
        
        // فلتر التقييم
        if ($min_rating && $hospital['rating'] < $min_rating) {
            $matches = false;
        }
        
        if ($matches) {
            $filtered_hospitals[] = $hospital;
        }
    }
    $hospitals = $filtered_hospitals;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المستشفيات والعيادات - صحة</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hospitals-page {
            padding-top: 80px;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-primary) 100%);
        }
        
        .hospitals-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 20px;
        }
        
        .hospitals-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .hospitals-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hospitals-header p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .filters-section {
            background: var(--bg-primary);
            padding: 2rem;
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-lg);
            margin-bottom: 3rem;
            border: 1px solid rgba(79, 70, 229, 0.1);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .filter-group label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .filter-group input,
        .filter-group select {
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .filter-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.5rem;
        }
        
        .btn-filter {
            padding: 0.75rem 2rem;
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-filter:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-clear {
            padding: 0.75rem 2rem;
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-clear:hover {
            background: var(--border-color);
        }
        
        .hospitals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .hospital-card {
            background: var(--bg-primary);
            border-radius: var(--radius-2xl);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            border: 1px solid rgba(79, 70, 229, 0.1);
        }
        
        .hospital-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }
        
        .hospital-image {
            height: 200px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        
        .hospital-content {
            padding: 1.5rem;
        }
        
        .hospital-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .hospital-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .hospital-type {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .hospital-type.government {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }
        
        .hospital-type.private {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .hospital-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .rating-stars {
            color: #fbbf24;
        }
        
        .rating-text {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .hospital-info {
            margin-bottom: 1.5rem;
        }
        
        .hospital-description {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .hospital-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .detail-item i {
            color: var(--primary-blue);
            width: 16px;
        }
        
        .hospital-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .feature-tag {
            padding: 0.25rem 0.75rem;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-blue);
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .hospital-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn-view {
            flex: 1;
            padding: 0.75rem;
            background: var(--primary-blue);
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: var(--radius);
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-view:hover {
            background: var(--primary-dark);
        }
        
        .btn-clinics {
            flex: 1;
            padding: 0.75rem;
            background: var(--bg-secondary);
            color: var(--text-primary);
            text-decoration: none;
            text-align: center;
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-clinics:hover {
            background: var(--border-color);
        }
        
        .no-hospitals {
            text-align: center;
            padding: 3rem;
            background: var(--bg-primary);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-lg);
        }
        
        .no-hospitals i {
            font-size: 4rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }
        
        .no-hospitals h3 {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .no-hospitals p {
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .hospitals-grid {
                grid-template-columns: 1fr;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .hospital-details {
                grid-template-columns: 1fr;
            }
            
            .hospital-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <div class="hospitals-page">
        <div class="hospitals-container">
            <!-- Header -->
            <div class="hospitals-header">
                <h1>🏥 المستشفيات والعيادات</h1>
                <p>اكتشف أفضل المستشفيات والعيادات الطبية في مصر. اختر من بين مجموعة واسعة من المرافق الطبية المتخصصة</p>
            </div>

            <!-- Filters -->
            <div class="filters-section">
                <form method="GET" action="">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label for="q">البحث</label>
                            <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="ابحث عن مستشفى أو عيادة...">
                        </div>
                        
                        <div class="filter-group">
                            <label for="type">نوع المستشفى</label>
                            <select id="type" name="type">
                                <option value="">جميع الأنواع</option>
                                <option value="حكومي" <?php echo $hospital_type === 'حكومي' ? 'selected' : ''; ?>>حكومي</option>
                                <option value="خاص" <?php echo $hospital_type === 'خاص' ? 'selected' : ''; ?>>خاص</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="24h">مفتوح 24 ساعة</label>
                            <select id="24h" name="24h">
                                <option value="0">جميع المستشفيات</option>
                                <option value="1" <?php echo $is_24h ? 'selected' : ''; ?>>مفتوح 24 ساعة فقط</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="rating">الحد الأدنى للتقييم</label>
                            <select id="rating" name="rating">
                                <option value="0">جميع التقييمات</option>
                                <option value="4.5" <?php echo $min_rating == 4.5 ? 'selected' : ''; ?>>4.5+ نجوم</option>
                                <option value="4.0" <?php echo $min_rating == 4.0 ? 'selected' : ''; ?>>4.0+ نجوم</option>
                                <option value="3.5" <?php echo $min_rating == 3.5 ? 'selected' : ''; ?>>3.5+ نجوم</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i>
                            تطبيق الفلاتر
                        </button>
                        <a href="hospitals.php" class="btn-clear">
                            <i class="fas fa-times"></i>
                            مسح الفلاتر
                        </a>
                    </div>
                </form>
            </div>

            <!-- Hospitals Grid -->
            <?php if (empty($hospitals)): ?>
                <div class="no-hospitals">
                    <i class="fas fa-hospital"></i>
                    <h3>لا توجد مستشفيات</h3>
                    <p>لم يتم العثور على مستشفيات تطابق معايير البحث المحددة.</p>
                    <a href="hospitals.php" class="btn btn-primary">عرض جميع المستشفيات</a>
                </div>
            <?php else: ?>
                <div class="hospitals-grid">
                    <?php foreach ($hospitals as $hospital): ?>
                        <div class="hospital-card">
                            <div class="hospital-image">
                                <i class="fas fa-hospital"></i>
                            </div>
                            
                            <div class="hospital-content">
                                <div class="hospital-header">
                                    <div>
                                        <h3 class="hospital-name"><?php echo htmlspecialchars($hospital['name']); ?></h3>
                                        <span class="hospital-type <?php echo (isset($hospital['type']) && $hospital['type'] === 'حكومي') ? 'government' : 'private'; ?>">
                                            <?php echo htmlspecialchars(isset($hospital['type']) ? $hospital['type'] : 'حكومي'); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="hospital-rating">
                                        <div class="rating-stars">
                                            <?php
                                            $rating = $hospital['rating'];
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
                                
                                <div class="hospital-info">
                                    <p class="hospital-description"><?php echo htmlspecialchars($hospital['description']); ?></p>
                                    
                                    <div class="hospital-details">
                                        <div class="detail-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?php echo htmlspecialchars($hospital['address']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-phone"></i>
                                            <span><?php echo htmlspecialchars($hospital['phone']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-envelope"></i>
                                            <span><?php echo htmlspecialchars($hospital['email']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo (isset($hospital['is_24h']) && $hospital['is_24h']) ? 'مفتوح 24 ساعة' : 'ساعات عمل محددة'; ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="hospital-features">
                                        <?php if (isset($hospital['is_24h']) && $hospital['is_24h']): ?>
                                            <span class="feature-tag">24 ساعة</span>
                                        <?php endif; ?>
                                        <span class="feature-tag">خدمات طبية متكاملة</span>
                                        <span class="feature-tag">أطباء متخصصون</span>
                                        <?php if (isset($hospital['type']) && $hospital['type'] === 'خاص'): ?>
                                            <span class="feature-tag">خدمات فاخرة</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="hospital-actions">
                                    <a href="hospital-details.php?id=<?php echo $hospital['id']; ?>" class="btn-view">
                                        <i class="fas fa-eye"></i>
                                        عرض التفاصيل
                                    </a>
                                    <a href="clinics.php?hospital=<?php echo $hospital['id']; ?>" class="btn-clinics">
                                        <i class="fas fa-stethoscope"></i>
                                        العيادات
                                    </a>
                                </div>
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