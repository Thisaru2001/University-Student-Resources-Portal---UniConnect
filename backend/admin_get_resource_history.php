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

$query = "SELECT r.resource_id, r.file_name, r.uploaded_at, r.anonymous_upload, r.students_id,
                 s.fname AS uploader_name, s.student_id AS uploader_student_id,
                 c.course_code, c.course_name, c.semester,
                 t.type AS resource_type,
                 IFNULL(rp.reports_count, 0) AS report_count
          FROM resources r
          LEFT JOIN students s ON r.students_id = s.id
          LEFT JOIN courses c ON r.course_id = c.course_id
          LEFT JOIN type t ON r.type_id = t.id
          LEFT JOIN (
              SELECT resource_id, COUNT(*) AS reports_count
              FROM resource_reports
              WHERE status = 'pending'
              GROUP BY resource_id
          ) rp ON rp.resource_id = r.resource_id
          ORDER BY r.uploaded_at DESC";

$result = Database::$connection->query($query);
$resources = [];
while ($row = $result->fetch_assoc()) {
    $resources[] = [
        'resource_id' => $row['resource_id'],
        'file_name' => $row['file_name'],
        'uploaded_at' => $row['uploaded_at'],
        'course_code' => $row['course_code'] ?? 'N/A',
        'course_name' => $row['course_name'] ?? 'N/A',
        'semester' => $row['semester'] ?? 'N/A',
        'resource_type' => $row['resource_type'] ?? 'N/A',
        'uploader_display' => $row['anonymous_upload'] ? 'Anonymous' : ($row['uploader_name'] ? $row['uploader_name'] . ' (' . $row['uploader_student_id'] . ')' : 'Unknown'),
        'report_count' => intval($row['report_count'])
    ];
}

echo json_encode(['success' => true, 'resources' => $resources]);
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
