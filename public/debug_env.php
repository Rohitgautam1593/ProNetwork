<?php
// Temporary debug file. Will be deleted immediately.
header('Content-Type: text/plain');

require_once '../app/config/config.php';

echo "URLROOT: " . URLROOT . "\n";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_PORT: " . DB_PORT . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_NAME: " . DB_NAME . "\n";

// Test PDO connection
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_TIMEOUT => 3 // 3 seconds timeout
);

try {
    $dbh = new PDO($dsn, DB_USER, DB_PASS, $options);
    echo "DB Connection: SUCCESS\n";
    // Check tables
    $stmt = $dbh->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . "\n";
    
    // Check users
    $stmt = $dbh->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "User count: " . $count . "\n";
    
    if ($count > 0) {
        $stmt = $dbh->query("SELECT email, full_name, status, role FROM users LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $u) {
            echo " - User: {$u['email']} | Name: {$u['full_name']} | Status: {$u['status']} | Role: {$u['role']}\n";
        }
    }
} catch (Exception $e) {
    echo "DB Connection: FAILED - " . $e->getMessage() . "\n";
}

echo "Session save path: " . session_save_path() . "\n";
echo "Session writeable: " . (is_writable(session_save_path() ?: sys_get_temp_dir()) ? 'YES' : 'NO') . "\n";
