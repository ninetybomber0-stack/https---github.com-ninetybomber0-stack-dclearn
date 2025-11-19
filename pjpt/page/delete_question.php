<?php
include "config/connect.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare("DELETE FROM exercises1_questions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: admin_exercises1.php");
exit;
?>
