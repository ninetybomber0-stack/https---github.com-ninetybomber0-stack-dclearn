CREATE TABLE IF NOT EXISTS `user_lesson_status` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` VARCHAR(255) NOT NULL,
  `lesson_id` INT(11) NOT NULL,
  `pre_test_completed` TINYINT(1) NOT NULL DEFAULT 0,
  `post_test_completed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_lesson_unique` (`user_id`, `lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
