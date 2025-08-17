<?php
session_start();
header('Content-Type: text/css');

$theme = $_SESSION['doctor_theme'] ?? 'medical-blue';

// Define color schemes for different themes
$themes = [
    'medical-blue' => [
        'primary' => '#3b82f6',
        'primary-dark' => '#2563eb',
        'primary-light' => '#dbeafe',
        'secondary' => '#1e40af',
        'accent' => '#60a5fa',
        'success' => '#10b981',
        'warning' => '#f59e0b',
        'danger' => '#ef4444',
        'text-primary' => '#1f2937',
        'text-secondary' => '#4b5563',
        'bg-primary' => '#ffffff',
        'bg-secondary' => '#f8fafc',
        'border-light' => '#e2e8f0',
        'glass-bg' => 'rgba(255, 255, 255, 0.9)',
        'glass-shadow' => '0 8px 32px rgba(59, 130, 246, 0.1)',
        'gradient-primary' => 'linear-gradient(135deg, #3b82f6 0%, #1e40af 100%)',
        'shadow-primary' => 'rgba(59, 130, 246, 0.2)'
    ],
    'medical-green' => [
        'primary' => '#10b981',
        'primary-dark' => '#059669',
        'primary-light' => '#d1fae5',
        'secondary' => '#047857',
        'accent' => '#34d399',
        'success' => '#059669',
        'warning' => '#f59e0b',
        'danger' => '#ef4444',
        'text-primary' => '#1f2937',
        'text-secondary' => '#4b5563',
        'bg-primary' => '#ffffff',
        'bg-secondary' => '#f0fdf4',
        'border-light' => '#d1fae5',
        'glass-bg' => 'rgba(255, 255, 255, 0.9)',
        'glass-shadow' => '0 8px 32px rgba(16, 185, 129, 0.1)',
        'gradient-primary' => 'linear-gradient(135deg, #10b981 0%, #047857 100%)',
        'shadow-primary' => 'rgba(16, 185, 129, 0.2)'
    ],
    'warm-orange' => [
        'primary' => '#f97316',
        'primary-dark' => '#ea580c',
        'primary-light' => '#fed7aa',
        'secondary' => '#c2410c',
        'accent' => '#fb923c',
        'success' => '#10b981',
        'warning' => '#f59e0b',
        'danger' => '#ef4444',
        'text-primary' => '#1f2937',
        'text-secondary' => '#4b5563',
        'bg-primary' => '#ffffff',
        'bg-secondary' => '#fff7ed',
        'border-light' => '#fed7aa',
        'glass-bg' => 'rgba(255, 255, 255, 0.9)',
        'glass-shadow' => '0 8px 32px rgba(249, 115, 22, 0.1)',
        'gradient-primary' => 'linear-gradient(135deg, #f97316 0%, #c2410c 100%)',
        'shadow-primary' => 'rgba(249, 115, 22, 0.2)'
    ],
    'professional-gray' => [
        'primary' => '#6b7280',
        'primary-dark' => '#4b5563',
        'primary-light' => '#f3f4f6',
        'secondary' => '#374151',
        'accent' => '#9ca3af',
        'success' => '#10b981',
        'warning' => '#f59e0b',
        'danger' => '#ef4444',
        'text-primary' => '#1f2937',
        'text-secondary' => '#4b5563',
        'bg-primary' => '#ffffff',
        'bg-secondary' => '#f9fafb',
        'border-light' => '#e5e7eb',
        'glass-bg' => 'rgba(255, 255, 255, 0.9)',
        'glass-shadow' => '0 8px 32px rgba(107, 114, 128, 0.1)',
        'gradient-primary' => 'linear-gradient(135deg, #6b7280 0%, #374151 100%)',
        'shadow-primary' => 'rgba(107, 114, 128, 0.2)'
    ],
    'royal-purple' => [
        'primary' => '#8b5cf6',
        'primary-dark' => '#7c3aed',
        'primary-light' => '#e9d5ff',
        'secondary' => '#6d28d9',
        'accent' => '#a78bfa',
        'success' => '#10b981',
        'warning' => '#f59e0b',
        'danger' => '#ef4444',
        'text-primary' => '#1f2937',
        'text-secondary' => '#4b5563',
        'bg-primary' => '#ffffff',
        'bg-secondary' => '#faf5ff',
        'border-light' => '#e9d5ff',
        'glass-bg' => 'rgba(255, 255, 255, 0.9)',
        'glass-shadow' => '0 8px 32px rgba(139, 92, 246, 0.1)',
        'gradient-primary' => 'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)',
        'shadow-primary' => 'rgba(139, 92, 246, 0.2)'
    ],
    'emergency-red' => [
        'primary' => '#ef4444',
        'primary-dark' => '#dc2626',
        'primary-light' => '#fecaca',
        'secondary' => '#b91c1c',
        'accent' => '#f87171',
        'success' => '#10b981',
        'warning' => '#f59e0b',
        'danger' => '#dc2626',
        'text-primary' => '#1f2937',
        'text-secondary' => '#4b5563',
        'bg-primary' => '#ffffff',
        'bg-secondary' => '#fef2f2',
        'border-light' => '#fecaca',
        'glass-bg' => 'rgba(255, 255, 255, 0.9)',
        'glass-shadow' => '0 8px 32px rgba(239, 68, 68, 0.1)',
        'gradient-primary' => 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)',
        'shadow-primary' => 'rgba(239, 68, 68, 0.2)'
    ]
];

$colors = $themes[$theme] ?? $themes['medical-blue'];
?>

