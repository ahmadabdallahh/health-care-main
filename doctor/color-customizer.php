<?php
session_start();
require_once '../config.php';

// Handle color theme selection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_theme'])) {
    $_SESSION['doctor_theme'] = $_POST['selected_theme'];
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'profile.php');
    exit();
}

$current_theme = $_SESSION['doctor_theme'] ?? 'medical-blue';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تخصيص ألوان لوحة التحكم الطبية</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">تخصيص ألوان لوحة التحكم الطبية</h1>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-xl font-semibold mb-6">اختر نمط الألوان المفضل لديك</h2>

            <form method="POST" class="space-y-6">
                <!-- Medical Blue Theme -->
                <div class="theme-option border-2 border-blue-200 rounded-lg p-4 hover:border-blue-400 transition-all">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="selected_theme" value="medical-blue"
                               <?php echo $current_theme === 'medical-blue' ? 'checked' : ''; ?> class="mr-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-blue-800">الأزرق الطبي</h3>
                            <p class="text-gray-600 mb-3">الألوان التقليدية للمجال الطبي - أزرق هادئ ومهني</p>
                            <div class="flex space-x-2 space-x-reverse">
                                <div class="w-8 h-8 bg-blue-500 rounded"></div>
                                <div class="w-8 h-8 bg-blue-600 rounded"></div>
                                <div class="w-8 h-8 bg-blue-700 rounded"></div>
                                <div class="w-8 h-8 bg-blue-100 rounded"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Medical Green Theme -->
                <div class="theme-option border-2 border-green-200 rounded-lg p-4 hover:border-green-400 transition-all">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="selected_theme" value="medical-green"
                               <?php echo $current_theme === 'medical-green' ? 'checked' : ''; ?> class="mr-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-green-800">الأخضر الطبي</h3>
                            <p class="text-gray-600 mb-3">أخضر مريح للعين - مناسب للعمل الطويل</p>
                            <div class="flex space-x-2 space-x-reverse">
                                <div class="w-8 h-8 bg-green-500 rounded"></div>
                                <div class="w-8 h-8 bg-green-600 rounded"></div>
                                <div class="w-8 h-8 bg-green-700 rounded"></div>
                                <div class="w-8 h-8 bg-green-100 rounded"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Warm Orange Theme -->
                <div class="theme-option border-2 border-orange-200 rounded-lg p-4 hover:border-orange-400 transition-all">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="selected_theme" value="warm-orange"
                               <?php echo $current_theme === 'warm-orange' ? 'checked' : ''; ?> class="mr-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-orange-800">البرتقالي الدافئ</h3>
                            <p class="text-gray-600 mb-3">برتقالي دافئ ومريح - يبعث على الراحة</p>
                            <div class="flex space-x-2 space-x-reverse">
                                <div class="w-8 h-8 bg-orange-500 rounded"></div>
                                <div class="w-8 h-8 bg-orange-600 rounded"></div>
                                <div class="w-8 h-8 bg-orange-700 rounded"></div>
                                <div class="w-8 h-8 bg-orange-100 rounded"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Professional Gray Theme -->
                <div class="theme-option border-2 border-gray-200 rounded-lg p-4 hover:border-gray-400 transition-all">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="selected_theme" value="professional-gray"
                               <?php echo $current_theme === 'professional-gray' ? 'checked' : ''; ?> class="mr-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-gray-800">الرمادي المهني</h3>
                            <p class="text-gray-600 mb-3">رمادي أنيق ومهني - مناسب للمكاتب</p>
                            <div class="flex space-x-2 space-x-reverse">
                                <div class="w-8 h-8 bg-gray-500 rounded"></div>
                                <div class="w-8 h-8 bg-gray-600 rounded"></div>
                                <div class="w-8 h-8 bg-gray-700 rounded"></div>
                                <div class="w-8 h-8 bg-gray-100 rounded"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Royal Purple Theme -->
                <div class="theme-option border-2 border-purple-200 rounded-lg p-4 hover:border-purple-400 transition-all">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="selected_theme" value="royal-purple"
                               <?php echo $current_theme === 'royal-purple' ? 'checked' : ''; ?> class="mr-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-purple-800">البنفسجي الملكي</h3>
                            <p class="text-gray-600 mb-3">بنفسجي أنيق ومميز - لمسة من الفخامة</p>
                            <div class="flex space-x-2 space-x-reverse">
                                <div class="w-8 h-8 bg-purple-500 rounded"></div>
                                <div class="w-8 h-8 bg-purple-600 rounded"></div>
                                <div class="w-8 h-8 bg-purple-700 rounded"></div>
                                <div class="w-8 h-8 bg-purple-100 rounded"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Emergency Red Theme -->
                <div class="theme-option border-2 border-red-200 rounded-lg p-4 hover:border-red-400 transition-all">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="selected_theme" value="emergency-red"
                               <?php echo $current_theme === 'emergency-red' ? 'checked' : ''; ?> class="mr-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-red-800">الأحمر الطوارئ</h3>
                            <p class="text-gray-600 mb-3">أحمر ديناميكي - مناسب لقسم الطوارئ</p>
                            <div class="flex space-x-2 space-x-reverse">
                                <div class="w-8 h-8 bg-red-500 rounded"></div>
                                <div class="w-8 h-8 bg-red-600 rounded"></div>
                                <div class="w-8 h-8 bg-red-700 rounded"></div>
                                <div class="w-8 h-8 bg-red-100 rounded"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="flex gap-4 pt-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-palette mr-2"></i>تطبيق الألوان
                    </button>
                    <a href="profile.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>العودة للوحة التحكم
                    </a>
                </div>
            </form>
        </div>

        <!-- Preview Section -->
        <div class="bg-white rounded-lg shadow-lg p-8 mt-8">
            <h2 class="text-xl font-semibold mb-6">معاينة الألوان</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Sample Cards -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-lg">
                    <h3 class="font-semibold">بطاقة إحصائية</h3>
                    <p class="text-2xl font-bold">25</p>
                    <p>مواعيد اليوم</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg">
                    <h3 class="font-semibold">مريض جديد</h3>
                    <p class="text-2xl font-bold">3</p>
                    <p>في الانتظار</p>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-4 rounded-lg">
                    <h3 class="font-semibold">تنبيه عاجل</h3>
                    <p class="text-sm">مريض في حالة طوارئ</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Highlight selected theme
    document.querySelectorAll('input[name="selected_theme"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove all highlights
            document.querySelectorAll('.theme-option').forEach(option => {
                option.classList.remove('border-blue-500', 'border-green-500', 'border-orange-500', 'border-gray-500', 'border-purple-500', 'border-red-500');
                option.classList.add('border-gray-200');
            });

            // Highlight selected theme
            const selectedOption = this.closest('.theme-option');
            const theme = this.value;

            if (theme === 'medical-blue') selectedOption.classList.replace('border-gray-200', 'border-blue-500');
            else if (theme === 'medical-green') selectedOption.classList.replace('border-gray-200', 'border-green-500');
            else if (theme === 'warm-orange') selectedOption.classList.replace('border-gray-200', 'border-orange-500');
            else if (theme === 'professional-gray') selectedOption.classList.replace('border-gray-200', 'border-gray-500');
            else if (theme === 'royal-purple') selectedOption.classList.replace('border-gray-200', 'border-purple-500');
            else if (theme === 'emergency-red') selectedOption.classList.replace('border-gray-200', 'border-red-500');
        });
    });

    // Initialize highlight for current theme
    document.addEventListener('DOMContentLoaded', function() {
        const currentTheme = '<?php echo $current_theme; ?>';
        const radio = document.querySelector(`input[value="${currentTheme}"]`);
        if (radio) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        }
    });
    </script>
</body>
</html>
