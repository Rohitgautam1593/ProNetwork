<?php
/**
 * One-time messaging schema upgrade. Run: php database/run_messaging_migration.php
 */
require_once dirname(__DIR__) . '/app/config/config.php';

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$statements = [
    "CREATE TABLE IF NOT EXISTS `blocked_users` (
      `id` int NOT NULL AUTO_INCREMENT,
      `blocker_id` int NOT NULL,
      `blocked_id` int NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uk_block_pair` (`blocker_id`,`blocked_id`),
      KEY `idx_blocked` (`blocked_id`),
      CONSTRAINT `fk_block_blocker` FOREIGN KEY (`blocker_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk_block_blocked` FOREIGN KEY (`blocked_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
];

$columns = [
    'media_path' => "ALTER TABLE `messages` ADD COLUMN `media_path` varchar(500) NULL DEFAULT NULL AFTER `message_text`",
    'is_edited'  => "ALTER TABLE `messages` ADD COLUMN `is_edited` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_read`",
    'is_deleted' => "ALTER TABLE `messages` ADD COLUMN `is_deleted` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_edited`",
    'updated_at' => "ALTER TABLE `messages` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `sent_at`",
];

foreach ($statements as $sql) {
    try {
        $pdo->exec($sql);
        echo "OK: blocked_users\n";
    } catch (PDOException $e) {
        echo "blocked_users: " . $e->getMessage() . "\n";
    }
}

$existing = [];
try {
    $cols = $pdo->query("SHOW COLUMNS FROM messages")->fetchAll(PDO::FETCH_COLUMN);
    $existing = array_flip($cols);
} catch (PDOException $e) {
    die("messages table missing: " . $e->getMessage() . "\n");
}

foreach ($columns as $name => $sql) {
    if (isset($existing[$name])) {
        echo "Skip column: $name\n";
        continue;
    }
    try {
        $pdo->exec($sql);
        echo "Added column: $name\n";
    } catch (PDOException $e) {
        echo "Column $name: " . $e->getMessage() . "\n";
    }
}

echo "Messaging migration complete.\n";
