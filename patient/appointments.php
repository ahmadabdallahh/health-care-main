<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Ensure user is logged in as a patient
if (!is_logged_in() || $_SESSION['user_type'] !== 'patient') {
    header('Location: ../login.php?error=access_denied');
    exit();
}

$user_id = $_SESSION['user_id'];
$appointments = get_appointments_by_user_id($user_id);

$pageTitle = 'مواعيــدي';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts (Cairo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <script>
        // Custom Tailwind Config
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="font-cairo bg-gray-50">

<?php require_once '../includes/header.php'; ?>

<main class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">

        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-10"><?php echo htmlspecialchars($pageTitle); ?></h1>

        <!-- Appointments Section -->
        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-xl max-w-5xl mx-auto">

            <?php if (empty($appointments)): ?>
                <div class="text-center py-16">
                    <i class="fas fa-calendar-times fa-4x text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">ليس لديك أي مواعيد محجوزة حتى الآن.</p>
                    <a href="../search.php" class="mt-6 inline-block bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition">
                        ابحث عن طبيب واحجز الآن
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطبيب</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ والوقت</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العيادة</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($appointments as $appt): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($appt['doctor_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($appt['specialty_name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo date('d M, Y', strtotime($appt['appointment_date'])); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo date('h:i A', strtotime($appt['appointment_time'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo htmlspecialchars($appt['clinic_name']); ?><br>
                                        <small class="text-gray-500"><?php echo htmlspecialchars($appt['clinic_address']); ?></small>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php
                                                switch ($appt['status']) {
                                                    case 'confirmed': echo 'bg-green-100 text-green-800'; break;
                                                    case 'completed': echo 'bg-blue-100 text-blue-800'; break;
                                                    case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                                    default: echo 'bg-yellow-100 text-yellow-800';
                                                }
                                            ?>">
                                            <?php echo htmlspecialchars($appt['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php if ($appt['status'] === 'confirmed'): ?>
                                            <a href="../cancel_appointment.php?id=<?php echo $appt['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('هل أنت متأكد من رغبتك في إلغاء هذا الموعد؟');">إلغاء</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
require_once '../includes/footer.php';
?>
