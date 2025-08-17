<?php
// Get the selected theme from session
session_start();
$selected_theme = $_SESSION['selected_theme'] ?? 'default';

// Set content type to CSS
header('Content-Type: text/css');
?>

/*
 * Medical Appointment System - Color Scheme Configuration
 *
 * This file contains CSS custom properties (variables) that define the color scheme
 * for the entire Medical Appointment System. To change colors across the project:
 *
 * 1. Use the color customizer at color-customizer.php
 * 2. All pages that include this file will automatically use the new colors
 * 3. No need to modify individual page styles
 */

:root {
  /* Primary Brand Colors */
  --primary-color: #2563eb; /* Main brand color (blue) */
  --primary-dark: #1d4ed8; /* Darker shade for hover states */
  --primary-light: #3b82f6; /* Lighter shade for backgrounds */

  /* Secondary Colors */
  --secondary-color: #7c3aed; /* Secondary brand color (purple) */
  --secondary-dark: #6d28d9; /* Darker secondary */
  --secondary-light: #8b5cf6; /* Lighter secondary */

  /* Success Colors (Green) */
  --success-color: #059669; /* Success/Completed actions */
  --success-dark: #047857; /* Darker success */
  --success-light: #10b981; /* Lighter success */

  /* Warning Colors (Orange/Amber) */
  --warning-color: #d97706; /* Warning/Total counts */
  --warning-dark: #b45309; /* Darker warning */
  --warning-light: #f59e0b; /* Lighter warning */

  /* Danger Colors (Red) */
  --danger-color: #dc2626; /* Danger/Cancel actions */
  --danger-dark: #b91c1c; /* Darker danger */
  --danger-light: #ef4444; /* Lighter danger */

  /* Neutral Colors */
  --text-primary: #1f2937; /* Primary text color */
  --text-secondary: #4b5563; /* Secondary text color */
  --text-muted: #6b7280; /* Muted text color */
  --text-light: #9ca3af; /* Light text color */

  /* Background Colors */
  --bg-primary: #ffffff; /* Primary background */
  --bg-secondary: #f9fafb; /* Secondary background */
  --bg-tertiary: #f3f4f6; /* Tertiary background */
  --bg-dark: #111827; /* Dark background */

  /* Border Colors */
  --border-light: #e5e7eb; /* Light borders */
  --border-medium: #d1d5db; /* Medium borders */
  --border-dark: #9ca3af; /* Dark borders */

  /* Gradient Definitions */
  --gradient-primary: linear-gradient(
    135deg,
    var(--primary-color) 0%,
    var(--secondary-color) 100%
  );
  --gradient-success: linear-gradient(
    135deg,
    var(--success-color) 0%,
    var(--success-light) 100%
  );
  --gradient-warning: linear-gradient(
    135deg,
    var(--warning-color) 0%,
    var(--warning-light) 100%
  );
  --gradient-danger: linear-gradient(
    135deg,
    var(--danger-color) 0%,
    var(--danger-light) 100%
  );

  /* Shadow Colors */
  --shadow-light: rgba(0, 0, 0, 0.05);
  --shadow-medium: rgba(0, 0, 0, 0.1);
  --shadow-dark: rgba(0, 0, 0, 0.15);
  --shadow-primary: rgba(37, 99, 235, 0.2);
  --shadow-success: rgba(5, 150, 105, 0.2);
  --shadow-warning: rgba(217, 119, 6, 0.2);
  --shadow-danger: rgba(220, 38, 38, 0.2);

  /* Glassmorphism Effects */
  --glass-bg: rgba(255, 255, 255, 0.95);
  --glass-border: rgba(255, 255, 255, 0.2);
  --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);

  /* Status Colors */
  --status-confirmed-bg: #dbeafe;
  --status-confirmed-text: #1e40af;
  --status-completed-bg: #d1fae5;
  --status-completed-text: #065f46;
  --status-cancelled-bg: #fee2e2;
  --status-cancelled-text: #991b1b;
  --status-pending-bg: #fef3c7;
  --status-pending-text: #92400e;
}

