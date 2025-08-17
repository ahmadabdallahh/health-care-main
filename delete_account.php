<?php
/**
 * Handle user account deletion.
 */
session_start();
require_once 'config.php';
require_once 'includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Ensure user is logged in as a patient
if (!is_logged_in() || ($_SESSION['role'] !== 'patient' && $_SESSION['user_type'] !== 'patient')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

try {
    $conn->beginTransaction();

    // Delete related records first to maintain referential integrity
    // Delete medical records
    $stmt = $conn->prepare("DELETE mr FROM medical_records mr JOIN appointments a ON mr.appointment_id = a.id WHERE a.user_id = ?");
    $stmt->execute([$user_id]);

    // Delete appointments
    $stmt = $conn->prepare("DELETE FROM appointments WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Finally, delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    $conn->commit();

    // Destroy the session and log the user out
    session_destroy();

    echo json_encode(['success' => true, 'message' => 'Account deleted successfully.']);

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Account deletion failed for user $user_id: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting your account. Please try again later.']);
}

exit();
