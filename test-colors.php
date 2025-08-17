<?php
session_start();
require_once 'config.php';

// Handle theme selection for testing
if ($_POST && isset($_POST['test_theme'])) {
    $_SESSION['selected_theme'] = $_POST['test_theme'];
    $message = "Test theme applied! Refresh to see changes.";
}

$current_theme = $_SESSION['selected_theme'] ?? 'default';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Scheme Test - Medical Appointment System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/color-scheme.php">
    <style>
        body {
            background: var(--gradient-primary);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            transition: all 0.3s ease;
        }
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px var(--shadow-dark);
        }
        .color-swatch {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-block;
            margin: 0 5px;
            border: 2px solid white;
            box-shadow: 0 2px 8px var(--shadow-medium);
        }
    </style>
</head>
<body class="p-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4">
                <i class="fas fa-palette mr-3"></i>
                Color Scheme Test Page
            </h1>
            <p class="text-white text-lg opacity-90">
                Testing the dynamic color scheme system
            </p>
        </div>

        <?php if (isset($message)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-8 text-center">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Current Theme Info -->
        <div class="test-card p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                Current Theme: <?php echo ucfirst(str_replace('-', ' ', $current_theme)); ?>
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="color-swatch" style="background: var(--primary-color);"></div>
                    <p class="text-sm text-gray-600 mt-2">Primary</p>
                </div>
                <div class="text-center">
                    <div class="color-swatch" style="background: var(--secondary-color);"></div>
                    <p class="text-sm text-gray-600 mt-2">Secondary</p>
                </div>
                <div class="text-center">
                    <div class="color-swatch" style="background: var(--success-color);"></div>
                    <p class="text-sm text-gray-600 mt-2">Success</p>
                </div>
                <div class="text-center">
                    <div class="color-swatch" style="background: var(--warning-color);"></div>
                    <p class="text-sm text-gray-600 mt-2">Warning</p>
                </div>
            </div>
        </div>

        <!-- Quick Theme Test -->
        <div class="test-card p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Theme Test</h3>
            <form method="POST" class="flex flex-wrap gap-4">
                <button type="submit" name="test_theme" value="default"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Default
                </button>
                <button type="submit" name="test_theme" value="modern-blue"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Modern Blue
                </button>
                <button type="submit" name="test_theme" value="medical-green"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Medical Green
                </button>
                <button type="submit" name="test_theme" value="warm-orange"
                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                    Warm Orange
                </button>
                <button type="submit" name="test_theme" value="professional-gray"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg transition-colors">
                    Professional Gray
                </button>
                <button type="submit" name="test_theme" value="royal-purple"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    Royal Purple
                </button>
            </form>
        </div>

        <!-- Color Examples -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Buttons Example -->
            <div class="test-card p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Button Examples</h3>
                <div class="space-y-3">
                    <button class="w-full py-3 px-6 rounded-lg text-white font-semibold transition-all duration-300 transform hover:scale-105" style="background: var(--gradient-primary);">
                        Primary Button
                    </button>
                    <button class="w-full py-3 px-6 rounded-lg text-white font-semibold transition-all duration-300 transform hover:scale-105" style="background: var(--gradient-success);">
                        Success Button
                    </button>
                    <button class="w-full py-3 px-6 rounded-lg text-white font-semibold transition-all duration-300 transform hover:scale-105" style="background: var(--gradient-warning);">
                        Warning Button
                    </button>
                    <button class="w-full py-3 px-6 rounded-lg text-white font-semibold transition-all duration-300 transform hover:scale-105" style="background: var(--gradient-danger);">
                        Danger Button
                    </button>
                </div>
            </div>

            <!-- Text Examples -->
            <div class="test-card p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Text Color Examples</h3>
                <div class="space-y-3">
                    <p style="color: var(--text-primary); font-weight: 600;">Primary Text Color</p>
                    <p style="color: var(--text-secondary); font-weight: 600;">Secondary Text Color</p>
                    <p style="color: var(--text-muted); font-weight: 600;">Muted Text Color</p>
                    <p style="color: var(--primary-color); font-weight: 600;">Primary Brand Color</p>
                    <p style="color: var(--secondary-color); font-weight: 600;">Secondary Brand Color</p>
                </div>
            </div>

            <!-- Status Examples -->
            <div class="test-card p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Status Examples</h3>
                <div class="space-y-3">
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold" style="background: var(--status-confirmed-bg); color: var(--status-confirmed-text);">
                        Confirmed
                    </span>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold" style="background: var(--status-completed-bg); color: var(--status-completed-text);">
                        Completed
                    </span>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold" style="background: var(--status-cancelled-bg); color: var(--status-cancelled-text);">
                        Cancelled
                    </span>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold" style="background: var(--status-pending-bg); color: var(--status-pending-text);">
                        Pending
                    </span>
                </div>
            </div>

            <!-- Gradient Examples -->
            <div class="test-card p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Gradient Examples</h3>
                <div class="space-y-3">
                    <div class="h-12 rounded-lg" style="background: var(--gradient-primary);"></div>
                    <div class="h-12 rounded-lg" style="background: var(--gradient-success);"></div>
                    <div class="h-12 rounded-lg" style="background: var(--gradient-warning);"></div>
                    <div class="h-12 rounded-lg" style="background: var(--gradient-danger);"></div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center mt-8">
            <a href="color-customizer.php" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 mr-4">
                <i class="fas fa-palette mr-2"></i>
                Color Customizer
            </a>
            <a href="patient/index.php" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 mr-4">
                <i class="fas fa-eye mr-2"></i>
                Patient Dashboard
            </a>
            <a href="index.php" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-home mr-2"></i>
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>
