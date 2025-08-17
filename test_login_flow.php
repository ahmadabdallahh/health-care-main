<?php
/**
 * Test Login Flow
 * This script tests the complete login process
 */

session_start();
require_once 'config.php';

echo "<h1>🧪 Test Login Flow</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .test-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
    .test-error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    .test-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
</style>";

// Test 1: Check if we can start a session
echo "<div class='test-section test-info'>";
echo "<h2>🔐 Test 1: Session Management</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✅ Session started successfully</p>";
    echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
} else {
    echo "<p style='color: red;'>❌ Session failed to start</p>";
}
echo "</div>";

// Test 2: Check database connection
echo "<div class='test-section test-info'>";
echo "<h2>📊 Test 2: Database Connection</h2>";
if ($conn && $conn instanceof PDO) {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
}
echo "</div>";

// Test 3: Check if patient directory exists
echo "<div class='test-section test-info'>";
echo "<h2>📁 Test 3: Patient Directory</h2>";
$patient_file = 'patient/index.php';
if (file_exists($patient_file)) {
    echo "<p style='color: green;'>✅ Patient directory exists</p>";
} else {
    echo "<p style='color: red;'>❌ Patient directory missing</p>";
}
echo "</div>";

// Test 4: Simulate login process
echo "<div class='test-section test-info'>";
echo "<h2>🚀 Test 4: Simulate Login Process</h2>";

if ($conn && $conn instanceof PDO) {
    try {
        // Get a sample patient user
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_type IN ('patient', 'user') LIMIT 1");
        $stmt->execute();
        $test_user = $stmt->fetch();

        if ($test_user) {
            echo "<p style='color: green;'>✅ Found test user: " . htmlspecialchars($test_user['full_name']) . "</p>";
            echo "<p><strong>User Type:</strong> " . htmlspecialchars($test_user['user_type']) . "</p>";

            // Simulate setting session variables
            $_SESSION['user_id'] = $test_user['id'];
            $_SESSION['user_name'] = $test_user['full_name'];
            $_SESSION['user_type'] = $test_user['user_type'];
            $_SESSION['role'] = $test_user['user_type'];

            echo "<p style='color: green;'>✅ Session variables set</p>";
            echo "<p><strong>Session Data:</strong></p>";
            echo "<pre>" . htmlspecialchars(print_r($_SESSION, true)) . "</pre>";

            // Test redirect logic
            $user_role = $test_user['user_type'];
            echo "<p><strong>Redirect Logic Test:</strong></p>";

            if ($user_role === 'admin') {
                echo "<p>→ Would redirect to: <code>admin/index.php</code></p>";
            } elseif ($user_role === 'doctor') {
                echo "<p>→ Would redirect to: <code>doctor/index.php</code></p>";
            } elseif ($user_role === 'user' || $user_role === 'patient') {
                echo "<p>→ Would redirect to: <code>patient/index.php</code></p>";

                // Test if we can access the patient dashboard
                if (file_exists('patient/index.php')) {
                    echo "<p style='color: green;'>✅ Patient dashboard file exists and accessible</p>";
                } else {
                    echo "<p style='color: red;'>❌ Patient dashboard file not accessible</p>";
                }
            } else {
                echo "<p>→ Would redirect to: <code>index.php</code> (default)</p>";
            }

        } else {
            echo "<p style='color: red;'>❌ No patient users found in database</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error during login simulation: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Cannot test login process - database not connected</p>";
}

echo "</div>";

// Test 5: Check for common issues
echo "<div class='test-section test-info'>";
echo "<h2>🔍 Test 5: Common Issues Check</h2>";

// Check for whitespace issues
$login_file = 'login.php';
if (file_exists($login_file)) {
    $content = file_get_contents($login_file);
    if (preg_match('/^\s*<\?php/', $content)) {
        echo "<p style='color: green;'>✅ No whitespace before PHP opening tag</p>";
    } else {
        echo "<p style='color: red;'>❌ Whitespace detected before PHP opening tag</p>";
    }

    // Check for BOM
    if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
        echo "<p style='color: red;'>❌ BOM detected in login.php</p>";
    } else {
        echo "<p style='color: green;'>✅ No BOM detected</p>";
    }
}

// Check if output buffering is enabled
if (function_exists('ob_get_level')) {
    echo "<p style='color: green;'>✅ Output buffering functions available</p>";
} else {
    echo "<p style='color: red;'>❌ Output buffering not available</p>";
}

echo "</div>";

// Test 6: Manual redirect test
echo "<div class='test-section test-info'>";
echo "<h2>🧭 Test 6: Manual Redirect Test</h2>";
echo "<p>Click the button below to test if you can manually access the patient dashboard:</p>";
echo "<a href='patient/index.php' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Test Patient Dashboard Access</a>";
echo "</div>";

echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>
