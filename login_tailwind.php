<?php
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        // اختبار الاتصال بقاعدة البيانات
        $db = new Database();
        $conn = $db->getConnection();

        if (!$conn) {
            $error = 'خطأ في الاتصال بقاعدة البيانات';
        } else {
            // التحقق من وجود المستخدم
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                $error = 'البريد الإلكتروني غير موجود';
            } elseif (!verify_password($password, $user['password'])) {
                $error = 'كلمة المرور غير صحيحة';
            } else {
                if (login_user($email, $password)) {
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = 'خطأ في تسجيل الدخول';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - مستشفى الأمل</title>
    <link rel="stylesheet" href="assets/css/tailwind.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-hospital-primary to-blue-700 min-h-screen flex items-center justify-center p-4">
    <!-- Back to Home -->
    <a href="index_tailwind.php" class="absolute top-6 right-6 text-white hover:text-blue-200 transition-colors flex items-center space-x-2 space-x-reverse">
        <i class="fas fa-arrow-right"></i>
        <span>العودة للرئيسية</span>
    </a>

    <!-- Login Card -->
    <div class="bg-white rounded-hospital shadow-hospital-hover p-8 w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="hospital-icon mx-auto mb-4">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">تسجيل الدخول</h1>
            <p class="text-gray-600">أدخل بياناتك للوصول إلى حسابك</p>
        </div>

        <!-- Error/Success Messages -->
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-hospital mb-6 flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-hospital mb-6 flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $success; ?></span>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="" class="space-y-6">
            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">البريد الإلكتروني</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-hospital focus:ring-2 focus:ring-hospital-primary focus:border-transparent transition-all"
                        placeholder="أدخل بريدك الإلكتروني"
                        required
                    >
                </div>
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">كلمة المرور</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-hospital focus:ring-2 focus:ring-hospital-primary focus:border-transparent transition-all"
                        placeholder="أدخل كلمة المرور"
                        required
                    >
                </div>
            </div>

            <!-- Forgot Password -->
            <div class="text-left">
                <a href="forgot-password.php" class="text-sm text-hospital-primary hover:text-blue-700 transition-colors">
                    نسيت كلمة المرور؟
                </a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="hospital-btn-primary w-full text-lg py-4">
                <i class="fas fa-sign-in-alt ml-2"></i>
                تسجيل الدخول
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">أو</span>
            </div>
        </div>

        <!-- Social Login -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <a href="#" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-hospital text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fab fa-google text-red-500 ml-2"></i>
                جوجل
            </a>
            <a href="#" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-hospital text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fab fa-facebook-f text-blue-600 ml-2"></i>
                فيسبوك
            </a>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <p class="text-gray-600">
                ليس لديك حساب؟
                <a href="register.php" class="text-hospital-primary hover:text-blue-700 font-semibold transition-colors">
                    سجل الآن
                </a>
            </p>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
