<?php
/**
 * Seed realistic ProNetwork demo data.
 * Run from project root with: php database/seed_sample_data.php
 */

$host = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'localhost';
$port = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
$user = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? 'root';
$pass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? '102004';
$dbname = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? 'pronetwork';

$pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
    $user,
    $pass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

function firstId(PDO $pdo, string $sql, array $params = []): ?int {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ? (int)array_values($row)[0] : null;
}

function insertUser(PDO $pdo, array $user): int {
    $existing = firstId($pdo, 'SELECT user_id FROM users WHERE email = ?', [$user['email']]);
    if ($existing) {
        return $existing;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO users (full_name, email, password, role, headline, location, industry, bio, phone, website, profile_pic, cover_image, is_admin)
         VALUES (:full_name, :email, :password, :role, :headline, :location, :industry, :bio, :phone, :website, :profile_pic, :cover_image, :is_admin)'
    );
    $stmt->execute([
        ':full_name' => $user['full_name'],
        ':email' => $user['email'],
        ':password' => password_hash('TestPass1', PASSWORD_DEFAULT),
        ':role' => $user['role'],
        ':headline' => $user['headline'],
        ':location' => $user['location'],
        ':industry' => $user['industry'],
        ':bio' => $user['bio'],
        ':phone' => $user['phone'] ?? null,
        ':website' => $user['website'] ?? null,
        ':profile_pic' => null,
        ':cover_image' => null,
        ':is_admin' => $user['is_admin'] ?? 0,
    ]);

    return (int)$pdo->lastInsertId();
}

function insertCompany(PDO $pdo, array $company): int {
    $existing = firstId($pdo, 'SELECT company_id FROM companies WHERE name = ?', [$company['name']]);
    if ($existing) {
        return $existing;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO companies (name, industry, logo_path, banner_path, description, website, size, founded_year, followers)
         VALUES (:name, :industry, :logo_path, :banner_path, :description, :website, :size, :founded_year, :followers)'
    );
    $stmt->execute([
        ':name' => $company['name'],
        ':industry' => $company['industry'],
        ':logo_path' => $company['logo_path'] ?? null,
        ':banner_path' => $company['banner_path'] ?? null,
        ':description' => $company['description'],
        ':website' => $company['website'],
        ':size' => $company['size'],
        ':founded_year' => $company['founded_year'],
        ':followers' => $company['followers'],
    ]);

    return (int)$pdo->lastInsertId();
}

function insertCompanyFollow(PDO $pdo, int $userId, int $companyId): void {
    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO company_followers (company_id, user_id) VALUES (?, ?)'
    );
    $stmt->execute([$companyId, $userId]);
}

function insertConnection(PDO $pdo, int $a, int $b, string $status): void {
    if ($a === $b) {
        return;
    }
    $existing = firstId(
        $pdo,
        'SELECT connection_id FROM connections WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)',
        [$a, $b, $b, $a]
    );
    if ($existing) {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO connections (sender_id, receiver_id, status) VALUES (?, ?, ?)');
    $stmt->execute([$a, $b, $status]);
}

function insertPost(PDO $pdo, int $userId, string $content): int {
    $existing = firstId($pdo, 'SELECT post_id FROM posts WHERE user_id = ? AND content = ?', [$userId, $content]);
    if ($existing) {
        return $existing;
    }

    $stmt = $pdo->prepare('INSERT INTO posts (user_id, content, visibility) VALUES (?, ?, "Public")');
    $stmt->execute([$userId, $content]);
    return (int)$pdo->lastInsertId();
}

function insertReaction(PDO $pdo, int $postId, int $userId, string $type = 'like'): void {
    $existing = firstId($pdo, 'SELECT reaction_id FROM post_reactions WHERE post_id = ? AND user_id = ?', [$postId, $userId]);
    if ($existing) {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO post_reactions (post_id, user_id, type) VALUES (?, ?, ?)');
    $stmt->execute([$postId, $userId, $type]);
}

function insertComment(PDO $pdo, int $postId, int $userId, string $content): void {
    $existing = firstId($pdo, 'SELECT comment_id FROM comments WHERE post_id = ? AND user_id = ? AND content = ?', [$postId, $userId, $content]);
    if ($existing) {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
    $stmt->execute([$postId, $userId, $content]);
}

function insertMessage(PDO $pdo, int $senderId, int $receiverId, string $text): void {
    $existing = firstId($pdo, 'SELECT message_id FROM messages WHERE sender_id = ? AND receiver_id = ? AND message_text = ?', [$senderId, $receiverId, $text]);
    if ($existing) {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)');
    $stmt->execute([$senderId, $receiverId, $text]);
}

function insertNotification(PDO $pdo, int $userId, string $type, string $message, ?int $sourceId = null, ?string $sourceType = null): void {
    $existing = firstId($pdo, 'SELECT notification_id FROM notifications WHERE user_id = ? AND type = ? AND message = ?', [$userId, $type, $message]);
    if ($existing) {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO notifications (user_id, type, source_id, source_type, message) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$userId, $type, $sourceId, $sourceType, $message]);
}

function insertExperience(PDO $pdo, int $userId, array $exp): void {
    $existing = firstId($pdo, 'SELECT exp_id FROM user_experience WHERE user_id = ? AND job_title = ? AND company = ?', [$userId, $exp['job_title'], $exp['company']]);
    if ($existing) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO user_experience (user_id, job_title, company, emp_type, start_date, end_date, is_current, location, description)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $userId,
        $exp['job_title'],
        $exp['company'],
        $exp['emp_type'],
        $exp['start_date'],
        $exp['end_date'],
        $exp['is_current'],
        $exp['location'],
        $exp['description'],
    ]);
}

