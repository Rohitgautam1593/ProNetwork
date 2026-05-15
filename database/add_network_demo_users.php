<?php
/**
 * Add extra demo users for My Network suggestions (safe to re-run).
 * Run: php database/add_network_demo_users.php
 */

$pdo = new PDO(
    'mysql:host=localhost;dbname=pronetwork;charset=utf8mb4',
    'root',
    '102004',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

$extra = [
    ['Priya Nair', 'priya.nair@demo.pronetwork', 'Product manager shipping B2B SaaS features', 'Product Management'],
    ['Vikram Desai', 'vikram.desai@demo.pronetwork', 'DevOps engineer automating cloud deployments', 'Cloud Infrastructure'],
    ['Anita Rao', 'anita.rao@demo.pronetwork', 'QA lead building reliable release pipelines', 'Quality Assurance'],
    ['Dev Patel', 'dev.patel@demo.pronetwork', 'Mobile developer learning Flutter and APIs', 'Mobile Development'],
    ['Isha Verma', 'isha.verma@demo.pronetwork', 'Content strategist for tech brands', 'Marketing'],
    ['Arjun Malhotra', 'arjun.malhotra@demo.pronetwork', 'Cybersecurity analyst monitoring threat surfaces', 'Security'],
    ['Meera Joshi', 'meera.joshi@demo.pronetwork', 'HR partner hiring engineering talent', 'Human Resources'],
    ['Karan Mehta', 'karan.mehta@demo.pronetwork', 'ML enthusiast exploring computer vision', 'Machine Learning'],
    ['Nina Kapoor', 'nina.kapoor@demo.pronetwork', 'Technical writer documenting developer tools', 'Technical Writing'],
    ['Rahul Choudhary', 'rahul.choudhary@demo.pronetwork', 'Solutions architect designing scalable systems', 'Architecture'],
    ['Zara Sheikh', 'zara.sheikh@demo.pronetwork', 'Graphic designer crafting brand identities', 'Design'],
    ['Omar Hassan', 'omar.hassan@demo.pronetwork', 'Sales engineer demoing data platforms', 'Sales'],
];

$stmt = $pdo->prepare(
    'INSERT INTO users (full_name, email, password, role, headline, industry, bio, is_admin)
     SELECT ?, ?, ?, "Professional", ?, ?, ?, 0 FROM DUAL
     WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = ?)'
);

$hash = password_hash('TestPass1', PASSWORD_DEFAULT);
$added = 0;
foreach ($extra as [$name, $email, $headline, $industry]) {
    $stmt->execute([$name, $email, $hash, $headline, $industry, "Demo profile for network suggestions.", $email]);
    $added += $stmt->rowCount();
}

echo "Added {$added} new demo user(s). Re-open My Network to see up to 12 suggestions.\n";
