-- Messaging upgrade: media, edit/unsend, blocks
-- Run once on existing databases (safe to re-run with IF NOT EXISTS checks where supported)

CREATE TABLE IF NOT EXISTS `blocked_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `blocker_id` int NOT NULL,
  `blocked_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_block_pair` (`blocker_id`,`blocked_id`),
  KEY `idx_blocked` (`blocked_id`),
  CONSTRAINT `fk_block_blocker` FOREIGN KEY (`blocker_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_block_blocked` FOREIGN KEY (`blocked_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `messages`
  ADD COLUMN IF NOT EXISTS `media_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `message_text`,
  ADD COLUMN IF NOT EXISTS `is_edited` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_read`,
  ADD COLUMN IF NOT EXISTS `is_deleted` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_edited`,
  ADD COLUMN IF NOT EXISTS `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `sent_at`;
