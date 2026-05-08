<?php
$p = password_hash('TestPass1', PASSWORD_DEFAULT);
try {
    $pdo = new PDO('mysql:host=localhost;dbname=pronetwork', 'root', '102004');
    $stmt = $pdo->prepare("UPDATE users SET password = :p WHERE email IN ('admin@pronetwork.com', 'test2@example.com')");
    $stmt->execute([':p' => $p]);
    echo "Passwords reset successfully for admin@pronetwork.com and test2@example.com\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