/* Doctor Dashboard Theme: <?php echo $theme; ?> */

:root {
    --primary-color: <?php echo $colors['primary']; ?>;
    --primary-dark: <?php echo $colors['primary-dark']; ?>;
    --primary-light: <?php echo $colors['primary-light']; ?>;
    --secondary-color: <?php echo $colors['secondary']; ?>;
    --accent-color: <?php echo $colors['accent']; ?>;
    --success-color: <?php echo $colors['success']; ?>;
    --warning-color: <?php echo $colors['warning']; ?>;
    --danger-color: <?php echo $colors['danger']; ?>;
    --text-primary: <?php echo $colors['text-primary']; ?>;
    --text-secondary: <?php echo $colors['text-secondary']; ?>;
    --bg-primary: <?php echo $colors['bg-primary']; ?>;
    --bg-secondary: <?php echo $colors['bg-secondary']; ?>;
    --border-light: <?php echo $colors['border-light']; ?>;
    --glass-bg: <?php echo $colors['glass-bg']; ?>;
    --glass-shadow: <?php echo $colors['glass-shadow']; ?>;
    --gradient-primary: <?php echo $colors['gradient-primary']; ?>;
    --shadow-primary: <?php echo $colors['shadow-primary']; ?>;

    /* Status Colors */
    --status-completed-bg: rgba(16, 185, 129, 0.1);
    --status-completed-text: #059669;
    --status-pending-bg: rgba(245, 158, 11, 0.1);
    --status-pending-text: #d97706;
    --status-cancelled-bg: rgba(239, 68, 68, 0.1);
    --status-cancelled-text: #dc2626;

    /* Additional Colors */
    --text-muted: #9ca3af;
    --border-medium: #d1d5db;
    --bg-tertiary: #f3f4f6;
    --success-dark: #047857;
    --warning-dark: #d97706;
}

/* Override existing styles with theme colors */
.medical-dashboard {
    background: var(--gradient-primary);
}

.doctor-sidebar,
.main-content,
.notifications-panel {
    background: var(--glass-bg);
    box-shadow: var(--glass-shadow);
}

.doctor-avatar {
    border-color: var(--primary-color);
    box-shadow: 0 10px 30px var(--shadow-primary);
}

.doctor-name {
    color: var(--text-primary);
}

.doctor-specialty {
    color: var(--text-secondary);
}

.toggle-switch.active {
    background: var(--success-color);
}

.stat-card {
    background: var(--bg-primary);
    border-color: var(--border-light);
}

.stat-card:hover {
    box-shadow: 0 10px 25px var(--shadow-primary);
}

.stat-number {
    color: var(--primary-color);
}

.stat-label {
    color: var(--text-secondary);
}

.calendar-title {
    color: var(--text-primary);
}

.calendar-nav button {
    background: var(--primary-color);
}

.calendar-nav button:hover {
    background: var(--primary-dark);
}

.appointment-card {
    background: var(--bg-primary);
    border-left-color: var(--primary-color);
}

.appointment-card.urgent {
    border-left-color: var(--danger-color);
    background: linear-gradient(135deg, var(--status-cancelled-bg), var(--bg-primary));
}

.appointment-card.regular {
    border-left-color: var(--success-color);
}

.appointment-card:hover {
    box-shadow: 0 15px 35px var(--shadow-primary);
}

.appointment-time {
    color: var(--text-primary);
}

.patient-name {
    color: var(--text-primary);
}

.appointment-type {
    color: var(--text-secondary);
}

.btn-start {
    background: var(--success-color);
}

.btn-start:hover {
    background: var(--success-dark);
}

.btn-reschedule {
    background: var(--warning-color);
}

.btn-reschedule:hover {
    background: var(--warning-dark);
}

.panel-header {
    color: var(--text-primary);
}

.emergency-alert {
    background: linear-gradient(135deg, var(--danger-color), #dc2626);
}

.notification-item {
    background: var(--bg-primary);
    border-left-color: var(--primary-color);
}

.notification-item:hover {
    box-shadow: 0 5px 15px var(--shadow-primary);
}

.notification-time {
    color: var(--text-muted);
}

.notification-text {
    color: var(--text-primary);
}

.queue-item {
    background: var(--bg-primary);
    border-left-color: var(--border-light);
}

.queue-item.priority-high {
    border-left-color: var(--danger-color);
    background: linear-gradient(135deg, var(--status-cancelled-bg), var(--bg-primary));
}

.queue-item.priority-medium {
    border-left-color: var(--warning-color);
}

.queue-item.priority-low {
    border-left-color: var(--success-color);
}

.queue-item:hover {
    box-shadow: 0 5px 15px var(--shadow-primary);
}

.queue-number {
    background: var(--primary-color);
}

.queue-patient {
    color: var(--text-primary);
}

.queue-time {
    color: var(--text-secondary);
}

.status-indicator.status-online {
    background: var(--success-color);
    box-shadow: 0 0 10px var(--success-color);
}

.status-indicator.status-busy {
    background: var(--warning-color);
    box-shadow: 0 0 10px var(--warning-color);
}

.status-indicator.status-offline {
    background: var(--text-muted);
}

/* Form Elements */
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px var(--shadow-primary);
}

.btn-primary {
    background: var(--gradient-primary);
}

.btn-primary:hover {
    box-shadow: 0 10px 20px var(--shadow-primary);
}

/* Chart Colors */
#weeklyChart {
    --chart-primary: var(--primary-color);
    --chart-secondary: var(--primary-light);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .medical-dashboard {
        background: var(--gradient-primary);
    }
}
