<?php
/**
 * Export user data as a JSON file.
 */
session_start();
require_once 'config.php';
require_once 'includes/functions.php';

// Ensure user is logged in as a patient
if (!is_logged_in() || ($_SESSION['role'] !== 'patient' && $_SESSION['user_type'] !== 'patient')) {
    http_response_code(403);
    die('Access denied');
}

$user_id = $_SESSION['user_id'];
$user_data = [];

try {
    // Get user profile data
    $stmt = $conn->prepare("SELECT id, full_name, email, phone, date_of_birth, gender, insurance_provider, insurance_number, profile_image, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data['profile'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get appointments
    $stmt = $conn->prepare("SELECT a.*, d.full_name as doctor_name, c.name as clinic_name, s.name as specialty_name FROM appointments a JOIN doctors d ON a.doctor_id = d.id JOIN clinics c ON d.clinic_id = c.id JOIN specialties s ON d.specialty_id = s.id WHERE a.user_id = ? ORDER BY a.appointment_datetime DESC");
    $stmt->execute([$user_id]);
    $user_data['appointments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Medical records are part of the appointments table as doctor_notes
    // No separate query is needed as it's included in the appointments query.
    // The appointments query already fetches all necessary appointment details including notes.

} catch (Exception $e) {
    error_log("Data export error for user $user_id: " . $e->getMessage());
    http_response_code(500);
    // Provide a more detailed error message for debugging
    die('Failed to export data. Error: ' . $e->getMessage());
}

$filename = 'user_data_' . $user_id . '_' . date('Y-m-d') . '.json';

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo json_encode($user_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit();
