<?php
// save_answer.php (unchanged logic; timeshow is not needed to log an answer)
// Accepts JSON (preferred) or form-encoded.
// Body fields: quiz_id, lesson, answer (A-D), correct (optional), is_correct (optional), user_id (optional)
header('Content-Type: application/json; charset=utf-8');

$DB_HOST = 'localhost';
$DB_USER = 'YOUR_DB_USER';
$DB_PASS = 'YOUR_DB_PASS';
$DB_NAME = 'YOUR_DB_NAME';

$mysqli = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>'db_connect_failed'], JSON_UNESCAPED_UNICODE);
  exit;
}
$mysqli->set_charset('utf8mb4');

// Parse JSON or fallback to POST
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) { $data = $_POST; }

$quiz_id = isset($data['quiz_id']) ? intval($data['quiz_id']) : 0;
$lesson  = isset($data['lesson']) ? trim($data['lesson']) : '';
$answer  = isset($data['answer']) ? strtoupper(trim($data['answer'])) : '';
$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;

if (!$quiz_id || !$lesson || !in_array($answer, ['A','B','C','D'])) {
  echo json_encode(['ok'=>false, 'error'=>'bad_params'], JSON_UNESCAPED_UNICODE);
  exit;
}

// If client didn't send correct / is_correct, compute from tb_test
$correct = isset($data['correct']) ? strtoupper(trim($data['correct'])) : '';
if (!in_array($correct, ['A','B','C','D'])) {
  $stmt = $mysqli->prepare("SELECT correct FROM tb_test WHERE id=?");
  $stmt->bind_param('i', $quiz_id);
  $stmt->execute();
  $res = $stmt->get_result();
  if (!$row = $res->fetch_assoc()) {
    echo json_encode(['ok'=>false, 'error'=>'quiz_not_found'], JSON_UNESCAPED_UNICODE);
    exit;
  }
  $correct = strtoupper($row['correct']);
}

$is_correct = ($answer === $correct) ? 1 : 0;

$stmt = $mysqli->prepare("
  INSERT INTO tb_test_log (quiz_id, lesson, answer, correct, is_correct, user_id)
  VALUES (?,?,?,?,?,?)
");
$stmt->bind_param('isssii', $quiz_id, $lesson, $answer, $correct, $is_correct, $user_id);
$ok = $stmt->execute();

echo json_encode([
  'ok' => (bool)$ok,
  'is_correct' => $is_correct,
  'correct' => $correct
], JSON_UNESCAPED_UNICODE);