function insertEducation(PDO $pdo, int $userId, array $edu): void {
    $existing = firstId($pdo, 'SELECT edu_id FROM user_education WHERE user_id = ? AND institution = ? AND degree = ?', [$userId, $edu['institution'], $edu['degree']]);
    if ($existing) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO user_education (user_id, institution, degree, field, start_year, end_year, description)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $userId,
        $edu['institution'],
        $edu['degree'],
        $edu['field'],
        $edu['start_year'],
        $edu['end_year'],
        $edu['description'],
    ]);
}

function insertJob(PDO $pdo, int $companyId, array $job): int {
    $existing = firstId($pdo, 'SELECT job_id FROM jobs WHERE company_id = ? AND title = ?', [$companyId, $job['title']]);
    if ($existing) {
        return $existing;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO jobs (company_id, title, description, location, salary_range, job_type, experience_level, easy_apply, applicant_count, applicant_limit, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $companyId,
        $job['title'],
        $job['description'],
        $job['location'],
        $job['salary_range'],
        $job['job_type'],
        $job['experience_level'],
        $job['easy_apply'],
        $job['applicant_count'],
        $job['applicant_limit'] ?? null,
        $job['status'],
    ]);

    return (int)$pdo->lastInsertId();
}

function insertApplication(PDO $pdo, int $jobId, int $userId, array $application): void {
    $existing = firstId($pdo, 'SELECT application_id FROM applications WHERE job_id = ? AND user_id = ?', [$jobId, $userId]);
    if ($existing) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO applications (job_id, user_id, first_name, last_name, phone, resume_path, cover_letter, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $jobId,
        $userId,
        $application['first_name'],
        $application['last_name'],
        $application['phone'],
        $application['resume_path'],
        $application['cover_letter'],
        $application['status'],
    ]);
}

$pdo->beginTransaction();

