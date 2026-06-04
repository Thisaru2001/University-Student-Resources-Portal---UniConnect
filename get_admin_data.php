<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once 'connection.php';
Database::setUpConnection();

// Get student count
$studentCount = Database::$connection->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];

// Get resource count
$resourceCount = Database::$connection->query("SELECT COUNT(*) as count FROM resources")->fetch_assoc()['count'];

// Get admin count
$adminCount = Database::$connection->query("SELECT COUNT(*) as count FROM students WHERE admin = 1")->fetch_assoc()['count'];

// Get all students with created_at
$studentsQuery = "SELECT id, student_id, fname, email, admin, status, 
                         (SELECT COUNT(*) FROM resources WHERE students_id = students.id) as resource_count,
                         created_at
                  FROM students 
                  ORDER BY created_at DESC";
$studentsResult = Database::$connection->query($studentsQuery);
$students = [];
while ($row = $studentsResult->fetch_assoc()) {
    $students[] = $row;
}

// Get monthly registrations - STARTING FROM MAY 2026
$months = [];
$counts = [];

// Set start date to May 2026
$startDate = '2026-03-01';
$endDate = date('Y-m-01'); // Current month

// Generate months from May 2026 to current month
$current = strtotime($startDate);
$end = strtotime($endDate);

while ($current <= $end) {
    $monthYear = date('Y-m', $current);
    $monthLabel = date('M Y', $current);
    
    $months[] = $monthLabel;
    
    $countQuery = "SELECT COUNT(*) as count FROM students 
                   WHERE DATE_FORMAT(created_at, '%Y-%m') = '$monthYear'";
    $countResult = Database::$connection->query($countQuery);
    $countRow = $countResult->fetch_assoc();
    $counts[] = (int)$countRow['count'];
    
    // Move to next month
    $current = strtotime('+1 month', $current);
}

echo json_encode([
    'success' => true,
    'student_count' => $studentCount,
    'resource_count' => $resourceCount,
    'admin_count' => $adminCount,
    'students' => $students,
    'months' => $months,
    'counts' => $counts
]);
?>