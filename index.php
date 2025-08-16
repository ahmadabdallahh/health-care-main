<?php
require_once 'includes/functions.php';

// الحصول على البيانات (يمكن استخدامها في أقسام مستقبلية)
$hospitals = get_all_hospitals();
$specialties = get_all_specialties();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شفاء - حجز المواعيد الطبية</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts (Cairo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Apply Cairo font to the entire page */
        body {
            font-family: 'Cairo', sans-serif;
        }

        /* Enhanced specialty card animations */
        .specialty-card {
            position: relative;
            overflow: hidden;
        }

        .specialty-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .specialty-card:hover::before {
            left: 100%;
        }

        /* Category button active state */
        .category-btn.active {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Enhanced navbar animations */
        .nav-link-underline {
            position: relative;
        }

        .nav-link-underline::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            transition: width 0.3s ease;
        }

        .nav-link-underline:hover::after {
            width: 100%;
        }

        /* Specialty card hover effects */
        .specialty-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        /* Gradient text effects */
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Floating animation for icons */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .floating-icon {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Sticky Navbar -->
    <header class="bg-white/90 backdrop-blur-lg shadow-lg sticky top-0 z-50 border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="#" class="text-3xl font-black text-blue-600 hover:text-blue-700 transition-colors duration-300 flex items-center space-x-2 space-x-reverse">
                    <i class="fas fa-heartbeat text-red-500"></i>
                    <span>شفاء</span>
                </a>

                <!-- Centered Nav Links (for Desktop) -->
                <nav class="hidden md:flex items-center space-x-8 space-x-reverse">
                    <a href="#features" class="text-gray-700 hover:text-blue-600 transition-all duration-300 font-medium relative group">
                        المميزات
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#specialties" class="text-gray-700 hover:text-blue-600 transition-all duration-300 font-medium relative group">
                        التخصصات
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#hospitals" class="text-gray-700 hover:text-blue-600 transition-all duration-300 font-medium relative group">
                        المستشفيات
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 transition-all duration-300 font-medium relative group">
                        تواصل معنا
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </nav>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-2 space-x-reverse">
                    <?php if (is_logged_in()): ?>
                        <?php
                            $dashboard_url = '#';
                                        if (isset($_SESSION['role'])) {
                switch ($_SESSION['role']) {
                                    case 'admin': $dashboard_url = 'admin/index.php'; break;
                                    case 'doctor': $dashboard_url = 'doctor/index.php'; break;
                                    case 'patient': $dashboard_url = 'patient/index.php'; break;
                                    case 'hospital': $dashboard_url = 'hospital/index.php'; break;
                                }
                            }
                        ?>
                        <a href="<?php echo $dashboard_url; ?>" class="px-4 py-2 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">لوحة التحكم</a>
                        <a href="logout.php" class="px-4 py-2 text-sm font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all duration-300 border border-gray-200">تسجيل الخروج</a>
                    <?php else: ?>
                        <a href="login.php" class="px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-green-500 to-green-600 rounded-lg hover:from-green-600 hover:to-green-700 focus:ring-4 focus:ring-green-300 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">تسجيل الدخول</a>
                        <a href="register.php" class="px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">إنشاء حساب</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="relative h-screen flex items-center justify-center text-center bg-gradient-to-br from-teal-400 to-blue-600 text-white p-4">
            <div class="z-10 flex flex-col items-center">
                <h1 class="text-4xl md:text-6xl font-black leading-tight mb-4">
                    أهلاً بك! احجز موعدك الطبي بكل سهولة
                </h1>
                <p class="text-lg md:text-xl text-white/80 mb-8 max-w-3xl">
                    نظام شفاء هو بوابتك الأولى للوصول إلى أفضل الأطباء والمستشفيات. ابحث، قارن، واحجز موعدك في دقائق معدودة.
                </p>

                <!-- Search Bar -->
                <div class="w-full max-w-2xl">
                    <form action="search.php" method="GET" class="relative">
                        <input
                            type="text"
                            name="query"
                            placeholder="ابحث عن طبيب، تخصص، أو مستشفى..."
                            class="w-full h-16 pr-16 pl-4 text-lg text-gray-800 bg-white rounded-full shadow-2xl focus:outline-none focus:ring-4 focus:ring-blue-300 transition-shadow duration-300"
                        />
                        <button type="submit" class="absolute top-0 left-0 h-16 w-16 flex items-center justify-center text-white bg-blue-600 rounded-full hover:bg-blue-700 transition-colors duration-300">
                            <i class="fas fa-search text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900">لماذا تختار <span class="text-blue-600">شفاء</span>؟</h2>
                    <p class="text-lg text-gray-600 mt-4 max-w-2xl mx-auto">نحن نقدم لك تجربة متكاملة وسلسة للعثور على أفضل الأطباء وحجز المواعيد بكل سهولة.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <!-- Feature 1 -->
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-2">
                        <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-search-plus fa-2x"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">بحث متقدم وذكي</h3>
                        <p class="text-gray-600">ابحث عن الطبيب المناسب حسب التخصص، الموقع، وحتى اسم المستشفى. نتائج دقيقة في ثوانٍ.</p>
                    </div>
                    <!-- Feature 2 -->
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-2">
                        <div class="bg-green-100 text-green-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">حجز فوري ومؤكد</h3>
                        <p class="text-gray-600">اختر الموعد المناسب لك من جدول الطبيب مباشرة واحصل على تأكيد فوري لحجزك.</p>
                    </div>
                    <!-- Feature 3 -->
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-2">
                        <div class="bg-orange-100 text-orange-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">ملفات شخصية موثوقة</h3>
                        <p class="text-gray-600">اطلع على تقييمات المرضى الآخرين، خبرات الطبيب، والشهادات قبل اتخاذ قرارك.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Specialties Section -->
        <section id="specialties" class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl font-black text-gray-800 mb-4">ابحث حسب التخصص</h2>
                <p class="text-lg text-gray-600 mb-12 max-w-3xl mx-auto">نغطي كافة التخصصات الطبية لمساعدتك في العثور على الطبيب المناسب لحالتك الصحية. اختر من بين أكثر من 50 تخصصاً طبياً مع نخبة من الأطباء المتميزين.</p>

                <!-- Specialty Categories -->
                <div class="mb-12">
                    <div class="flex flex-wrap justify-center gap-4 mb-8">
                        <button class="category-btn active px-6 py-3 rounded-full bg-blue-600 text-white font-medium hover:bg-blue-700 transition-all duration-300" data-category="all">
                            جميع التخصصات
                        </button>
                        <button class="category-btn px-6 py-3 rounded-full bg-gray-200 text-gray-700 font-medium hover:bg-blue-100 transition-all duration-300" data-category="internal">
                            الطب الباطني
                        </button>
                        <button class="category-btn px-6 py-3 rounded-full bg-gray-200 text-gray-700 font-medium hover:bg-blue-100 transition-all duration-300" data-category="surgical">
                            الجراحة
                        </button>
                        <button class="category-btn px-6 py-3 rounded-full bg-gray-200 text-gray-700 font-medium hover:bg-blue-100 transition-all duration-300" data-category="pediatric">
                            طب الأطفال
                        </button>
                        <button class="category-btn px-6 py-3 rounded-full bg-gray-200 text-gray-700 font-medium hover:bg-blue-100 transition-all duration-300" data-category="specialized">
                            التخصصات الدقيقة
                        </button>
                    </div>
                </div>

                <!-- Enhanced Specialties Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php
                    // Enhanced specialty data with categories and descriptions
                    $enhanced_specialties = [
                        [
                            'name' => 'طب القلب',
                            'arabic_name' => 'طب القلب والأوعية الدموية',
                            'description' => 'تشخيص وعلاج أمراض القلب والأوعية الدموية',
                            'icon' => 'fas fa-heartbeat',
                            'category' => 'internal',
                            'doctor_count' => rand(15, 45),
                            'color' => 'from-red-500 to-red-600'
                        ],
                        [
                            'name' => 'طب الأعصاب',
                            'arabic_name' => 'طب الأعصاب والدماغ',
                            'description' => 'تشخيص وعلاج أمراض الجهاز العصبي',
                            'icon' => 'fas fa-brain',
                            'category' => 'internal',
                            'doctor_count' => rand(12, 35),
                            'color' => 'from-purple-500 to-purple-600'
                        ],
                        [
                            'name' => 'طب الجهاز الهضمي',
                            'arabic_name' => 'طب الجهاز الهضمي والكبد',
                            'description' => 'تشخيص وعلاج أمراض الجهاز الهضمي',
                            'icon' => 'fas fa-stomach',
                            'category' => 'internal',
                            'doctor_count' => rand(18, 40),
                            'color' => 'from-green-500 to-green-600'
                        ],
                        [
                            'name' => 'طب الرئة',
                            'arabic_name' => 'طب الرئة والجهاز التنفسي',
                            'description' => 'تشخيص وعلاج أمراض الجهاز التنفسي',
                            'icon' => 'fas fa-lungs',
                            'category' => 'internal',
                            'doctor_count' => rand(10, 30),
                            'color' => 'from-blue-500 to-blue-600'
                        ],
                        [
                            'name' => 'جراحة القلب',
                            'arabic_name' => 'جراحة القلب والصدر',
                            'description' => 'عمليات القلب المفتوح وجراحات الصدر',
                            'icon' => 'fas fa-heart',
                            'category' => 'surgical',
                            'doctor_count' => rand(8, 25),
                            'color' => 'from-red-600 to-red-700'
                        ],
                        [
                            'name' => 'جراحة المخ والأعصاب',
                            'arabic_name' => 'جراحة المخ والأعصاب',
                            'description' => 'عمليات المخ والعمود الفقري',
                            'icon' => 'fas fa-brain',
                            'category' => 'surgical',
                            'doctor_count' => rand(6, 20),
                            'color' => 'from-indigo-600 to-indigo-700'
                        ],
                        [
                            'name' => 'جراحة العظام',
                            'arabic_name' => 'جراحة العظام والمفاصل',
                            'description' => 'علاج كسور العظام وأمراض المفاصل',
                            'icon' => 'fas fa-bone',
                            'category' => 'surgical',
                            'doctor_count' => rand(20, 50),
                            'color' => 'from-yellow-600 to-yellow-700'
                        ],
                        [
                            'name' => 'طب الأطفال',
                            'arabic_name' => 'طب الأطفال العام',
                            'description' => 'رعاية الأطفال من الولادة حتى 18 سنة',
                            'icon' => 'fas fa-baby',
                            'category' => 'pediatric',
                            'doctor_count' => rand(25, 60),
                            'color' => 'from-pink-500 to-pink-600'
                        ],
                        [
                            'name' => 'طب الأسنان',
                            'arabic_name' => 'طب وجراحة الأسنان',
                            'description' => 'علاج الأسنان واللثة وتقويم الأسنان',
                            'icon' => 'fas fa-tooth',
                            'category' => 'specialized',
                            'doctor_count' => rand(30, 70),
                            'color' => 'from-teal-500 to-teal-600'
                        ],
                        [
                            'name' => 'طب العيون',
                            'arabic_name' => 'طب وجراحة العيون',
                            'description' => 'تشخيص وعلاج أمراض العيون',
                            'icon' => 'fas fa-eye',
                            'category' => 'specialized',
                            'doctor_count' => rand(15, 40),
                            'color' => 'from-cyan-500 to-cyan-600'
                        ],
                        [
                            'name' => 'طب الجلد',
                            'arabic_name' => 'طب الأمراض الجلدية',
                            'description' => 'تشخيص وعلاج أمراض الجلد',
                            'icon' => 'fas fa-user-md',
                            'category' => 'specialized',
                            'doctor_count' => rand(12, 35),
                            'color' => 'from-orange-500 to-orange-600'
                        ],
                        [
                            'name' => 'طب النساء',
                            'arabic_name' => 'طب النساء والتوليد',
                            'description' => 'رعاية صحة المرأة والحمل والولادة',
                            'icon' => 'fas fa-female',
                            'category' => 'specialized',
                            'doctor_count' => rand(20, 45),
                            'color' => 'from-rose-500 to-rose-600'
                        ]
                    ];

                    foreach ($enhanced_specialties as $specialty):
                    ?>
                        <div class="specialty-card bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 group" data-category="<?php echo $specialty['category']; ?>">
                            <div class="p-6">
                                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-r <?php echo $specialty['color']; ?> flex items-center justify-center text-white text-3xl group-hover:scale-110 transition-transform duration-300">
                                    <i class="<?php echo $specialty['icon']; ?>"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($specialty['arabic_name']); ?></h3>
                                <p class="text-gray-600 text-sm mb-4 leading-relaxed"><?php echo htmlspecialchars($specialty['description']); ?></p>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-blue-600 font-semibold text-sm">
                                        <i class="fas fa-user-md ml-1"></i>
                                        <?php echo $specialty['doctor_count']; ?> طبيب
                                    </span>
                                    <span class="text-green-600 font-semibold text-sm">
                                        <i class="fas fa-star ml-1"></i>
                                        <?php echo number_format(rand(45, 50) / 10, 1); ?>
                                    </span>
                                </div>
                                <a href="search.php?specialty=<?php echo urlencode($specialty['name']); ?>" class="block w-full py-3 px-4 bg-gradient-to-r <?php echo $specialty['color']; ?> text-white font-bold rounded-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                    ابحث عن طبيب
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- View All Specialties Button -->
                <div class="mt-12">
                    <a href="search.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold text-lg rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-search ml-3"></i>
                        عرض جميع التخصصات
                        <i class="fas fa-arrow-left mr-3"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Hospitals Section -->
        <section id="hospitals" class="py-20 bg-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl font-black text-gray-800 mb-4">أفضل المستشفيات والمراكز الطبية</h2>
                <p class="text-lg text-gray-600 mb-12 max-w-3xl mx-auto">تعرف على نخبة من المستشفيات والمراكز الطبية المتميزة التي تقدم أعلى مستويات الرعاية الصحية مع أحدث التقنيات الطبية.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php
                    // Sample hospital data (you can replace this with actual database data)
                    $hospitals_data = [
                        [
                            'name' => 'مستشفى الملك فهد',
                            'type' => 'حكومي',
                            'rating' => 4.8,
                            'location' => 'الرياض',
                            'specialties' => ['طب القلب', 'جراحة المخ', 'طب الأطفال'],
                            'image' => 'assets/images/hospital-1.jpg',
                            'description' => 'مستشفى متخصص في علاج أمراض القلب والجراحات المتقدمة'
                        ],
                        [
                            'name' => 'مركز الأمير سلطان الطبي',
                            'type' => 'خاص',
                            'rating' => 4.9,
                            'location' => 'جدة',
                            'specialties' => ['طب العيون', 'طب الأسنان', 'جراحة التجميل'],
                            'image' => 'assets/images/hospital-2.jpg',
                            'description' => 'مركز طبي متقدم في طب العيون وجراحات التجميل'
                        ],
                        [
                            'name' => 'مستشفى الملك خالد',
                            'type' => 'حكومي',
                            'rating' => 4.7,
                            'location' => 'الدمام',
                            'specialties' => ['طب الأعصاب', 'جراحة العظام', 'طب النساء'],
                            'image' => 'assets/images/hospital-3.jpg',
                            'description' => 'مستشفى متخصص في طب الأعصاب وجراحات العظام'
                        ]
                    ];

                    foreach ($hospitals_data as $hospital):
                        $type_color = $hospital['type'] === 'حكومي' ? 'from-green-500 to-green-600' : 'from-blue-500 to-blue-600';
                        $type_bg = $hospital['type'] === 'حكومي' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
                    ?>
                        <div class="hospital-card bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 group border border-gray-100">
                            <div class="relative">
                                <div class="h-48 bg-gradient-to-br <?php echo $type_color; ?> rounded-t-2xl flex items-center justify-center">
                                    <i class="fas fa-hospital text-white text-6xl group-hover:scale-110 transition-transform duration-300"></i>
                                </div>
                                <div class="absolute top-4 right-4">
                                    <span class="<?php echo $type_bg; ?> px-3 py-1 rounded-full text-sm font-medium">
                                        <?php echo $hospital['type']; ?>
                                    </span>
                                </div>
                                <div class="absolute top-4 left-4 flex items-center bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full">
                                    <i class="fas fa-star text-yellow-500 ml-1"></i>
                                    <span class="font-bold text-gray-800"><?php echo $hospital['rating']; ?></span>
                                </div>
                            </div>

                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($hospital['name']); ?></h3>
                                <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($hospital['description']); ?></p>

                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <i class="fas fa-map-marker-alt ml-1"></i>
                                    <span><?php echo $hospital['location']; ?></span>
                                </div>

                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">التخصصات الرئيسية:</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach (array_slice($hospital['specialties'], 0, 3) as $specialty): ?>
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                                                <?php echo $specialty; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <a href="hospital-details.php?name=<?php echo urlencode($hospital['name']); ?>" class="flex-1 py-3 px-4 bg-gradient-to-r <?php echo $type_color; ?> text-white font-bold rounded-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105 text-center">
                                        عرض التفاصيل
                                    </a>
                                    <a href="search.php?hospital=<?php echo urlencode($hospital['name']); ?>" class="flex-1 py-3 px-4 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition-all duration-300 text-center">
                                        ابحث عن طبيب
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- View All Hospitals Button -->
                <div class="mt-12">
                    <a href="hospitals.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-700 to-gray-800 text-white font-bold text-lg rounded-xl hover:from-gray-800 hover:to-gray-900 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-hospital ml-3"></i>
                        عرض جميع المستشفيات
                        <i class="fas fa-arrow-left mr-3"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section id="cta" class="bg-blue-600">
            <div class="container mx-auto px-4 py-20 text-center">
                <h2 class="text-4xl md:text-5xl font-black text-white">جاهز لبدء رحلتك نحو صحة أفضل؟</h2>
                <p class="text-lg text-blue-100 mt-4 max-w-2xl mx-auto">انضم إلى آلاف المستخدمين الذين يثقون في شفاء للعثور على الرعاية الصحية التي يستحقونها، أو ساهم بخبرتك وانضم إلينا كطبيب.</p>
                <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="search.php" class="w-full sm:w-auto bg-white text-blue-600 font-bold text-lg px-8 py-4 rounded-lg hover:bg-blue-50 transition-transform transform hover:scale-105 shadow-lg">
                        <i class="fas fa-search ml-2"></i> ابحث عن طبيب الآن
                    </a>
                    <a href="register.php?type=doctor" class="w-full sm:w-auto bg-green-500 text-white font-bold text-lg px-8 py-4 rounded-lg hover:bg-green-600 transition-transform transform hover:scale-105 shadow-lg">
                        <i class="fas fa-user-md ml-2"></i> انضم إلينا كطبيب
                    </a>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-black text-gray-800 mb-4">تواصل معنا</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">نحن هنا لمساعدتك! إذا كان لديك أي استفسارات أو تحتاج إلى مساعدة، لا تتردد في التواصل معنا.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-2xl shadow-lg">
                            <h3 class="text-2xl font-bold text-gray-800 mb-6">معلومات التواصل</h3>

                            <div class="space-y-6">
                                <div class="flex items-start space-x-4 space-x-reverse">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-phone text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-1">الهاتف</h4>
                                        <p class="text-gray-600">+20 1006429525</p>
                                        <p class="text-gray-600">+20 101 592 8525</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 space-x-reverse">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-envelope text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-1">البريد الإلكتروني</h4>
                                        <p class="text-gray-600">health.tech404@gmail.com</p>
                                        <p class="text-gray-600">support@shifa.com</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 space-x-reverse">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-1">العنوان</h4>
                                        <p class="text-gray-600">ايتاي البارود ، البحيرة</p>
                                        <p class="text-gray-600">دمنهور ، البحيرة</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 space-x-reverse">
                                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-1">ساعات العمل</h4>
                                        <p class="text-gray-600">الأحد - الخميس: 8:00 ص - 6:00 م</p>
                                        <p class="text-gray-600">الجمعة - السبت: 9:00 ص - 2:00 م</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="bg-white p-8 rounded-2xl shadow-lg">
                            <h3 class="text-2xl font-bold text-gray-800 mb-6">تابعنا على وسائل التواصل</h3>
                            <div class="flex justify-center space-x-6 space-x-reverse">
                                <a href="#" class="w-14 h-14 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl hover:bg-blue-700 transition-colors duration-300 transform hover:scale-110">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="w-14 h-14 bg-blue-400 rounded-full flex items-center justify-center text-white text-xl hover:bg-blue-500 transition-colors duration-300 transform hover:scale-110">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="w-14 h-14 bg-pink-600 rounded-full flex items-center justify-center text-white text-xl hover:bg-pink-700 transition-colors duration-300 transform hover:scale-110">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="w-14 h-14 bg-blue-700 rounded-full flex items-center justify-center text-white text-xl hover:bg-blue-800 transition-colors duration-300 transform hover:scale-110">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-white p-8 rounded-2xl shadow-lg">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">أرسل لنا رسالة</h3>

                        <form class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأول</label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300" placeholder="أدخل اسمك الأول">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم العائلة</label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300" placeholder="أدخل اسم العائلة">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                                <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300" placeholder="أدخل بريدك الإلكتروني">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                                <input type="tel" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300" placeholder="أدخل رقم هاتفك">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">نوع الاستفسار</label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                    <option value="">اختر نوع الاستفسار</option>
                                    <option value="technical">مشكلة تقنية</option>
                                    <option value="booking">مشكلة في الحجز</option>
                                    <option value="general">استفسار عام</option>
                                    <option value="partnership">شراكة أو تعاون</option>
                                    <option value="other">أخرى</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">الرسالة</label>
                                <textarea rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300" placeholder="اكتب رسالتك هنا..."></textarea>
                            </div>

                            <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold text-lg rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-paper-plane ml-2"></i>
                                إرسال الرسالة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About Section -->
                <div class="md:col-span-1">
                    <h3 class="text-2xl font-bold mb-4">شفاء</h3>
                    <p class="text-gray-400">منصة رائدة تهدف إلى تسهيل الوصول للرعاية الصحية عبر ربط المرضى بأفضل الأطباء والمستشفيات بكفاءة وسهولة.</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">روابط سريعة</h3>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition">المميزات</a></li>
                        <li><a href="#specialties" class="text-gray-400 hover:text-white transition">التخصصات</a></li>
                        <li><a href="search.php" class="text-gray-400 hover:text-white transition">ابحث عن طبيب</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">تواصل معنا</a></li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">تابعنا</h3>
                    <div class="flex space-x-4 space-x-reverse">
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin-in fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-500">
                <p>&copy; <?php echo date('Y'); ?> شفاء. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Enhanced Functionality -->
    <script>
        // Category filtering functionality
        document.addEventListener('DOMContentLoaded', function() {
            const categoryButtons = document.querySelectorAll('.category-btn');
            const specialtyCards = document.querySelectorAll('.specialty-card');

            // Category filter function
            function filterSpecialties(category) {
                specialtyCards.forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                        card.style.animation = 'fadeIn 0.5s ease-in-out';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Add click event to category buttons
            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    categoryButtons.forEach(btn => btn.classList.remove('active'));

                    // Add active class to clicked button
                    this.classList.add('active');

                    // Filter specialties
                    const category = this.dataset.category;
                    filterSpecialties(category);
                });
            });

            // Smooth scrolling for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add floating animation to specialty icons
            const specialtyIcons = document.querySelectorAll('.specialty-card i');
            specialtyIcons.forEach((icon, index) => {
                icon.style.animationDelay = `${index * 0.1}s`;
                icon.classList.add('floating-icon');
            });

            // Enhanced navbar scroll effect
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('header');
                if (window.scrollY > 100) {
                    navbar.classList.add('bg-white/95', 'shadow-xl');
                    navbar.classList.remove('bg-white/90', 'shadow-lg');
                } else {
                    navbar.classList.remove('bg-white/95', 'shadow-xl');
                    navbar.classList.add('bg-white/90', 'shadow-lg');
                }
            });

            // Add intersection observer for specialty cards
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all specialty cards
            specialtyCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .specialty-card {
                transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .category-btn {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .category-btn:hover {
                transform: translateY(-2px);
            }
        `;
        document.head.appendChild(style);
    </script>

</body>
</html>
