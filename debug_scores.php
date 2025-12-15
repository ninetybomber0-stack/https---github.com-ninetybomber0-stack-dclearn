<?php
require_once 'config/connect.php';

echo "<h2>Debug tb_scores</h2>";
$sql = "SELECT * FROM tb_scores ORDER BY id DESC LIMIT 50";
$res = $mysqli->query($sql);
echo "<table border='1'><tr><th>ID</th><th>MemberID</th><th>LessonID</th><th>Score</th><th>Full</th><th>Type</th><th>Date</th></tr>";
while ($row = $res->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $k => $v) echo "<td>$v</td>";
    echo "</tr>";
}
echo "</table>";
?>
