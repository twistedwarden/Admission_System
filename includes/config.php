<?php
// Session configuration
session_start();

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Site configuration
define('SITE_NAME', 'University Admission System');
define('SITE_URL', 'http://localhost/admission-system');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'admission31415@gmail.com');
define('SMTP_PASSWORD', 'dtne dxak agcr rrrb');
define('SMTP_FROM_EMAIL', 'admission31415@gmail.com');
define('SMTP_FROM_NAME', SITE_NAME);

// System constants
define('ADMIN_EMAIL', 'admin@example.com');
define('ADMISSION_FEE', 500); // in PHP peso

// Application statuses
define('STATUS_PENDING', 'pending');
define('STATUS_UNDER_REVIEW', 'under_review');
define('STATUS_ACCEPTED', 'accepted');
define('STATUS_REJECTED', 'rejected');
define('STATUS_INCOMPLETE', 'incomplete');

// Payment statuses
define('PAYMENT_UNPAID', 'unpaid');
define('PAYMENT_PROCESSING', 'processing');
define('PAYMENT_PAID', 'paid');

// Applicant types
define('APPLICANT_NEW', 'new');
define('APPLICANT_RETURNING', 'returning');
define('APPLICANT_TRANSFER', 'transfer');

// Create upload directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
?>