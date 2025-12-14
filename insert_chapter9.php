<?php
require_once __DIR__ . '/config/connect.php';

// 1. Shift existing records to make space (if needed)
// We see orders 1-8 are fine. 9 is w10-vid (Week 10). We want Week 9 to be order 9.
// So we need to shift everyone with order >= 9 up by 1.
$update_sql = "UPDATE tb_content SET lesson_order = lesson_order + 1 WHERE lesson_order >= 9";
if ($mysqli->query($update_sql)) {
    echo "Successfully shifted lesson orders.\n";
} else {
    die("Error updating orders: " . $mysqli->error);
}

// 2. Insert Chapter 9
$stmt = $mysqli->prepare("INSERT INTO tb_content (lesson_id_text, name, title, lesson_order, video_path_720p, video_path_480p) VALUES (?, ?, ?, ?, ?, ?)");
$lid = 'w9-vid';
$name = 'Week 9: Data Link Control'; // More realistic title based on context
$title = 'Week 9: Data Link Control (DLC)';
$order = 9;
$vid = 'Lesson9.mp4';

$stmt->bind_param('sssiss', $lid, $name, $title, $order, $vid, $vid);

if ($stmt->execute()) {
    echo "Successfully inserted Chapter 9.\n";
} else {
    echo "Error inserting Chapter 9: " . $stmt->error . "\n";
}

$stmt->close();
?>
