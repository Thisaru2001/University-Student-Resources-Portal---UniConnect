<?php
// Turn off error display for production, but log errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any accidental output
ob_start();

session_start();

// Set JSON header
header('Content-Type: application/json');

try {
    require_once 'connection.php';
    
    $id = trim($_POST['id'] ?? '');
    $pwd = trim($_POST['pwd'] ?? '');
    $remember = isset($_POST['remember']) ? filter_var($_POST['remember'], FILTER_VALIDATE_BOOLEAN) : false;
    
    // Validate empty fields
    if (empty($id) || empty($pwd)) {
        throw new Exception('Please fill in both fields');
    }
    
    // Validate password length (minimum 6 characters)
    if (strlen($pwd) < 6) {
        throw new Exception('Password must be at least 6 characters');
    }
    
    // Validate ID pattern: XX/XXXX/XXX (e.g., UJ/2024/001)
    if (!preg_match('/^[A-Z]{2}\/\d{4}\/\d{3}$/', $id)) {
        throw new Exception('Invalid ID format.');
    }
    
    Database::setUpConnection();
    
    // Check if connection was successful
    if (!Database::$connection) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = Database::$connection->prepare("SELECT * FROM students WHERE student_id = ? LIMIT 1");
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . Database::$connection->error);
    }
    
    $stmt->bind_param("s", $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($pwd, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['fname'] = $user['fname'];
            $_SESSION['admin'] = $user['admin'];
            
            if ($remember) {
                setcookie('remember_user', $user['student_id'], time() + (86400 * 30), "/");
            }
            
            $response = ['success' => true, 'message' => 'Welcome back, ' . $user['fname']];
        } else {
            $response = ['success' => false, 'message' => 'Invalid credentials'];
        }
    } else {
        $response = ['success' => false, 'message' => 'User not found'];
    }
    
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
?>