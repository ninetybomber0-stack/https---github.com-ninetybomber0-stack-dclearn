<?php
// Ensure no output before headers
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use the correct session variable as used in other parts of the application
$sess_username = $_SESSION['sess_username'] ?? null;

// 1. Check user permission
if ($sess_username !== 'kamol') {
  http_response_code(403); // Forbidden
  echo json_encode(['success' => false, 'error' => 'Permission denied.']);
  exit;
}

// 2. Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405); // Method Not Allowed
  echo json_encode(['success' => false, 'error' => 'Method Not Allowed.']);
  exit;
}

// 3. Validate input
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
  http_response_code(400); // Bad Request
  echo json_encode(['success' => false, 'error' => 'Invalid or missing ID.']);
  exit;
}

// 4. Connect to DB and perform deletion
require_once __DIR__ . '/../config/connect.php';

$stmt = $mysqli->prepare("DELETE FROM tb_notice WHERE id = ?");
if (!$stmt) {
  http_response_code(500); // Internal Server Error
  echo json_encode(['success' => false, 'error' => 'Database prepare statement failed.']);
  exit;
}

$stmt->bind_param('i', $id);
if ($stmt->execute()) {
  // Success
  echo json_encode(['success' => true]);
} else {
  // Execution failed
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => 'Failed to delete the notice from the database.']);
}

$stmt->close();
$mysqli->close();
?>