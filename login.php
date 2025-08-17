<?php
// Ensure no output before headers
ob_start();
session_start();
require_once 'includes/functions.php';

$error = '';
$email_value = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $email_value = htmlspecialchars($email); // Store email for pre-filling form

    if (empty($email) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        // Use existing database connection from config.php
        global $conn;

        if (!$conn) {
            $error = 'خطأ في الاتصال بقاعدة البيانات';
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Debug: Log user data (remove in production)
            error_log("Login attempt - Email: " . $email . ", User found: " . ($user ? 'Yes' : 'No'));

            if (!$user || $password !== $user['password']) {
                $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
                // Debug: Log password comparison (remove in production)
                if ($user) {
                    error_log("Password comparison - Input: " . $password . ", Stored: " . $user['password'] . ", Match: " . ($password === $user['password'] ? 'Yes' : 'No'));
                }
            } else {
                // Debug logging
                error_log("Login successful - User ID: " . $user['id'] . ", Role: " . $user['user_type']);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['role'] = $user['user_type']; // Add this for compatibility

                // Final, corrected redirection logic
                $user_role = $user['user_type'];
                error_log("Redirecting user with role: " . $user_role);

                if ($user_role === 'admin') {
                    error_log("Redirecting to admin dashboard");
                    header("Location: admin/index.php");
                    exit();
                }

                if ($user_role === 'doctor') {
                    error_log("Redirecting to doctor dashboard");
                    header("Location: doctor/index.php");
                    exit();
                }

                if ($user_role === 'user' || $user_role === 'patient') {
                    error_log("Redirecting to patient dashboard");
                    header("Location: patient/index.php");
                    exit();
                }

                // Default fallback if no role matches
                error_log("No role match, redirecting to index");
                header("Location: index.php");
                exit();
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
    <title>تسجيل الدخول - شفاء</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="image-section">
                <div class="image-content">
                    <img src="assets/images/doctor-illustration.png" alt="Doctor Illustration">
                    <div class="welcome-text">
                        <h2>أهلاً بعودتك!</h2>
                        <p>من فضلك أدخل بياناتك للمتابعة</p>
                    </div>
                </div>
            </div>
            <div class="form-section">
                <div class="form-content">
                    <div class="logo">
                        <i class="fas fa-heartbeat"></i>
                        <span>شفاء</span>
                    </div>
                    <h3>تسجيل الدخول</h3>
                    <form method="POST" action="login.php">
                        <div class="form-group">
                            <label for="email">اسم المستخدم أو البريد الإلكتروني</label>
                            <input type="email" id="email" name="email" value="<?php echo $email_value; ?>" required>
                        </div>
                        <div class="form-group <?php echo !empty($error) ? 'error' : ''; ?>">
                            <label for="password">كلمة المرور</label>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password" required>
                                <i class="fas fa-eye-slash toggle-password"></i>
                            </div>
                            <?php if (!empty($error)): ?>
                                <span class="error-message"><?php echo $error; ?></span>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn-login">تسجيل الدخول</button>
                    </form>
                    <div class="links">
                        <a href="forgot-password.php">نسيت كلمة المرور؟</a>
                        <p>ليس لديك حساب؟ <a href="register.php">سجل الآن</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('.toggle-password').addEventListener('click', function (e) {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
