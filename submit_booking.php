<?php
session_start();
require_once 'config.php';
require_once 'includes/functions.php';

// 1. Ensure user is logged in as a patient
if (!is_logged_in() || $_SESSION['role'] !== 'patient') {
    header('Location: login.php?error=auth_required');
    exit();
}

// 2. Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// 3. Validate incoming data
$doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
$appointment_date = filter_input(INPUT_POST, 'appointment_date');
$appointment_time = filter_input(INPUT_POST, 'appointment_time');
$user_id = $_SESSION['user_id'];

if (!$doctor_id || !$appointment_date || !$appointment_time) {
    $_SESSION['error_message'] = 'بيانات الحجز غير مكتملة. الرجاء المحاولة مرة أخرى.';
    header('Location: book_appointment.php?doctor_id=' . $doctor_id);
    exit();
}

// 4. Get Doctor's clinic_id
$doctor = get_doctor_by_id($doctor_id);
if (!$doctor || !$doctor['clinic_id']) {
    $_SESSION['error_message'] = 'لم يتم العثور على الطبيب أو العيادة.';
    header('Location: search.php');
    exit();
}
$clinic_id = $doctor['clinic_id'];

// 5. Final check: Is the slot still available?
// This prevents race conditions where two users book the same slot simultaneously.
$available_slots = get_available_slots($doctor_id, $appointment_date);
$is_still_available = false;
foreach ($available_slots as $slot) {
    if (date('H:i:s', strtotime($slot)) == date('H:i:s', strtotime($appointment_time))) {
        $is_still_available = true;
        break;
    }
}

if (!$is_still_available) {
    $_SESSION['error_message'] = 'عذراً، هذا الموعد تم حجزه للتو. الرجاء اختيار موعد آخر.';
    header('Location: book_appointment.php?doctor_id=' . $doctor_id);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure all required fields are present
    if (!isset($_SESSION['user_id'], $_POST['doctor_id'], $_POST['clinic_id'], $_POST['date'], $_POST['time'])) {
        $_SESSION['error'] = 'Incomplete booking information. Please try again.';
        header('Location: search.php'); // Redirect to a safe starting point
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor_id'];
    $clinic_id = $_POST['clinic_id'];
    $appointment_date = $_POST['date'];
    $appointment_time = $_POST['time'];

    // Extra validation to ensure clinic_id is not empty
    if (empty($clinic_id)) {
        $_SESSION['error'] = 'The clinic ID is missing. Please select a doctor from the list.';
        header('Location: search.php');
        exit();
    }

    // Create appointment
    $sql = "INSERT INTO appointments (user_id, doctor_id, clinic_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, 'confirmed')";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$user_id, $doctor_id, $clinic_id, $appointment_date, $appointment_time])) {
        $_SESSION['success'] = 'Appointment booked successfully!';
        // Redirect to a confirmation or appointments list page
        header('Location: appointments.php');
        exit();
    } else {
        $_SESSION['error'] = 'Failed to book appointment due to a database error. Please try again.';
        // Redirect back to the booking page for the specific doctor
        header('Location: book_appointment.php?doctor_id=' . $doctor_id);
        exit();
    }
}
