<?php
session_start();
require_once 'config.php';
require_once 'includes/functions.php';

// Simulate doctor login for testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Assuming doctor ID 1 exists
    $_SESSION['user_type'] = 'doctor';
    $_SESSION['role'] = 'doctor';
}

$doctor_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Doctor Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Test Doctor Dashboard</h1>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Database Connection Test</h2>

            <?php
            try {
                // Test database connection
                $stmt = $conn->query("SELECT 1");
                echo "<p style='color: green;'>✅ Database connection successful</p>";

                // Test doctor functions
                $stats = get_doctor_dashboard_stats($conn, $doctor_id);
                echo "<p style='color: green;'>✅ Doctor stats function working</p>";
                echo "<pre>" . print_r($stats, true) . "</pre>";

                $appointments = get_doctor_upcoming_appointments($conn, $doctor_id, 5);
                echo "<p style='color: green;'>✅ Doctor appointments function working</p>";
                echo "<pre>" . print_r($appointments, true) . "</pre>";

                $patients = get_doctor_recent_patients($conn, $doctor_id, 3);
                echo "<p style='color: green;'>✅ Doctor patients function working</p>";
                echo "<pre>" . print_r($patients, true) . "</pre>";

            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Sample Data Creation</h2>

            <?php
            try {
                // Create sample appointments if none exist
                $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ?");
                $stmt->execute([$doctor_id]);
                $appointment_count = $stmt->fetchColumn();

                if ($appointment_count == 0) {
                    echo "<p style='color: orange;'>⚠️ No appointments found. Creating sample data...</p>";

                    // Get some patients
                    $stmt = $conn->prepare("SELECT id FROM users WHERE user_type = 'patient' LIMIT 5");
                    $stmt->execute();
                    $patients = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    if (!empty($patients)) {
                        $appointment_types = ['استشارة عامة', 'فحص دوري', 'متابعة', 'استشارة عاجلة'];
                        $statuses = ['confirmed', 'pending'];

                        for ($i = 0; $i < 5; $i++) {
                            $patient_id = $patients[array_rand($patients)];
                            $appointment_time = date('Y-m-d H:i:s', strtotime('+' . ($i + 1) . ' hours'));
                            $appointment_type = $appointment_types[array_rand($appointment_types)];
                            $status = $statuses[array_rand($statuses)];

                            $stmt = $conn->prepare("
                                INSERT INTO appointments (doctor_id, patient_id, appointment_time, appointment_type, status, created_at)
                                VALUES (?, ?, ?, ?, ?, NOW())
                            ");
                            $stmt->execute([$doctor_id, $patient_id, $appointment_time, $appointment_type, $status]);
                        }

                        echo "<p style='color: green;'>✅ Sample appointments created successfully</p>";
                    } else {
                        echo "<p style='color: red;'>❌ No patients found in database</p>";
                    }
                } else {
                    echo "<p style='color: green;'>✅ Appointments already exist (" . $appointment_count . " found)</p>";
                }

            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error creating sample data: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Navigation</h2>
            <div class="flex gap-4">
                <a href="add_doctor_features.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Setup Doctor Features
                </a>
                <a href="doctor/profile.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    View Doctor Dashboard
                </a>
                <a href="doctor/index.php" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    View Doctor Index
                </a>
            </div>
        </div>
    </div>
</body>
</html>
