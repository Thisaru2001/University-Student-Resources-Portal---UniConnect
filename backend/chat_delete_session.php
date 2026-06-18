<?php
require_once 'logger.php';
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit; }
require_once './connection.php';
Database::setUpConnection();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$session_id = intval($input['session_id'] ?? 0);
$deleteAll  = $input['delete_all'] ?? false;

if ($deleteAll) {
    $stmt = Database::$connection->prepare("DELETE FROM chat_sessions WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'All chats deleted']);
} elseif ($session_id) {
    $stmt = Database::$connection->prepare("DELETE FROM chat_sessions WHERE session_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $session_id, $_SESSION['user_id']);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Chat deleted']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}