<?php
session_start();

// Mark this page as a Tailwind CSS page to prevent old CSS from loading.
$use_tailwind = true;

require_once 'config.php';
require_once 'includes/functions.php';

// Get doctor ID from URL
$doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($doctor_id === 0) {
    header('Location: search.php');
    exit();
}

// Fetch doctor details
$doctor = get_doctor_by_id($doctor_id);

$pageTitle = $doctor ? 'ملف الطبيب: ' . htmlspecialchars($doctor['full_name']) : 'خطأ';

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts (Cairo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <script>
        // Custom Tailwind Config
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        .prose {
            text-align: right;
        }
    </style>
</head>
<body class="font-cairo bg-slate-50">

<?php require_once 'includes/header.php'; ?>

<?php if (!$doctor): ?>
    <div class='container mx-auto px-4 py-16 text-center'>
        <h1 class='text-3xl font-bold text-red-600'>لم يتم العثور على الطبيب</h1>
        <p class='text-gray-600 mt-4'>قد يكون الرابط غير صحيح أو تم حذف الطبيب.</p>
        <a href='search.php' class='mt-6 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg'>العودة للبحث</a>
    </div>
<?php else: ?>
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Doctor Profile Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 flex flex-col md:flex-row gap-8">
            <!-- Doctor Image -->
            <div class="flex-shrink-0 text-center md:text-right">
                <img src="<?php echo htmlspecialchars($doctor['image'] ?? 'assets/images/default-avatar.png'); ?>"
                     alt="صورة الطبيب <?php echo htmlspecialchars($doctor['full_name']); ?>"
                     class="w-40 h-40 rounded-full object-cover mx-auto md:mx-0 border-4 border-blue-100 shadow-lg">
            </div>

            <div class="flex-grow text-center md:text-right">
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800"><?php echo htmlspecialchars($doctor['full_name']); ?></h1>
                <p class="text-lg font-semibold text-blue-600 mt-2"><?php echo htmlspecialchars($doctor['specialty_name']); ?></p>

                <!-- Rating -->
                <div class="flex items-center justify-center md:justify-start mt-4 text-yellow-500 space-x-1 space-x-reverse">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                    <span class="text-gray-600 font-bold mr-2 text-md">4.8</span>
                    <span class="text-gray-500 text-sm">(120 تقييم)</span>
                </div>

                <!-- Clinic Info -->
                <div class="mt-5 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center gap-4 text-gray-600">
                    <div class="flex items-center justify-center md:justify-start">
                        <i class="fas fa-clinic-medical text-blue-500 fa-lg ml-3"></i>
                        <span class="font-semibold"><?php echo htmlspecialchars($doctor['clinic_name']); ?></span>
                    </div>
                    <div class="flex items-center justify-center md:justify-start">
                        <i class="fas fa-map-marker-alt text-blue-500 fa-lg ml-3"></i>
                        <span><?php echo htmlspecialchars($doctor['clinic_address']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Booking Section -->
            <div class="md:border-r md:border-gray-200 md:pr-8 flex-shrink-0 text-center">
                <div class="bg-slate-50 rounded-lg p-5">
                    <h3 class="text-lg font-bold text-gray-700">سعر الكشف</h3>
                    <p class="text-4xl font-black text-blue-600 my-2">
                        <?php
                        if (!empty($doctor['consultation_fee']) && is_numeric($doctor['consultation_fee'])) {
                            echo htmlspecialchars($doctor['consultation_fee']) . ' <span class="text-xl font-bold">جنيه</span>';
                        } else {
                            echo '<span class="text-2xl font-semibold">يُحدد لاحقًا</span>';
                        }
                        ?>
                    </p>
                    <a href="book_appointment.php?doctor_id=<?php echo $doctor['id']; ?>"
                       class="block w-full text-center bg-orange-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-orange-600 transition transform hover:-translate-y-1 shadow-lg">
                        <i class="fas fa-calendar-check ml-2"></i>
                        احجز موعد الآن
                    </a>
                </div>
            </div>
        </div>

        <!-- Doctor Details Tabs -->
        <div class="mt-12">
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8">
                <!-- Tabs Navigation (Future Feature) -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-6 space-x-reverse" aria-label="Tabs">
                        <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg text-blue-600 border-blue-500">
                            عن الطبيب
                        </a>
                        <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300">
                            آراء المرضى
                        </a>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">نبذة تعريفية</h2>
                    <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed">
                        <p>
                            <?php echo nl2br(htmlspecialchars($doctor['bio'] ?? 'لا توجد نبذة تعريفية متاحة حالياً. الطبيب متخصص في تقديم أفضل رعاية صحية للمرضى باستخدام أحدث التقنيات والأساليب الطبية.')); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </main>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

</body>
</html>
