<?php
require_once __DIR__ . '/config/connect.php';

$result = $mysqli->query("SELECT id, lesson_order, lesson_id_text, name FROM tb_content ORDER BY lesson_order ASC");
while ($row = $result->fetch_assoc()) {
    echo "Order: " . $row['lesson_order'] . " | ID: " . $row['id'] . " | TextID: " . $row['lesson_id_text'] . " | Name: " . $row['name'] . "\n";
}
?>
