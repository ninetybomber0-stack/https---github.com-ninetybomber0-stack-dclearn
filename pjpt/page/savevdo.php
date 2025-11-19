<?php
// -------------------- อัปโหลดวิดีโอ --------------------
if(isset($_POST['upload_video'])){
    $target_dir = "videos/";
    $file = $_FILES["video_file"]["name"];
    $filee=$_POST['video_file'];
    $target_file = $target_dir . basename($file);
echo "XXX".$target_file;
    if(move_uploaded_file($_FILES["video_file"]["tmp_name"], $target_file)){
        // บันทึกลง DB
        $sql="INSERT INTO exercises1_video (filename) VALUES ('$file')";
         $conn->query($sql);

        // -------------------- คัดลอกไฟล์ไปยัง backup --------------------
        $backup_dir = "videos_backup/";
        if(!is_dir($backup_dir)){
            mkdir($backup_dir, 0777, true); // สร้างโฟลเดอร์ถ้าไม่มี
        }
        $backup_file = $backup_dir . basename($file);

        if(copy($target_file, $backup_file)){
            $message = "อัปโหลดวิดีโอและคัดลอกสำรองเรียบร้อย!";
        } else {
            $message = "อัปโหลดวิดีโอสำเร็จ แต่คัดลอกสำรองล้มเหลว!";
        }
    } else {
        $message = "อัปโหลดวิดีโอล้มเหลว!";
    }
}
echo "XXX".$file;
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
?>