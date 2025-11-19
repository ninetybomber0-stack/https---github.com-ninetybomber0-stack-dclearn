<?php
/* DEBUG เปิดชั่วคราว
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');


echo "METHOD=" . $_SERVER['REQUEST_METHOD'] . PHP_EOL;
echo "CONTENT_TYPE=" . ($_SERVER['CONTENT_TYPE'] ?? '') . PHP_EOL;
echo "\$_POST="; var_dump($_POST);
echo "\$_GET="; var_dump($_GET);
$raw = file_get_contents('php://input');
echo "RAW_BODY=" . $raw . PHP_EOL;
*/

// เริ่ม session เพื่อเข้าถึงข้อมูลผู้ใช้ที่ล็อกอิน
require_once __DIR__ . '/../config/session_boot.php';

// เชื่อมต่อฐานข้อมูล (ปรับพาธตามโครงจริงของคุณ)
require_once __DIR__ . '/../config/connect.php';   // ถ้า connect.php อยู่โฟลเดอร์บน
// require_once __DIR__ . '/connect.php';          // ถ้าอยู่โฟลเดอร์เดียวกัน

if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
  http_response_code(500);
  exit("DB error: \$mysqli not initialized (ตรวจพาธ require_once connect.php)");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

// ใช้ข้อมูลจาก Session เพื่อความปลอดภัย แทนการรับจาก POST โดยตรง
$student_id = $_SESSION['sess_id_std'] ?? null; // ใช้ id_std จาก session
$lesson_id  = $_POST['lesson_id'] ?? null;      // lesson_id ยังคงรับจาก form ได้
$score      = isset($_POST['score']) ? (int)$_POST['score'] : 0;

if (empty($student_id) || empty($lesson_id)) {
  http_response_code(400);
  exit('missing params: student_id/lesson_id');
}

// ตรวจว่าเราอยู่ DB ไหน (กันต่อคนละ DB แล้วหาไม่เจอ)
list($current_db) = $mysqli->query("SELECT DATABASE()")->fetch_row();

// ใช้ prepared statement
$sql = "INSERT INTO tb_scores (student_id, lesson_id, score) VALUES (?, ?, ?)";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  exit("DB prepare error: " . $mysqli->error . " | DB=" . $current_db);
}

if (!$stmt->bind_param('ssi', $student_id, $lesson_id, $score)) {
  http_response_code(500);
  exit("bind_param error: " . $stmt->error);
}

if (!$stmt->execute()) {
  http_response_code(500);
  exit("DB execute error: " . $stmt->error . " | DB=" . $current_db);
}

echo "บันทึกคะแนนสำเร็จ";
//student_id={$student_id}, lesson_id={$lesson_id}, score={$score} | rows={$stmt->affected_rows} | last_id={$mysqli->insert_id} | DB={$current_db}
?>