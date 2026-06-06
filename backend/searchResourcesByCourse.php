<?php
require_once 'connection.php';
Database::setUpConnection();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (empty($search)) {
    echo json_encode(['success' => false, 'message' => 'Search term is required']);
    exit;
}

$searchTerm = "%{$search}%";
$query = "SELECT r.resource_id, r.file_name,r.file_path, 
                r.anonymous_upload, r.uploaded_at, r.type_id,
                s.fname AS uploader_name,
                t.type AS resource_type,
                c.course_code,
                c.course_name
         FROM resources r
         LEFT JOIN students s ON r.students_id = s.id
         LEFT JOIN type t ON r.type_id = t.id
         LEFT JOIN courses c ON r.course_id = c.course_id
         WHERE c.course_code LIKE ? 
            OR c.course_name LIKE ?
         ORDER BY r.uploaded_at DESC";

$stmt = Database::$connection->prepare($query);
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$resources = [];
while ($row = $result->fetch_assoc()) {
    $file_ext = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
    $file_path = $row['file_path'];
    $file_size = '';
    if (file_exists($file_path)) {
        $size_bytes = filesize($file_path);
        if ($size_bytes >= 1048576) {
            $file_size = round($size_bytes / 1048576, 1) . ' MB';
        } else {
            $file_size = round($size_bytes / 1024, 1) . ' KB';
        }
    }
    
    $resources[] = [
        'resource_id' => $row['resource_id'],
        'file_name' => $row['file_name'],
        'description' => $row['description'],
        'file_path' => $row['file_path'],
        'file_ext' => $file_ext,
        'file_size' => $file_size,
        'uploader_display' => $row['anonymous_upload'] ? 'Anonymous' : ($row['uploader_name'] ?? 'Unknown'),
        'course_code' => $row['course_code'] ?? 'N/A',
        'course_name' => $row['course_name'] ?? 'N/A',
        'date_display' => date('d M Y', strtotime($row['uploaded_at']))
    ];
}

echo json_encode([
    'success' => true,
    'count' => count($resources),
    'resources' => $resources
]);
?>