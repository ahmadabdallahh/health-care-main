<?php
/**
 * Database Setup Script
 * Creates database and tables if they don't exist
 */

echo "<h1>Database Setup</h1>";

// Database connection without database name first
try {
    $pdo = new PDO("mysql:host=localhost", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "<p>✅ Connected to MySQL server</p>";
} catch (PDOException $e) {
    die("<p>❌ Failed to connect to MySQL: " . $e->getMessage() . "</p>");
}

// Create database if it doesn't exist
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS medical_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✅ Database 'medical_booking' created/verified</p>";
} catch (PDOException $e) {
    die("<p>❌ Failed to create database: " . $e->getMessage() . "</p>");
}

// Connect to the specific database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=medical_booking;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "<p>✅ Connected to medical_booking database</p>";
} catch (PDOException $e) {
    die("<p>❌ Failed to connect to database: " . $e->getMessage() . "</p>");
}

// Check if tables exist
$tables = ['users', 'doctors', 'hospitals', 'appointments', 'reminders', 'notifications'];
$existingTables = [];

foreach ($tables as $table) {
    try {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            $existingTables[] = $table;
            echo "<p>✅ Table '$table' exists</p>";
        } else {
            echo "<p>⚠️ Table '$table' missing</p>";
        }
    } catch (PDOException $e) {
        echo "<p>❌ Error checking table '$table': " . $e->getMessage() . "</p>";
    }
}

// If tables don't exist, create them from the SQL file
if (count($existingTables) < count($tables)) {
    echo "<p>📝 Some tables are missing. Creating them...</p>";

    if (file_exists('complete_database.sql')) {
        try {
            $sql = file_get_contents('complete_database.sql');
            $pdo->exec($sql);
            echo "<p>✅ Database tables created from complete_database.sql</p>";
        } catch (PDOException $e) {
            echo "<p>❌ Error creating tables: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>❌ complete_database.sql file not found</p>";
    }
}

echo "<p>🎉 Database setup completed!</p>";
?>
