<?php
require_once 'logger.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

session_start();
header('Content-Type: application/json');

try {
    require_once 'connection.php';

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Please sign in first.');
    }

    $students_id = $_SESSION['user_id'];
    
    $original_file_name = $_FILES['file']['name'] ?? '';
    $file_tmp = $_FILES['file']['tmp_name'] ?? '';
    $file_size = $_FILES['file']['size'] ?? 0;
    $file_error = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
    
    $custom_file_name = trim($_POST['custom_file_name'] ?? '');
    $type_id = trim($_POST['type_id'] ?? '');
    $course_id = trim($_POST['course_id'] ?? '');
    $anonymous_upload = ($_POST['anonymous_upload'] ?? '0') === '1' ? 1 : 0;

    // Validate required fields
    if (empty($original_file_name) || empty($type_id) || empty($course_id)) {
        throw new Exception('Please fill in all required fields.');
    }

    // Validate file upload
    if ($file_error !== UPLOAD_ERR_OK) {
        throw new Exception('File upload failed. Please try again.');
    }

    // Validate file size (max 50MB)
    $max_size = 50 * 1024 * 1024;
    if ($file_size > $max_size) {
        throw new Exception('File size must be less than 50MB.');
    }

    // Allowed file extensions
    $allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt'];
    $file_extension = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        throw new Exception('Invalid file type. Allowed: PDF, DOC, DOCX, PPT, PPTX, TXT');
    }

    Database::setUpConnection();

    if (!Database::$connection) {
        throw new Exception('Database connection failed');
    }

    // Get course details
    $courseStmt = Database::$connection->prepare(
        "SELECT c.course_code, c.semester, a.year, d.department_name 
         FROM courses c
         JOIN academic_years a ON c.year_id = a.year_id
         JOIN departments d ON c.department_id = d.department_id
         WHERE c.course_id = ? LIMIT 1"
    );
    
    if (!$courseStmt) {
        throw new Exception('Prepare failed: ' . Database::$connection->error);
    }
    
    $courseStmt->bind_param("i", $course_id);
    $courseStmt->execute();
    $courseResult = $courseStmt->get_result();

    if ($courseResult->num_rows === 0) {
        throw new Exception('Invalid course selected.');
    }
    
    $courseData = $courseResult->fetch_assoc();
    $course_code = $courseData['course_code'];
    $semester = $courseData['semester'];
    $year = $courseData['year'];
    $courseStmt->close();

    // Get resource type name
    $typeStmt = Database::$connection->prepare("SELECT id, type FROM type WHERE id = ? LIMIT 1");
    $typeStmt->bind_param("i", $type_id);
    $typeStmt->execute();
    $typeResult = $typeStmt->get_result();

    if ($typeResult->num_rows === 0) {
        throw new Exception('Invalid resource type selected.');
    }
    
    $typeData = $typeResult->fetch_assoc();
    $type_name = strtolower(str_replace(' ', '_', $typeData['type']));
    $typeStmt->close();

    // Create folder structure
    $upload_dir = 'uploads/resources/' . $year . '/semester_' . $semester . '/' . $type_name . '/' . $course_code . '/';
    
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception('Failed to create directory. Please check permissions.');
        }
    }

    // Determine final file name
    $final_file_name = $original_file_name;
    if (!empty($custom_file_name)) {
        // Use custom name with original extension
        $final_file_name = $custom_file_name . '.' . $file_extension;
    }

    // Generate unique file name
    $unique_name = $final_file_name;
    $file_path = $upload_dir . $unique_name;

    $counter = 1;
    while (file_exists($file_path)) {
        $name_without_ext = pathinfo($final_file_name, PATHINFO_FILENAME);
        $unique_name = $name_without_ext . '_' . $counter . '.' . $file_extension;
        $file_path = $upload_dir . $unique_name;
        $counter++;
    }

    // Move uploaded file
    if (!move_uploaded_file($file_tmp, $file_path)) {
        throw new Exception('Failed to save file. Please try again.');
    }

    // Get current datetime
    $uploaded_at = date('Y-m-d H:i:s');

    // Insert resource
    $insertStmt = Database::$connection->prepare(
        "INSERT INTO resources (file_name, file_path, type_id, course_id, anonymous_upload, uploaded_at, students_id) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$insertStmt) {
        unlink($file_path);
        throw new Exception('Prepare failed: ' . Database::$connection->error);
    }

    $insertStmt->bind_param(
        "ssiiisi", 
        $unique_name, 
        $file_path, 
        $type_id, 
        $course_id, 
        $anonymous_upload, 
        $uploaded_at, 
        $students_id
    );

    if (!$insertStmt->execute()) {
        unlink($file_path);
        throw new Exception('Registration failed: ' . $insertStmt->error);
    }

    $response = [
        'success' => true,
        'message' => 'Resource uploaded successfully! 📚',
        'resource_id' => Database::$connection->insert_id,
        'file_path' => $file_path
    ];

    $insertStmt->close();
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

ob_end_clean();
echo json_encode($response);
exit();
?>