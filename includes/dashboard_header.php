<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - نظام الحجوزات' : 'نظام الحجوزات'; ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">

    <!-- Dashboard-specific Stylesheet (we will create this next) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dashboard.css">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts (Cairo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

</head>
<body>

<header class="bg-white shadow-md p-4 flex justify-between items-center">
    <!-- Hamburger Menu for mobile -->
    <button id="sidebar-toggle" class="text-gray-600 hover:text-gray-800 focus:outline-none lg:hidden">
        <i class="fas fa-bars fa-lg"></i>
    </button>

    <!-- Search bar -->
    <div class="relative hidden md:block">
        <input type="text" class="bg-gray-100 border-2 border-gray-200 rounded-full py-2 px-4 pl-10 focus:outline-none focus:bg-white focus:border-blue-500" placeholder="بحث...">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400"></i>
        </div>
    </div>

    <!-- Profile Dropdown -->
    <div class="relative">
        <button class="flex items-center space-x-3 focus:outline-none">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'U'); ?>&background=random&color=fff" alt="User Avatar" class="w-10 h-10 rounded-full object-cover">
            <span class="hidden md:inline-block font-semibold text-gray-700"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'المستخدم'); ?></span>
        </button>
        <!-- Dropdown menu here if needed -->
    </div>
</header>
