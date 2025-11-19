<?php
include "config/connect.php";
//.....................................
// -------------------- อัปโหลดวิดีโอ --------------------
if(isset($_POST['upload_video'])){
    $target_dir = "videos/";
    $file = $_FILES["video_file"]["name"];
    //$filee=$_POST['video_file'];
    $target_file = $target_dir . basename($file);
//echo "XXX".$target_file;
    if(move_uploaded_file($_FILES["video_file"]["tmp_name"], $target_file)){
        // บันทึกลง DB
        $sql="INSERT INTO exercises1_video (filename) VALUES ('$file')";
       // echo $sql;
        $conn->query($sql);
        $message = "อัปโหลดวิดีโอเรียบร้อย!";
    } else {
        $message = "อัปโหลดวิดีโอล้มเหลว!";
    }
}
//echo "XXX".$file;
// -------------------- เพิ่มคำถาม --------------------
if(isset($_POST['add_question'])){
    $q = $_POST['question'];
    $opt1 = $_POST['option1'];
    $opt2 = $_POST['option2'];
    $opt3 = $_POST['option3'];
    $opt4 = $_POST['option4'];
    $answer = $_POST['answer'];
    $timecode = $_POST['timecode'];

    $stmt = $conn->prepare("INSERT INTO exercises1_questions (question, option1, option2, option3, option4, answer, timecode) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiii", $q, $opt1, $opt2, $opt3, $opt4, $answer, $timecode);
    $stmt->execute();

    $message = "เพิ่มคำถามเรียบร้อย!";
}






// ดึงวิดีโอล่าสุด
$video_result = $conn->query("SELECT * FROM exercises1_video ORDER BY id DESC LIMIT 1");
$video = $video_result->fetch_assoc();

// ดึงคำถามทั้งหมด
$questions_result = $conn->query("SELECT * FROM exercises1_questions ORDER BY timecode ASC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin Exercises1</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        input, select, textarea { margin-bottom: 10px; width: 100%; padding:5px; }
        .message { color: green; }
    </style>
</head>
<body>

<h1>Admin Exercises1</h1>

<?php if(isset($message)) echo "<p class='message'>$message</p>"; ?>

<h2>1. อัปโหลด/เปลี่ยนวิดีโอ</h2>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="video_file" required>
    <button type="submit" name="upload_video">อัปโหลด</button>
</form>

<?php if($video): ?>
<p>วิดีโอปัจจุบัน: <?= $video['filename'] ?></p>
<video width="320" controls>
    <source src="videos/<?= $video['filename'] ?>" type="video/mp4">
</video>
<?php endif; ?>

<hr>

<h2>2. เพิ่มคำถาม</h2>
<form method="post">
    <label>คำถาม:</label>
    <textarea name="question" required></textarea>

    <label>ตัวเลือก 1:</label>
    <input type="text" name="option1" required>
    <label>ตัวเลือก 2:</label>
    <input type="text" name="option2" required>
    <label>ตัวเลือก 3:</label>
    <input type="text" name="option3" required>
    <label>ตัวเลือก 4:</label>
    <input type="text" name="option4" required>

    <label>คำตอบที่ถูกต้อง (1-4):</label>
    <select name="answer" required>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
    </select>

    <label>เวลาแสดงคำถาม (วินาที):</label>
    <input type="number" name="timecode" min="0" required>

    <button type="submit" name="add_question">เพิ่มคำถาม</button>
</form>

<hr>

<h2>3. คำถามทั้งหมด</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th><th>คำถาม</th><th>ตัวเลือก</th><th>คำตอบ</th><th>เวลา (วินาที)</th><th>จัดการ</th>
    </tr>
    <?php while($q = $questions_result->fetch_assoc()): ?>
    <tr>
        <td><?= $q['id'] ?></td>
        <td><?= $q['question'] ?></td>
        <td>
            1. <?= $q['option1'] ?><br>
            2. <?= $q['option2'] ?><br>
            3. <?= $q['option3'] ?><br>
            4. <?= $q['option4'] ?>
        </td>
        <td><?= $q['answer'] ?></td>
        <td><?= $q['timecode'] ?></td>
        <td>
            <a href="edit_question.php?id=<?= $q['id'] ?>">แก้ไข</a> |
            <a href="delete_question.php?id=<?= $q['id'] ?>" onclick="return confirm('ลบคำถาม?')">ลบ</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
