<?php
require_once 'logger.php';
session_start();
header('Content-Type: application/json');

try {
    if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
        throw new Exception('Unauthorized');
    }

    require_once 'connection.php';
    Database::setUpConnection();
    ensureReportsTable();

    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = trim($input['action'] ?? '');

    if ($action === 'dismiss_report') {
        $report_id = intval($input['report_id'] ?? 0);
        if (!$report_id) {
            throw new Exception('Invalid report selected.');
        }

        $stmt = Database::$connection->prepare(
            "UPDATE resource_reports SET status = 'dismissed', reviewed_at = ?, reviewed_by = ? WHERE report_id = ? AND status = 'pending'"
        );
        $now = date('Y-m-d H:i:s');
        $stmt->bind_param('sii', $now, $_SESSION['user_id'], $report_id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception('Report not found or already reviewed.');
        }

        $stmt->close();
        $response = ['success' => true, 'message' => 'Report dismissed successfully.'];
    } elseif ($action === 'delete_resource') {
        $resource_id = intval($input['resource_id'] ?? 0);
        $report_id = intval($input['report_id'] ?? 0);

        if (!$resource_id && $report_id) {
            $lookup = Database::$connection->prepare(
                "SELECT resource_id FROM resource_reports WHERE report_id = ? LIMIT 1"
            );
            $lookup->bind_param('i', $report_id);
            $lookup->execute();
            $lookupResult = $lookup->get_result();
            $lookup->close();

            if ($lookupResult->num_rows === 0) {
                throw new Exception('Report not found.');
            }

            $resource_id = intval($lookupResult->fetch_assoc()['resource_id']);
        }

        if (!$resource_id) {
            throw new Exception('Invalid resource selected.');
        }

        $resourceStmt = Database::$connection->prepare(
            "SELECT file_path FROM resources WHERE resource_id = ? LIMIT 1"
        );
        $resourceStmt->bind_param('i', $resource_id);
        $resourceStmt->execute();
        $resourceResult = $resourceStmt->get_result();
        $resourceStmt->close();

        if ($resourceResult->num_rows === 0) {
            throw new Exception('Resource not found.');
        }

        $row = $resourceResult->fetch_assoc();
        $filePath = $row['file_path'];

        $deleteStmt = Database::$connection->prepare(
            "DELETE FROM resources WHERE resource_id = ?"
        );
        $deleteStmt->bind_param('i', $resource_id);
        $deleteStmt->execute();

        if ($deleteStmt->affected_rows === 0) {
            throw new Exception('Failed to remove resource.');
        }

        $deleteStmt->close();

        if (!empty($filePath) && file_exists($filePath)) {
            @unlink($filePath);
        }

        $updateReports = Database::$connection->prepare(
            "UPDATE resource_reports SET status = 'removed', reviewed_at = ?, reviewed_by = ? WHERE resource_id = ? AND status = 'pending'"
        );
        $now = date('Y-m-d H:i:s');
        $updateReports->bind_param('sii', $now, $_SESSION['user_id'], $resource_id);
        $updateReports->execute();
        $updateReports->close();

        $response = ['success' => true, 'message' => 'Resource removed successfully. Reports have been updated.'];
    } else {
        throw new Exception('Invalid action.');
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
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
