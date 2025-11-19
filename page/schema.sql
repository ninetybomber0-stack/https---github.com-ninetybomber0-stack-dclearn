-- SQL schema for quiz tables

CREATE TABLE IF NOT EXISTS tb_test (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  lesson VARCHAR(50) NOT NULL,          -- บท/สัปดาห์ เช่น 'w4-vid' หรือ 'สัปดาห์ 4'
  question TEXT NOT NULL,
  choice_a VARCHAR(500) NOT NULL,       -- ตัวเลือก ก (A)
  choice_b VARCHAR(500) NOT NULL,       -- ข (B)
  choice_c VARCHAR(500) NOT NULL,       -- ค (C)
  choice_d VARCHAR(500) NOT NULL,       -- ง (D)
  correct ENUM('A','B','C','D') NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_lesson (lesson)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_test_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  quiz_id INT UNSIGNED NOT NULL,
  lesson VARCHAR(50) NOT NULL,
  answer ENUM('A','B','C','D') NOT NULL,
  correct ENUM('A','B','C','D') NOT NULL,
  is_correct TINYINT(1) NOT NULL DEFAULT 0,
  user_id INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_quiz (quiz_id),
  KEY idx_user (user_id),
  KEY idx_lesson (lesson),
  CONSTRAINT fk_log_quiz FOREIGN KEY (quiz_id) REFERENCES tb_test(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample data
INSERT INTO tb_test (lesson, question, choice_a, choice_b, choice_c, choice_d, correct)
VALUES
('w4-vid', 'เลเยอร์ใดของ OSI ที่รับผิดชอบการกำหนดเส้นทาง (Routing)?', 'Physical', 'Data Link', 'Network', 'Transport', 'C'),
('w1-vid', 'องค์ประกอบใดไม่ใช่ของระบบการสื่อสารข้อมูล?', 'ผู้ส่ง', 'ผู้รับ', 'ซอฟต์แวร์สำรองข้อมูล', 'สื่อกลาง', 'C');
