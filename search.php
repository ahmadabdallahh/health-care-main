<?php
session_start();
require_once 'config.php';
require_once 'includes/functions.php';

// Get search parameters
$search_query = isset($_GET['q']) ? clean_input($_GET['q']) : '';
$specialty_id = isset($_GET['specialty']) ? (int)$_GET['specialty'] : 0;

// Fetch search results
$is_searching = !empty($search_query) || !empty($specialty_id);
$doctors = search_doctors($search_query, $specialty_id);

// Get all specialties for the filter dropdown
$specialties = get_all_specialties();

$pageTitle = 'نتائج البحث';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - شفاء</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .space-x-reverse > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 1; margin-right: calc(1rem * var(--tw-space-x-reverse)); margin-left: calc(1rem * calc(1 - var(--tw-space-x-reverse))); }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-3xl font-black text-blue-600">شفاء</a>
            <div class="hidden md:flex items-center space-x-8 space-x-reverse">
                <a href="index.php#features" class="text-gray-600 hover:text-blue-600 transition">المميزات</a>
                <a href="index.php#specialties" class="text-gray-600 hover:text-blue-600 transition">التخصصات</a>
                <a href="index.php#contact" class="text-gray-600 hover:text-blue-600 transition">تواصل معنا</a>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <?php if (isset($_SESSION['user_name'])): ?>
                    <a href="<?php echo ($_SESSION['user_type'] === 'doctor' ? 'doctor' : 'patient'); ?>/index.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">لوحة التحكم</a>
                    <a href="logout.php" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition">تسجيل الخروج</a>
                <?php else: ?>
                    <a href="login.php" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">تسجيل الدخول</a>
                    <a href="register.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">إنشاء حساب</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- Hero Section with Search -->
    <section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white shadow-inner">
        <div class="container mx-auto px-4 py-16 text-center">
            <h1 class="text-4xl md:text-5xl font-black mb-3">ابحث عن أفضل الأطباء</h1>
            <p class="text-lg md:text-xl text-blue-200 mb-8">احجز موعدك بكل سهولة وسرعة من بين نخبة من الأطباء.</p>

            <div class="max-w-4xl mx-auto bg-white/10 backdrop-blur-sm p-4 rounded-xl shadow-lg border border-white/20">
                <form action="search.php" method="get" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    <div class="md:col-span-5 relative">
                        <i class="fas fa-user-md absolute top-1/2 right-4 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="q" id="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="اسم الطبيب أو العيادة..." class="w-full p-4 pr-12 text-gray-800 bg-white border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                    </div>
                    <div class="md:col-span-4 relative">
                        <i class="fas fa-stethoscope absolute top-1/2 right-4 transform -translate-y-1/2 text-gray-400"></i>
                        <select name="specialty" id="specialty" class="w-full p-4 pr-12 text-gray-800 bg-white border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 appearance-none cursor-pointer">
                            <option value="">كل التخصصات</option>
                            <?php foreach ($specialties as $spec): ?>
                                <option value="<?php echo $spec['id']; ?>" <?php if ($specialty_id == $spec['id']) echo 'selected'; ?>><?php echo htmlspecialchars($spec['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-chevron-down absolute top-1/2 left-4 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                    <div class="md:col-span-3 flex space-x-2 space-x-reverse">
                        <button type="submit" class="w-full flex-1 bg-orange-500 text-white py-4 px-6 rounded-lg font-bold hover:bg-orange-600 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5"><i class="fas fa-search ml-2"></i>بحث</button>
                        <a href="search.php" class="bg-white/20 text-white py-4 px-5 rounded-lg hover:bg-white/30 transition" title="مسح الفلاتر">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Main Content: Search Results -->
    <main class="container mx-auto px-4 py-10">
        <section>
            <h2 class="text-3xl font-bold text-gray-800 mb-8">نتائج البحث <span class="text-blue-600">(<?php echo count($doctors); ?>)</span></h2>

            <?php if ($is_searching && !empty($doctors)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($doctors as $doctor): ?>
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:-translate-y-2 transition-all duration-300 group">
                            <div class="relative">
                                <img src="<?php echo htmlspecialchars($doctor['profile_image'] ?? 'assets/images/default-avatar.png'); ?>" alt="<?php echo htmlspecialchars($doctor['full_name']); ?>" class="w-full h-48 object-cover">
                                <div class="absolute top-4 right-4 bg-blue-600 text-white text-sm font-bold px-3 py-1 rounded-full shadow-md">
                                    <?php echo htmlspecialchars($doctor['specialty_name']); ?>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <h3 class="text-xl font-bold text-gray-900 flex-1"><?php echo htmlspecialchars($doctor['full_name']); ?></h3>
                                    <div class="text-yellow-500 flex items-center">
                                        <i class="fas fa-star"></i>
                                        <span class="text-gray-700 font-bold mr-1">4.8</span>
                                    </div>
                                </div>
                                <div class="space-y-3 text-gray-600">
                                    <p class="flex items-center"><i class="fas fa-clinic-medical text-gray-400 ml-3"></i> <?php echo htmlspecialchars($doctor['clinic_name'] ?? 'عيادة خاصة'); ?></p>
                                    <p class="flex items-center"><i class="fas fa-map-marker-alt text-gray-400 ml-3"></i> <?php echo htmlspecialchars($doctor['clinic_address'] ?? 'العنوان غير متوفر'); ?></p>
                                </div>
                                <div class="mt-6">
                                    <a href="/app-demo-test/doctor_profile.php?id=<?php echo $doctor['id']; ?>" class="block w-full text-center bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition transform group-hover:scale-105">
                                        عرض الملف الشخصي والحجز
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($is_searching): ?>
                <div class="text-center bg-white p-16 rounded-xl shadow-lg border">
                    <div class="w-24 h-24 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search-minus fa-3x"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">لا توجد نتائج مطابقة لبحثك</h3>
                    <p class="text-gray-500 mt-2 max-w-md mx-auto">حاول تعديل كلمات البحث أو تغيير الفلاتر للحصول على نتائج أفضل. قد يكون الطبيب الذي تبحث عنه غير متاح حالياً.</p>
                </div>
            <?php else: ?>
                 <div class="text-center bg-white p-16 rounded-xl shadow-lg border">
                    <div class="w-24 h-24 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-hand-pointer fa-3x"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">ابدأ البحث عن طبيبك المثالي</h3>
                    <p class="text-gray-500 mt-2 max-w-md mx-auto">استخدم شريط البحث في الأعلى للعثور على الطبيب المناسب حسب الاسم أو التخصص.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="border-t border-gray-700 pt-6 text-center text-gray-500">
                <p>&copy; <?php echo date('Y'); ?> شفاء. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

</body>
</html>
