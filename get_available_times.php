<?php
require_once 'includes/functions.php';

header('Content-Type: application/json');

// This script is called via a JavaScript fetch() with a POST method.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method.']);
    exit();
}

// The request body is expected to be JSON.
$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid JSON provided.']);
    exit();
}

$doctor_id = isset($data['doctor_id']) ? (int)$data['doctor_id'] : 0;
$date = isset($data['date']) ? $data['date'] : '';

if (empty($doctor_id) || empty($date)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Doctor ID and date are required.']);
    exit();
}

// Now, call the function to get the data.
// The root cause of the bug is likely inside this function.
try {
    $available_slots = get_public_available_slots($doctor_id, $date);
    echo json_encode($available_slots);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    // Log the detailed error for debugging purposes.
    error_log('Error in get_available_times.php: ' . $e->getMessage());
    // Send a generic error message to the client.
    echo json_encode(['error' => 'An internal server error occurred.']);
}