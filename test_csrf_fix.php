<?php
/**
 * Simple CSRF Token Test Script
 * This script tests the CSRF token functionality
 */

session_start();

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

echo "<h1>🔒 CSRF Token Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .token { background: #f0f0f0; padding: 10px; margin: 10px 0; font-family: monospace; }
</style>";

echo "<h2>📊 Current Status</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . session_status() . "</p>";
echo "<p><strong>CSRF Token:</strong> <span class='token'>" . htmlspecialchars($_SESSION['csrf_token']) . "</span></p>";

echo "<h2>🧪 Test Form</h2>";
echo "<form method='POST' action='test_csrf_fix.php'>";
echo "<input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token']) . "'>";
echo "<p>Test Input: <input type='text' name='test_input' value='test_value'></p>";
echo "<input type='submit' value='Test CSRF Protection' name='test_submit'>";
echo "</form>";

// Handle form submission
if (isset($_POST['test_submit'])) {
    echo "<h2>📝 Form Submission Results</h2>";

    if (!isset($_POST['csrf_token'])) {
        echo "<p class='error'>❌ CSRF token missing from POST data</p>";
    } elseif (!isset($_SESSION['csrf_token'])) {
        echo "<p class='error'>❌ CSRF token missing from session</p>";
    } elseif (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "<p class='error'>❌ CSRF token validation failed</p>";
        echo "<p class='info'>POST token: " . htmlspecialchars($_POST['csrf_token']) . "</p>";
        echo "<p class='info'>Session token: " . htmlspecialchars($_SESSION['csrf_token']) . "</p>";
    } else {
        echo "<p class='success'>✅ CSRF token validation successful!</p>";
        echo "<p class='info'>Test input received: " . htmlspecialchars($_POST['test_input']) . "</p>";
    }
}

echo "<h2>🔄 Token Management</h2>";
echo "<p><a href='?regenerate=1' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔄 Regenerate CSRF Token</a></p>";

if (isset($_GET['regenerate'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    echo "<p class='success'>✅ CSRF token regenerated successfully!</p>";
    echo "<p class='info'>New token: <span class='token'>" . htmlspecialchars($_SESSION['csrf_token']) . "</span></p>";
    echo "<script>setTimeout(function(){ window.location.href = 'test_csrf_fix.php'; }, 2000);</script>";
}

echo "<h2>📋 Debug Information</h2>";
echo "<p><strong>POST Data:</strong></p>";
echo "<pre>" . htmlspecialchars(print_r($_POST, true)) . "</pre>";

echo "<p><strong>Session Data:</strong></p>";
echo "<pre>" . htmlspecialchars(print_r($_SESSION, true)) . "</pre>";

echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>
