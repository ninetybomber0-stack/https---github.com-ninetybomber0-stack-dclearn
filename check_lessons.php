<?php
require_once __DIR__ . '/config/connect.php';

$result = $mysqli->query("SELECT * FROM tb_content ORDER BY id");
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . "\n";
}
?>
