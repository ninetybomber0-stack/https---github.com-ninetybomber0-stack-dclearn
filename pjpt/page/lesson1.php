<?php include "header.php"; ?>
<?php include "config/connect.php";

// รับ ID ของบทเรียนจาก URL
$lesson_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($lesson_id > 0) {
    // ดึงชื่อบทเรียน
    $lesson_stmt = $conn->prepare("SELECT title FROM lessons WHERE id = ?");
    $lesson_stmt->bind_param("i", $lesson_id);
    $lesson_stmt->execute();
    $lesson_result = $lesson_stmt->get_result();
    $lesson = $lesson_result->fetch_assoc();

    // ดึงรูปภาพของบทเรียน
    $images_stmt = $conn->prepare("SELECT filename FROM lesson_images WHERE lesson_id = ? ORDER BY id ASC");
    $images_stmt->bind_param("i", $lesson_id);
    $images_stmt->execute();
    $images_result = $images_stmt->get_result();

} else {
    echo "<h1>ไม่พบบทเรียน</h1>";
    include "footer.php";
    exit;
}
?>

<h1><?= htmlspecialchars($lesson['title']) ?></h1>
<p>บทเรียนนี้เป็นแบบอ่านรูปภาพหลายหน้า เปรียบเสมือนหนังสือออนไลน์</p>

<div class="book-pages">
    <?php
    if ($images_result->num_rows > 0) {
        while($img = $images_result->fetch_assoc()) {
            // สร้าง path ไปยังรูปภาพ
            $image_path = "images/lesson" . $lesson_id . "/" . htmlspecialchars($img['filename']);
            echo '<div class="page"><img src="'.$image_path.'" alt="'.htmlspecialchars($lesson['title']).'" /></div>';
        }
    } else {
        echo "<p>ยังไม่มีเนื้อหาในบทเรียนนี้</p>";
    }
    ?>
</div>

<p><a href="lessons.php">⬅ กลับไปหน้าเลือกบทเรียน</a></p>

<style>
.book-pages { display: flex; flex-direction: column; gap: 20px; }
.page img { max-width: 100%; height: auto; border: 1px solid #ccc; box-shadow: 2px 2px 8px rgba(0,0,0,0.1); }
</style>

<?php include "footer.php"; ?>