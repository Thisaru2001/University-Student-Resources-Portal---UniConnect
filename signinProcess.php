<?php
session_start();
header('Content-Type: application/json');

require_once 'connection.php';

$id = trim($_POST['id'] ?? '');
$pwd = trim($_POST['pwd'] ?? '');
$remember = isset($_POST['remember']) ? filter_var($_POST['remember'], FILTER_VALIDATE_BOOLEAN) : false;

if (empty($id) || empty($pwd)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in both fields']);
    exit();
}

Database::setUpConnection();

$stmt = Database::$connection->prepare("SELECT * FROM students WHERE student_id = ? LIMIT 1");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    if (password_verify($pwd, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['student_id'] = $user['student_id'];
        $_SESSION['name'] = $user['name'];

        if ($remember) {
            setcookie('remember_user', $user['student_id'], time() + (86400 * 30), "/");
        }

        echo json_encode(['success' => true, 'message' => 'Welcome back, ' . $user['name']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>