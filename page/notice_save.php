<?php
// notice_save.php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/../config/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success'=>false, 'error'=>'Method Not Allowed']); exit;
}

$message = trim($_POST['message'] ?? '');
if ($message === '') {
  http_response_code(400);
  echo json_encode(['success'=>false, 'error'=>'กรุณากรอกข้อความประกาศ']); exit;
}

// บังคับความยาวสูงสุดกันสแปม (ตรงกับ maxlength ฟอร์ม)
if (mb_strlen($message) > 1000) {
  http_response_code(400);
  echo json_encode(['success'=>false, 'error'=>'ข้อความยาวเกินกำหนด']); exit;
}

$sql = "INSERT INTO tb_notice (message) VALUES (?)";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['success'=>false, 'error'=>'DB prepare error']); exit;
}
$stmt->bind_param('s', $message);
$ok = $stmt->execute();
if (!$ok) {
  http_response_code(500);
  echo json_encode(['success'=>false, 'error'=>'DB execute error']); exit;
}

// ดึงแถวล่าสุดที่เพิ่ง insert เพื่อส่งกลับไป render
$id = $mysqli->insert_id;
$q = $mysqli->query("SELECT id, message, `time` FROM tb_notice WHERE id = {$id} LIMIT 1");
$row = $q ? $q->fetch_assoc() : ['id'=>$id, 'message'=>$message, 'time'=>date('Y-m-d H:i:s')];

echo json_encode(['success'=>true, 'row'=>$row], JSON_UNESCAPED_UNICODE);
?>