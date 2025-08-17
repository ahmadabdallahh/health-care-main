<?php
session_start();
require_once 'config.php';

// Handle theme selection
if ($_POST && isset($_POST['theme'])) {
    $selected_theme = $_POST['theme'];
    $_SESSION['selected_theme'] = $selected_theme;
    $message = "Theme updated successfully! Refresh the patient dashboard to see changes.";
}

$current_theme = $_SESSION['selected_theme'] ?? 'default';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Scheme Customizer - Medical Appointment System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .theme-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        .theme-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .color-preview {
            width: 100%;
            height: 120px;
            border-radius: 15px;
            margin-bottom: 15px;
        }
        .color-swatch {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-block;
            margin: 0 5px;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="p-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4">
                <i class="fas fa-palette mr-3"></i>
                Color Scheme Customizer
            </h1>
            <p class="text-white text-lg opacity-90">
                Choose your preferred color scheme for the Medical Appointment System
            </p>
        </div>

        <?php if (isset($message)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-8 text-center">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Theme Selection Form -->
        <form method="POST" class="mb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Modern Blue Theme -->
                <div class="theme-card p-6">
                    <div class="color-preview" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);"></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Modern Blue</h3>
                    <div class="mb-4">
                        <span class="color-swatch" style="background: #2563eb;"></span>
                        <span class="color-swatch" style="background: #7c3aed;"></span>
                        <span class="color-swatch" style="background: #059669;"></span>
                        <span class="color-swatch" style="background: #d97706;"></span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Professional blue and purple gradient with green accents</p>
                    <label class="flex items-center">
                        <input type="radio" name="theme" value="modern-blue"
                               <?php echo $current_theme === 'modern-blue' ? 'checked' : ''; ?>
                               class="mr-3">
                        <span class="text-gray-700 font-medium">Select This Theme</span>
                    </label>
                </div>

                <!-- Medical Green Theme -->
                <div class="theme-card p-6">
                    <div class="color-preview" style="background: linear-gradient(135deg, #059669 0%, #0891b2 100%);"></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Medical Green</h3>
                    <div class="mb-4">
                        <span class="color-swatch" style="background: #059669;"></span>
                        <span class="color-swatch" style="background: #0891b2;"></span>
                        <span class="color-swatch" style="background: #10b981;"></span>
                        <span class="color-swatch" style="background: #f59e0b;"></span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Healthcare-focused green and teal with warm accents</p>
                    <label class="flex items-center">
                        <input type="radio" name="theme" value="medical-green"
                               <?php echo $current_theme === 'medical-green' ? 'checked' : ''; ?>
                               class="mr-3">
                        <span class="text-gray-700 font-medium">Select This Theme</span>
                    </label>
                </div>

                <!-- Warm Orange Theme -->
                <div class="theme-card p-6">
                    <div class="color-preview" style="background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);"></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Warm Orange</h3>
                    <div class="mb-4">
                        <span class="color-swatch" style="background: #ea580c;"></span>
                        <span class="color-swatch" style="background: #dc2626;"></span>
                        <span class="color-swatch" style="background: #fb923c;"></span>
                        <span class="color-swatch" style="background: #059669;"></span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Energetic orange and red with green balance</p>
                    <label class="flex items-center">
                        <input type="radio" name="theme" value="warm-orange"
                               <?php echo $current_theme === 'warm-orange' ? 'checked' : ''; ?>
                               class="mr-3">
                        <span class="text-gray-700 font-medium">Select This Theme</span>
                    </label>
                </div>

                <!-- Professional Gray Theme -->
                <div class="theme-card p-6">
                    <div class="color-preview" style="background: linear-gradient(135deg, #374151 0%, #4b5563 100%);"></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Professional Gray</h3>
                    <div class="mb-4">
                        <span class="color-swatch" style="background: #374151;"></span>
                        <span class="color-swatch" style="background: #4b5563;"></span>
                        <span class="color-swatch" style="background: #6b7280;"></span>
                        <span class="color-swatch" style="background: #059669;"></span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Sophisticated gray tones with green highlights</p>
                    <label class="flex items-center">
                        <input type="radio" name="theme" value="professional-gray"
                               <?php echo $current_theme === 'professional-gray' ? 'checked' : ''; ?>
                               class="mr-3">
                        <span class="text-gray-700 font-medium">Select This Theme</span>
                    </label>
                </div>

                <!-- Royal Purple Theme -->
                <div class="theme-card p-6">
                    <div class="color-preview" style="background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);"></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Royal Purple</h3>
                    <div class="mb-4">
                        <span class="color-swatch" style="background: #7c3aed;"></span>
                        <span class="color-swatch" style="background: #ec4899;"></span>
                        <span class="color-swatch" style="background: #8b5cf6;"></span>
                        <span class="color-swatch" style="background: #f472b6;"></span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Luxurious purple and pink gradient</p>
                    <label class="flex items-center">
                        <input type="radio" name="theme" value="royal-purple"
                               <?php echo $current_theme === 'royal-purple' ? 'checked' : ''; ?>
                               class="mr-3">
                        <span class="text-gray-700 font-medium">Select This Theme</span>
                    </label>
                </div>

                <!-- Current Default Theme -->
                <div class="theme-card p-6">
                    <div class="color-preview" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Current Default</h3>
                    <div class="mb-4">
                        <span class="color-swatch" style="background: #667eea;"></span>
                        <span class="color-swatch" style="background: #764ba2;"></span>
                        <span class="color-swatch" style="background: #48bb78;"></span>
                        <span class="color-swatch" style="background: #ed8936;"></span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Original purple-blue gradient theme</p>
                    <label class="flex items-center">
                        <input type="radio" name="theme" value="default"
                               <?php echo $current_theme === 'default' ? 'checked' : ''; ?>
                               class="mr-3">
                        <span class="text-gray-700 font-medium">Select This Theme</span>
                    </label>
                </div>

            </div>

            <!-- Submit Button -->
            <div class="text-center mt-8">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-save mr-2"></i>
                    Apply Selected Theme
                </button>
            </div>
        </form>

        <!-- Instructions -->
        <div class="theme-card p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                How to Use
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">1. Select a Theme</h3>
                    <p class="text-gray-600">Choose from the predefined color schemes above. Each theme includes:</p>
                    <ul class="text-gray-600 text-sm mt-2 space-y-1">
                        <li>• Primary and secondary brand colors</li>
                        <li>• Success, warning, and danger states</li>
                        <li>• Text and background colors</li>
                        <li>• Gradient combinations</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">2. Apply Changes</h3>
                    <p class="text-gray-600">After selecting a theme:</p>
                    <ul class="text-gray-600 text-sm mt-2 space-y-1">
                        <li>• Click "Apply Selected Theme"</li>
                        <li>• Navigate to the patient dashboard</li>
                        <li>• Refresh the page to see changes</li>
                        <li>• All pages will use the new colors</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center mt-8">
            <a href="patient/index.php" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 mr-4">
                <i class="fas fa-eye mr-2"></i>
                View Patient Dashboard
            </a>
            <a href="index.php" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-home mr-2"></i>
                Back to Home
            </a>
        </div>
    </div>

    <script>
        // Preview theme changes
        document.querySelectorAll('input[name="theme"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove active class from all cards
                document.querySelectorAll('.theme-card').forEach(card => {
                    card.classList.remove('ring-2', 'ring-blue-500');
                });

                // Add active class to selected card
                if (this.checked) {
                    this.closest('.theme-card').classList.add('ring-2', 'ring-blue-500');
                }
            });
        });

        // Initialize active state
        document.querySelector('input[name="theme"]:checked')?.closest('.theme-card').classList.add('ring-2', 'ring-blue-500');
    </script>
</body>
</html>
