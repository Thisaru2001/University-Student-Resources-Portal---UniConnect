<?php
session_start();
header('Content-Type: application/json');

require_once 'connection.php';
Database::setUpConnection();

$query = "SELECT r.resource_id, r.file_name,r.file_path, 
                 r.anonymous_upload, r.uploaded_at, r.type_id,
                 s.fname AS uploader_name,
                 t.type AS resource_type,
                 c.course_code
          FROM resources r
          LEFT JOIN students s ON r.students_id = s.id
          LEFT JOIN type t ON r.type_id = t.id
          LEFT JOIN courses c ON r.course_id = c.course_id
          ORDER BY r.uploaded_at DESC
          LIMIT 4";

$result = Database::$connection->query($query);

$resources = [];
while ($row = $result->fetch_assoc()) {
    // Get file size
    $file_size = '';
    if (file_exists($row['file_path'])) {
        $size_bytes = filesize($row['file_path']);
        $file_size = $size_bytes >= 1048576 ? 
            round($size_bytes / 1048576, 1) . ' MB' : 
            round($size_bytes / 1024, 1) . ' KB';
    }
    
    $row['file_size'] = $file_size;
    $row['uploader_display'] = $row['anonymous_upload'] ? 'Anonymous' : ($row['uploader_name'] ?? 'Unknown');
    $row['date_display'] = date('d M Y', strtotime($row['uploaded_at']));
    $row['file_ext'] = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
    
    $resources[] = $row;
}

echo json_encode([
    'success' => true,
    'resources' => $resources
]);
?>