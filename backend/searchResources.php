<?php
require_once 'connection.php';
Database::setUpConnection();

$department_id = isset($_GET['department_id']) ? $_GET['department_id'] : '';
$year_id = isset($_GET['year_id']) ? $_GET['year_id'] : '';
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';
$type_id = isset($_GET['type_id']) ? $_GET['type_id'] : '';

$query = "SELECT r.resource_id, r.file_name,r.file_path, 
                r.anonymous_upload, r.uploaded_at, r.type_id,
                s.fname AS uploader_name,
                t.type AS resource_type,
                c.course_code
         FROM resources r
         LEFT JOIN students s ON r.students_id = s.id
         LEFT JOIN type t ON r.type_id = t.id
         LEFT JOIN courses c ON r.course_id = c.course_id
         WHERE 1=1";

$params = [];
$types = '';

if (!empty($department_id)) {
    $query .= " AND c.department_id = ?";
    $params[] = $department_id;
    $types .= 'i';
}

if (!empty($year_id)) {
    $query .= " AND c.year_id = ?";
    $params[] = $year_id;
    $types .= 'i';
}

if (!empty($semester)) {
    $query .= " AND c.semester = ?";
    $params[] = $semester;
    $types .= 'i';
}

if (!empty($course_id)) {
    $query .= " AND c.course_id = ?";
    $params[] = $course_id;
    $types .= 'i';
}

if (!empty($type_id)) {
    $query .= " AND r.type_id = ?";
    $params[] = $type_id;
    $types .= 'i';
}

// If no filters selected, return recent resources
if (empty($params)) {
    $query .= " ORDER BY r.uploaded_at DESC LIMIT 10";
} else {
    $query .= " ORDER BY r.uploaded_at DESC";
}

$stmt = Database::$connection->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

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
        'date_display' => date('d M Y', strtotime($row['uploaded_at']))
    ];
}

echo json_encode([
    'success' => true,
    'count' => count($resources),
    'resources' => $resources
]);
?>