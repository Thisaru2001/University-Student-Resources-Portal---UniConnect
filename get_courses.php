<?php
require_once 'connection.php';
Database::setUpConnection();

$department_id = isset($_GET['department_id']) ? $_GET['department_id'] : '';
$year_id = isset($_GET['year_id']) ? $_GET['year_id'] : '';
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';

$query = "SELECT course_id, course_code, course_name 
          FROM courses 
          WHERE 1=1";

$params = [];
$types = '';

if (!empty($department_id)) {
    $query .= " AND department_id = ?";
    $params[] = $department_id;
    $types .= 'i';
}

if (!empty($year_id)) {
    $query .= " AND year_id = ?";
    $params[] = $year_id;
    $types .= 'i';
}

if (!empty($semester)) {
    $query .= " AND semester = ?";
    $params[] = $semester;
    $types .= 'i';
}

$query .= " ORDER BY course_code";

$stmt = Database::$connection->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode([
    'success' => true,
    'courses' => $courses
]);
?>