<?php
/**
 * Test Patient Dashboard Access
 * This script tests if the patient dashboard can be accessed without errors
 */

session_start();
require_once 'config.php';
require_once 'includes/functions.php';

echo "<h1>🧪 Test Patient Dashboard Access</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .test-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
    .test-error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    .test-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
</style>";

// Test 1: Check database connection
echo "<div class='test-section test-info'>";
echo "<h2>📊 Test 1: Database Connection</h2>";
if ($conn && $conn instanceof PDO) {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
}
echo "</div>";

// Test 2: Check if patient users exist
echo "<div class='test-section test-info'>";
echo "<h2>👥 Test 2: Patient Users Check</h2>";
if ($conn && $conn instanceof PDO) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_type IN ('patient', 'user')");
        $stmt->execute();
        $patient_count = $stmt->fetchColumn();

        if ($patient_count > 0) {
            echo "<p style='color: green;'>✅ Found $patient_count patient users</p>";

            // Get a sample patient
            $stmt = $conn->prepare("SELECT id, email, full_name, user_type FROM users WHERE user_type IN ('patient', 'user') LIMIT 1");
            $stmt->execute();
            $sample_patient = $stmt->fetch();

            if ($sample_patient) {
                echo "<p><strong>Sample Patient:</strong></p>";
                echo "<ul>";
                echo "<li>ID: " . htmlspecialchars($sample_patient['id']) . "</li>";
                echo "<li>Email: " . htmlspecialchars($sample_patient['email']) . "</li>";
                echo "<li>Name: " . htmlspecialchars($sample_patient['full_name']) . "</li>";
                echo "<li>Type: " . htmlspecialchars($sample_patient['user_type']) . "</li>";
                echo "</ul>";
            }
        } else {
            echo "<p style='color: red;'>❌ No patient users found</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error checking patient users: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Cannot check patient users - database not connected</p>";
}
echo "</div>";

// Test 3: Test the get_patient_appointment_count function
echo "<div class='test-section test-info'>";
echo "<h2>📅 Test 3: Appointment Count Function</h2>";
if ($conn && $conn instanceof PDO) {
    try {
        // Get a sample patient ID
        $stmt = $conn->prepare("SELECT id FROM users WHERE user_type IN ('patient', 'user') LIMIT 1");
        $stmt->execute();
        $patient_id = $stmt->fetchColumn();

        if ($patient_id) {
            echo "<p style='color: green;'>✅ Testing with patient ID: $patient_id</p>";

            // Test the function
            $confirmed_count = get_patient_appointment_count($conn, $patient_id, 'confirmed');
            $completed_count = get_patient_appointment_count($conn, $patient_id, 'completed');

            echo "<p><strong>Confirmed Appointments:</strong> $confirmed_count</p>";
            echo "<p><strong>Completed Appointments:</strong> $completed_count</p>";
            echo "<p style='color: green;'>✅ Function working correctly</p>";
        } else {
            echo "<p style='color: red;'>❌ No patient ID found for testing</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error testing appointment count function: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Cannot test function - database not connected</p>";
}
echo "</div>";

// Test 4: Test session simulation
echo "<div class='test-section test-info'>";
echo "<h2>🔐 Test 4: Session Simulation</h2>";
if ($conn && $conn instanceof PDO) {
    try {
        // Get a sample patient
        $stmt = $conn->prepare("SELECT id, full_name, user_type FROM users WHERE user_type IN ('patient', 'user') LIMIT 1");
        $stmt->execute();
        $test_patient = $stmt->fetch();

        if ($test_patient) {
            // Simulate login
            $_SESSION['user_id'] = $test_patient['id'];
            $_SESSION['user_name'] = $test_patient['full_name'];
            $_SESSION['user_type'] = $test_patient['user_type'];
            $_SESSION['role'] = $test_patient['user_type'];

            echo "<p style='color: green;'>✅ Session variables set</p>";
            echo "<p><strong>Session Data:</strong></p>";
            echo "<pre>" . htmlspecialchars(print_r($_SESSION, true)) . "</pre>";

            // Test is_logged_in function
            if (is_logged_in()) {
                echo "<p style='color: green;'>✅ is_logged_in() function working</p>";
            } else {
                echo "<p style='color: red;'>❌ is_logged_in() function failed</p>";
            }

            // Test get_logged_in_user function
            $logged_user = get_logged_in_user();
            if ($logged_user) {
                echo "<p style='color: green;'>✅ get_logged_in_user() function working</p>";
                echo "<p><strong>User Data:</strong> " . htmlspecialchars($logged_user['full_name']) . "</p>";
            } else {
                echo "<p style='color: red;'>❌ get_logged_in_user() function failed</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ No test patient found</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error during session simulation: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Cannot test session - database not connected</p>";
}
echo "</div>";

// Test 5: Check file accessibility
echo "<div class='test-section test-info'>";
echo "<h2>📁 Test 5: File Accessibility</h2>";
$required_files = [
    'patient/index.php',
    'includes/dashboard_header.php',
    'includes/dashboard_sidebar.php',
    'includes/dashboard_footer.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file exists</p>";
    } else {
        echo "<p style='color: red;'>❌ $file missing</p>";
    }
}
echo "</div>";

// Test 6: Manual access test
echo "<div class='test-section test-info'>";
echo "<h2>🧭 Test 6: Manual Access Test</h2>";
echo "<p>Click the button below to test if you can manually access the patient dashboard:</p>";
echo "<a href='patient/index.php' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Test Patient Dashboard</a>";
echo "</div>";

echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>
