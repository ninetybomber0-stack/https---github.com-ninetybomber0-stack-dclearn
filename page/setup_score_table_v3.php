<?php
include 'config/connect.php';

$sql = "CREATE TABLE IF NOT EXISTS tb_exam_scores (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  student_id VARCHAR(50) NOT NULL,
  exam_type ENUM('pre_test', 'post_test') NOT NULL,
  score INT NOT NULL DEFAULT 0,
  full_score INT NOT NULL DEFAULT 100,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY idx_student_exam (student_id, exam_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($mysqli->query($sql) === TRUE) {
    echo "Table tb_exam_scores created successfully";
} else {
    echo "Error creating table: " . $mysqli->error;
}
?>
