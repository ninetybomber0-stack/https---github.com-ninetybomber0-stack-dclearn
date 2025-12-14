<?php
require_once __DIR__ . '/config/connect.php';

$result = $mysqli->query("SELECT id, lesson_id_text, name, title, video_path_720p FROM tb_content ORDER BY id");
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | TextID: " . $row['lesson_id_text'] . " | Name: " . $row['name'] . " | Title: " . $row['title'] . " | Video: " . $row['video_path_720p'] . "\n";
}
?>
