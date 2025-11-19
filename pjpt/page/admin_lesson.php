<?php
include "config/connect.php";

// -------------------- อัปโหลดรูปหลายรูป --------------------
if(isset($_POST['upload_images'])){
    $lesson_id = intval($_POST['lesson_id']);
    $target_dir = "images/lesson".$lesson_id."/";

    if(!is_dir($target_dir)){
        mkdir($target_dir, 0777, true);
    }

    foreach($_FILES['images']['name'] as $key => $name){
        $tmp_name = $_FILES['images']['tmp_name'][$key];
        $target_file = $target_dir . basename($name);

        if(move_uploaded_file($tmp_name, $target_file)){
            $stmt = $conn->prepare("INSERT INTO lesson_images (lesson_id, filename) VALUES (?, ?)");
            $stmt->bind_param("is", $lesson_id, $name);
            $stmt->execute();
        }
    }
    $message = "อัปโหลดรูปเรียบร้อย!";
}

$lesson_id = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 1;

// ดึงข้อมูลบทเรียนทั้งหมดสำหรับ Dropdown
$lessons_result = $conn->query("SELECT * FROM lessons ORDER BY id ASC");

// ดึงรูปทั้งหมดของบทเรียนที่เลือก
$images_result = $conn->query("SELECT * FROM lesson_images WHERE lesson_id=$lesson_id ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin จัดการบทเรียน</title>
</head>
<body>
<h1>Admin จัดการบทเรียน</h1>

<?php if(isset($message)) echo "<p style='color:green;'>$message</p>"; ?>

<form method="get">
    เลือกบทเรียน:
    <select name="lesson_id" onchange="this.form.submit()">
        <?php while($lesson = $lessons_result->fetch_assoc()): ?>
            <option value="<?= $lesson['id'] ?>" <?= ($lesson_id == $lesson['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($lesson['title']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<hr>

<h2>อัปโหลดรูปภาพสำหรับบทเรียนนี้</h2>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="lesson_id" value="<?= $lesson_id ?>">
    <input type="file" name="images[]" multiple required>
    <button type="submit" name="upload_images">อัปโหลด</button>
</form>

<hr>

<h2>รูปภาพทั้งหมดในบทเรียนนี้</h2>
<div>
    <?php
    if ($images_result->num_rows > 0) {
        while($img = $images_result->fetch_assoc()):
            $image_path = "images/lesson".$lesson_id."/".htmlspecialchars($img['filename']);
    ?>
        <div style="display:inline-block; margin:10px;">
            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($img['filename']) ?>" style="max-width:150px; border:1px solid #ccc;">
            <p><a href="delete_image.php?id=<?= $img['id'] ?>&lesson_id=<?= $lesson_id ?>" onclick="return confirm('คุณแน่ใจหรือไม่?')">ลบ</a></p>
        </div>
    <?php
        endwhile;
    } else {
        echo "<p>ยังไม่มีรูปภาพในบทเรียนนี้</p>";
    }
    ?>
</div>

</body>
</html>