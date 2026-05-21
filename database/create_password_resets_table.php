<?php
/**
 * Database Migration: Create password_resets table
 */
require_once dirname(__DIR__) . '/app/config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $sql = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        otp VARCHAR(6) NOT NULL,
        token VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_otp (otp),
        INDEX idx_token (token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);

    $columns = $pdo->query("SHOW COLUMNS FROM password_resets")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('token', $columns, true)) {
        $pdo->exec("ALTER TABLE password_resets ADD token VARCHAR(64) NOT NULL DEFAULT '' AFTER otp");
    }

    $tokenIndex = $pdo->query("SHOW INDEX FROM password_resets WHERE Key_name = 'idx_token'")->fetchAll();
    if (empty($tokenIndex)) {
        $pdo->exec("ALTER TABLE password_resets ADD INDEX idx_token (token)");
    }

    echo "Migration Success: password_resets table created.\n";
} catch (PDOException $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
