<?php
if (empty($_SESSION['sess_userid'])) {
  header('Location: /dclearn/module/member/login.php'); // absolute path
  exit;
}

?>