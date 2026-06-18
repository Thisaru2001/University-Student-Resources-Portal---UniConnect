<?php
require_once 'logger.php';
// Start output buffering to catch any accidental output
ob_start();

session_start();

// Set JSON header
header('Content-Type: application/json');

try {
    require_once 'connection.php';
    
    // ============ LOGIN ATTEMPT LIMITING ============
    // Initialize attempt tracking if not set
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['first_attempt_time'] = time();
    }
    
    // Reset attempts if 5 minutes have passed
    if (time() - $_SESSION['first_attempt_time'] > 300) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['first_attempt_time'] = time();
    }
    
    // Check if too many attempts
    if ($_SESSION['login_attempts'] >= 50) {
        $remainingTime = 300 - (time() - $_SESSION['first_attempt_time']);
        $minutes = ceil($remainingTime / 60);
        throw new Exception('Too many login attempts. Please try again in ' . $minutes . ' minute(s).');
    }
    // ============ END LOGIN ATTEMPT LIMITING ============
    
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
    
  // Validate ID pattern: XX/XXXX/XXX (e.g., UJ/2024/001, ad/2022/001)
if (!preg_match('/^[A-Za-z]{2}\/\d{4}\/\d{3}$/', $id)) {
    throw new Exception('Invalid ID format. Use format: XX/XXXX/XXX (e.g., AD/2022/001)');
}
    
    Database::setUpConnection();
    
    // Check if connection was successful
    if (!Database::$connection) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = Database::$connection->prepare(
        "SELECT * FROM students WHERE student_id = ? LIMIT 1"
    );
    
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
        
        // Check if account is deactivated
        if ($user['status'] == 0) {
            // Increment attempt counter
            $_SESSION['login_attempts']++;
            throw new Exception('Your account has been deactivated. Please contact administrator.');
        }
        
        if (password_verify($pwd, $user['password'])) {
            // ============ SUCCESSFUL LOGIN - RESET ATTEMPTS ============
            $_SESSION['login_attempts'] = 0;
            unset($_SESSION['first_attempt_time']);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['fname'] = $user['fname'];
            $_SESSION['admin'] = $user['admin'];
            
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_user', $token, [
                    'expires' => time() + (86400 * 30),
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            }
            
             writeLog(
                'Logged In',
                $_SESSION['student_id']
            );
            $response = [
                'success' => true, 
                
                'fname' => $user['fname'],
                'attempts_remaining' => 50
            ];
        } else {
            // Increment attempt counter on failed password
            $_SESSION['login_attempts']++;
            $attemptsLeft = 50 - $_SESSION['login_attempts'];
            throw new Exception('Invalid credentials. ' . $attemptsLeft . ' attempt(s) remaining.');
        }
    } else {
        // Increment attempt counter on user not found
        $_SESSION['login_attempts']++;
        $attemptsLeft = 50 - $_SESSION['login_attempts'];
        throw new Exception('User not found. ' . $attemptsLeft . ' attempt(s) remaining.');
    }
    
    $stmt->close();
    
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