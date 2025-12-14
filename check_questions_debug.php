<?php
require_once __DIR__ . '/config/connect.php';

$sql = "SELECT lesson_id, test_type, COUNT(*) as qty FROM tb_test GROUP BY lesson_id, test_type";
$res = $mysqli->query($sql);
while ($row = $res->fetch_assoc()) {
    echo "Lesson ID: " . $row['lesson_id'] . " | Type: " . $row['test_type'] . " | Qty: " . $row['qty'] . "\n";
}
?>
