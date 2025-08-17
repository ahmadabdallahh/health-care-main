<?php
/**
 * Medical Appointment System - System Test Script
 * This script tests all major functions and database connections
 */

// Include configuration and functions
require_once 'config.php';
require_once 'includes/functions.php';

echo "<h1>🏥 Medical Appointment System - System Test</h1>\n";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .test-pass { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
    .test-fail { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    .test-warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
    .test-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
    .test-result { margin: 10px 0; padding: 10px; border-radius: 3px; }
    .test-details { margin-left: 20px; font-size: 0.9em; }
</style>\n";

$total_tests = 0;
$passed_tests = 0;
$failed_tests = 0;
$warnings = 0;

function run_test($test_name, $test_function) {
    global $total_tests, $passed_tests, $failed_tests, $warnings;

    $total_tests++;
    echo "<div class='test-section'>\n";
    echo "<h3>🧪 Test: $test_name</h3>\n";

    try {
        $result = $test_function();

        if ($result['status'] === 'PASS') {
            echo "<div class='test-result test-pass'>✅ PASSED</div>\n";
            $passed_tests++;
        } elseif ($result['status'] === 'WARNING') {
            echo "<div class='test-result test-warning'>⚠️ WARNING</div>\n";
            $warnings++;
        } else {
            echo "<div class='test-result test-fail'>❌ FAILED</div>\n";
            $failed_tests++;
        }

        if (isset($result['message'])) {
            echo "<div class='test-details'>📝 {$result['message']}</div>\n";
        }

        if (isset($result['details'])) {
            echo "<div class='test-details'>🔍 {$result['details']}</div>\n";
        }

    } catch (Exception $e) {
        echo "<div class='test-result test-fail'>❌ ERROR: {$e->getMessage()}</div>\n";
        $failed_tests++;
    }

    echo "</div>\n";
}

// ========================================
// Test 1: Database Connection
// ========================================
run_test("Database Connection", function() {
    try {
        global $conn;

        if (!$conn || !($conn instanceof PDO)) {
            return ['status' => 'FAIL', 'message' => 'Database connection failed'];
        }

        return ['status' => 'PASS', 'message' => 'Database connection successful'];
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'Database connection error: ' . $e->getMessage()];
    }
});

// ========================================
// Test 2: Core Tables Existence
// ========================================
run_test("Core Tables Existence", function() {
    try {
        global $conn;

        if (!$conn || !($conn instanceof PDO)) {
            return ['status' => 'FAIL', 'message' => 'Database connection not available'];
        }

        $required_tables = ['users', 'appointments', 'doctors', 'clinics', 'hospitals', 'cities', 'specialties'];
        $missing_tables = [];

        foreach ($required_tables as $table) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
            $stmt->execute([DB_NAME, $table]);
            if ($stmt->fetchColumn() == 0) {
                $missing_tables[] = $table;
            }
        }

        if (empty($missing_tables)) {
            return ['status' => 'PASS', 'message' => 'All required tables exist'];
        } else {
            return ['status' => 'FAIL', 'message' => 'Missing tables: ' . implode(', ', $missing_tables)];
        }
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'Table check error: ' . $e->getMessage()];
    }
});

// ========================================
// Test 3: Users Table Structure
// ========================================
run_test("Users Table Structure", function() {
    try {
        global $conn;

        if (!$conn || !($conn instanceof PDO)) {
            return ['status' => 'FAIL', 'message' => 'Database connection not available'];
        }

        $required_columns = ['id', 'username', 'email', 'password', 'full_name', 'phone', 'role'];
        $stmt = $conn->prepare("DESCRIBE users");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $missing_columns = array_diff($required_columns, $columns);

        if (empty($missing_columns)) {
            return ['status' => 'PASS', 'message' => 'All required columns exist in users table'];
        } else {
            return ['status' => 'WARNING', 'message' => 'Missing columns: ' . implode(', ', $missing_columns)];
        }
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'Column check error: ' . $e->getMessage()];
    }
});

// ========================================
// Test 4: Appointments Table Structure
// ========================================
run_test("Appointments Table Structure", function() {
    try {
        global $conn;

        if (!$conn || !($conn instanceof PDO)) {
            return ['status' => 'FAIL', 'message' => 'Database connection not available'];
        }

        $required_columns = ['id', 'user_id', 'doctor_id', 'clinic_id', 'appointment_date', 'appointment_time', 'status'];
        $stmt = $conn->prepare("DESCRIBE appointments");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $missing_columns = array_diff($required_columns, $columns);

        if (empty($missing_columns)) {
            return ['status' => 'PASS', 'message' => 'All required columns exist in appointments table'];
        } else {
            return ['status' => 'WARNING', 'message' => 'Missing columns: ' . implode(', ', $missing_columns)];
        }
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'Column check error: ' . $e->getMessage()];
    }
});

