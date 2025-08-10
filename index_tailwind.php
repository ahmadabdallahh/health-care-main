<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>موقع حجز المواعيد الطبية - الصفحة الرئيسية</title>
    <link rel="stylesheet" href="assets/css/tailwind.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 font-cairo">
    <!-- Header -->
    <header class="hospital-nav fixed top-0 left-0 right-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="index.php" class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-heartbeat text-3xl text-hospital-primary"></i>
                    <span class="text-2xl font-bold text-hospital-primary">مستشفى الأمل</span>
                </a>

                <!-- Navigation Menu -->
                <div class="hidden md:flex items-center space-x-8 space-x-reverse">
                    <a href="index.php" class="text-gray-700 hover:text-hospital-primary font-semibold transition-colors">الرئيسية</a>
                    <a href="hospitals.php" class="text-gray-700 hover:text-hospital-primary font-semibold transition-colors">المستشفيات</a>
                    <a href="doctors.php" class="text-gray-700 hover:text-hospital-primary font-semibold transition-colors">الأطباء</a>
                    <a href="about.php" class="text-gray-700 hover:text-hospital-primary font-semibold transition-colors">من نحن</a>
                    <a href="contact.php" class="text-gray-700 hover:text-hospital-primary font-semibold transition-colors">اتصل بنا</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4 space-x-reverse">
                    <?php if (is_logged_in()): ?>
                        <span class="text-gray-600">مرحباً، <?php echo $_SESSION['full_name']; ?></span>
                        <a href="dashboard.php" class="hospital-btn-primary">لوحة التحكم</a>
                        <a href="logout.php" class="hospital-btn-accent">تسجيل الخروج</a>
                    <?php else: ?>
                        <a href="login.php" class="hospital-btn-primary">تسجيل الدخول</a>
                        <a href="register.php" class="hospital-btn-accent">إنشاء حساب</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-hospital-primary">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hospital-header pt-24 pb-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-center md:text-right">
                    <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                        رعاية صحية <span class="text-blue-200">متميزة</span>
                    </h1>
                    <p class="text-xl text-blue-100 mb-8 leading-relaxed">
                        احجز موعدك الطبي بسهولة وسرعة مع أفضل الأطباء والمستشفيات
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="hospitals.php" class="hospital-btn-success text-lg px-8 py-4">
                            <i class="fas fa-calendar-check ml-2"></i>
                            احجز موعد الآن
                        </a>
                        <a href="about.php" class="hospital-btn-accent text-lg px-8 py-4">
                            <i class="fas fa-info-circle ml-2"></i>
                            تعرف علينا
                        </a>
                    </div>
                </div>
                <div class="text-center">
                    <div class="hospital-icon mx-auto mb-6">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="hospital-card text-center">
                            <div class="text-3xl font-bold text-hospital-primary mb-2">50+</div>
                            <div class="text-gray-600">مستشفى</div>
                        </div>
                        <div class="hospital-card text-center">
                            <div class="text-3xl font-bold text-hospital-primary mb-2">200+</div>
                            <div class="text-gray-600">طبيب</div>
                        </div>
                        <div class="hospital-card text-center">
                            <div class="text-3xl font-bold text-hospital-primary mb-2">1000+</div>
                            <div class="text-gray-600">مريض</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">خدماتنا المتميزة</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    نقدم مجموعة شاملة من الخدمات الطبية لضمان رعاية صحية متميزة
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="hospital-card text-center">
                    <div class="hospital-icon mx-auto mb-6">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">حجز المواعيد</h3>
                    <p class="text-gray-600 leading-relaxed">
                        احجز موعدك الطبي بسهولة وسرعة مع أفضل الأطباء في أي وقت ومن أي مكان
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="hospital-card text-center">
                    <div class="hospital-icon mx-auto mb-6">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">أطباء متخصصون</h3>
                    <p class="text-gray-600 leading-relaxed">
                        فريق من الأطباء المتخصصين ذوي الخبرة العالية في جميع التخصصات الطبية
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="hospital-card text-center">
                    <div class="hospital-icon mx-auto mb-6">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">مستشفيات معتمدة</h3>
                    <p class="text-gray-600 leading-relaxed">
                        شبكة من المستشفيات والمراكز الطبية المعتمدة بأحدث التقنيات والأجهزة
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Hospitals Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">مستشفياتنا الشريكة</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    تعرف على أفضل المستشفيات والمراكز الطبية المتعاونة معنا
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $hospitals = get_all_hospitals();
                $count = 0;
                foreach ($hospitals as $hospital):
                    if ($count >= 6) break;
                ?>
                <div class="hospital-card">
                    <div class="aspect-video bg-gray-200 rounded-hospital mb-4 flex items-center justify-center">
                        <i class="fas fa-hospital text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($hospital['name']); ?></h3>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($hospital['address']); ?></p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <div class="flex text-yellow-400">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-gray-600">(4.8)</span>
                        </div>
                        <a href="hospital-details.php?id=<?php echo $hospital['id']; ?>" class="hospital-btn-primary">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
                <?php
                    $count++;
                endforeach;
                ?>
            </div>

            <div class="text-center mt-12">
                <a href="hospitals.php" class="hospital-btn-success text-lg px-8 py-4">
                    <i class="fas fa-eye ml-2"></i>
                    عرض جميع المستشفيات
                </a>
            </div>
        </div>
    </section>

    <!-- Specialties Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">التخصصات الطبية</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    نغطي جميع التخصصات الطبية مع أفضل الأطباء المتخصصين
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="hospital-card text-center">
                    <div class="hospital-icon mx-auto mb-4">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">أمراض القلب</h3>
                </div>

                <div class="hospital-card text-center">
                    <div class="hospital-icon mx-auto mb-4">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">طب الأعصاب</h3>
                </div>

                <div class="hospital-card text-center">
                    <div class="hospital-icon mx-auto mb-4">
                        <i class="fas fa-bone"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">جراحة العظام</h3>
                </div>

                <div class="hospital-card text-center">
                    <div class="hospital-icon mx-auto mb-4">
                        <i class="fas fa-baby"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">طب الأطفال</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 hospital-header">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">ابدأ رحلتك الصحية اليوم</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                انضم إلى آلاف المرضى الذين يثقون بنا للحصول على أفضل رعاية صحية
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="register.php" class="hospital-btn-success text-lg px-8 py-4">
                    <i class="fas fa-user-plus ml-2"></i>
                    إنشاء حساب جديد
                </a>
                <a href="contact.php" class="hospital-btn-accent text-lg px-8 py-4">
                    <i class="fas fa-phone ml-2"></i>
                    اتصل بنا الآن
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">مستشفى الأمل</h3>
                    <p class="text-gray-300 leading-relaxed">
                        نقدم أفضل الخدمات الطبية مع فريق من الأطباء المتخصصين والمستشفيات المعتمدة
                    </p>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">روابط سريعة</h4>
                    <ul class="space-y-2">
                        <li><a href="hospitals.php" class="text-gray-300 hover:text-white transition-colors">المستشفيات</a></li>
                        <li><a href="doctors.php" class="text-gray-300 hover:text-white transition-colors">الأطباء</a></li>
                        <li><a href="about.php" class="text-gray-300 hover:text-white transition-colors">من نحن</a></li>
                        <li><a href="contact.php" class="text-gray-300 hover:text-white transition-colors">اتصل بنا</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">الخدمات</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">حجز المواعيد</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">الاستشارات الطبية</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">الفحوصات الطبية</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">الطوارئ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">تواصل معنا</h4>
                    <div class="space-y-2">
                        <p class="text-gray-300"><i class="fas fa-phone ml-2"></i> 0123456789</p>
                        <p class="text-gray-300"><i class="fas fa-envelope ml-2"></i> info@hospital.com</p>
                        <p class="text-gray-300"><i class="fas fa-map-marker-alt ml-2"></i> القاهرة، مصر</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-300">&copy; 2024 مستشفى الأمل. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
