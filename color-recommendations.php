<?php
session_start();
require_once 'config.php';

// Handle theme selection
if ($_POST && isset($_POST['recommended_theme'])) {
    $_SESSION['selected_theme'] = $_POST['recommended_theme'];
    $message = "Recommended theme applied! All patient pages will now use this color scheme.";
}

$current_theme = $_SESSION['selected_theme'] ?? 'default';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Recommendations - Medical Appointment System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .recommendation-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .recommendation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .recommendation-card.recommended {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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
        .pros-cons {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
        }
        .pros-cons h4 {
            font-weight: 600;
            margin-bottom: 10px;
        }
        .pros {
            color: #059669;
        }
        .cons {
            color: #dc2626;
        }
    </style>
</head>
<body class="p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4">
                <i class="fas fa-palette mr-3"></i>
                Color Scheme Recommendations
            </h1>
            <p class="text-white text-lg opacity-90">
                Choose the perfect color scheme for your Medical Appointment System
            </p>
        </div>

        <?php if (isset($message)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-8 text-center">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Current Theme Info -->
        <div class="recommendation-card p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                Current Theme: <?php echo ucfirst(str_replace('-', ' ', $current_theme)); ?>
            </h2>
            <p class="text-gray-600">All patient pages are currently using this color scheme.</p>
        </div>

        <!-- Color Recommendations -->
        <form method="POST" class="mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Medical Green - RECOMMENDED -->
                <div class="recommendation-card p-6 recommended">
                    <div class="flex items-center mb-4">
                        <div class="color-preview" style="background: linear-gradient(135deg, #059669 0%, #0891b2 100%);"></div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Medical Green</h3>
                            <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-semibold">
                                <i class="fas fa-star mr-1"></i>RECOMMENDED
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="color-swatch" style="background: #059669;"></span>
                        <span class="color-swatch" style="background: #0891b2;"></span>
                        <span class="color-swatch" style="background: #10b981;"></span>
                        <span class="color-swatch" style="background: #f59e0b;"></span>
                    </div>

                    <p class="text-gray-600 text-sm mb-4">Perfect for healthcare applications. Green represents health, healing, and trust.</p>

                    <div class="pros-cons">
                        <h4 class="pros">✅ Pros:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Instills trust and calmness</li>
                            <li>• Associated with health and healing</li>
                            <li>• Professional and medical-friendly</li>
                            <li>• Easy on the eyes for long use</li>
                            <li>• Universal appeal across cultures</li>
                        </ul>

                        <h4 class="cons mt-3">❌ Cons:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• May seem too traditional</li>
                            <li>• Less vibrant than other options</li>
                        </ul>
                    </div>

                    <button type="submit" name="recommended_theme" value="medical-green"
                            class="w-full mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Use This Theme
                    </button>
                </div>

                <!-- Modern Blue -->
                <div class="recommendation-card p-6">
                    <div class="flex items-center mb-4">
                        <div class="color-preview" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);"></div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Modern Blue</h3>
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-semibold">
                                <i class="fas fa-thumbs-up mr-1"></i>POPULAR
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="color-swatch" style="background: #2563eb;"></span>
                        <span class="color-swatch" style="background: #7c3aed;"></span>
                        <span class="color-swatch" style="background: #059669;"></span>
                        <span class="color-swatch" style="background: #d97706;"></span>
                    </div>

                    <p class="text-gray-600 text-sm mb-4">Professional and modern. Blue represents trust, technology, and reliability.</p>

                    <div class="pros-cons">
                        <h4 class="pros">✅ Pros:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Very professional appearance</li>
                            <li>• Trustworthy and reliable</li>
                            <li>• Modern and tech-savvy</li>
                            <li>• Good contrast and readability</li>
                            <li>• Widely accepted in business</li>
                        </ul>

                        <h4 class="cons mt-3">❌ Cons:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Less healthcare-specific</li>
                            <li>• May feel cold or corporate</li>
                        </ul>
                    </div>

                    <button type="submit" name="recommended_theme" value="modern-blue"
                            class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Use This Theme
                    </button>
                </div>

                <!-- Warm Orange -->
                <div class="recommendation-card p-6">
                    <div class="flex items-center mb-4">
                        <div class="color-preview" style="background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);"></div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Warm Orange</h3>
                            <span class="inline-block bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full font-semibold">
                                <i class="fas fa-fire mr-1"></i>ENERGETIC
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="color-swatch" style="background: #ea580c;"></span>
                        <span class="color-swatch" style="background: #dc2626;"></span>
                        <span class="color-swatch" style="background: #fb923c;"></span>
                        <span class="color-swatch" style="background: #059669;"></span>
                    </div>

                    <p class="text-gray-600 text-sm mb-4">Energetic and warm. Orange represents enthusiasm, creativity, and friendliness.</p>

                    <div class="pros-cons">
                        <h4 class="pros">✅ Pros:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Very energetic and engaging</li>
                            <li>• Creates a friendly atmosphere</li>
                            <li>• Stands out from competitors</li>
                            <li>• Encourages action and urgency</li>
                            <li>• Warm and welcoming feeling</li>
                        </ul>

                        <h4 class="cons mt-3">❌ Cons:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• May be too energetic for medical use</li>
                            <li>• Could feel less professional</li>
                            <li>• Might be overwhelming for some users</li>
                        </ul>
                    </div>

                    <button type="submit" name="recommended_theme" value="warm-orange"
                            class="w-full mt-4 bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Use This Theme
                    </button>
                </div>

                <!-- Professional Gray -->
                <div class="recommendation-card p-6">
                    <div class="flex items-center mb-4">
                        <div class="color-preview" style="background: linear-gradient(135deg, #374151 0%, #4b5563 100%);"></div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Professional Gray</h3>
                            <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full font-semibold">
                                <i class="fas fa-briefcase mr-1"></i>CORPORATE
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="color-swatch" style="background: #374151;"></span>
                        <span class="color-swatch" style="background: #4b5563;"></span>
                        <span class="color-swatch" style="background: #6b7280;"></span>
                        <span class="color-swatch" style="background: #059669;"></span>
                    </div>

                    <p class="text-gray-600 text-sm mb-4">Sophisticated and corporate. Gray represents professionalism, stability, and neutrality.</p>

                    <div class="pros-cons">
                        <h4 class="pros">✅ Pros:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Very professional and sophisticated</li>
                            <li>• Neutral and inoffensive</li>
                            <li>• Excellent readability</li>
                            <li>• Timeless and classic</li>
                            <li>• Works well with any accent color</li>
                        </ul>

                        <h4 class="cons mt-3">❌ Cons:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• May feel too corporate</li>
                            <li>• Less engaging than colorful options</li>
                            <li>• Could appear dull or boring</li>
                        </ul>
                    </div>

                    <button type="submit" name="recommended_theme" value="professional-gray"
                            class="w-full mt-4 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Use This Theme
                    </button>
                </div>

                <!-- Royal Purple -->
                <div class="recommendation-card p-6">
                    <div class="flex items-center mb-4">
                        <div class="color-preview" style="background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);"></div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Royal Purple</h3>
                            <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full font-semibold">
                                <i class="fas fa-crown mr-1"></i>LUXURY
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="color-swatch" style="background: #7c3aed;"></span>
                        <span class="color-swatch" style="background: #ec4899;"></span>
                        <span class="color-swatch" style="background: #8b5cf6;"></span>
                        <span class="color-swatch" style="background: #f472b6;"></span>
                    </div>

                    <p class="text-gray-600 text-sm mb-4">Luxurious and creative. Purple represents wisdom, creativity, and premium quality.</p>

                    <div class="pros-cons">
                        <h4 class="pros">✅ Pros:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Luxurious and premium feel</li>
                            <li>• Creative and innovative</li>
                            <li>• Stands out from competitors</li>
                            <li>• Associated with wisdom</li>
                            <li>• Modern and trendy</li>
                        </ul>

                        <h4 class="cons mt-3">❌ Cons:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• May feel too luxury-focused</li>
                            <li>• Less healthcare-specific</li>
                            <li>• Could be too bold for some users</li>
                        </ul>
                    </div>

                    <button type="submit" name="recommended_theme" value="royal-purple"
                            class="w-full mt-4 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Use This Theme
                    </button>
                </div>

                <!-- Default Theme -->
                <div class="recommendation-card p-6">
                    <div class="flex items-center mb-4">
                        <div class="color-preview" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Default Theme</h3>
                            <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full font-semibold">
                                <i class="fas fa-home mr-1"></i>ORIGINAL
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="color-swatch" style="background: #667eea;"></span>
                        <span class="color-swatch" style="background: #764ba2;"></span>
                        <span class="color-swatch" style="background: #48bb78;"></span>
                        <span class="color-swatch" style="background: #ed8936;"></span>
                    </div>

                    <p class="text-gray-600 text-sm mb-4">Original purple-blue gradient theme. Balanced and versatile.</p>

                    <div class="pros-cons">
                        <h4 class="pros">✅ Pros:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Balanced and versatile</li>
                            <li>• Good contrast and readability</li>
                            <li>• Professional yet modern</li>
                            <li>• Works well for most use cases</li>
                        </ul>

                        <h4 class="cons mt-3">❌ Cons:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Less healthcare-specific</li>
                            <li>• May not stand out enough</li>
                        </ul>
                    </div>

                    <button type="submit" name="recommended_theme" value="default"
                            class="w-full mt-4 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Use This Theme
                    </button>
                </div>

            </div>
        </form>

        <!-- Expert Recommendations -->
        <div class="recommendation-card p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-lightbulb mr-2 text-yellow-600"></i>
                Expert Recommendations
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">🏥 For Medical/Healthcare:</h3>
                    <p class="text-gray-600 mb-3">Choose <strong>Medical Green</strong> - it's specifically designed for healthcare applications and instills trust and calmness in patients.</p>

                    <h3 class="text-lg font-semibold text-gray-700 mb-3">💼 For Corporate/Professional:</h3>
                    <p class="text-gray-600 mb-3">Choose <strong>Professional Gray</strong> or <strong>Modern Blue</strong> - both convey professionalism and reliability.</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">🎨 For Creative/Modern:</h3>
                    <p class="text-gray-600 mb-3">Choose <strong>Royal Purple</strong> or <strong>Warm Orange</strong> - both are modern and engaging.</p>

                    <h3 class="text-lg font-semibold text-gray-700 mb-3">⚡ For High-Energy:</h3>
                    <p class="text-gray-600 mb-3">Choose <strong>Warm Orange</strong> - it's energetic and encourages action.</p>
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
                View Patient Dashboard
            </a>
            <a href="test-colors.php" class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 mr-4">
                <i class="fas fa-flask mr-2"></i>
                Test Colors
            </a>
            <a href="index.php" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-home mr-2"></i>
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>
