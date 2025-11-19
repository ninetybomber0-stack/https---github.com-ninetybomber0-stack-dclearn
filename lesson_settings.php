<?php
// session_start() ถูกเรียกแล้วใน index.php ไม่ต้องเรียกซ้ำ
// --- 1. เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล และตรวจสอบสิทธิ์ Admin ---
if (!isset($mysqli)) { // ตรวจสอบว่า $mysqli ถูกเรียกมาจาก index.php หรือยัง
    require_once __DIR__ . '/config/connect.php';
}

// ตรวจสอบสิทธิ์ Admin (ใช้เงื่อนไขเดียวกับ index.php)
if (!isset($_SESSION['sess_username']) || $_SESSION['sess_username'] !== 'kamol') {
    header('HTTP/1.0 403 Forbidden');
    echo 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้';
    exit;
}

// 2. ดึงข้อมูลบทเรียนและคำถามทั้งหมดจากฐานข้อมูล
// เปลี่ยนไปใช้ตาราง tb_content (บทเรียน) และ tb_test (คำถาม)
$sql = 
    'SELECT 
        c.id as lesson_id, 
        c.name as lesson_name, 
        c.score, 
        t.id as question_id, 
        t.question as question_text, 
        t.correct as answer, 
        t.timeshow as popup_time_seconds 
    FROM tb_content c
    LEFT JOIN tb_test t ON c.id = t.lesson_id
    ORDER BY c.id, t.id'
;
$result = $mysqli->query($sql);
$lessons = [];
 
// เพิ่มการตรวจสอบว่า query สำเร็จหรือไม่
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $lessons[$row['lesson_id']]['details'] = [
            'name' => $row['lesson_name'],
            'score' => $row['score']
        ];
        if ($row['question_id']) {
            $lessons[$row['lesson_id']]['questions'][] = [
                'id' => $row['question_id'],
                'text' => $row['question_text'],
                'answer' => $row['answer'],
                'popup_time' => $row['popup_time_seconds']
            ];
        }
    }
} else {
    // แสดงข้อความผิดพลาดหาก query ไม่สำเร็จ
    echo '<div class="alert alert-danger">เกิดข้อผิดพลาดในการดึงข้อมูลบทเรียน: ' . $mysqli->error . '</div>';
}

// --- แสดงข้อความแจ้งเตือน ---
$status_message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $status_message = '<div class="alert alert-success">✅ บันทึกข้อมูลสำเร็จ!</div>';
    } elseif ($_GET['status'] === 'error') {
        $status_message = '<div class="alert alert-danger">❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล</div>';
    }
}


?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งค่าบทเรียน (สำหรับ Admin)</title>
    <style>
        /* สามารถเพิ่ม CSS เพื่อความสวยงามได้ที่นี่ */
        body { font-family: sans-serif; }
        .lesson-card { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .question-group { margin-left: 20px; border-left: 2px solid #eee; padding-left: 15px; margin-top: 10px; }
        label { display: block; margin-top: 10px; }
        input[type=text], input[type=number] { width: 95%; padding: 8px; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; margin-top:15px;}
    </style>
</head>
<body>

    <h1>หน้าตั้งค่าบทเรียน (สำหรับ Admin)</h1>
    
    <?= $status_message // แสดงข้อความแจ้งเตือนที่นี่ ?>

    <?php foreach ($lessons as $lessonId => $data): ?>
        <div class="lesson-card">
            <form action="update_lesson.php" method="post">
                <input type="hidden" name="lesson_id" value="<?= htmlspecialchars($lessonId) ?>">
                <h2>บทเรียน: <?= htmlspecialchars($data['details']['name']) ?></h2>
                
                <label for="score_<?= htmlspecialchars($lessonId) ?>">คะแนนบทเรียน:</label>
                <input type="number" id="score_<?= htmlspecialchars($lessonId) ?>" name="score" value="<?= htmlspecialchars($data['details']['score']) ?>">

                <?php if (isset($data['questions'])): ?>
                    <h3>คำถามในบทเรียน:</h3>
                    <?php foreach ($data['questions'] as $question): ?>
                        <div class="question-group">
                            <input type="hidden" name="questions[<?= htmlspecialchars($question['id']) ?>][id]" value="<?= htmlspecialchars($question['id']) ?>">
                            
                            <label>คำถาม (จากตาราง tb_test):</label>
                            <input type="text" name="questions[<?= htmlspecialchars($question['id']) ?>][text]" value="<?= htmlspecialchars($question['text']) ?>">
                            
                            <label>คำตอบ (ตัวเลือกที่ถูกต้อง 1-4):</label>
                            <input type="text" name="questions[<?= htmlspecialchars($question['id']) ?>][answer]" value="<?= htmlspecialchars($question['answer']) ?>">

                            <label>เวลาที่ Popup (วินาที) (timeshow):</label>
                            <input type="number" name="questions[<?= htmlspecialchars($question['id']) ?>][popup_time]" value="<?= htmlspecialchars($question['popup_time']) ?>">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <button type="submit">บันทึกการเปลี่ยนแปลง</button>
            </form>
        </div>
    <?php endforeach; ?>

</body>
</html>