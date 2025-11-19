<?php
// populate_lessons.php
// This script is now designed to be included by other files.

// 1. เชื่อมต่อฐานข้อมูล
// The $mysqli connection is expected to be provided by the including file (e.g., index.php)
if (!isset($mysqli) || !($mysqli instanceof mysqli)) return;

// 2. ตรวจสอบว่าตาราง tb_content มีอยู่หรือไม่ ถ้าไม่มีให้สร้าง
$mysqli->query("
CREATE TABLE IF NOT EXISTS `tb_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `video_file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
");

// 3. รายการบทเรียนที่ต้องการเพิ่ม
$lessons_to_add = [
    1 => ['name' => 'ความรู้เบื้องต้นเกี่ยวกับการสื่อสารข้อมูล', 'video_file' => 'copy_73602C8E-308E-4B41-A22A-F416B0E38193.mp4'],
    2 => ['name' => 'รูปแบบและส่วนประกอบของระบบสื่อสารข้อมูล', 'video_file' => 'chapter_2_placeholder.mp4']
];

// 4. วนลูปเพื่อตรวจสอบและเพิ่มข้อมูล
$stmt_insert = $mysqli->prepare(
    "INSERT INTO tb_content (id, name, video_file) 
     VALUES (?, ?, ?) 
     ON DUPLICATE KEY UPDATE name=VALUES(name), video_file=VALUES(video_file)"
);

if ($stmt_insert) {
    foreach ($lessons_to_add as $id => $details) {
        $stmt_insert->bind_param('iss', $id, $details['name'], $details['video_file']);
        $stmt_insert->execute();
    }
    $stmt_insert->close();
}

// Do not close the $mysqli connection here.
?>