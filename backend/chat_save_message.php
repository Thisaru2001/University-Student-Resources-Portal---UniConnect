<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit; }
require_once './connection.php';
Database::setUpConnection();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$session_id = intval($input['session_id'] ?? 0);
$role       = $input['role'] ?? '';
$content    = $input['content'] ?? '';

if (!$session_id || !in_array($role, ['user','assistant']) || $content === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid input']); exit;
}

// Verify ownership
$check = Database::$connection->prepare("SELECT session_id, title FROM chat_sessions WHERE session_id = ? AND user_id = ?");
$check->bind_param("ii", $session_id, $_SESSION['user_id']);
$check->execute();
$session = $check->get_result()->fetch_assoc();
if (!$session) { echo json_encode(['success' => false, 'message' => 'Not found']); exit; }

// Serialize content if array (vision messages)
$contentToSave = is_array($content) ? json_encode($content) : $content;

// Save message
$stmt = Database::$connection->prepare("INSERT INTO chat_messages (session_id, role, content) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $session_id, $role, $contentToSave);
$stmt->execute();

// Auto-title: use first user message if still "New Chat"
if ($role === 'user' && trim($session['title']) === 'New Chat') {
    // Extract text for title
    if (is_array($content)) {
        $titleText = '';
        foreach ($content as $part) {
            if (isset($part['type']) && $part['type'] === 'text') {
                $titleText = $part['text'];
                break;
            }
        }
    } else {
        $titleText = $content;
    }
    $title = mb_substr(strip_tags(trim($titleText)), 0, 45);
    if (!empty($title)) {
        $upd = Database::$connection->prepare("UPDATE chat_sessions SET title = ?, updated_at = NOW() WHERE session_id = ?");
        $upd->bind_param("si", $title, $session_id);
        $upd->execute();
    }
} else {
    $upd = Database::$connection->prepare("UPDATE chat_sessions SET updated_at = NOW() WHERE session_id = ?");
    $upd->bind_param("i", $session_id);
    $upd->execute();
}

echo json_encode(['success' => true]);