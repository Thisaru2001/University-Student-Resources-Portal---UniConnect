<?php
require_once 'logger.php';
ob_start();
session_start();
header('Content-Type: application/json');

require_once 'connection.php';

$response = ['success' => false, 'message' => 'An unknown error occurred'];

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $student_id = $input['student_id'] ?? '';
    $otp = $input['otp'] ?? '';
    $new_password = $input['new_password'] ?? '';

    if (empty($student_id) || empty($otp) || empty($new_password)) {
        throw new Exception('Please fill in all fields');
    }

    // Verify OTP session
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry'])) {
        throw new Exception('No OTP request found. Please try again.');
    }

    if ($_SESSION['otp'] != $otp) {
        throw new Exception('Invalid OTP');
    }

    if (time() > $_SESSION['otp_expiry']) {
        throw new Exception('OTP has expired. Please request a new one.');
    }

    if ($_SESSION['otp_student_id'] != $student_id) {
        throw new Exception('Student ID mismatch');
    }

    // Database initialization
    Database::setUpConnection();

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password in database (using correct column name 'password')
    $updateQuery = "UPDATE students SET password = ? WHERE student_id = ?";
    $stmt = Database::$connection->prepare($updateQuery);

    if (!$stmt) {
        throw new Exception('Prepare failed: ' . Database::$connection->error);
    }

    $stmt->bind_param("ss", $hashed_password, $student_id);

    if ($stmt->execute()) {
        // Clear OTP session
        unset($_SESSION['otp']);
        unset($_SESSION['otp_expiry']);
        unset($_SESSION['otp_student_id']);
         writeLog(
            'Logged In',
            $_SESSION['student_id']
        );

        $response = ['success' => true, 'message' => 'Password updated successfully!'];
    } else {
        throw new Exception('Failed to update password in database');
    }

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

// Ensure no other output exists
ob_end_clean();
echo json_encode($response);
exit();