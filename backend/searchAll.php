<?php
require_once 'logger.php';
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once 'connection.php';
Database::setUpConnection();

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if (empty($keyword)) {
    echo json_encode(['success' => false, 'message' => 'Search keyword is required']);
    exit;
}

// Search across multiple tables and columns
$searchTerm = "%{$keyword}%";
$query = "SELECT DISTINCT r.resource_id, r.file_name, r.file_path, 
                r.anonymous_upload, r.uploaded_at, r.type_id,
                s.fname AS uploader_name,
                t.type AS resource_type,
                c.course_code,
                c.course_name,
                d.department_name,
                a.year,
                c.semester
         FROM resources r
         LEFT JOIN students s ON r.students_id = s.id
         LEFT JOIN type t ON r.type_id = t.id
         LEFT JOIN courses c ON r.course_id = c.course_id
         LEFT JOIN departments d ON c.department_id = d.department_id
         LEFT JOIN academic_years a ON c.year_id = a.year_id
         WHERE r.file_name LIKE ? 
            OR r.file_path LIKE ?
            OR t.type LIKE ?
            OR c.course_code LIKE ?
            OR c.course_name LIKE ?
            OR d.department_name LIKE ?
            OR a.year LIKE ?
            OR c.semester LIKE ?
            OR s.fname LIKE ?
         ORDER BY r.uploaded_at DESC";

$stmt = Database::$connection->prepare($query);
$stmt->bind_param("sssssssss", 
    $searchTerm, $searchTerm, $searchTerm, $searchTerm, 
    $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm
);
$stmt->execute();
$result = $stmt->get_result();

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
    
    $resources[] = [
        'resource_id' => $row['resource_id'],
        'file_name' => $row['file_name'],
        'file_path' => $row['file_path'],
        'file_size' => $file_size,
        'uploader_display' => $row['anonymous_upload'] ? 'Anonymous' : ($row['uploader_name'] ?? 'Unknown'),
        'course_code' => $row['course_code'] ?? 'N/A',
        'course_name' => $row['course_name'] ?? 'N/A',
        'department_name' => $row['department_name'] ?? 'N/A',
        'year' => $row['year'] ?? 'N/A',
        'semester' => $row['semester'] ?? 'N/A',
        'resource_type' => $row['resource_type'] ?? 'N/A',
        'date_display' => date('d M Y', strtotime($row['uploaded_at'])),
        'file_ext' => strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION))
    ];
}

echo json_encode([
    'success' => true,
    'count' => count($resources),
    'keyword' => $keyword,
    'resources' => $resources
]);
?>