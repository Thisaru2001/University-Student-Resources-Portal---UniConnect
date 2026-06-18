<?php
require_once 'logger.php';
session_start();
header('Content-Type: application/json');

try {
    require_once 'connection.php';

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Please sign in first.');
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $resource_id = intval($input['resource_id'] ?? 0);
    $reason = trim($input['reason'] ?? '');

    if (!$resource_id || $reason === '') {
        throw new Exception('Please provide a resource and a reason for the report.');
    }

    Database::setUpConnection();
    ensureReportsTable();

    $resourceStmt = Database::$connection->prepare(
        "SELECT resource_id FROM resources WHERE resource_id = ? LIMIT 1"
    );
    $resourceStmt->bind_param('i', $resource_id);
    $resourceStmt->execute();
    $resourceResult = $resourceStmt->get_result();
    $resourceStmt->close();

    if ($resourceResult->num_rows === 0) {
        throw new Exception('Resource not found.');
    }

    $existingStmt = Database::$connection->prepare(
        "SELECT report_id FROM resource_reports WHERE resource_id = ? AND reporter_id = ? AND status = 'pending' LIMIT 1"
    );
    $existingStmt->bind_param('ii', $resource_id, $_SESSION['user_id']);
    $existingStmt->execute();
    $existingResult = $existingStmt->get_result();
    $existingStmt->close();

    if ($existingResult->num_rows > 0) {
        throw new Exception('You have already reported this resource.');
    }

    $insertStmt = Database::$connection->prepare(
        "INSERT INTO resource_reports (resource_id, reporter_id, reason, status, created_at) VALUES (?, ?, ?, 'pending', ?)"
    );
    $reportedAt = date('Y-m-d H:i:s');
    $insertStmt->bind_param('iiss', $resource_id, $_SESSION['user_id'], $reason, $reportedAt);

    if (!$insertStmt->execute()) {
        throw new Exception('Failed to submit report.');
    }

    $insertStmt->close();

    $response = [
        'success' => true,
        'message' => 'Report submitted successfully. Admins will review it shortly.'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
exit();

function ensureReportsTable()
{
    $create = "CREATE TABLE IF NOT EXISTS resource_reports (
        report_id INT AUTO_INCREMENT PRIMARY KEY,
        resource_id INT NOT NULL,
        reporter_id INT NOT NULL,
        reason TEXT NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        created_at DATETIME NOT NULL,
        reviewed_at DATETIME DEFAULT NULL,
        reviewed_by INT DEFAULT NULL,
        INDEX (resource_id),
        INDEX (reporter_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    Database::$connection->query($create);
}
