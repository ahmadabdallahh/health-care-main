<?php
session_start();
require_once 'config.php';
require_once 'includes/functions.php';

echo "<h2>Debug Profile Picture Upload</h2>";

// Check if user is logged in
if (!is_logged_in()) {
    echo "<p style='color: red;'>❌ User not logged in</p>";
    exit();
}

echo "<p style='color: green;'>✅ User logged in (ID: " . $_SESSION['user_id'] . ")</p>";

// Check upload directory
$upload_dir = 'uploads/profile_pictures/';
echo "<h3>Upload Directory Check:</h3>";
if (file_exists($upload_dir)) {
    echo "<p style='color: green;'>✅ Upload directory exists: $upload_dir</p>";
    echo "<p>Directory permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "</p>";
} else {
    echo "<p style='color: red;'>❌ Upload directory does not exist: $upload_dir</p>";
    echo "<p>Attempting to create directory...</p>";
    if (mkdir($upload_dir, 0755, true)) {
        echo "<p style='color: green;'>✅ Directory created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create directory</p>";
    }
}

// Check if directory is writable
if (is_writable($upload_dir)) {
    echo "<p style='color: green;'>✅ Directory is writable</p>";
} else {
    echo "<p style='color: red;'>❌ Directory is not writable</p>";
}

// Check PHP upload settings
echo "<h3>PHP Upload Settings:</h3>";
echo "<p>upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>post_max_size: " . ini_get('post_max_size') . "</p>";
echo "<p>max_file_uploads: " . ini_get('max_file_uploads') . "</p>";
echo "<p>file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</p>";

// Check if we can write to the directory
echo "<h3>Write Test:</h3>";
$test_file = $upload_dir . 'test_write.txt';
if (file_put_contents($test_file, 'test')) {
    echo "<p style='color: green;'>✅ Can write to directory</p>";
    unlink($test_file); // Clean up
} else {
    echo "<p style='color: red;'>❌ Cannot write to directory</p>";
}

// Test database connection
echo "<h3>Database Connection Test:</h3>";
try {
    $stmt = $conn->prepare("SELECT id, full_name, profile_image FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<p style='color: green;'>✅ Database connection working</p>";
        echo "<p>Current user: " . htmlspecialchars($user['full_name']) . "</p>";
        echo "<p>Current profile_image: " . htmlspecialchars($user['profile_image'] ?? 'None') . "</p>";
    } else {
        echo "<p style='color: red;'>❌ User not found in database</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Check if profile_image column exists
echo "<h3>Database Schema Check:</h3>";
try {
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($column) {
        echo "<p style='color: green;'>✅ profile_image column exists</p>";
        echo "<p>Column type: " . $column['Type'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ profile_image column does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database schema error: " . $e->getMessage() . "</p>";
}

// Test file upload simulation
echo "<h3>File Upload Test:</h3>";
if ($_FILES) {
    echo "<p>Files received:</p>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
} else {
    echo "<p>No files uploaded yet</p>";
}

// Create a simple upload test form
echo "<h3>Upload Test Form:</h3>";
?>
<form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file" accept="image/*">
    <input type="submit" value="Test Upload">
</form>

<?php
if ($_POST && isset($_FILES['test_file'])) {
    echo "<h3>Upload Test Results:</h3>";
    $file = $_FILES['test_file'];

    echo "<p>File name: " . htmlspecialchars($file['name']) . "</p>";
    echo "<p>File type: " . htmlspecialchars($file['type']) . "</p>";
    echo "<p>File size: " . $file['size'] . " bytes</p>";
    echo "<p>Upload error: " . $file['error'] . "</p>";

    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (in_array($file['type'], $allowed_types)) {
            echo "<p style='color: green;'>✅ File type is valid</p>";

            if ($file['size'] <= 5 * 1024 * 1024) {
                echo "<p style='color: green;'>✅ File size is acceptable</p>";

                $filename = 'test_' . time() . '_' . $file['name'];
                $filepath = $upload_dir . $filename;

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    echo "<p style='color: green;'>✅ File uploaded successfully to: $filepath</p>";

                    // Test database update
                    try {
                        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                        $stmt->execute([$filepath, $_SESSION['user_id']]);
                        echo "<p style='color: green;'>✅ Database updated successfully</p>";
                    } catch (Exception $e) {
                        echo "<p style='color: red;'>❌ Database update failed: " . $e->getMessage() . "</p>";
                    }
                } else {
                    echo "<p style='color: red;'>❌ Failed to move uploaded file</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ File too large</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Invalid file type</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Upload error occurred</p>";
    }
}
?>
