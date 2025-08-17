<?php
require_once 'config.php';

echo "<h2>Fix Profile Image Column</h2>";

try {
    // Check if profile_image column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($column) {
        echo "<p style='color: green;'>✅ profile_image column already exists</p>";
        echo "<p>Column type: " . $column['Type'] . "</p>";
        echo "<p>Null: " . $column['Null'] . "</p>";
        echo "<p>Default: " . $column['Default'] . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ profile_image column does not exist. Adding it...</p>";

        // Add the profile_image column
        $sql = "ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL AFTER phone";
        $conn->exec($sql);

        echo "<p style='color: green;'>✅ profile_image column added successfully</p>";

        // Verify the column was added
        $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
        $column = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($column) {
            echo "<p style='color: green;'>✅ Column verified: " . $column['Type'] . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Column was not added properly</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Current Users Table Structure:</h3>";
try {
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' style='border-collapse: collapse;'>";
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
    echo "<p style='color: red;'>❌ Error getting table structure: " . $e->getMessage() . "</p>";
}
?>
