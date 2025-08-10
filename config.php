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
$protocol = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Ensure script_name is not just '/' if it's in the root, otherwise append a slash.
$base_path = rtrim($script_name, '/') . '/';
define('BASE_URL', $protocol . '://' . $host . $base_path);

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
