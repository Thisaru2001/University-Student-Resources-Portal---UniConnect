<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once 'connection.php';
Database::setUpConnection();

$input = json_decode(file_get_contents('php://input'), true);
$targetStudentId = $input['student_id'] ?? '';
$action = $input['action'] ?? '';

if (empty($targetStudentId) || empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

// Get current admin's created_at timestamp
$adminId = $_SESSION['user_id'];
$adminQuery = "SELECT created_at FROM students WHERE id = ?";
$adminStmt = Database::$connection->prepare($adminQuery);
$adminStmt->bind_param("i", $adminId);
$adminStmt->execute();
$adminResult = $adminStmt->get_result();
$adminData = $adminResult->fetch_assoc();
$adminCreatedAt = $adminData['created_at']; // DateTime format
$adminStmt->close();

// Get target student's created_at timestamp
$targetQuery = "SELECT created_at, admin FROM students WHERE id = ?";
$targetStmt = Database::$connection->prepare($targetQuery);
$targetStmt->bind_param("i", $targetStudentId);
$targetStmt->execute();
$targetResult = $targetStmt->get_result();
$targetData = $targetResult->fetch_assoc();
$targetCreatedAt = $targetData['created_at']; // DateTime format
$targetIsAdmin = $targetData['admin'];
$targetStmt->close();

// Convert DateTime strings to timestamps for comparison
$adminTimestamp = strtotime($adminCreatedAt);
$targetTimestamp = strtotime($targetCreatedAt);

// Check if target is an admin and was created before current admin
$isTargetOlderAdmin = ($targetIsAdmin == 1 && $targetTimestamp < $adminTimestamp);

// ===== ADMIN AUTHORIZATION CHECKS =====

switch ($action) {
    case 'promote':
        // Any admin can promote any student
        $stmt = Database::$connection->prepare("UPDATE students SET admin = 1 WHERE id = ?");
        break;
        
    case 'demote':
        // Check: Can't demote an admin who was created before you
        if ($isTargetOlderAdmin) {
            echo json_encode(['success' => false, 'message' => 'Cannot remove admin privileges from an older admin']);
            exit();
        }
        $stmt = Database::$connection->prepare("UPDATE students SET admin = 0 WHERE id = ?");
        break;
        
    case 'activate':
        // Check: Can't activate an older admin
        if ($targetIsAdmin == 1 && $isTargetOlderAdmin) {
            echo json_encode(['success' => false, 'message' => 'Cannot activate an older admin']);
            exit();
        }
        $stmt = Database::$connection->prepare("UPDATE students SET status = 1 WHERE id = ?");
        break;
        
    case 'deactivate':
        // Check: Can't deactivate an older admin
        if ($targetIsAdmin == 1 && $isTargetOlderAdmin) {
            echo json_encode(['success' => false, 'message' => 'Cannot deactivate an older admin']);
            exit();
        }
        $stmt = Database::$connection->prepare("UPDATE students SET status = 0 WHERE id = ?");
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
        exit();
}

$stmt->bind_param("i", $targetStudentId);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Status updated successfully',
        'action' => $action
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
}

$stmt->close();
?>