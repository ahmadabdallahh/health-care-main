<?php
/**
 * Simple System Test
 */

require_once 'config.php';
require_once 'includes/functions.php';

echo "<h1>System Test</h1>";

// Test database connection
if ($conn) {
    echo "<p>✅ Database connection: OK</p>";
} else {
    echo "<p>❌ Database connection: Failed</p>";
}

// Test core functions
$functions = ['get_logged_in_user', 'get_user_by_id', 'get_doctor_by_id'];
foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "<p>✅ Function $func: OK</p>";
    } else {
        echo "<p>❌ Function $func: Missing</p>";
    }
}

// Test database tables
$tables = ['users', 'doctors', 'hospitals', 'appointments'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->rowCount() > 0) {
        echo "<p>✅ Table $table: OK</p>";
    } else {
        echo "<p>❌ Table $table: Missing</p>";
    }
}

echo "<p>Test completed!</p>";
?>
