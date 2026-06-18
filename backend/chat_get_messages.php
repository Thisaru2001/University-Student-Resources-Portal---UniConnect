<?php

session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit; }
require_once './connection.php';
Database::setUpConnection();
header('Content-Type: application/json');

$session_id = intval($_GET['session_id'] ?? 0);
// verify ownership
$check = Database::$connection->prepare("SELECT session_id FROM chat_sessions WHERE session_id = ? AND user_id = ?");
$check->bind_param("ii", $session_id, $_SESSION['user_id']);
$check->execute();
if (!$check->get_result()->fetch_assoc()) { echo json_encode(['success' => false, 'message' => 'Not found']); exit; }

$stmt = Database::$connection->prepare("SELECT role, content FROM chat_messages WHERE session_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $session_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) $messages[] = $row;
echo json_encode(['success' => true, 'messages' => $messages]);