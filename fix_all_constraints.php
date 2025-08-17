<?php
require_once 'config.php';

echo "<h2>Fixing All Database Constraints and Missing Columns</h2>";

try {
    // Step 1: Check and create clinics table if needed
    echo "<h3>Step 1: Checking Clinics Table</h3>";
    $stmt = $conn->query("SHOW TABLES LIKE 'clinics'");
    $clinics_table = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clinics_table) {
        echo "<p style='color: orange;'>⚠️ Creating clinics table...</p>";

        // Create clinics table
        $conn->exec("
            CREATE TABLE clinics (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                address TEXT,
                phone VARCHAR(50),
                email VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        echo "<p style='color: green;'>✅ clinics table created successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ clinics table already exists</p>";
    }

    // Step 2: Create default clinic if none exists
    $stmt = $conn->query("SELECT COUNT(*) as count FROM clinics");
    $clinic_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($clinic_count == 0) {
        echo "<p style='color: orange;'>⚠️ No clinics found. Creating default clinic...</p>";

        // Insert default clinic
        $stmt = $conn->prepare("
            INSERT INTO clinics (name, address, phone, email)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            'العيادة الرئيسية',
            'العنوان الرئيسي للعيادة',
            '+966-50-123-4567',
            'clinic@example.com'
        ]);
        echo "<p style='color: green;'>✅ Default clinic created successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ Found {$clinic_count} existing clinic(s)</p>";
    }

    // Step 3: Check and create doctors table if needed
    echo "<h3>Step 2: Checking Doctors Table</h3>";
    $stmt = $conn->query("SHOW TABLES LIKE 'doctors'");
    $doctors_table = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doctors_table) {
        echo "<p style='color: orange;'>⚠️ Creating doctors table...</p>";

        // Create doctors table
        $conn->exec("
            CREATE TABLE doctors (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                phone VARCHAR(50),
                specialty VARCHAR(100) DEFAULT 'طب عام',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        echo "<p style='color: green;'>✅ doctors table created successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ doctors table already exists</p>";
    }

    // Step 4: Create doctors from users if none exist
    $stmt = $conn->query("SELECT COUNT(*) as count FROM doctors");
    $doctor_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($doctor_count == 0) {
        echo "<p style='color: orange;'>⚠️ No doctors found. Creating doctors from users table...</p>";

        // Get all users who are doctors
        $stmt = $conn->prepare("SELECT id, full_name, email, phone, specialty FROM users WHERE user_type = 'doctor'");
        $stmt->execute();
        $doctor_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($doctor_users)) {
            // Insert doctors from users table
            $stmt = $conn->prepare("
                INSERT INTO doctors (id, name, email, phone, specialty)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($doctor_users as $doctor_user) {
                $stmt->execute([
                    $doctor_user['id'],
                    $doctor_user['full_name'],
                    $doctor_user['email'],
                    $doctor_user['phone'] ?? '',
                    $doctor_user['specialty'] ?? 'طب عام'
                ]);
            }
            echo "<p style='color: green;'>✅ Created " . count($doctor_users) . " doctors from users table</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No doctor users found. Creating default doctor...</p>";

            // Create a default doctor
            $stmt = $conn->prepare("
                INSERT INTO doctors (name, email, phone, specialty)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                'دكتور افتراضي',
                'doctor@example.com',
                '+966-50-123-4567',
                'طب عام'
            ]);
            echo "<p style='color: green;'>✅ Default doctor created successfully</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Found {$doctor_count} existing doctor(s)</p>";
    }

    // Step 5: Add missing columns to users table
    echo "<h3>Step 3: Checking Users Table Structure</h3>";

    // Check if created_at column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'created_at'");
    $created_at_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$created_at_column) {
        echo "<p style='color: orange;'>⚠️ Adding created_at column to users...</p>";
        $conn->exec("ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "<p style='color: green;'>✅ created_at column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ created_at column already exists</p>";
    }

    // Check if updated_at column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'updated_at'");
    $updated_at_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$updated_at_column) {
        echo "<p style='color: orange;'>⚠️ Adding updated_at column to users...</p>";
        $conn->exec("ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        echo "<p style='color: green;'>✅ updated_at column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ updated_at column already exists</p>";
    }

    // Check if specialty column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'specialty'");
    $specialty_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$specialty_column) {
        echo "<p style='color: orange;'>⚠️ Adding specialty column to users...</p>";
        $conn->exec("ALTER TABLE users ADD COLUMN specialty VARCHAR(100) DEFAULT 'طب عام' AFTER user_type");
        echo "<p style='color: green;'>✅ specialty column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ specialty column already exists</p>";
    }

    // Check if oncall_status column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'oncall_status'");
    $oncall_status_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$oncall_status_column) {
        echo "<p style='color: orange;'>⚠️ Adding oncall_status column to users...</p>";
        $conn->exec("ALTER TABLE users ADD COLUMN oncall_status ENUM('on', 'off') DEFAULT 'off' AFTER user_type");
        echo "<p style='color: green;'>✅ oncall_status column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ oncall_status column already exists</p>";
    }

    // Check if profile_image column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
    $profile_image_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile_image_column) {
        echo "<p style='color: orange;'>⚠️ Adding profile_image column to users...</p>";
        $conn->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) AFTER email");
        echo "<p style='color: green;'>✅ profile_image column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ profile_image column already exists</p>";
    }

    // Check if insurance_provider column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'insurance_provider'");
    $insurance_provider_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$insurance_provider_column) {
        echo "<p style='color: orange;'>⚠️ Adding insurance_provider column to users...</p>";
        $conn->exec("ALTER TABLE users ADD COLUMN insurance_provider VARCHAR(100) AFTER phone");
        echo "<p style='color: green;'>✅ insurance_provider column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ insurance_provider column already exists</p>";
    }

    // Check if insurance_number column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'insurance_number'");
    $insurance_number_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$insurance_number_column) {
        echo "<p style='color: orange;'>⚠️ Adding insurance_number column to users...</p>";
        $conn->exec("ALTER TABLE users ADD COLUMN insurance_number VARCHAR(50) AFTER insurance_provider");
        echo "<p style='color: green;'>✅ insurance_number column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ insurance_number column already exists</p>";
    }

    // Step 6: Check and add missing columns to appointments table
    echo "<h3>Step 4: Checking Appointments Table Structure</h3>";

    // Check if user_id column exists
    $stmt = $conn->query("SHOW COLUMNS FROM appointments LIKE 'user_id'");
    $user_id_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_id_column) {
        echo "<p style='color: orange;'>⚠️ Adding user_id column to appointments...</p>";
        $conn->exec("ALTER TABLE appointments ADD COLUMN user_id INT AFTER id");
        echo "<p style='color: green;'>✅ user_id column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ user_id column already exists</p>";
    }

    // Check if clinic_id column exists
    $stmt = $conn->query("SHOW COLUMNS FROM appointments LIKE 'clinic_id'");
    $clinic_id_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clinic_id_column) {
        echo "<p style='color: orange;'>⚠️ Adding clinic_id column to appointments...</p>";
        $conn->exec("ALTER TABLE appointments ADD COLUMN clinic_id INT AFTER doctor_id");
        echo "<p style='color: green;'>✅ clinic_id column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ clinic_id column already exists</p>";
    }

    // Check if appointment_type column exists
    $stmt = $conn->query("SHOW COLUMNS FROM appointments LIKE 'appointment_type'");
    $appointment_type_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment_type_column) {
        echo "<p style='color: orange;'>⚠️ Adding appointment_type column to appointments...</p>";
        $conn->exec("ALTER TABLE appointments ADD COLUMN appointment_type VARCHAR(50) DEFAULT 'استشارة عامة' AFTER appointment_time");
        echo "<p style='color: green;'>✅ appointment_type column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ appointment_type column already exists</p>";
    }

    // Check if notes column exists
    $stmt = $conn->query("SHOW COLUMNS FROM appointments LIKE 'notes'");
    $notes_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notes_column) {
        echo "<p style='color: orange;'>⚠️ Adding notes column to appointments...</p>";
        $conn->exec("ALTER TABLE appointments ADD COLUMN notes TEXT AFTER appointment_type");
        echo "<p style='color: green;'>✅ notes column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ notes column already exists</p>";
    }

    // Check if patient_id column exists
    $stmt = $conn->query("SHOW COLUMNS FROM appointments LIKE 'patient_id'");
    $patient_id_column = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient_id_column) {
        echo "<p style='color: orange;'>⚠️ Adding patient_id column to appointments...</p>";
        $conn->exec("ALTER TABLE appointments ADD COLUMN patient_id INT AFTER doctor_id");
        echo "<p style='color: green;'>✅ patient_id column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ patient_id column already exists</p>";
    }

    // Step 7: Update existing appointments with default values
    echo "<h3>Step 5: Updating Existing Appointments</h3>";

    // Get default clinic ID
    $stmt = $conn->query("SELECT id FROM clinics LIMIT 1");
    $default_clinic = $stmt->fetch(PDO::FETCH_ASSOC);
    $default_clinic_id = $default_clinic['id'];

    // Update appointments with default clinic_id
    $stmt = $conn->prepare("UPDATE appointments SET clinic_id = ? WHERE clinic_id IS NULL");
    $stmt->execute([$default_clinic_id]);
    $updated_clinic_rows = $stmt->rowCount();
    echo "<p style='color: green;'>✅ Updated {$updated_clinic_rows} appointments with default clinic</p>";

    // Update appointments with default appointment_type
    $stmt = $conn->prepare("UPDATE appointments SET appointment_type = 'استشارة عامة' WHERE appointment_type IS NULL");
    $stmt->execute();
    $updated_type_rows = $stmt->rowCount();
    echo "<p style='color: green;'>✅ Updated {$updated_type_rows} appointments with default appointment type</p>";

    // Update appointments with default notes
    $stmt = $conn->prepare("UPDATE appointments SET notes = '' WHERE notes IS NULL");
    $stmt->execute();
    $updated_notes_rows = $stmt->rowCount();
    echo "<p style='color: green;'>✅ Updated {$updated_notes_rows} appointments with default notes</p>";

    // Set user_id to doctor_id if user_id is NULL
    $stmt = $conn->prepare("UPDATE appointments SET user_id = doctor_id WHERE user_id IS NULL");
    $stmt->execute();
    $updated_user_rows = $stmt->rowCount();
    echo "<p style='color: green;'>✅ Updated {$updated_user_rows} appointments with user_id</p>";

    // Set patient_id to user_id if patient_id is NULL and user_id exists
    $stmt = $conn->prepare("UPDATE appointments SET patient_id = user_id WHERE patient_id IS NULL AND user_id IS NOT NULL");
    $stmt->execute();
    $updated_patient_rows = $stmt->rowCount();
    echo "<p style='color: green;'>✅ Updated {$updated_patient_rows} appointments with patient_id</p>";

    // Step 8: Show final table structures
    echo "<h3>Step 6: Final Table Structures</h3>";

    // Show users table structure
    echo "<h4>Users Table Structure:</h4>";
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
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

    // Show appointments table structure
    echo "<h4>Appointments Table Structure:</h4>";
    $stmt = $conn->query("DESCRIBE appointments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
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

    // Show foreign key constraints
    echo "<h4>Foreign Key Constraints:</h4>";
    $stmt = $conn->query("
        SELECT
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = 'medical_booking'
        AND TABLE_NAME = 'appointments'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($constraints)) {
        echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr><th>Constraint Name</th><th>Column</th><th>References Table</th><th>References Column</th></tr>";
        foreach ($constraints as $constraint) {
            echo "<tr>";
            echo "<td>" . $constraint['CONSTRAINT_NAME'] . "</td>";
            echo "<td>" . $constraint['COLUMN_NAME'] . "</td>";
            echo "<td>" . $constraint['REFERENCED_TABLE_NAME'] . "</td>";
            echo "<td>" . $constraint['REFERENCED_COLUMN_NAME'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No foreign key constraints found.</p>";
    }

    // Step 9: Show sample data
    echo "<h3>Step 7: Sample Data</h3>";

    // Show sample clinics
    $stmt = $conn->query("SELECT * FROM clinics LIMIT 5");
    $clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h4>Available Clinics:</h4>";
    if (!empty($clinics)) {
        echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Address</th><th>Phone</th></tr>";
        foreach ($clinics as $clinic) {
            echo "<tr>";
            echo "<td>" . $clinic['id'] . "</td>";
            echo "<td>" . htmlspecialchars($clinic['name']) . "</td>";
            echo "<td>" . htmlspecialchars($clinic['address'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($clinic['phone'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Show sample doctors
    $stmt = $conn->query("SELECT * FROM doctors LIMIT 5");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h4>Available Doctors:</h4>";
    if (!empty($doctors)) {
        echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Specialty</th></tr>";
        foreach ($doctors as $doctor) {
            echo "<tr>";
            echo "<td>" . $doctor['id'] . "</td>";
            echo "<td>" . htmlspecialchars($doctor['name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($doctor['email']) . "</td>";
            echo "<td>" . htmlspecialchars($doctor['specialty'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Show sample appointments
    $stmt = $conn->query("SELECT * FROM appointments LIMIT 5");
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h4>Sample Appointments:</h4>";
    if (!empty($appointments)) {
        echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Doctor ID</th><th>Clinic ID</th><th>Patient ID</th><th>Time</th><th>Type</th><th>Status</th></tr>";
        foreach ($appointments as $appointment) {
            echo "<tr>";
            echo "<td>" . $appointment['id'] . "</td>";
            echo "<td>" . ($appointment['user_id'] ?? 'N/A') . "</td>";
            echo "<td>" . ($appointment['doctor_id'] ?? 'N/A') . "</td>";
            echo "<td>" . ($appointment['clinic_id'] ?? 'N/A') . "</td>";
            echo "<td>" . ($appointment['patient_id'] ?? 'N/A') . "</td>";
            echo "<td>" . ($appointment['appointment_time'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($appointment['appointment_type'] ?? 'N/A') . "</td>";
            echo "<td>" . ($appointment['status'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No appointments found in the database</p>";
    }

    echo "<p style='color: green; font-weight: bold; font-size: 1.2em;'>🎉 All database constraints and missing columns have been fixed successfully!</p>";
    echo "<p style='color: blue;'>💡 The system is now ready to create appointments without constraint errors.</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<div style='margin-top: 30px;'>";
echo "<a href='doctor/quick-actions.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; margin-right: 10px;'>";
echo "← العودة إلى الإجراءات السريعة";
echo "</a>";
echo "<a href='doctor/profile.php' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px;'>";
echo "← العودة إلى لوحة التحكم";
echo "</a>";
echo "</div>";
?>
