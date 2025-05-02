<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'config.php';
require_once 'db.php';

// Send email function
function sendEmail($to, $subject, $message, $applicationId = null) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'admission31415@gmail.com';
        $mail->Password   = 'dtne dxak agcr rrrb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        
        $mail->send();
        
        // Log email if application ID is provided
        if ($applicationId) {
            logEmail($applicationId, $subject, $message, $to, 'sent');
        }
        
        return true;
    } catch (Exception $e) {
        // Log error
        if ($applicationId) {
            logEmail($applicationId, $subject, $message, $to, 'failed', $mail->ErrorInfo);
        }
        error_log("Email error: " . $mail->ErrorInfo);
        return false;
    }
}

// Log email in database
function logEmail($applicationId, $subject, $message, $sentTo, $status, $error = null) {
    $sql = "INSERT INTO email_logs (application_id, subject, message, sent_to, status, error_message) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    insert($sql, [$applicationId, $subject, $message, $sentTo, $status, $error]);
}

// Send application confirmation email
function sendApplicationConfirmation($application) {
    $program = getProgramById($application['program_id']);
    $subject = "Application Confirmation - " . $application['reference_no'];
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #5B26A6; color: #fff; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
            .reference { font-weight: bold; font-size: 18px; color: #3B1273; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Application Confirmation</h1>
            </div>
            <div class='content'>
                <p>Dear {$application['first_name']} {$application['last_name']},</p>
                
                <p>Thank you for applying to our institution. Your application has been received and is being processed.</p>
                
                <p>Your application reference number is: <span class='reference'>{$application['reference_no']}</span></p>
                
                <p><strong>Application Details:</strong><br>
                Program: {$program['name']}<br>
                Application Type: " . ucfirst($application['applicant_type']) . " Student<br>
                Date Submitted: " . formatDate($application['created_at']) . "</p>
                
                <p>You can check the status of your application at any time by visiting our website and entering your reference number.</p>
                
                <p>If you have any questions, please don't hesitate to contact us.</p>
                
                <p>Best regards,<br>
                Admissions Team</p>
            </div>
            <div class='footer'>
                <p>This is an automated message, please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($application['email'], $subject, $message, $application['id']);
}

// Send status update email
function sendStatusUpdateEmail($application) {
    $program = getProgramById($application['program_id']);
    $subject = "Application Status Update - " . $application['reference_no'];
    
    // Different message based on status
    $statusMessage = '';
    switch ($application['status']) {
        case STATUS_UNDER_REVIEW:
            $statusMessage = "Your application is now under review by our admissions committee.";
            break;
        case STATUS_ACCEPTED:
            $statusMessage = "Congratulations! Your application has been accepted. Please check our website for next steps.";
            break;
        case STATUS_REJECTED:
            $statusMessage = "We regret to inform you that your application has not been approved at this time.";
            break;
        case STATUS_INCOMPLETE:
            $statusMessage = "Your application is incomplete. Please login to your account to submit any missing documents or information.";
            break;
        default:
            $statusMessage = "Your application status has been updated.";
    }
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #5B26A6; color: #fff; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
            .status { font-weight: bold; font-size: 18px; color: #3B1273; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Application Status Update</h1>
            </div>
            <div class='content'>
                <p>Dear {$application['first_name']} {$application['last_name']},</p>
                
                <p>We are writing to inform you that your application status has been updated.</p>
                
                <p>Application Reference: <strong>{$application['reference_no']}</strong><br>
                Program: {$program['name']}<br>
                Current Status: <span class='status'>" . ucwords(str_replace('_', ' ', $application['status'])) . "</span></p>
                
                <p>{$statusMessage}</p>
                
                <p>" . ($application['notes'] ? "Additional Notes: {$application['notes']}" : "") . "</p>
                
                <p>You can check the status of your application at any time by visiting our website and entering your reference number.</p>
                
                <p>Best regards,<br>
                Admissions Team</p>
            </div>
            <div class='footer'>
                <p>This is an automated message, please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($application['email'], $subject, $message, $application['id']);
}

// Send payment confirmation email
function sendPaymentConfirmationEmail($application) {
    $subject = "Payment Confirmation - " . $application['reference_no'];
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #5B26A6; color: #fff; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
            .payment { font-weight: bold; font-size: 18px; color: #3B1273; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Payment Confirmation</h1>
            </div>
            <div class='content'>
                <p>Dear {$application['first_name']} {$application['last_name']},</p>
                
                <p>We are pleased to confirm that we have received your admission fee payment.</p>
                
                <p><strong>Payment Details:</strong><br>
                Reference Number: {$application['reference_no']}<br>
                Amount: PHP " . number_format($application['payment_amount'], 2) . "<br>
                Payment Reference: {$application['payment_reference']}<br>
                Date: " . formatDate($application['payment_date']) . "</p>
                
                <p>Your payment has been successfully processed and your application will now be reviewed by our admissions team.</p>
                
                <p>Best regards,<br>
                Admissions Team</p>
            </div>
            <div class='footer'>
                <p>This is an automated message, please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($application['email'], $subject, $message, $application['id']);
}
?>