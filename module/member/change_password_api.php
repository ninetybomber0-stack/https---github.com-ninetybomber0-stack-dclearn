<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/session_boot.php';
header('Content-Type: application/json; charset=utf-8');

function respond(int $code, array $payload){ http_response_code($code); echo json_encode($payload, JSON_UNESCAPED_UNICODE); exit; }

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['sess_userid']) || (int)$_SESSION['sess_userid'] <= 0) {
  respond(401, ['ok'=>false, 'message'=>'กรุณาล็อกอินก่อน']);
}

require_once __DIR__ . '/../../config/connect.php';
if (empty($mysqli) || !($mysqli instanceof mysqli)) respond(500, ['ok'=>false,'message'=>'DB ไม่พร้อม']);
$mysqli->set_charset('utf8mb4');

// รับและตรวจ CSRF
$csrf = $_POST['csrf_token'] ?? '';
if (!$csrf || !hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
  respond(403, ['ok'=>false, 'message'=>'CSRF token ไม่ถูกต้อง']);
}

// รับค่า
$current = (string)($_POST['current_password'] ?? '');
$new     = (string)($_POST['new_password'] ?? '');
if ($current === '' || $new === '') respond(200, ['ok'=>false,'message'=>'กรอกข้อมูลให้ครบ']);

$user_id = (int)$_SESSION['sess_userid'];

// ดึงข้อมูลผู้ใช้
$stmt = $mysqli->prepare("SELECT id, id_std, password AS pass_hash FROM tb_member WHERE id = ? LIMIT 1");
if (!$stmt) respond(500, ['ok'=>false,'message'=>'DB prepare error']);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;
$stmt->close();

if(!$user){
  respond(404, ['ok'=>false,'message'=>'ไม่พบผู้ใช้']);
}

// ตรวจรหัสผ่านเดิม (ตาม logic login เดิม)
$dbPass = (string)($user['pass_hash'] ?? '');
$ok = false;
if ($dbPass !== '' && (preg_match('/^\$2y\$/',$dbPass) || preg_match('/^\$argon2/',$dbPass))) {
  // แฮชสมัยใหม่
  $ok = password_verify($current, $dbPass);
} else {
  // plaintext → เทียบแบบยืดหยุ่น (ตัดขีด/เว้นวรรค)
  $norm = function ($s) {
    $out = preg_replace('~[\-\x{2010}\x{2011}\x{2012}\x{2013}\x{2014}\s]+~u', '', (string)$s);
    return $out === null ? '' : $out; // กันเคส UTF-8 พัง
  };
  $ok = hash_equals($dbPass, $current) || hash_equals($norm($dbPass), $norm($current));
}

if (!$ok) {
  respond(200, ['ok'=>false,'message'=>'รหัสผ่านเดิมไม่ถูกต้อง']);
}

// ตรวจความแข็งแรงเบื้องต้น
if (strlen($new) < 8 || !preg_match('/[A-Za-z]/',$new) || !preg_match('/\d/',$new)) {
  respond(200, ['ok'=>false,'message'=>'รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษร และมีทั้งตัวอักษรกับตัวเลข']);
}
if (hash_equals($current, $new)) {
  respond(200, ['ok'=>false,'message'=>'รหัสผ่านใหม่ต้องไม่เหมือนเดิม']);
}
if (!empty($user['id_std']) && hash_equals($new, (string)$user['id_std'])) {
  respond(200, ['ok'=>false,'message'=>'ห้ามใช้รหัสผ่านเป็นรหัสนักศึกษา']);
}

// แฮชใหม่และอัปเดต (bcrypt/argon2 แล้วแต่ค่าเริ่มต้นของ PHP)
$new_hash = password_hash($new, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("UPDATE tb_member SET password = ? WHERE id = ? LIMIT 1");
if (!$stmt) respond(500, ['ok'=>false,'message'=>'DB prepare error']);
$stmt->bind_param('si', $new_hash, $user_id);
if (!$stmt->execute()) {
  respond(500, ['ok'=>false,'message'=>'บันทึกไม่สำเร็จ: '.$stmt->error]);
}
$stmt->close();

// รีเจน session id (ทางเลือก)
session_regenerate_id(true);
respond(200, ['ok'=>true]);
?>