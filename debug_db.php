<?php
// Debug script to test database connection and table structure
session_start();
require_once 'config.php';

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to regenerate CSRF token
function regenerateCSRFToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

echo "<h1>Database Debug Information</h1>";

try {
    // Test database connection
    echo "<h2>1. Testing Database Connection</h2>";
    // $conn is already available from config.php

    if ($conn && $conn instanceof PDO) {
        echo "<p style='color: green;'>✓ Database connection successful</p>";
        echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
        echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
        echo "<p><strong>User:</strong> " . DB_USER . "</p>";

        // Test users table structure
        echo "<h2>2. Testing Users Table Structure</h2>";
        $stmt = $conn->prepare("DESCRIBE users");
        $stmt->execute();
        $columns = $stmt->fetchAll();

        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Test if users table has any data
        echo "<h2>3. Testing Users Table Data</h2>";
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "<p>Total users in database: " . $result['count'] . "</p>";

        if ($result['count'] > 0) {
            echo "<h3>Sample User Data:</h3>";
            $stmt = $conn->prepare("SELECT id, username, email, full_name, user_type FROM users LIMIT 3");
            $stmt->execute();
            $users = $stmt->fetchAll();

            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th><th>User Type</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
                echo "<td>" . htmlspecialchars($user['user_type']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // Test doctors table structure
        echo "<h2>4. Testing Doctors Table Structure</h2>";
        $stmt = $conn->prepare("DESCRIBE doctors");
        $stmt->execute();
        $columns = $stmt->fetchAll();

        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";

    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>";
        echo "<p><strong>Connection Status:</strong> " . gettype($conn) . "</p>";
        if ($conn instanceof Exception) {
            echo "<p><strong>Error:</strong> " . htmlspecialchars($conn->getMessage()) . "</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Error details: " . htmlspecialchars(print_r($e, true)) . "</p>";
}

echo "<h2>5. PHP Environment Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Session Status: " . session_status() . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>CSRF Token: " . (isset($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : 'Not set') . "</p>";

echo "<h2>6. Test Form Submission</h2>";
echo "<p><strong>Current CSRF Token:</strong> " . htmlspecialchars($_SESSION['csrf_token']) . "</p>";
echo "<p><a href='?regenerate_token=1' style='background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>🔄 Regenerate CSRF Token</a></p>";

// Handle token regeneration
if (isset($_GET['regenerate_token'])) {
    regenerateCSRFToken();
    echo "<p style='color: green;'>✓ CSRF token regenerated successfully!</p>";
    echo "<p><strong>New CSRF Token:</strong> " . htmlspecialchars($_SESSION['csrf_token']) . "</p>";
}

echo "<form method='POST' action='debug_db.php'>";
echo "<input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token']) . "'>";
echo "<p>Username: <input type='text' name='test_username' value='test_user'></p>";
echo "<p>Email: <input type='email' name='test_email' value='test@example.com'></p>";
echo "<p>Password: <input type='password' name='test_password' value='test123'></p>";
echo "<p>Full Name: <input type='text' name='test_full_name' value='Test User'></p>";
echo "<p>Phone: <input type='tel' name='test_phone' value='0123456789'></p>";
echo "<p>Date of Birth: <input type='date' name='test_date_of_birth' value='1990-01-01'></p>";
echo "<p>Gender: <select name='test_gender'><option value='male'>Male</option><option value='female'>Female</option></select></p>";
echo "<p>Role: <select name='test_role'><option value='patient'>Patient</option><option value='doctor'>Doctor</option></select></p>";
echo "<input type='submit' value='Test Registration' name='test_submit'>";
echo "</form>";

// Handle test form submission
if (isset($_POST['test_submit'])) {
    echo "<h2>7. Test Form Results</h2>";

    // CSRF validation
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "<p style='color: red;'>✗ CSRF validation failed</p>";
        echo "<p>Debug info: POST token: " . (isset($_POST['csrf_token']) ? 'Set' : 'Missing') . ", Session token: " . (isset($_SESSION['csrf_token']) ? 'Set' : 'Missing') . "</p>";
    } else {
        echo "<p style='color: green;'>✓ CSRF validation passed</p>";

        // Test database insert
        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, password, phone, date_of_birth, gender, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $_POST['test_username'],
                $_POST['test_full_name'],
                $_POST['test_email'],
                $_POST['test_password'],
                $_POST['test_phone'],
                $_POST['test_date_of_birth'],
                $_POST['test_gender'],
                $_POST['test_role']
            ]);

            if ($result) {
                $user_id = $conn->lastInsertId();
                echo "<p style='color: green;'>✓ Test user created successfully with ID: " . $user_id . "</p>";

                // If doctor, add to doctors table
                if ($_POST['test_role'] === 'doctor') {
                    $stmt_doctor = $conn->prepare("INSERT INTO doctors (user_id, full_name, email, phone, is_active) VALUES (?, ?, ?, ?, 1)");
                    $result_doctor = $stmt_doctor->execute([$user_id, $_POST['test_full_name'], $_POST['test_email'], $_POST['test_phone']]);

                    if ($result_doctor) {
                        echo "<p style='color: green;'>✓ Test doctor added to doctors table successfully</p>";
                    } else {
                        echo "<p style='color: red;'>✗ Failed to add test doctor to doctors table</p>";
                    }
                }

                $conn->commit();
                echo "<p style='color: green;'>✓ Transaction committed successfully</p>";

                // Clean up test data
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                echo "<p style='color: blue;'>ℹ Test user cleaned up</p>";

            } else {
                echo "<p style='color: red;'>✗ Failed to create test user</p>";
                $conn->rollBack();
            }

        } catch (Exception $e) {
            $conn->rollBack();
            echo "<p style='color: red;'>✗ Error during test: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>Stack trace: <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre></p>";
        }
    }
}

// Additional debugging information
echo "<h2>8. Session Debug Information</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . session_status() . "</p>";
echo "<p><strong>Session Data:</strong></p>";
echo "<pre>" . htmlspecialchars(print_r($_SESSION, true)) . "</pre>";

echo "<h2>9. POST Data Debug</h2>";
if (!empty($_POST)) {
    echo "<p><strong>POST Data Received:</strong></p>";
    echo "<pre>" . htmlspecialchars(print_r($_POST, true)) . "</pre>";
} else {
    echo "<p>No POST data received</p>";
}

echo "<p><em>Debug script completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

