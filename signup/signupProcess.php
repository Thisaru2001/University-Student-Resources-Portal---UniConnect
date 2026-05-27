<?php

ob_start();
session_start();
header('Content-Type: application/json');

try {

    require_once 'connection.php';

    $id = trim($_POST['id'] ?? '');
    $fname = trim($_POST['fname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pwd = trim($_POST['pwd'] ?? '');

    // Validation
    if (empty($id) || empty($fname) || empty($email) || empty($pwd)) {
        throw new Exception("All fields required");
    }

    if (strlen($pwd) < 6) {
        throw new Exception("Password must be at least 6 characters");
    }

    // ID format check
    if (!preg_match('/^[A-Z]{2}\/\d{4}\/\d{3}$/', $id)) {
        throw new Exception("Invalid ID format");
    }

    Database::setUpConnection();

    if (!Database::$connection) {
        throw new Exception("DB connection failed");
    }

    // Check duplicate user
    $check = Database::$connection->prepare(
        "SELECT id FROM students WHERE student_id = ?"
    );

    $check->bind_param("s", $id);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows > 0) {
        throw new Exception("User already exists");
    }

    // Hash password
    $hashed = password_hash($pwd, PASSWORD_DEFAULT);

    // Insert user
    $insert = Database::$connection->prepare(
        "INSERT INTO students (student_id, fname, email, password)
         VALUES (?, ?, ?, ?)"
    );

    $insert->bind_param("ssss", $id, $fname, $email, $hashed);

    if (!$insert->execute()) {
        throw new Exception("Registration failed");
    }

    echo json_encode([
        "success" => true,
        "message" => "Registration successful"
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}

ob_end_clean();
exit();

?>