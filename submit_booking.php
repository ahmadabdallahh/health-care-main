<?php
session_start();
require_once 'config.php';
require_once 'includes/functions.php';

// 1. Ensure user is logged in as a patient
if (!is_logged_in() || $_SESSION['user_type'] !== 'patient') {
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

// 6. Insert the appointment into the database
try {
    $sql = "INSERT INTO appointments (user_id, doctor_id, clinic_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, 'confirmed')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $doctor_id, $clinic_id, $appointment_date, $appointment_time]);

    // 7. Redirect to a success page (e.g., patient dashboard)
    $_SESSION['success_message'] = 'تم تأكيد حجزك بنجاح!';
    header('Location: patient/index.php'); // Assuming a patient dashboard exists
    exit();

} catch (PDOException $e) {
    error_log('Booking submission error: ' . $e->getMessage());
    $_SESSION['error_message'] = 'حدث خطأ فني أثناء تأكيد الحجز. الرجاء المحاولة لاحقاً.';
    header('Location: book_appointment.php?doctor_id=' . $doctor_id);
    exit();
}
