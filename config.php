<?php
// config.php

// **Database Configuration** //
define('DB_HOST', 'localhost');
define('DB_NAME', 'medical_booking');
define('DB_USER', 'root');
define('DB_PASS', '');

// **Site Configuration** //
// Define the base URL of the site.
// IMPORTANT: Make sure to change this if your site is in a subfolder.
// Example: http://localhost/my-app/
if (php_sapi_name() !== 'cli') {
    $protocol = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Compute the project root folder from the current script path (first path segment)
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $parts = array_values(array_filter(explode('/', trim($scriptDir, '/'))));
    $baseFolder = $parts[0] ?? '';
    $basePath = '/' . ($baseFolder !== '' ? $baseFolder . '/' : '');
    define('BASE_URL', $protocol . '://' . $host . $basePath);

    // Start the session only in web mode
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
} else {
    // CLI mode
    define('BASE_URL', 'http://localhost/');
}

// **Create Database Connection** //
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // For security, don't display detailed errors in production
    error_log('Database Connection Error: ' . $e->getMessage());
    die('عذراً، حدث خطأ أثناء الاتصال بقاعدة البيانات. يرجى المحاولة مرة أخرى لاحقاً.');
}

?>
