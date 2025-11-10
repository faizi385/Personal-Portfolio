<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type to JSON
header('Content-Type: application/json');

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => 'An error occurred while sending your message.',
    'errors' => []
];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    // Check for email injection
    $pattern = "/(content-type|bcc:|cc:|to:)/i";
    if (preg_match($pattern, $name) || preg_match($pattern, $email) || preg_match($pattern, $message)) {
        $errors[] = 'Invalid input detected';
    }
    
    // If no validation errors, proceed with sending email
    if (empty($errors)) {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'faizanmoeen385@gmail.com';  // Your Gmail address
            $mail->Password = 'iian ksfk wzgk tuwj';  // Your App Password (not your Gmail password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Recipients
            $mail->setFrom('faizanmoeen385@gmail.com', 'Portfolio Contact Form');
            $mail->addAddress('faizanmoeen385@gmail.com', 'Faizan Moeen');
            $mail->addReplyTo($email, $name);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = "Contact Form: $subject";
            
            // Email body with better formatting
            $email_body = "
            <html>
            <head>
                <title>New Contact Form Submission</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #f8f9fa; padding: 15px; border-radius: 5px; }
                    .content { margin-top: 20px; }
                    .field { margin-bottom: 15px; }
                    .field-label { font-weight: bold; margin-bottom: 5px; display: block; }
                    .field-value { padding: 8px; background-color: #f8f9fa; border-radius: 4px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>New Message from Contact Form</h2>
                    </div>
                    <div class='content'>
                        <div class='field'>
                            <span class='field-label'>Name:</span>
                            <div class='field-value'>$name</div>
                        </div>
                        <div class='field'>
                            <span class='field-label'>Email:</span>
                            <div class='field-value'><a href='mailto:$email'>$email</a></div>
                        </div>
                        <div class='field'>
                            <span class='field-label'>Subject:</span>
                            <div class='field-value'>$subject</div>
                        </div>
                        <div class='field'>
                            <span class='field-label'>Message:</span>
                            <div class='field-value'>" . nl2br(htmlspecialchars($message)) . "</div>
                        </div>
                    </div>
                </div>
            </body>
            </html>";
            
            $mail->Body = $email_body;
            
            // Send email
            $mail->send();
            
            $response['success'] = true;
            $response['message'] = 'Thank you! Your message has been sent successfully.';
            
        } catch (Exception $e) {
            $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            error_log('Mailer Error: ' . $mail->ErrorInfo);
        }
    } else {
        $response['errors'] = $errors;
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Return JSON response
echo json_encode($response);
?>