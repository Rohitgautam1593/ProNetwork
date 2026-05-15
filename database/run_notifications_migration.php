<?php
require_once dirname(__DIR__) . '/app/config/config.php';
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$cols = array_flip($pdo->query('SHOW COLUMNS FROM notifications')->fetchAll(PDO::FETCH_COLUMN));
if (!isset($cols['actor_id'])) {
    $pdo->exec('ALTER TABLE notifications ADD COLUMN actor_id INT NULL DEFAULT NULL AFTER user_id, ADD KEY idx_notif_actor (actor_id)');
    echo "Added actor_id\n";
} else {
    echo "actor_id exists\n";
}
echo "Done.\n";