try {
    $users = [];
    foreach ([
        [
            'full_name' => 'Aarav Mehta',
            'email' => 'aarav.mehta@demo.pronetwork',
            'role' => 'Student',
            'headline' => 'Computer science student focused on full-stack projects',
            'location' => 'Pune, India',
            'industry' => 'Software Development',
            'bio' => 'Learning Laravel, PHP, JavaScript, and practical product engineering.',
            'phone' => '+91 90000 10001',
            'website' => 'https://portfolio.example.com/aarav',
        ],
        [
            'full_name' => 'Maya Kapoor',
            'email' => 'maya.kapoor@demo.pronetwork',
            'role' => 'Professional',
            'headline' => 'Frontend engineer building accessible dashboards',
            'location' => 'Bengaluru, India',
            'industry' => 'Design Systems',
            'bio' => 'I build fast interfaces with clean component patterns and strong UX details.',
            'phone' => '+91 90000 10002',
            'website' => 'https://maya.example.com',
        ],
        [
            'full_name' => 'Rohan Singh',
            'email' => 'rohan.singh@demo.pronetwork',
            'role' => 'Professional',
            'headline' => 'Backend developer working with PHP and MySQL',
            'location' => 'Delhi, India',
            'industry' => 'Backend Engineering',
            'bio' => 'Focused on reliable APIs, database design, and performance tuning.',
            'phone' => '+91 90000 10003',
            'website' => 'https://rohan.example.com',
        ],
        [
            'full_name' => 'Neha Sharma',
            'email' => 'neha.sharma@demo.pronetwork',
            'role' => 'Company',
            'headline' => 'Company hiring lead for early-career engineering roles',
            'location' => 'Hyderabad, India',
            'industry' => 'Recruiting',
            'bio' => 'Helping students and professionals find strong software teams.',
            'phone' => '+91 90000 10004',
            'website' => 'https://talent.example.com/neha',
        ],
        [
            'full_name' => 'Kabir Khan',
            'email' => 'kabir.khan@demo.pronetwork',
            'role' => 'Professional',
            'headline' => 'Data analyst turning product data into decisions',
            'location' => 'Mumbai, India',
            'industry' => 'Data Analytics',
            'bio' => 'I enjoy dashboards, SQL, product metrics, and storytelling with data.',
            'phone' => '+91 90000 10005',
            'website' => 'https://kabir.example.com',
        ],
        [
            'full_name' => 'Sara Thomas',
            'email' => 'sara.thomas@demo.pronetwork',
            'role' => 'Student',
            'headline' => 'UI/UX learner exploring product design and research',
            'location' => 'Kochi, India',
            'industry' => 'Product Design',
            'bio' => 'Design student interested in accessible interfaces and usability testing.',
            'phone' => '+91 90000 10006',
            'website' => 'https://sara.example.com',
        ],
        ['full_name' => 'Priya Nair', 'email' => 'priya.nair@demo.pronetwork', 'role' => 'Professional', 'headline' => 'Product manager shipping B2B SaaS features', 'location' => 'Chennai, India', 'industry' => 'Product Management', 'bio' => 'Focused on roadmap planning and user research.', 'phone' => '+91 90000 10007', 'website' => 'https://priya.example.com'],
        ['full_name' => 'Vikram Desai', 'email' => 'vikram.desai@demo.pronetwork', 'role' => 'Professional', 'headline' => 'DevOps engineer automating cloud deployments', 'location' => 'Pune, India', 'industry' => 'Cloud Infrastructure', 'bio' => 'CI/CD, Docker, and observability enthusiast.', 'phone' => '+91 90000 10008', 'website' => 'https://vikram.example.com'],
        ['full_name' => 'Anita Rao', 'email' => 'anita.rao@demo.pronetwork', 'role' => 'Professional', 'headline' => 'QA lead building reliable release pipelines', 'location' => 'Bengaluru, India', 'industry' => 'Quality Assurance', 'bio' => 'Test automation and accessibility checks.', 'phone' => '+91 90000 10009', 'website' => 'https://anita.example.com'],
        ['full_name' => 'Dev Patel', 'email' => 'dev.patel@demo.pronetwork', 'role' => 'Student', 'headline' => 'Mobile developer learning Flutter and APIs', 'location' => 'Ahmedabad, India', 'industry' => 'Mobile Development', 'bio' => 'Building cross-platform apps for campus projects.', 'phone' => '+91 90000 10010', 'website' => 'https://dev.example.com'],
        ['full_name' => 'Isha Verma', 'email' => 'isha.verma@demo.pronetwork', 'role' => 'Professional', 'headline' => 'Content strategist for tech brands', 'location' => 'Jaipur, India', 'industry' => 'Marketing', 'bio' => 'Storytelling, SEO, and social campaigns.', 'phone' => '+91 90000 10011', 'website' => 'https://isha.example.com'],
        ['full_name' => 'Arjun Malhotra', 'email' => 'arjun.malhotra@demo.pronetwork', 'role' => 'Professional', 'headline' => 'Cybersecurity analyst monitoring threat surfaces', 'location' => 'Gurgaon, India', 'industry' => 'Security', 'bio' => 'Incident response and secure SDLC practices.', 'phone' => '+91 90000 10012', 'website' => 'https://arjun.example.com'],
        ['full_name' => 'Meera Joshi', 'email' => 'meera.joshi@demo.pronetwork', 'role' => 'Professional', 'headline' => 'HR partner hiring engineering talent', 'location' => 'Mumbai, India', 'industry' => 'Human Resources', 'bio' => 'Campus hiring and employer branding.', 'phone' => '+91 90000 10013', 'website' => 'https://meera.example.com'],
        ['full_name' => 'Karan Mehta', 'email' => 'karan.mehta@demo.pronetwork', 'role' => 'Student', 'headline' => 'ML enthusiast exploring computer vision', 'location' => 'Indore, India', 'industry' => 'Machine Learning', 'bio' => 'Python, PyTorch, and dataset curation.', 'phone' => '+91 90000 10014', 'website' => 'https://karan.example.com'],
        ['full_name' => 'Nina Kapoor', 'email' => 'nina.kapoor@demo.pronetwork', 'role' => 'Professional', 'headline' => 'Technical writer documenting developer tools', 'location' => 'Noida, India', 'industry' => 'Technical Writing', 'bio' => 'API docs, tutorials, and release notes.', 'phone' => '+91 90000 10015', 'website' => 'https://nina.example.com'],
        ['full_name' => 'Rahul Choudhary', 'email' => 'rahul.choudhary@demo.pronetwork', 'role' => 'Professional', 'headline' => 'Solutions architect designing scalable systems', 'location' => 'Kolkata, India', 'industry' => 'Architecture', 'bio' => 'Microservices and integration patterns.', 'phone' => '+91 90000 10016', 'website' => 'https://rahul.example.com'],
        ['full_name' => 'Zara Sheikh', 'email' => 'zara.sheikh@demo.pronetwork', 'role' => 'Student', 'headline' => 'Graphic designer crafting brand identities', 'location' => 'Lucknow, India', 'industry' => 'Design', 'bio' => 'Visual systems for startups and nonprofits.', 'phone' => '+91 90000 10017', 'website' => 'https://zara.example.com'],
        ['full_name' => 'Omar Hassan', 'email' => 'omar.hassan@demo.pronetwork', 'role' => 'Professional', 'headline' => 'Sales engineer demoing data platforms', 'location' => 'Dubai, UAE', 'industry' => 'Sales', 'bio' => 'Helping teams evaluate analytics stacks.', 'phone' => '+971 50000 10018', 'website' => 'https://omar.example.com'],
    ] as $user) {
        $users[$user['email']] = insertUser($pdo, $user);
    }

    $companyIds = [];
    foreach ([
        [
            'name' => 'Nexa Analytics',
            'industry' => 'Data Analytics',
            'logo_path' => 'logos/nexa.png',
            'banner_path' => 'banners/nexa-analytics-banner.jpeg',
            'description' => 'Nexa Analytics helps businesses understand customer behavior through modern BI and machine learning workflows.',
            'website' => 'https://nexa-analytics.example.com',
            'size' => '201-500 employees',
            'founded_year' => 2018,
            'followers' => 8420,
        ],
        [
            'name' => 'GreenGrid Labs',
            'industry' => 'Clean Technology',
            'logo_path' => 'logos/greengrid.png',
            'banner_path' => 'banners/greengrid-labs-banner.jpeg',
            'description' => 'GreenGrid Labs builds software for energy monitoring, smart buildings, and sustainability reporting.',
            'website' => 'https://greengrid.example.com',
            'size' => '51-200 employees',
            'founded_year' => 2020,
            'followers' => 3160,
        ],
    ] as $company) {
        $companyIds[$company['name']] = insertCompany($pdo, $company);
    }

    $cloudScaleId = firstId($pdo, 'SELECT company_id FROM companies WHERE name = ?', ['CloudScale Systems']);
    $jobs = [];
    $jobs[] = insertJob($pdo, $cloudScaleId ?: $companyIds['Nexa Analytics'], [
        'title' => 'Junior PHP Developer',
        'description' => "Work on MVC features, MySQL-backed pages, and user-facing improvements for a professional networking product.",
        'location' => 'Remote, India',
        'salary_range' => '₹4 LPA - ₹7 LPA',
        'job_type' => 'Full-time',
        'experience_level' => 'Entry',
        'easy_apply' => 1,
        'applicant_count' => 18,
        'applicant_limit' => 20,
        'status' => 'Live',
    ]);
    $jobs[] = insertJob($pdo, $companyIds['Nexa Analytics'], [
        'title' => 'Data Analyst Intern',
        'description' => "Create SQL reports, product dashboards, and weekly insights for customer success teams.",
        'location' => 'Bengaluru, India',
        'salary_range' => '₹20k - ₹35k/month',
        'job_type' => 'Internship',
        'experience_level' => 'Entry',
        'easy_apply' => 1,
        'applicant_count' => 32,
        'applicant_limit' => 35,
        'status' => 'Live',
    ]);
    $jobs[] = insertJob($pdo, $companyIds['GreenGrid Labs'], [
        'title' => 'Frontend Engineer',
        'description' => "Build responsive dashboards for sustainability analytics using HTML, CSS, JavaScript, and API data.",
        'location' => 'Hyderabad, India',
        'salary_range' => '₹8 LPA - ₹12 LPA',
        'job_type' => 'Full-time',
        'experience_level' => 'Mid-Senior',
        'easy_apply' => 0,
        'applicant_count' => 11,
        'applicant_limit' => 12,
        'status' => 'Live',
    ]);

    insertConnection($pdo, $users['aarav.mehta@demo.pronetwork'], $users['maya.kapoor@demo.pronetwork'], 'Accepted');
    insertConnection($pdo, $users['aarav.mehta@demo.pronetwork'], $users['rohan.singh@demo.pronetwork'], 'Accepted');
    insertConnection($pdo, $users['maya.kapoor@demo.pronetwork'], $users['kabir.khan@demo.pronetwork'], 'Accepted');
    insertConnection($pdo, $users['sara.thomas@demo.pronetwork'], $users['neha.sharma@demo.pronetwork'], 'Pending');
    insertConnection($pdo, $users['kabir.khan@demo.pronetwork'], $users['rohan.singh@demo.pronetwork'], 'Pending');

    $aaravId = $users['aarav.mehta@demo.pronetwork'] ?? null;
    if ($aaravId) {
        insertCompanyFollow($pdo, $aaravId, $companyIds['Nexa Analytics']);
        insertCompanyFollow($pdo, $aaravId, $companyIds['GreenGrid Labs']);
    }

    $post1 = insertPost($pdo, $users['maya.kapoor@demo.pronetwork'], 'Finished a responsive dashboard redesign today. Small spacing decisions made the data much easier to scan.');
    $post2 = insertPost($pdo, $users['rohan.singh@demo.pronetwork'], 'Prepared statements and clean model methods make PHP MVC projects much easier to maintain.');
    $post3 = insertPost($pdo, $users['aarav.mehta@demo.pronetwork'], 'Working on my ProNetwork project and finally connected the UI with real MySQL data.');
    $post4 = insertPost($pdo, $users['neha.sharma@demo.pronetwork'], 'Hiring note: clear project screenshots and working demo data make student portfolios stand out immediately.');

    insertComment($pdo, $post1, $users['kabir.khan@demo.pronetwork'], 'The dashboard looks clean. Would love to see the metrics layout.');
    insertComment($pdo, $post2, $users['maya.kapoor@demo.pronetwork'], 'Agreed. It also makes frontend integration more predictable.');
    insertComment($pdo, $post3, $users['rohan.singh@demo.pronetwork'], 'Nice work. Database-backed features always feel more complete.');
    insertComment($pdo, $post4, $users['sara.thomas@demo.pronetwork'], 'This is helpful advice for our submissions.');

    insertReaction($pdo, $post1, $users['aarav.mehta@demo.pronetwork']);
    insertReaction($pdo, $post1, $users['rohan.singh@demo.pronetwork']);
    insertReaction($pdo, $post2, $users['maya.kapoor@demo.pronetwork']);
    insertReaction($pdo, $post3, $users['neha.sharma@demo.pronetwork']);
    insertReaction($pdo, $post4, $users['sara.thomas@demo.pronetwork']);

    insertMessage($pdo, $users['aarav.mehta@demo.pronetwork'], $users['maya.kapoor@demo.pronetwork'], 'Hi Maya, can you review the profile page layout?');
    insertMessage($pdo, $users['maya.kapoor@demo.pronetwork'], $users['aarav.mehta@demo.pronetwork'], 'Sure. The cards look good, but the bio should use real data.');
    insertMessage($pdo, $users['aarav.mehta@demo.pronetwork'], $users['rohan.singh@demo.pronetwork'], 'Rohan, I fixed the connection request flow.');
    insertMessage($pdo, $users['rohan.singh@demo.pronetwork'], $users['aarav.mehta@demo.pronetwork'], 'Great. Test messaging after accepting the request.');

    insertNotification($pdo, $users['maya.kapoor@demo.pronetwork'], 'Like', 'Aarav Mehta liked your post', $post1, 'post');
    insertNotification($pdo, $users['aarav.mehta@demo.pronetwork'], 'Connection_Accepted', 'Maya Kapoor accepted your connection request', null, 'connection');
    insertNotification($pdo, $users['sara.thomas@demo.pronetwork'], 'Connection_Request', 'Neha Sharma sent you a connection request', null, 'connection');
    insertNotification($pdo, $users['aarav.mehta@demo.pronetwork'], 'Job_Alert', 'Junior PHP Developer is open at CloudScale Systems', $jobs[0], 'job');
    insertNotification($pdo, $users['kabir.khan@demo.pronetwork'], 'Application_Update', 'Your Data Analyst Intern application is under review', $jobs[1], 'job');

    insertExperience($pdo, $users['maya.kapoor@demo.pronetwork'], [
        'job_title' => 'Frontend Engineer',
        'company' => 'Nexa Analytics',
        'emp_type' => 'Full-time',
        'start_date' => '2024-06-01',
        'end_date' => null,
        'is_current' => 1,
        'location' => 'Bengaluru, India',
        'description' => 'Builds responsive product dashboards and shared UI components.',
    ]);
    insertExperience($pdo, $users['rohan.singh@demo.pronetwork'], [
        'job_title' => 'Backend Developer',
        'company' => 'CloudScale Systems',
        'emp_type' => 'Full-time',
        'start_date' => '2023-08-15',
        'end_date' => null,
        'is_current' => 1,
        'location' => 'Remote',
        'description' => 'Maintains PHP services, MySQL queries, and MVC application modules.',
    ]);
    insertExperience($pdo, $users['aarav.mehta@demo.pronetwork'], [
        'job_title' => 'Web Development Intern',
        'company' => 'GreenGrid Labs',
        'emp_type' => 'Internship',
        'start_date' => '2025-01-10',
        'end_date' => '2025-04-30',
        'is_current' => 0,
        'location' => 'Pune, India',
        'description' => 'Created UI pages and connected them with database-backed endpoints.',
    ]);

    insertEducation($pdo, $users['aarav.mehta@demo.pronetwork'], [
        'institution' => 'Pune Institute of Technology',
        'degree' => 'B.Tech',
        'field' => 'Computer Science',
        'start_year' => 2022,
        'end_year' => 2026,
        'description' => 'Coursework in web development, DBMS, and software engineering.',
    ]);
    insertEducation($pdo, $users['sara.thomas@demo.pronetwork'], [
        'institution' => 'Kochi Design School',
        'degree' => 'B.Des',
        'field' => 'User Experience Design',
        'start_year' => 2023,
        'end_year' => 2027,
        'description' => 'Focused on interface design, research, and product usability.',
    ]);
    insertEducation($pdo, $users['kabir.khan@demo.pronetwork'], [
        'institution' => 'Mumbai Business Analytics College',
        'degree' => 'PG Diploma',
        'field' => 'Business Analytics',
        'start_year' => 2021,
        'end_year' => 2022,
        'description' => 'Built skills in SQL, dashboards, statistics, and product analysis.',
    ]);

    insertApplication($pdo, $jobs[0], $users['aarav.mehta@demo.pronetwork'], [
        'first_name' => 'Aarav',
        'last_name' => 'Mehta',
        'phone' => '+91 90000 10001',
        'resume_path' => 'uploads/resumes/aarav-mehta-resume.pdf',
        'cover_letter' => 'I have built MVC features with PHP and MySQL and would like to contribute to your product team.',
        'status' => 'Pending',
    ]);
    insertApplication($pdo, $jobs[1], $users['kabir.khan@demo.pronetwork'], [
        'first_name' => 'Kabir',
        'last_name' => 'Khan',
        'phone' => '+91 90000 10005',
        'resume_path' => 'uploads/resumes/kabir-khan-resume.pdf',
        'cover_letter' => 'My SQL and dashboard experience aligns well with this analytics internship.',
        'status' => 'Reviewed',
    ]);

    // Synchronize company followers to match actual company_followers count
    $pdo->exec("UPDATE companies c SET c.followers = (SELECT COUNT(*) FROM company_followers cf WHERE cf.company_id = c.company_id)");

    // Synchronize job applicant counts to match actual applications count
    $pdo->exec("UPDATE jobs j SET j.applicant_count = (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.job_id)");

    $pdo->commit();
    echo "Seed data inserted successfully.\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "Seed failed: " . $e->getMessage() . "\n");
    exit(1);
}
