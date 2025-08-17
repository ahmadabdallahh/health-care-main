<?php
/**
 * Debug Login Issues
 * This script helps troubleshoot login problems
 */

session_start();
require_once 'config.php';

echo "<h1>🔍 Login Debug Information</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .debug-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
    .debug-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
    .debug-error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
</style>";

// Check database connection
echo "<div class='debug-section debug-info'>";
echo "<h2>📊 Database Connection Status</h2>";
if ($conn && $conn instanceof PDO) {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
    echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
}
echo "</div>";

// Check session status
echo "<div class='debug-section debug-info'>";
echo "<h2>🔐 Session Information</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . session_status() . "</p>";
echo "<p><strong>Session Data:</strong></p>";
echo "<pre>" . htmlspecialchars(print_r($_SESSION, true)) . "</pre>";
echo "</div>";

// Test user query
if ($conn && $conn instanceof PDO) {
    echo "<div class='debug-section debug-info'>";
    echo "<h2>👥 Test User Query</h2>";

    try {
        // Test query to see what users exist
        $stmt = $conn->prepare("SELECT id, email, full_name, user_type FROM users LIMIT 5");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users) {
            echo "<p style='color: green;'>✅ Found " . count($users) . " users in database</p>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Email</th><th>Full Name</th><th>User Type</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
                echo "<td>" . htmlspecialchars($user['user_type']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>❌ No users found in database</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error querying users: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    echo "</div>";
}

// Test login form
echo "<div class='debug-section debug-info'>";
echo "<h2>🧪 Test Login Form</h2>";
echo "<form method='POST' action='debug_login.php'>";
echo "<p>Email: <input type='email' name='test_email' placeholder='Enter email to test'></p>";
echo "<p>Password: <input type='password' name='test_password' placeholder='Enter password to test'></p>";
echo "<input type='submit' value='Test Login' name='test_login'>";
echo "</form>";
echo "</div>";

// Handle test login
if (isset($_POST['test_login'])) {
    echo "<div class='debug-section debug-info'>";
    echo "<h2>📝 Login Test Results</h2>";

    $email = $_POST['test_email'];
    $password = $_POST['test_password'];

    if (empty($email) || empty($password)) {
        echo "<p style='color: red;'>❌ Please enter both email and password</p>";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                echo "<p style='color: green;'>✅ User found in database</p>";
                echo "<p><strong>User ID:</strong> " . htmlspecialchars($user['id']) . "</p>";
                echo "<p><strong>Full Name:</strong> " . htmlspecialchars($user['full_name']) . "</p>";
                echo "<p><strong>User Type:</strong> " . htmlspecialchars($user['user_type']) . "</p>";
                echo "<p><strong>Password Match:</strong> " . ($password === $user['password'] ? '✅ Yes' : '❌ No') . "</p>";

                if ($password === $user['password']) {
                    echo "<p style='color: green;'>✅ Password matches! This user should be able to log in.</p>";

                    // Show what would happen during login
                    $user_role = $user['user_type'];
                    echo "<p><strong>Login Redirect Logic:</strong></p>";

                    if ($user_role === 'admin') {
                        echo "<p>→ Would redirect to: <code>admin/index.php</code></p>";
                    } elseif ($user_role === 'doctor') {
                        echo "<p>→ Would redirect to: <code>doctor/index.php</code></p>";
                    } elseif ($user_role === 'user' || $user_role === 'patient') {
                        echo "<p>→ Would redirect to: <code>patient/index.php</code></p>";
                    } else {
                        echo "<p>→ Would redirect to: <code>index.php</code> (default)</p>";
                    }
                } else {
                    echo "<p style='color: red;'>❌ Password does not match. Check the password.</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ No user found with email: " . htmlspecialchars($email) . "</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error during login test: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    echo "</div>";
}

// Check if patient directory exists
echo "<div class='debug-section debug-info'>";
echo "<h2>📁 Directory Check</h2>";
$patient_dir = 'patient/index.php';
if (file_exists($patient_dir)) {
    echo "<p style='color: green;'>✅ Patient directory exists: <code>$patient_dir</code></p>";
} else {
    echo "<p style='color: red;'>❌ Patient directory missing: <code>$patient_dir</code></p>";
}
echo "</div>";

echo "<p><em>Debug completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>