<?php if ($selected_theme === 'modern-blue'): ?>
/* Modern Blue Theme Override */
:root {
  --primary-color: #2563eb;
  --primary-dark: #1d4ed8;
  --primary-light: #3b82f6;
  --secondary-color: #7c3aed;
  --secondary-dark: #6d28d9;
  --secondary-light: #8b5cf6;
  --gradient-primary: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
  --shadow-primary: rgba(37, 99, 235, 0.2);
}
<?php elseif ($selected_theme === 'medical-green'): ?>
/* Medical Green Theme Override */
:root {
  --primary-color: #059669;
  --primary-dark: #047857;
  --primary-light: #10b981;
  --secondary-color: #0891b2;
  --secondary-dark: #0e7490;
  --secondary-light: #06b6d4;
  --gradient-primary: linear-gradient(135deg, #059669 0%, #0891b2 100%);
  --shadow-primary: rgba(5, 150, 105, 0.2);
}
<?php elseif ($selected_theme === 'warm-orange'): ?>
/* Warm Orange Theme Override */
:root {
  --primary-color: #ea580c;
  --primary-dark: #c2410c;
  --primary-light: #fb923c;
  --secondary-color: #dc2626;
  --secondary-dark: #b91c1c;
  --secondary-light: #ef4444;
  --gradient-primary: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
  --shadow-primary: rgba(234, 88, 12, 0.2);
}
<?php elseif ($selected_theme === 'professional-gray'): ?>
/* Professional Gray Theme Override */
:root {
  --primary-color: #374151;
  --primary-dark: #1f2937;
  --primary-light: #6b7280;
  --secondary-color: #4b5563;
  --secondary-dark: #374151;
  --secondary-light: #9ca3af;
  --gradient-primary: linear-gradient(135deg, #374151 0%, #4b5563 100%);
  --shadow-primary: rgba(55, 65, 81, 0.2);
}
<?php elseif ($selected_theme === 'royal-purple'): ?>
/* Royal Purple Theme Override */
:root {
  --primary-color: #7c3aed;
  --primary-dark: #6d28d9;
  --primary-light: #8b5cf6;
  --secondary-color: #ec4899;
  --secondary-dark: #db2777;
  --secondary-light: #f472b6;
  --gradient-primary: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);
  --shadow-primary: rgba(124, 58, 237, 0.2);
}
<?php elseif ($selected_theme === 'default'): ?>
/* Default Theme Override (Original) */
:root {
  --primary-color: #667eea;
  --primary-dark: #5a67d8;
  --primary-light: #7c3aed;
  --secondary-color: #764ba2;
  --secondary-dark: #6b46c1;
  --secondary-light: #8b5cf6;
  --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --shadow-primary: rgba(102, 126, 234, 0.2);
}
<?php endif; ?>

/* Utility Classes for Quick Color Application */
.text-primary {
  color: var(--primary-color) !important;
}
.text-secondary {
  color: var(--secondary-color) !important;
}
.text-success {
  color: var(--success-color) !important;
}
.text-warning {
  color: var(--warning-color) !important;
}
.text-danger {
  color: var(--danger-color) !important;
}

.bg-primary {
  background-color: var(--primary-color) !important;
}
.bg-secondary {
  background-color: var(--secondary-color) !important;
}
.bg-success {
  background-color: var(--success-color) !important;
}
.bg-warning {
  background-color: var(--warning-color) !important;
}
.bg-danger {
  background-color: var(--danger-color) !important;
}

.border-primary {
  border-color: var(--primary-color) !important;
}
.border-secondary {
  border-color: var(--secondary-color) !important;
}
.border-success {
  border-color: var(--success-color) !important;
}
.border-warning {
  border-color: var(--warning-color) !important;
}
.border-danger {
  border-color: var(--danger-color) !important;
}

.gradient-primary {
  background: var(--gradient-primary) !important;
}
.gradient-success {
  background: var(--gradient-success) !important;
}
.gradient-warning {
  background: var(--gradient-warning) !important;
}
.gradient-danger {
  background: var(--gradient-danger) !important;
}
