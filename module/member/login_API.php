<?php
declare(strict_types=1);

/* เปิด DEBUG เฉพาะทดสอบ เท่านั้น */
const DEBUG_AUTH = true;

/* session cookie ใช้ได้ทั้งไซต์ */
session_set_cookie_params([
  'lifetime'=>0,'path'=>'/','secure'=>false,'httponly'=>true,'samesite'=>'Lax'
]);
session_start();
header('Content-Type: application/json; charset=utf-8');

function respond(int $status, array $payload){
  http_response_code($status);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

/* helper: ตัดขีด/เว้นวรรคทุกแบบ */
function norm_dash_space(string $s): string {
  return preg_replace('/[\-\x{2010}\x{2011}\x{2012}\x{2013}\x{2014}\s]/u','',$s) ?? '';
}

/* รับ JSON หรือ form */
$ct = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
$in = (strpos($ct,'application/json')!==false)
        ? (json_decode(file_get_contents('php://input'), true) ?? [])
        : $_POST;

$username = trim((string)($in['username'] ?? ''));
$password = (string)($in['password'] ?? '');

if ($username==='' || $password==='') {
  respond(200, ['ok'=>false,'message'=>'กรอกชื่อผู้ใช้และรหัสผ่านให้ครบ','reason'=>DEBUG_AUTH?'empty_input':null]);
}

/* ทางลัดล็อกอินสำหรับผู้ดูแล (dev only) */
if (DEBUG_AUTH && $username === 'kamol' && $password === 'kamolch') {
  session_regenerate_id(true);
  $_SESSION['sess_userid']   = -1;                 // id พิเศษสำหรับ override
  $_SESSION['sess_username'] = 'kamol';
  $_SESSION['sess_fullname'] = 'Kamol (override)'; // ปรับข้อความได้
  $_SESSION['sess_position'] = 'teacher';          // หรือ 'admin' ตามระบบคุณ
  respond(200, ['ok'=>true, 'redirect'=>'/dclearn/index.php', 'override'=>true]);
}

/* DB */
require_once __DIR__ . '/../../config/connect.php';
if (empty($mysqli) || !($mysqli instanceof mysqli)) {
  respond(500, ['ok'=>false, 'message'=>'DB ไม่พร้อม']);
}
$mysqli->set_charset('utf8mb4');

/* หา user: ยอมรับ id_std มี/ไม่มีขีด */
$u_raw  = $username;
$u_norm = norm_dash_space($u_raw);

$sql = "
  SELECT id, id_std, fullname, `class`, `password` AS pass_hash
  FROM tb_member
  WHERE TRIM(id_std)=?
     OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(id_std), '-', ''), '–', ''), '—', ''), '-', ''), ' ', '') = ?
  LIMIT 1
";
$stmt = $mysqli->prepare($sql);
if(!$stmt) respond(500, ['ok'=>false,'message'=>'DB error: '.$mysqli->error]);
$stmt->bind_param('ss', $u_raw, $u_norm);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;
$stmt->close();

if(!$user){
  $p=['ok'=>false,'message'=>'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'];
  if(DEBUG_AUTH) $p += ['reason'=>'not_found','u_raw'=>$u_raw,'u_norm'=>$u_norm];
  respond(200,$p);
}

/* ตรวจรหัสผ่าน */
$dbPass = (string)($user['pass_hash'] ?? '');
$ok=false;
$used_plaintext_check = false; // เอาไว้รู้ว่าเข้าแขนง plaintext เพื่อลงมือ upgrade hash
$pw_norm = null; $db_norm = null;

if ($dbPass!=='' && (preg_match('/^\$2y\$/',$dbPass) || preg_match('/^\$argon2/',$dbPass))) {
  // bcrypt/argon2
  $ok = password_verify($password,$dbPass);
} else {
  // plaintext → เทียบแบบยืดหยุ่น (กันขีด/ช่องว่าง)
  $used_plaintext_check = true;
  $pw_raw  = $password;
  $pw_norm = norm_dash_space($pw_raw);
  $db_norm = norm_dash_space($dbPass);
  $ok = hash_equals($dbPass,$pw_raw) || hash_equals($db_norm,$pw_norm);
}

if(!$ok){
  $p=['ok'=>false,'message'=>'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'];
  if(DEBUG_AUTH){
    $p += [
      'reason'=>'bad_password',
      'id_std'=>$user['id_std'] ?? null,
      'pw_len'=>strlen($password),
      'db_len'=>strlen($dbPass),
      'pw_norm_len'=>$pw_norm!==null?strlen($pw_norm):null,
      'db_norm_len'=>$db_norm!==null?strlen($db_norm):null,
      'hashed_format'=> (strpos($dbPass,'$')===0)?'yes':'no',
    ];
  }
  // แก้บั๊กเดิม: ต้องตอบ ok=false (ไม่ใช่ redirect)
  respond(200, $p);
}

/* --- ผ่านการยืนยันรหัสแล้ว: อัปเกรด/รีแฮช ถ้าจำเป็น --- */
try {
  // 1) ถ้าเดิมเป็น plaintext → อัปเกรดเป็นแฮชทันที
  if ($used_plaintext_check) {
    $new_hash = password_hash($password, PASSWORD_DEFAULT);
    if ($upd = $mysqli->prepare("UPDATE tb_member SET password = ? WHERE id = ? LIMIT 1")) {
      $uid = (int)$user['id'];
      $upd->bind_param('si', $new_hash, $uid);
      $upd->execute();
      $upd->close();
    }
  } else {
    // 2) ถ้าเป็นแฮชอยู่แล้ว แต่เก่า/parameter เปลี่ยน → rehash
    if (password_needs_rehash($dbPass, PASSWORD_DEFAULT)) {
      $new_hash = password_hash($password, PASSWORD_DEFAULT);
      if ($upd = $mysqli->prepare("UPDATE tb_member SET password = ? WHERE id = ? LIMIT 1")) {
        $uid = (int)$user['id'];
        $upd->bind_param('si', $new_hash, $uid);
        $upd->execute();
        $upd->close();
      }
    }
  }
} catch (\Throwable $e) {
  if (DEBUG_AUTH) {
    // ไม่ทำให้ล้มการล็อกอิน แต่ออก debug ให้ทราบ
    error_log('rehash/upgrade password failed: '.$e->getMessage());
  }
}

/* ตั้ง session */
session_regenerate_id(true);
$_SESSION['sess_userid']   = (int)$user['id'];
$_SESSION['sess_username'] = (string)$user['id_std'];
$_SESSION['sess_fullname'] = (string)($user['fullname'] ?? '');
$_SESSION['sess_position'] = (string)($user['class'] ?? '');

// ส่งผลลัพธ์ให้หน้า login.php นำไป redirect ต่อเอง
respond(200, ['ok'=>true, 'redirect'=>'/dclearn/index.php']);
?>