<?php
require_once __DIR__ . '/config/connect.php';

if (!isset($mysqli)) {
    die("Database connection failed.");
}

$sql_create_table = "
CREATE TABLE IF NOT EXISTS `tb_google_form_scores` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` VARCHAR(255) NOT NULL,
  `lesson_id` INT UNSIGNED NOT NULL,
  `pre_score` VARCHAR(50) NULL DEFAULT NULL,
  `post_score` VARCHAR(50) NULL DEFAULT NULL,
  `pre_test_timestamp` DATETIME NULL DEFAULT NULL,
  `post_test_timestamp` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_lesson_unique` (`user_id`, `lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

echo "Creating tb_google_form_scores table if it doesn\'t exist...\n";
if ($mysqli->query($sql_create_table)) {
    echo "Table `tb_google_form_scores` is ready.\n";
} else {
    echo "Error creating table: " . $mysqli->error . "\n";
    exit;
}

$mysqli->close();

echo "Google Form score table setup complete.\n";
?>
