<?php
// config/session_boot.php

// ตรวจว่าเป็น HTTPS (เผื่อมี reverse proxy)
$https = (
  (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
  (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
  (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

// ตั้งค่า cookie ของ session — ต้องทำก่อน session_start()
if (PHP_VERSION_ID >= 70300) {
  session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',        // ใช้โดเมนปัจจุบัน; ถ้าต้องทุกซับโดเมนค่อยใส่ .example.com
    'secure'   => $https,    // โปรดักชันควรเป็น true (ใช้ HTTPS เท่านั้น)
    'httponly' => true,
    'samesite' => 'Lax',     // ถ้าต้องส่งข้ามโดเมน: ใช้ 'None' และต้อง secure=true
  ]);
} else {
  // สำหรับ PHP < 7.3
  ini_set('session.cookie_secure', $https ? '1' : '0');
  ini_set('session.cookie_httponly', '1');
  session_set_cookie_params(0, '/; samesite=Lax', '', $https, true);
}

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>