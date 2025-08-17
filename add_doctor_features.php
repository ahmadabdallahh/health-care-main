<?php
require_once 'config.php';

echo "<h2>Adding Doctor Dashboard Features</h2>";

try {
    // Check if oncall_status column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'oncall_status'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$column) {
        echo "<p style='color: orange;'>⚠️ Adding oncall_status column...</p>";
        $conn->exec("ALTER TABLE users ADD COLUMN oncall_status ENUM('on', 'off') DEFAULT 'off' AFTER user_type");
        echo "<p style='color: green;'>✅ oncall_status column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ oncall_status column already exists</p>";
    }

    // Check if specialty column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'specialty'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$column) {
        echo "<p style='color: orange;'>⚠️ Adding specialty column...</p>";
        $conn->exec("ALTER TABLE users ADD COLUMN specialty VARCHAR(100) DEFAULT 'طب عام' AFTER user_type");
        echo "<p style='color: green;'>✅ specialty column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ specialty column already exists</p>";
    }

    // Check if appointments table has the right structure
    $stmt = $conn->query("SHOW COLUMNS FROM appointments LIKE 'appointment_type'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$column) {
        echo "<p style='color: orange;'>⚠️ Adding appointment_type column...</p>";
        $conn->exec("ALTER TABLE appointments ADD COLUMN appointment_type VARCHAR(50) DEFAULT 'استشارة عامة' AFTER appointment_time");
        echo "<p style='color: green;'>✅ appointment_type column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ appointment_type column already exists</p>";
    }

    // Update some sample data for doctors
    $stmt = $conn->prepare("UPDATE users SET specialty = ? WHERE user_type = 'doctor' AND specialty IS NULL");
    $specialties = ['طب عام', 'طب القلب', 'طب الأطفال', 'طب النساء', 'طب العظام', 'طب الجلد'];
    $stmt->execute([$specialties[array_rand($specialties)]]);

    echo "<p style='color: green;'>✅ Sample specialties added to doctors</p>";

    // Show current table structure
    echo "<h3>Current Users Table Structure:</h3>";
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' style='border-collapse: collapse; margin-top: 20px;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<h3>Current Appointments Table Structure:</h3>";
    $stmt = $conn->query("DESCRIBE appointments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' style='border-collapse: collapse; margin-top: 20px;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<div style='margin-top: 30px;'>";
echo "<a href='doctor/profile.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px;'>";
echo "← العودة إلى لوحة التحكم الطبية";
echo "</a>";
echo "</div>";
?>