// ========================================
// Test 5: Function get_hospital_by_id
// ========================================
run_test("Function get_hospital_by_id", function() {
    try {
        global $conn;

        if (!$conn || !($conn instanceof PDO)) {
            return ['status' => 'FAIL', 'message' => 'Database connection not available'];
        }

        // First, check if there are any hospitals in the database
        $stmt = $conn->prepare("SELECT COUNT(*) FROM hospitals");
        $stmt->execute();
        $hospital_count = $stmt->fetchColumn();

        if ($hospital_count == 0) {
            return ['status' => 'WARNING', 'message' => 'No hospitals found in database to test with'];
        }

        // Get the first hospital ID
        $stmt = $conn->prepare("SELECT id FROM hospitals LIMIT 1");
        $stmt->execute();
        $hospital_id = $stmt->fetchColumn();

        // Test the function
        $hospital = get_hospital_by_id($hospital_id);

        if ($hospital && isset($hospital['id'])) {
            return ['status' => 'PASS', 'message' => 'Function get_hospital_by_id working correctly'];
        } else {
            return ['status' => 'FAIL', 'message' => 'Function get_hospital_by_id returned invalid data'];
        }
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'Function test error: ' . $e->getMessage()];
    }
});

// ========================================
// Test 6: Function get_appointments_by_user
// ========================================
run_test("Function get_appointments_by_user", function() {
    try {
        global $conn;

        if (!$conn || !($conn instanceof PDO)) {
            return ['status' => 'FAIL', 'message' => 'Database connection not available'];
        }

        // First, check if there are any users with appointments
        $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments");
        $stmt->execute();
        $appointment_count = $stmt->fetchColumn();

        if ($appointment_count == 0) {
            return ['status' => 'WARNING', 'message' => 'No appointments found in database to test with'];
        }

        // Get the first user ID with appointments
        $stmt = $conn->prepare("SELECT user_id FROM appointments LIMIT 1");
        $stmt->execute();
        $user_id = $stmt->fetchColumn();

        // Test the function
        $appointments = get_appointments_by_user($user_id);

        if (is_array($appointments)) {
            return ['status' => 'PASS', 'message' => 'Function get_appointments_by_user working correctly'];
        } else {
            return ['status' => 'FAIL', 'message' => 'Function get_appointments_by_user returned invalid data'];
        }
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'Function test error: ' . $e->getMessage()];
    }
});

