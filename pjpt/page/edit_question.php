<?php
include "config/connect.php";

if(!isset($_GET['id'])){
    header("Location: admin_exercises1.php");
    exit;
}

$id = intval($_GET['id']);

// ดึงข้อมูลคำถาม
$q_result = $conn->query("SELECT * FROM exercises1_questions WHERE id = $id");
$question = $q_result->fetch_assoc();

// อัปเดตคำถาม
if(isset($_POST['update_question'])){
    $q_text = $_POST['question'];
    $opt1 = $_POST['option1'];
    $opt2 = $_POST['option2'];
    $opt3 = $_POST['option3'];
    $opt4 = $_POST['option4'];
    $answer = $_POST['answer'];
    $timecode = $_POST['timecode'];

    $stmt = $conn->prepare("UPDATE exercises1_questions SET question=?, option1=?, option2=?, option3=?, option4=?, answer=?, timecode=? WHERE id=?");
    $stmt->bind_param("ssssiiii", $q_text, $opt1, $opt2, $opt3, $opt4, $answer, $timecode, $id);
    $stmt->execute();

    header("Location: admin_exercises1.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขคำถาม</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        input, select, textarea { margin-bottom: 10px; width: 100%; padding:5px; }
    </style>
</head>
<body>
<h1>แก้ไขคำถาม ID: <?= $id ?></h1>

<form method="post">
    <label>คำถาม:</label>
    <textarea name="question" required><?= $question['question'] ?></textarea>

    <label>ตัวเลือก 1:</label>
    <input type="text" name="option1" value="<?= $question['option1'] ?>" required>
    <label>ตัวเลือก 2:</label>
    <input type="text" name="option2" value="<?= $question['option2'] ?>" required>
    <label>ตัวเลือก 3:</label>
    <input type="text" name="option3" value="<?= $question['option3'] ?>" required>
    <label>ตัวเลือก 4:</label>
    <input type="text" name="option4" value="<?= $question['option4'] ?>" required>

    <label>คำตอบที่ถูกต้อง (1-4):</label>
    <select name="answer" required>
        <?php for($i=1;$i<=4;$i++): ?>
            <option value="<?= $i ?>" <?= ($question['answer']==$i)?'selected':'' ?>><?= $i ?></option>
        <?php endfor; ?>
    </select>

    <label>เวลาแสดงคำถาม (วินาที):</label>
    <input type="number" name="timecode" min="0" value="<?= $question['timecode'] ?>" required>

    <button type="submit" name="update_question">อัปเดตคำถาม</button>
</form>

<p><a href="admin_exercises1.php">กลับไปหน้า Admin</a></p>
</body>
</html>
