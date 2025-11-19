<?php
include "config/connect.php";

if(isset($_GET['id'])) {
    $image_id = intval($_GET['id']);
    $lesson_id = intval($_GET['lesson_id']); // รับ lesson_id เพื่อ redirect กลับ

    // 1. ดึงชื่อไฟล์เพื่อลบไฟล์จริงออกจากเซิร์ฟเวอร์
    $stmt = $conn->prepare("SELECT filename FROM lesson_images WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()) {
        $filename = $row['filename'];
        $filepath = "images/lesson".$lesson_id."/".$filename;

        // 2. ลบไฟล์จริง
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // 3. ลบข้อมูลออกจากฐานข้อมูล
        $delete_stmt = $conn->prepare("DELETE FROM lesson_images WHERE id = ?");
        $delete_stmt->bind_param("i", $image_id);
        $delete_stmt->execute();
    }
}

// 4. กลับไปยังหน้า admin ของบทเรียนนั้นๆ
header("Location: admin_lesson.php?lesson_id=" . $lesson_id);
exit;
?>