// ========================================
// Test 7: Database Foreign Key Integrity
// ========================================
run_test("Database Foreign Key Integrity", function() {
    try {
        global $conn;

        if (!$conn || !($conn instanceof PDO)) {
            return ['status' => 'FAIL', 'message' => 'Database connection not available'];
        }

        // Check for orphaned appointments
        $stmt = $conn->prepare("
            SELECT COUNT(*) as orphaned_count
            FROM appointments a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE u.id IS NULL
        ");
        $stmt->execute();
        $orphaned_users = $stmt->fetchColumn();

        $stmt = $conn->prepare("
            SELECT COUNT(*) as orphaned_count
            FROM appointments a
            LEFT JOIN doctors d ON a.doctor_id = d.id
            WHERE d.id IS NULL
        ");
        $stmt->execute();
        $orphaned_doctors = $stmt->fetchColumn();

        $stmt = $conn->prepare("
            SELECT COUNT(*) as orphaned_count
            FROM appointments a
            LEFT JOIN clinics c ON a.clinic_id = c.id
            WHERE c.id IS NULL
        ");
        $stmt->execute();
        $orphaned_clinics = $stmt->fetchColumn();

        $total_orphaned = $orphaned_users + $orphaned_doctors + $orphaned_clinics;

        if ($total_orphaned == 0) {
            return ['status' => 'PASS', 'message' => 'All foreign key relationships are intact'];
        } else {
            return ['status' => 'WARNING', 'message' => "Found $total_orphaned orphaned records"];
        }
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'Integrity check error: ' . $e->getMessage()];
    }
});

// ========================================
// Test 8: Data Consistency
// ========================================
run_test("Data Consistency", function() {
    try {
        global $conn;

        if (!$conn || !($conn instanceof PDO)) {
            return ['status' => 'FAIL', 'message' => 'Database connection not available'];
        }

        // Check for duplicate cities
        $stmt = $conn->prepare("
            SELECT COUNT(*) as duplicate_count
            FROM (
                SELECT name, governorate, COUNT(*) as cnt
                FROM cities
                GROUP BY name, governorate
                HAVING cnt > 1
            ) as duplicates
        ");
        $stmt->execute();
        $duplicate_cities = $stmt->fetchColumn();

        // Check for duplicate hospitals
        $stmt = $conn->prepare("
            SELECT COUNT(*) as duplicate_count
            FROM (
                SELECT name, address, COUNT(*) as cnt
                FROM hospitals
                GROUP BY name, address
                HAVING cnt > 1
            ) as duplicates
        ");
        $stmt->execute();
        $duplicate_hospitals = $stmt->fetchColumn();

        $total_duplicates = $duplicate_cities + $duplicate_hospitals;

        if ($total_duplicates == 0) {
            return ['status' => 'PASS', 'message' => 'No duplicate data found'];
        } else {
            return ['status' => 'WARNING', 'message' => "Found $total_duplicates duplicate records"];
        }
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'Consistency check error: ' . $e->getMessage()];
    }
});

// ========================================
// Test 9: PDO Compatibility
// ========================================
run_test("PDO Compatibility", function() {
    try {
        global $conn;

        if (!$conn || !($conn instanceof PDO)) {
            return ['status' => 'FAIL', 'message' => 'Database connection not available'];
        }

        // Test basic PDO operations
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        $user_count = $stmt->fetchColumn();

        if ($user_count >= 0) {
            return ['status' => 'PASS', 'message' => 'PDO operations working correctly'];
        } else {
            return ['status' => 'FAIL', 'message' => 'PDO operations failed'];
        }
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'PDO test error: ' . $e->getMessage()];
    }
});

// ========================================
// Test 10: System Performance
// ========================================
run_test("System Performance", function() {
    try {
        global $conn;

        if (!$conn || !($conn instanceof PDO)) {
            return ['status' => 'FAIL', 'message' => 'Database connection not available'];
        }

        $start_time = microtime(true);

        // Perform a complex query
        $stmt = $conn->prepare("
            SELECT u.full_name, d.full_name as doctor_name, c.name as clinic_name
            FROM appointments a
            JOIN users u ON a.user_id = u.id
            JOIN doctors d ON a.doctor_id = d.id
            JOIN clinics c ON a.clinic_id = c.id
            LIMIT 10
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000; // Convert to milliseconds

        if ($execution_time < 100) { // Less than 100ms
            return ['status' => 'PASS', 'message' => 'Query executed in ' . number_format($execution_time, 2) . 'ms'];
        } elseif ($execution_time < 500) { // Less than 500ms
            return ['status' => 'WARNING', 'message' => 'Query executed in ' . number_format($execution_time, 2) . 'ms (acceptable)'];
        } else {
            return ['status' => 'FAIL', 'message' => 'Query too slow: ' . number_format($execution_time, 2) . 'ms'];
        }
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'Performance test error: ' . $e->getMessage()];
    }
});

// ========================================
// Test Results Summary
// ========================================
echo "<div class='test-section test-info'>\n";
echo "<h2>📊 Test Results Summary</h2>\n";
echo "<div class='test-result'>\n";
echo "<strong>Total Tests:</strong> $total_tests<br>\n";
echo "<strong>Passed:</strong> <span style='color: #155724;'>$passed_tests</span><br>\n";
echo "<strong>Failed:</strong> <span style='color: #721c24;'>$failed_tests</span><br>\n";
echo "<strong>Warnings:</strong> <span style='color: #856404;'>$warnings</span><br>\n";
echo "<strong>Success Rate:</strong> " . round(($passed_tests / $total_tests) * 100, 1) . "%<br>\n";
echo "</div>\n";

if ($failed_tests == 0 && $warnings == 0) {
    echo "<div class='test-result test-pass'>🎉 All tests passed! System is ready for production.</div>\n";
} elseif ($failed_tests == 0) {
    echo "<div class='test-result test-warning'>⚠️ System is mostly ready but has some warnings to address.</div>\n";
} else {
    echo "<div class='test-result test-fail'>❌ System has critical issues that need to be fixed.</div>\n";
}

echo "</div>\n";

// ========================================
// Recommendations
// ========================================
echo "<div class='test-section test-info'>\n";
echo "<h2>💡 Recommendations</h2>\n";

if ($failed_tests > 0) {
    echo "<div class='test-result test-fail'>\n";
    echo "<strong>Immediate Actions Required:</strong><br>\n";
    echo "1. Fix all failed tests before proceeding<br>\n";
    echo "2. Review error logs for detailed information<br>\n";
    echo "3. Run the migration script: SQL/migration_fixes.sql<br>\n";
    echo "</div>\n";
}

if ($warnings > 0) {
    echo "<div class='test-result test-warning'>\n";
    echo "<strong>Recommended Actions:</strong><br>\n";
    echo "1. Address warnings to improve system stability<br>\n";
    echo "2. Consider running database optimization scripts<br>\n";
    echo "3. Review data consistency issues<br>\n";
    echo "</div>\n";
}

if ($failed_tests == 0 && $warnings == 0) {
    echo "<div class='test-result test-pass'>\n";
    echo "<strong>Next Steps:</strong><br>\n";
    echo "1. Proceed with new feature development<br>\n";
    echo "2. Implement advanced features from your plan<br>\n";
    echo "3. Consider performance monitoring and optimization<br>\n";
    echo "</div>\n";
}

echo "</div>\n";

echo "<div class='test-section test-info'>\n";
echo "<h2>🚀 Next Phase: New Features Development</h2>\n";
echo "<p>Based on your plan, the next steps would be:</p>\n";
echo "<ul>\n";
echo "<li>Real-time chat system</li>\n";
echo "<li>Interactive dashboard with charts</li>\n";
echo "<li>Advanced rating system</li>\n";
echo "<li>Progressive Web App (PWA) features</li>\n";
echo "<li>Electronic payment system</li>\n";
echo "<li>Smart reminder system</li>\n";
echo "<li>Interactive hospital map</li>\n";
echo "<li>Medical records system</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>\n";
?>
