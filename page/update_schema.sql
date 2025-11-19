-- This script updates the database schema for new features.
-- It is designed to be safely runnable multiple times.

-- 1. Create `tb_content` to hold lesson metadata.
CREATE TABLE IF NOT EXISTS `tb_content` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `lesson_id_text` VARCHAR(50) NOT NULL COMMENT 'The text identifier, e.g., w1-vid',
  `title` VARCHAR(255) NOT NULL,
  `lesson_order` INT NOT NULL DEFAULT 0,
  `video_path_720p` VARCHAR(255),
  `video_path_480p` VARCHAR(255),
  `poster_path` VARCHAR(255),
  `slide_url` VARCHAR(255),
  `quiz_url` VARCHAR(255),
  `score` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lesson_id_text` (`lesson_id_text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add `score` column to `tb_content` if it doesn't exist
SET @s = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tb_content'
    AND COLUMN_NAME = 'score'
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `tb_content` ADD `score` INT NOT NULL DEFAULT 0;"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add `name` column to `tb_content` if it doesn't exist
SET @s = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tb_content'
    AND COLUMN_NAME = 'name'
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `tb_content` ADD `name` VARCHAR(255) NOT NULL AFTER `id`;"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;


-- 2. Create `tb_test` for questions.
-- This includes the columns needed for lesson settings.
CREATE TABLE IF NOT EXISTS `tb_test` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lesson_id` INT UNSIGNED DEFAULT NULL,
  `lesson_id_text` VARCHAR(50),
  `question` TEXT NOT NULL,
  `choice_a` VARCHAR(500) NOT NULL,
  `choice_b` VARCHAR(500) NOT NULL,
  `choice_c` VARCHAR(500) NOT NULL,
  `choice_d` VARCHAR(500) NOT NULL,
  `correct` ENUM('A','B','C','D') NOT NULL,
  `timeshow` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Time in seconds to show the question',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lesson_id` (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add `timeshow` column to `tb_test` if it doesn't exist
SET @s = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tb_test'
    AND COLUMN_NAME = 'timeshow'
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `tb_test` ADD `timeshow` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Time in seconds to show the question';"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add `lesson_id` column to `tb_test` if it doesn't exist
SET @s = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tb_test'
    AND COLUMN_NAME = 'lesson_id'
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `tb_test` ADD `lesson_id` INT UNSIGNED DEFAULT NULL, ADD KEY `idx_lesson_id` (`lesson_id`);"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Rename old `lesson` column to `lesson_id_text` if it exists
SET @s = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tb_test'
    AND COLUMN_NAME = 'lesson'
  ) > 0,
  "ALTER TABLE `tb_test` CHANGE `lesson` `lesson_id_text` VARCHAR(50);",
  "SELECT 1"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;


-- 3. Populate `tb_content` with initial data.
INSERT INTO `tb_content` (`lesson_id_text`, `title`, `name`, `lesson_order`, `video_path_720p`, `video_path_480p`, `poster_path`, `slide_url`, `quiz_url`) VALUES
('w1-vid', 'สัปดาห์ 1: บทนำและพื้นฐาน', 'สัปดาห์ 1: บทนำและพื้นฐาน', 1, 'https://www.w3schools.com/html/mov_bbb.mp4', 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4', 'https://images.unsplash.com/photo-1523961131990-5ea7c61b2107?q=80&w=1600&auto=format&fit=crop', '#', '#'),
('w4-vid', 'สัปดาห์ 4: โมเดล OSI & TCP/IP', 'สัปดาห์ 4: โมเดล OSI & TCP/IP', 2, 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4', 'https://www.w3schools.com/html/mov_bbb.mp4', 'https://images.unsplash.com/photo-1518779578993-ec3579fee39f?q=80&w=1600&auto=format&fit=crop', '#', '#'),
('w6-vid', 'สัปดาห์ 6: IP Addressing & Subnetting', 'สัปดาห์ 6: IP Addressing & Subnetting', 3, 'https://www.w3schools.com/html/mov_bbb.mp4', 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4', 'https://images.unsplash.com/photo-1517433456452-f9633a875f6f?q=80&w=1600&auto=format&fit=crop', '#', '#')
ON DUPLICATE KEY UPDATE title=VALUES(title), name=VALUES(name), lesson_order=VALUES(lesson_order);

-- 4. Populate `tb_test` with sample data if it's empty.
INSERT IGNORE INTO `tb_test` (lesson_id_text, question, choice_a, choice_b, choice_c, choice_d, correct, timeshow)
VALUES
('w4-vid', 'เลเยอร์ใดของ OSI ที่รับผิดชอบการกำหนดเส้นทาง (Routing)?', 'Physical', 'Data Link', 'Network', 'Transport', 'C', 15),
('w1-vid', 'องค์ประกอบใดไม่ใช่ของระบบการสื่อสารข้อมูล?', 'ผู้ส่ง', 'ผู้รับ', 'ซอฟต์แวร์สำรองข้อมูล', 'สื่อกลาง', 'C', 15);

-- 5. Link `tb_test` to `tb_content` using the integer `id`.
-- This makes joins much more efficient.
UPDATE tb_test t
JOIN tb_content c ON t.lesson_id_text = c.lesson_id_text
SET t.lesson_id = c.id
WHERE t.lesson_id IS NULL;

-- 6. Create other tables for tracking progress.
CREATE TABLE IF NOT EXISTS `tb_transcripts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `lesson_id_text` VARCHAR(50) NOT NULL,
    `cue_time` INT NOT NULL COMMENT 'Time in seconds',
    `text` TEXT NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_lesson_id` (`lesson_id_text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_scores` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` VARCHAR(50) NOT NULL,
  `lesson_id` INT UNSIGNED NOT NULL,
  `score` INT NOT NULL DEFAULT 0,
  `total` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_student_lesson` (`student_id`, `lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: This script is now ready to be executed.