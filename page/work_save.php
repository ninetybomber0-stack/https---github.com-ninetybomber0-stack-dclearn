<?php
require_once __DIR__ . '/../config/session_boot.php';
require_once __DIR__ . '/../config/connect.php';

// Only allow admin access
if (!isset($_SESSION['sess_username']) || $_SESSION['sess_username'] !== 'kamol') {
    header('HTTP/1.1 403 Forbidden');
    echo "Access denied.";
    exit;
}

// SQL to create table if it doesn't exist
$createTableSql = "
CREATE TABLE IF NOT EXISTS tb_work (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  file_path VARCHAR(255),
  due_date DATE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Execute the create table query
if (!$mysqli->query($createTableSql)) {
    echo "Error creating table: " . $mysqli->error;
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $work_title = $_POST['work_title'] ?? '';
    $work_description = $_POST['work_description'] ?? '';
    $due_date = $_POST['due_date'] ?? null;
    $file_path = null;

    // --- File Upload Handling ---
    // NOTE: Ensure you have created an 'uploads' directory in the root of your project (e.g., c:/AppServ/www/dclearn/uploads/)
    $upload_dir = __DIR__ . '/../uploads/';
    if (!file_exists($upload_dir)) {
        // Try to create it if it doesn't exist
        mkdir($upload_dir, 0777, true);
    }

    if (isset($_FILES['work_file']) && $_FILES['work_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['work_file']['tmp_name'];
        $file_name = $_FILES['work_file']['name'];
        
        // Sanitize filename and create a unique name to prevent overwriting
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $sanitized_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file_name, PATHINFO_FILENAME));
        $unique_filename = $sanitized_name . '_' . time() . '.' . $file_extension;
        
        $target_file = $upload_dir . $unique_filename;

        if (move_uploaded_file($file_tmp_name, $target_file)) {
            // Store the relative path for web access
            $file_path = 'uploads/' . $unique_filename;
        } else {
            // Handle file move error
            echo "Error uploading file.";
            // You might want to log this error instead of showing it to the user
        }
    }

    // --- Database Insertion ---
    if (!empty($work_title) && !empty($due_date)) {
        $stmt = $mysqli->prepare("INSERT INTO tb_work (title, description, file_path, due_date) VALUES (?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ssss", $work_title, $work_description, $file_path, $due_date);
            
            if ($stmt->execute()) {
                // Success, redirect back to the work page
                header("Location: ../index.php?page=work&status=success");
                exit;
            } else {
                echo "Error executing statement: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $mysqli->error;
        }
    } else {
        // Handle missing required fields
        header("Location: ../index.php?page=work&status=error&msg=missing_fields");
        exit;
    }
    
    $mysqli->close();

} else {
    // If not a POST request, redirect away
    header("Location: ../index.php?page=work");
    exit;
}
?>
