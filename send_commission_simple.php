<?php
// send_commission_simple.php - No dependencies required!
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// ===== YOUR CONFIGURATION =====
$to_email = "coderjohndoe63841@gmail.com"; // YOUR email address
$from_name = "Masterful Magic Website";
// ===============================

// Check if configured
if ($to_email === "your-email@example.com") {
    echo json_encode([
        'success' => false, 
        'error' => 'Please update the email address in send_commission_simple.php (line 9)'
    ]);
    exit;
}

// Check request method
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
    echo json_encode([
        'success' => false, 
        'error' => 'Please fill in all required fields'
    ]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

// Package names
$package_names = [
    'fire' => '🔥 Fire Spark - FREE',
    'water' => '💧 Water Flow - $5',
    'earth' => '🌿 Earth Essence - $10',
    'storm' => '⛈️ Storm Force - $15'
];

$package_display = isset($package_names[$package]) ? $package_names[$package] : $package;

// Format deadline
$deadline_formatted = $deadline ? date('F j, Y', strtotime($deadline)) : 'Not specified';

// Create email subject
$subject = "New Moodboard Commission Request from " . $name;

// Create HTML email body
$html_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { 
            background: linear-gradient(135deg, #C0392B 0%, #2471A3 50%, #229954 100%); 
            color: white; 
            padding: 20px; 
            text-align: center; 
        }
        .content { background: #f9f9f9; padding: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #555; }
        .value { margin-top: 5px; }
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
                <div class='value'>{$name}</div>
            </div>
            <div class='field'>
                <div class='label'>Email Address:</div>
                <div class='value'>{$email}</div>
            </div>
            <div class='field'>
                <div class='label'>Selected Package:</div>
                <div class='value'>{$package_display}</div>
            </div>
            <div class='field'>
                <div class='label'>Project Type:</div>
                <div class='value'>{$project}</div>
            </div>
            <div class='field'>
                <div class='label'>Project Description:</div>
                <div class='value'>" . nl2br($description) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Desired Deadline:</div>
                <div class='value'>{$deadline_formatted}</div>
            </div>
        </div>
    </div>
</body>
</html>
";

// Create plain text version
$text_body = "New Commission Request\n\n" .
             "Client Name: {$name}\n" .
             "Email: {$email}\n" .
             "Package: {$package_display}\n" .
             "Project Type: {$project}\n" .
             "Description: {$description}\n" .
             "Deadline: {$deadline_formatted}\n";

// Email headers
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: {$from_name} <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send email
$success = mail($to_email, $subject, $html_body, $headers);

// Log for debugging
$log_entry = date('Y-m-d H:i:s') . " - ";
if ($success) {
    $log_entry .= "SUCCESS: Email sent to {$to_email} from {$name} ({$email})\n";
    file_put_contents('email_log.txt', $log_entry, FILE_APPEND);
    echo json_encode([
        'success' => true, 
        'message' => 'Your commission request has been sent successfully!'
    ]);
} else {
    $log_entry .= "FAILED: Could not send email to {$to_email}\n";
    file_put_contents('email_log.txt', $log_entry, FILE_APPEND);
    echo json_encode([
        'success' => false, 
        'error' => 'Failed to send email. This may be a server configuration issue. Please contact support.'
    ]);
}
?>
