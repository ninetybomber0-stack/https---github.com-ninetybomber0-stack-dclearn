<?php
include "config/connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $student_id = $_POST['student_id'];
    $lesson_id = intval($_POST['lesson_id']);
    $score = intval($_POST['score']);

    // เตรียมคำสั่ง SQL เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare("INSERT INTO scores (student_id, lesson_id, score) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $student_id, $lesson_id, $score);

    if ($stmt->execute()) {
        echo "บันทึกคะแนนเรียบร้อยแล้ว!";
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>