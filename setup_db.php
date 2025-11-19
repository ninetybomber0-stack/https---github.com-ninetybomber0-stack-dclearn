<?php
// This script is for one-time setup to create the lessons table.
// It should be deleted after use.

require_once __DIR__ . '/config/connect.php';

if (!isset($mysqli)) {
    die("Database connection failed.");
}

$sql_create_table = "
CREATE TABLE IF NOT EXISTS `tb_lessons` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `chapter_number` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `video_file` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_chapter_number` (`chapter_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_insert_data = "
INSERT INTO `tb_lessons` (chapter_number, title, video_file) VALUES
(1, 'บทที่ 1: ความรู้เบื้องต้นเกี่ยวกับการสื่อสารข้อมูล', 'copy_73602C8E-308E-4B41-A22A-F416B0E38193.mp4'),
(2, 'บทที่ 2: รูปแบบและส่วนประกอบของระบบสื่อสารข้อมูล', 'chapter_2.mp4')
ON DUPLICATE KEY UPDATE title=VALUES(title), video_file=VALUES(video_file);";

echo "Creating tb_lessons table if it doesn't exist...\n";
if ($mysqli->query($sql_create_table)) {
    echo "Table `tb_lessons` is ready.\n";
} else {
    echo "Error creating table: " . $mysqli->error . "\n";
    exit;
}

echo "Inserting/updating lesson data...\n";
if ($mysqli->query($sql_insert_data)) {
    echo "Lesson data for chapters 1 and 2 has been set up.\n";
} else {
    echo "Error inserting data: " . $mysqli->error . "\n";
}

$mysqli->close();

echo "Setup complete.\n";
?>
