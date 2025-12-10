<?php
// send_commission.php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ===== CONFIGURATION - UPDATE THESE VALUES =====
$recipient_email = "coderjohndoe63841@gmail.com"; // Your email where you want to receive commissions
$smtp_host = "smtp.gmail.com"; // SMTP server (Gmail example)
$smtp_port = 587; // SMTP port (587 for TLS, 465 for SSL)
$smtp_username = "coderjohndoe63841@gmail.com"; // Your SMTP username (usually your email)
$smtp_password = "tklc hxse mfog bdpl"; // Your SMTP password or app-specific password
$from_email = "noreply@masterfulmagic.com"; // From email address
$from_name = "Masterful Magic Website"; // From name
// ===============================================

// Set response header
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get and sanitize form data
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
$package = isset($_POST['package']) ? htmlspecialchars(trim($_POST['package'])) : '';
$project = isset($_POST['project']) ? htmlspecialchars(trim($_POST['project'])) : '';
$description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
$deadline = isset($_POST['deadline']) ? htmlspecialchars(trim($_POST['deadline'])) : '';

// Validate required fields
if (empty($name) || empty($email) || empty($package) || empty($project) || empty($description)) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email format']);
    exit;
}

// Package names mapping
$package_names = [
    'fire' => '🔥 Fire Spark - FREE',
    'water' => '💧 Water Flow - $5',
    'earth' => '🌿 Earth Essence - $10',
    'storm' => '⛈️ Storm Force - $15'
];

$package_display = isset($package_names[$package]) ? $package_names[$package] : $package;

// Create PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtp_port;

    // Recipients
    $mail->setFrom($from_email, $from_name);
    $mail->addAddress($recipient_email);
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New Moodboard Commission Request from ' . $name;
    
    // HTML email body
    $mail->Body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #C0392B 0%, #2471A3 50%, #229954 100%); color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #555; }
            .value { color: #333; margin-top: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Commission Request</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Client Name:</div>
                    <div class='value'>" . $name . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Email Address:</div>
                    <div class='value'>" . $email . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Selected Package:</div>
                    <div class='value'>" . $package_display . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Project Type:</div>
                    <div class='value'>" . $project . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Project Description:</div>
                    <div class='value'>" . nl2br($description) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Desired Deadline:</div>
                    <div class='value'>" . ($deadline ? date('F j, Y', strtotime($deadline)) : 'Not specified') . "</div>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";

    // Plain text alternative
    $mail->AltBody = "New Commission Request\n\n" .
                     "Client Name: " . $name . "\n" .
                     "Email: " . $email . "\n" .
                     "Package: " . $package_display . "\n" .
                     "Project Type: " . $project . "\n" .
                     "Description: " . $description . "\n" .
                     "Deadline: " . ($deadline ? $deadline : 'Not specified');

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Commission request sent successfully']);

} catch (Exception $e) {
    error_log("Mailer Error: {$mail->ErrorInfo}");
    echo json_encode(['success' => false, 'error' => 'Failed to send email. Please try again later.']);
}
?>
