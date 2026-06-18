<?php
require_once 'logger.php';
session_start();
ob_start();
header('Content-Type: application/json');

try {
    require_once 'connection.php';

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not logged in.');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $resource_id = intval($input['resource_id'] ?? 0);

    if (!$resource_id) {
        throw new Exception('Invalid resource ID.');
    }

    Database::setUpConnection();

    // Fetch file path AND verify ownership in one query
    $stmt = Database::$connection->prepare(
        "SELECT file_path FROM resources 
         WHERE resource_id = ? AND students_id = ?"
    );
    $stmt->bind_param("ii", $resource_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Resource not found or access denied.');
    }

    $row = $result->fetch_assoc();
    $file_path = $row['file_path'];
    $stmt->close();

    // Delete DB record
    $del = Database::$connection->prepare(
        "DELETE FROM resources WHERE resource_id = ? AND students_id = ?"
    );
    $del->bind_param("ii", $resource_id, $_SESSION['user_id']);

    if (!$del->execute()) {
        throw new Exception('Failed to delete record.');
    }
    $del->close();

    // Delete file from disk
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    $response = ['success' => true, 'message' => 'Resource deleted successfully.'];

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

ob_end_clean();
echo json_encode($response);
exit();
?>