<?php
// get_tests.php (แก้ไขสำหรับ dclearn project)
// Usage: GET ?lesson_id=1
header('Content-Type: application/json; charset=utf-8');

// --- 1. เชื่อมต่อฐานข้อมูล (ใช้ไฟล์กลาง) ---
require_once __DIR__ . '/../config/connect.php';
if (!isset($mysqli) || $mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>'db_connect_failed'], JSON_UNESCAPED_UNICODE);
  exit;
}

// --- 2. รับค่า lesson_id ---
$lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;

if (!$lesson_id) {
  echo json_encode([], JSON_UNESCAPED_UNICODE);
  exit;
}

// --- 3. ดึงข้อมูลคำถามจาก tb_test ---
// เราจะใช้ตาราง tb_test ตามที่หน้า manage_quizzes.php จัดการ
// และเรียงตามเวลาที่แสดง (timeshow)
$stmt = $mysqli->prepare("
  SELECT id, lesson_id, question, option1, option2, option3, option4, correct, timeshow, points
  FROM tb_test
  WHERE lesson_id = ?
  ORDER BY CAST(SUBSTRING_INDEX(timeshow, ':', 1) AS UNSIGNED) * 60 + CAST(SUBSTRING_INDEX(timeshow, ':', -1) AS UNSIGNED) ASC
  LIMIT 20
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok'=>false, 'error'=>'db_prepare_failed', 'message' => $mysqli->error], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param('i', $lesson_id);
$stmt->execute();
$res = $stmt->get_result();
$list = [];
while ($row = $res->fetch_assoc()) {
  $list[] = [
    'id' => (int)$row['id'],
    'lesson_id' => (int)$row['lesson_id'],
    'question' => $row['question'],
    // ส่งข้อมูลตัวเลือกกลับไปในรูปแบบที่ JavaScript ต้องการ
    'option1' => $row['option1'],
    'option2' => $row['option2'],
    'option3' => $row['option3'],
    'option4' => $row['option4'],
    // correct ใน tb_test เป็น index (0-3) ซึ่งตรงกับที่ JS ต้องการแล้ว
    'correct' => (int)$row['correct'],
    'timeshow' => (string)$row['timeshow'],
    'points' => (int)$row['points']
  ];
}

echo json_encode($list, JSON_UNESCAPED_UNICODE);

$stmt->close();
$mysqli->close();
?>
