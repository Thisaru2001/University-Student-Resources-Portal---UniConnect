<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit; }
require_once './connection.php';
Database::setUpConnection();
header('Content-Type: application/json');

$stmt = Database::$connection->prepare(
    "INSERT INTO chat_sessions (user_id, title) VALUES (?, 'New Chat')"
);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
echo json_encode(['success' => true, 'session_id' => Database::$connection->insert_id]);