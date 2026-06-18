<?php
require_once 'logger.php';

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once 'connection.php';
Database::setUpConnection();
ensureReportsTable();

$query = "SELECT rr.report_id, rr.resource_id, rr.reason, rr.created_at AS reported_at, rr.status,
                 r.file_name, r.uploaded_at, r.anonymous_upload, r.students_id,
                 s.fname AS uploader_name, s.student_id AS uploader_student_id,
                 c.course_code, c.course_name, t.type AS resource_type,
                 rep.fname AS reporter_name, rep.student_id AS reporter_student_id
          FROM resource_reports rr
          JOIN resources r ON rr.resource_id = r.resource_id
          LEFT JOIN students s ON r.students_id = s.id
          LEFT JOIN courses c ON r.course_id = c.course_id
          LEFT JOIN type t ON r.type_id = t.id
          LEFT JOIN students rep ON rr.reporter_id = rep.id
          WHERE rr.status = 'pending'
          ORDER BY rr.created_at DESC";

$result = Database::$connection->query($query);
$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = [
        'report_id' => $row['report_id'],
        'resource_id' => $row['resource_id'],
        'file_name' => $row['file_name'],
        'resource_type' => $row['resource_type'] ?? 'N/A',
        'course_code' => $row['course_code'] ?? 'N/A',
        'course_name' => $row['course_name'] ?? 'N/A',
        'uploaded_at' => $row['uploaded_at'],
        'uploader_display' => $row['anonymous_upload'] ? 'Anonymous' : ($row['uploader_name'] ? $row['uploader_name'] . ' (' . $row['uploader_student_id'] . ')' : 'Unknown'),
        'reporter_display' => $row['reporter_name'] ? $row['reporter_name'] . ' (' . $row['reporter_student_id'] . ')' : 'Unknown',
        'reason' => $row['reason'],
        'reported_at' => $row['reported_at']
    ];
}

echo json_encode(['success' => true, 'reports' => $reports]);
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
