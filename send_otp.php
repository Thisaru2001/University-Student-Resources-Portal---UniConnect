<?php
ob_start();
session_start();
header('Content-Type: application/json');

require_once 'connection.php';

// Load PHPMailer files from root
require_once 'PHPMailer.php';
require_once 'SMTP.php';
require_once 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$response = ['success' => false, 'message' => 'An unknown error occurred'];

try {
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    $student_id = $input['student_id'] ?? '';

    if (empty($student_id)) {
        throw new Exception('Please enter your student ID');
    }

    // Database initialization (also loads .env)
    Database::setUpConnection();

    // Check if student exists
    $query = "SELECT id, email, fname FROM students WHERE student_id = ?";
    $stmt = Database::$connection->prepare($query);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Student ID not found');
    }

    $student = $result->fetch_assoc();
    $email = $student['email'];
    $name = $student['fname'];

    // Generate 6-digit OTP
    $otp = rand(100000, 999999);

    // Store OTP in session with expiry (5 minutes)
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300;
    $_SESSION['otp_student_id'] = $student_id;

    // PHPMailer settings
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = getenv('MAIL_USERNAME') ?: '';
    $mail->Password = getenv('MAIL_PASSWORD') ?: '';
    $encryption = getenv('MAIL_ENCRYPTION') ?: 'ssl';
    $mail->SMTPSecure = ($encryption == 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = getenv('MAIL_PORT') ?: 465;
    $mail->CharSet = "UTF-8";

    // SMTP Options for local development (handles certificate issues)
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Recipients
    $mail->setFrom(getenv('MAIL_USERNAME') ?: 'noreply@uniconnect.com', 'UniConnect');
    $mail->addAddress($email, $name);
    $mail->addReplyTo(getenv('MAIL_USERNAME') ?: 'noreply@uniconnect.com', 'UniConnect');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset OTP - UniConnect';
    $mail->Body = '
    <div style="font-family: Arial, sans-serif; padding: 20px; background: #f4f4f5;">
        <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px;">
            <h2 style="color: #1a1a2e;">🔐 Password Reset Request</h2>
            <p>Dear ' . htmlspecialchars($name) . ',</p>
            <p>You requested to reset your password. Use the OTP below to complete the process:</p>
            <div style="background: #2d6a4f; color: white; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; border-radius: 8px; margin: 20px 0;">
                ' . $otp . '
            </div>
            <p>This OTP is valid for <strong>5 minutes</strong>.</p>
            <p>If you didn\'t request this, please ignore this email.</p>
            <p>Best regards,<br>UniConnect Team</p>
        </div>
    </div>';
    $mail->AltBody = "Your OTP for password reset is: $otp\n\nThis OTP is valid for 5 minutes.";

    $mail->send();
    $response = ['success' => true, 'message' => 'OTP sent to your email!'];

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

// Ensure no other output exists before the JSON
ob_end_clean();
echo json_encode($response);
exit();