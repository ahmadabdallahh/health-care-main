<?php
session_start();
require_once 'config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Ensure user is logged in
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false, 'message' => '', 'image_url' => ''];

// Enable error logging
error_log("Profile picture upload attempt for user ID: $user_id");

// Check if file was uploaded
if (!isset($_FILES['profile_picture'])) {
    $response['message'] = 'No file uploaded';
    error_log("No file uploaded for user ID: $user_id");
    echo json_encode($response);
    exit();
}

$file = $_FILES['profile_picture'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    $upload_errors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
    ];

    $error_message = isset($upload_errors[$file['error']]) ? $upload_errors[$file['error']] : 'Unknown upload error';
    $response['message'] = 'Upload error: ' . $error_message;
    error_log("Upload error for user ID $user_id: " . $error_message);
    echo json_encode($response);
    exit();
}

// Validate file type
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    $response['message'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
    error_log("Invalid file type for user ID $user_id: " . $file['type']);
    echo json_encode($response);
    exit();
}

// Validate file size (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    $response['message'] = 'File too large. Maximum size is 5MB.';
    error_log("File too large for user ID $user_id: " . $file['size'] . " bytes");
    echo json_encode($response);
    exit();
}

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/profile_pictures/';
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        $response['message'] = 'Failed to create upload directory';
        error_log("Failed to create upload directory: $upload_dir");
        echo json_encode($response);
        exit();
    }
}

// Check if directory is writable
if (!is_writable($upload_dir)) {
    $response['message'] = 'Upload directory is not writable';
    error_log("Upload directory not writable: $upload_dir");
    echo json_encode($response);
    exit();
}

// Generate unique filename
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
$filepath = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    try {
        // Check if profile_image column exists
        $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
        $column = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$column) {
            // Add the column if it doesn't exist
            $conn->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL AFTER phone");
            error_log("Added profile_image column to users table");
        }

        // Update user's profile_image in database
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->execute([$filepath, $user_id]);

        $response['success'] = true;
        $response['message'] = 'Profile picture updated successfully';
        $response['image_url'] = $filepath;

        error_log("Profile picture updated successfully for user ID $user_id: $filepath");

    } catch (Exception $e) {
        // Delete uploaded file if database update fails
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        $response['message'] = 'Database error: ' . $e->getMessage();
        error_log("Database error for user ID $user_id: " . $e->getMessage());
    }
} else {
    $response['message'] = 'Failed to save uploaded file';
    error_log("Failed to move uploaded file for user ID $user_id from " . $file['tmp_name'] . " to $filepath");
}

echo json_encode($response);
?>
