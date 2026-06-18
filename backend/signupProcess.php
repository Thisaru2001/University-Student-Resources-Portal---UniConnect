<?php
require_once 'logger.php';
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// Start output buffering to catch any accidental output
ob_start();

session_start();

// Set JSON header
header('Content-Type: application/json');

try {
    require_once 'connection.php';

    $student_id = trim($_POST['student_id'] ?? '');
    $fname = trim($_POST['fname'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $pwd = trim($_POST['pwd'] ?? '');
    // If the checkbox is NOT checked (value '0' or not set)
    if (!isset($_POST['tcp']) || $_POST['tcp'] !== '1') {
        throw new Exception('You must accept the terms & conditions and our policies.');
    }

    // Validate empty fields
    if (empty($student_id) || empty($fname) || empty($email) || empty($pwd)) {
        throw new Exception('Please fill in all fields');
    }

    // Validate ID pattern: XX/XXXX/XXX (e.g., UJ/2024/001)
    if (!preg_match('/^[A-Za-z]{2}\/\d{4}\/\d{3}$/', $student_id)) {
        throw new Exception('Invalid ID format. Use format: XX/XXXX/XXX');
    }
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Validate password length (minimum 6 characters)
    if (strlen($pwd) < 6) {
        throw new Exception('Password must be at least 6 characters');
    }
    // ONE condition that checks ALL requirements
    if (
        strlen($pwd) < 6 ||
        !preg_match('/[A-Z]/', $pwd) ||
        !preg_match('/[a-z]/', $pwd) ||
        !preg_match('/[0-9]/', $pwd) ||
        !preg_match('/[^a-zA-Z0-9]/', $pwd)
    ) {

        throw new Exception('Password must be at least 6 characters and contain: 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character');
    }

    // Validate name length
    if (strlen($fname) < 2) {
        throw new Exception('First name must be at least 2 characters');
    }

    Database::setUpConnection();

    // Check if connection was successful
    if (!Database::$connection) {
        throw new Exception('Database connection failed');
    }

    // Check if student ID already exists
    $checkStmt = Database::$connection->prepare("SELECT id FROM students WHERE student_id = ? LIMIT 1");

    if (!$checkStmt) {
        throw new Exception('Prepare failed: ' . Database::$connection->error);
    }

    $checkStmt->bind_param("s", $student_id);

    if (!$checkStmt->execute()) {
        throw new Exception('Execute failed: ' . $checkStmt->error);
    }

    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        throw new Exception('This Student ID is already registered. Please sign in instead.');
    }

    $checkStmt->close();

    // Hash password
    $hashed_password = password_hash($pwd, PASSWORD_DEFAULT);

    // Get current datetime
    $created_at = date('Y-m-d H:i:s');

    // Insert new student
    $insertStmt = Database::$connection->prepare(
        "INSERT INTO students (student_id, fname, password, created_at, admin, email,status) VALUES (?, ?, ?, ?, 0,?,1)"
    );

    if (!$insertStmt) {
        throw new Exception('Prepare failed: ' . Database::$connection->error);
    }

    $insertStmt->bind_param("sssss", $student_id, $fname, $hashed_password, $created_at, $email);

    if (!$insertStmt->execute()) {
        throw new Exception('Registration failed. Please try again later.');
    }

    // Get the new user's ID
    $newUserId = Database::$connection->insert_id;

    // Set session variables
    $_SESSION['user_id'] = $newUserId;
    $_SESSION['student_id'] = $student_id;
    $_SESSION['fname'] = $fname;
    $_SESSION['admin'] = 0;

      writeLog(
        'Student Registered',
        $_SESSION['student_id']
    );
    $response = [
        'success' => true,
        'message' => 'Registration successful! Welcome, ' . $fname . ' 🎓',
        'redirect' => 'student.php'
    ];

    $insertStmt->close();
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Clean output buffer and send JSON
ob_end_clean();
echo json_encode($response);
exit();
