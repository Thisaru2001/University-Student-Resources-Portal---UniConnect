<?php

session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit; }
require_once './connection.php';
Database::setUpConnection();
header('Content-Type: application/json');

$stmt = Database::$connection->prepare(
    "SELECT session_id, title, updated_at FROM chat_sessions WHERE user_id = ? ORDER BY updated_at DESC"
);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$sessions = [];
while ($row = $result->fetch_assoc()) $sessions[] = $row;
echo json_encode(['success' => true, 'sessions' => $sessions]);