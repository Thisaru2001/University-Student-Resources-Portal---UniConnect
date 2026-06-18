<?php
require_once 'logger.php';
session_start();
header('Content-Type: application/json');

try {
    require_once 'connection.php';

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized.');
    }

    $resource_id = intval($_GET['resource_id'] ?? 0);
    if (!$resource_id) {
        throw new Exception('Invalid resource requested.');
    }

    Database::setUpConnection();

    $query = "SELECT r.resource_id, r.file_name, r.file_path, r.uploaded_at, r.anonymous_upload, r.type_id, r.course_id, r.students_id,
                     t.type AS resource_type,
                     c.course_code, c.course_name, c.semester,
                     s.fname AS uploader_name, s.student_id AS uploader_student_id
              FROM resources r
              LEFT JOIN type t ON r.type_id = t.id
              LEFT JOIN courses c ON r.course_id = c.course_id
              LEFT JOIN students s ON r.students_id = s.id
              WHERE r.resource_id = ?
              LIMIT 1";

    $stmt = Database::$connection->prepare($query);
    $stmt->bind_param('i', $resource_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows === 0) {
        throw new Exception('Resource not found.');
    }

    $row = $result->fetch_assoc();
    $fileSize = '';
    if (!empty($row['file_path']) && file_exists($row['file_path'])) {
        $sizeBytes = filesize($row['file_path']);
        $fileSize = $sizeBytes >= 1048576 ? round($sizeBytes / 1048576, 1) . ' MB' : round($sizeBytes / 1024, 1) . ' KB';
    }

    $resource = [
        'resource_id' => $row['resource_id'],
        'file_name' => $row['file_name'],
        'resource_type' => $row['resource_type'] ?? 'Unknown',
        'course_code' => $row['course_code'] ?? 'Unknown',
        'course_name' => $row['course_name'] ?? '',
        'semester' => $row['semester'] ?? '',
        'uploaded_at' => $row['uploaded_at'],
        'uploader_display' => $row['anonymous_upload'] ? 'Anonymous' : ($row['uploader_name'] ?? 'Unknown'),
        'uploader_student_id' => $row['uploader_student_id'] ?? '',
        'file_path' => $row['file_path'],
        'file_size' => $fileSize,
    ];

    $response = ['success' => true, 'resource' => $resource];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
exit();
