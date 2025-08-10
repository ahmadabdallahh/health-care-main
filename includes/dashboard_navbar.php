<?php
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">لوحة التحكم</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item <?php echo ($current_page == 'dashboard.php' || $current_page == 'doctor_home.php' || $current_page == 'patient_home.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="dashboard.php">الرئيسية</a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'appointments.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="appointments.php">المواعيد</a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'profile.php' || $current_page == 'doctor_profile.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="profile.php">الملف الشخصي</a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'notifications.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="notifications.php">الإشعارات</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">تسجيل الخروج</a>
            </li>
        </ul>
    </div>
</nav>
