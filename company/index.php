<?php
/**
 * Standalone Enterprise Onboarding Entry Point
 * Direct database registration framework provisioning secure company roles and immediate profile access.
 */

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require Core Configurations and Database utilities
require_once dirname(__DIR__) . '/app/config/config.php';
require_once dirname(__DIR__) . '/app/core/Database.php';
require_once dirname(__DIR__) . '/app/helpers/session_helper.php';

// Redirect immediately if already active under Company role clearance
if (isLoggedIn() && hasRole('Company')) {
    header('Location: ' . URLROOT . '/company/dashboard');
    exit;
}

$error = '';
$successMessage = '';

// Process secure onboarding POST payload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON API payloads or standard form arrays
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    $companyName = trim($input['company_name'] ?? '');
    $email = strtolower(trim($input['email'] ?? ''));
    $password = trim($input['password'] ?? '');
    $industry = trim($input['industry'] ?? 'Technology & Systems');
    $size = trim($input['size'] ?? '11-50 employees');
    $website = trim($input['website'] ?? '');
    $description = trim($input['description'] ?? '');

    // Default domain string if empty
    if (empty($website)) {
        $website = 'https://' . preg_replace('/[^a-zA-Z0-9]/', '', strtolower($companyName)) . '.pronetwork.demo';
    }

    if (empty($companyName) || empty($email) || empty($password)) {
        $error = 'Please complete all required fields: Company Name, Corporate Email, and Password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please submit a standard, properly structured electronic mail address.';
    } elseif (strlen($password) < 6) {
        $error = 'Access clearance keys must be a minimum length of 6 characters.';
    } else {
        $db = Database::getInstance();

        // Self-healing database schema update to ensure 'role' enum supports 'Company' and legacy records are healed
        try {
            $db->query("ALTER TABLE users MODIFY COLUMN role ENUM('Student','Professional','Company','Recruiter','Admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Professional'");
            $db->execute();
            $db->query("UPDATE users SET role = 'Company' WHERE role = 'Recruiter'");
            $db->execute();
        } catch (Throwable $e) {
            // Safely continue if permissions block dynamic alteration
        }

        // Check if user email address is already locked
        $db->query("SELECT user_id FROM users WHERE email = :email");
        $db->bind(':email', $email);
        if ($db->single()) {
            $error = 'Specified email identifier is already attached to an active ProNetwork account profile.';
        } else {
            // Check if company entity already indexed
            $db->query("SELECT company_id FROM companies WHERE name = :name");
            $db->bind(':name', $companyName);
            if ($db->single()) {
                $error = 'Corporate identity tag already registered within our system network.';
            } else {
                // Execute Secure Cryptographic Password Hashing
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // 1. Provision root user identity mapping to Company role
                $db->query(
                    "INSERT INTO users (full_name, email, password, role, headline, location, industry, bio, status) 
                     VALUES (:full_name, :email, :password, 'Company', :headline, 'Global Headquarters', :industry, :bio, 'Approved')"
                );
                $db->bind(':full_name', $companyName);
                $db->bind(':email', $email);
                $db->bind(':password', $hashedPassword);
                $db->bind(':headline', 'Official Registered Hub &bull; ' . $industry);
                $db->bind(':industry', $industry);
                $db->bind(':bio', 'Verified employer workspace terminal actively managed on ProNetwork.');
                
                if ($db->execute()) {
                    $newUserId = $db->lastInsertId();

                    // 2. Synchronously bind master Company profile object metadata
                    $db->query(
                        "INSERT INTO companies (name, industry, description, website, size, founded_year, followers) 
                         VALUES (:name, :industry, :description, :website, :size, :founded_year, 1)"
                    );
                    $db->bind(':name', $companyName);
                    $db->bind(':industry', $industry);
                    $db->bind(':description', empty($description) ? 'Pioneering sophisticated platform capabilities and unlocking premium enterprise distributed network connections.' : $description);
                    $db->bind(':website', $website);
                    $db->bind(':size', $size);
                    $db->bind(':founded_year', date('Y'));
                    $db->execute();

                    // Instantly validate session bindings to bypass registration wait locks
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $newUserId;
                    $_SESSION['user_name'] = $companyName;
                    $_SESSION['role'] = 'Company';
                    $_SESSION['is_admin'] = false;

                    $successMessage = 'Workspace terminal provisioned successfully! Authorizing routing link...';

                    // Issue clean JSON feedback parameters if client interface requires fetch sync
                    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                        echo json_encode([
                            'success' => true,
                            'message' => $successMessage,
                            'redirect' => URLROOT . '/company/dashboard'
                        ]);
                        exit;
                    }
                } else {
                    $error = 'Critical database transaction failure during identity assignment.';
                }
            }
        }
    }

    // Output raw fetch response error submittals if triggered asynchronously
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        echo json_encode(['success' => false, 'message' => $error]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProNetwork Enterprise — Register Employer Hub</title>
    <!-- Tailwind CSS CDN Engine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter & Manrope -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Google Material Symbols Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Manrope', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-x-hidden">

    <!-- Absolute floating mesh light gradients -->
    <div class="absolute top-1/4 left-1/3 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px] pointer-events-none animate-pulse duration-1000"></div>
    <div class="absolute bottom-1/4 right-1/3 translate-x-1/2 translate-y-1/2 w-80 h-80 bg-indigo-600/10 rounded-full blur-[100px] pointer-events-none"></div>

    <!-- Centered Single-Column Premium Expanded Onboarding Frame -->
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden relative z-10 my-4 animate-in fade-in zoom-in-95 duration-300">
        
        <!-- Integrated Header Block -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 px-8 py-6 text-white relative">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_30%_30%,white_1px,transparent_1px)] bg-[size:16px_16px]"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-display font-extrabold text-sm text-white shadow-md shadow-blue-600/30">
                        PN
                    </div>
                    <div>
                        <span class="font-display font-extrabold text-base tracking-tight text-white block">ProNetwork</span>
                        <span class="text-[9px] text-blue-300 block uppercase tracking-widest font-black">Enterprise Gateway</span>
                    </div>
                </div>

                <a href="<?php echo URLROOT; ?>/company/login.php" class="text-xs text-blue-300 hover:text-white font-bold bg-white/10 hover:bg-white/20 border border-white/10 px-3 py-1.5 rounded-xl transition-all backdrop-blur-md flex items-center gap-1">
                    <span>Company Login</span>
                    <span class="material-symbols-outlined text-[14px]">login</span>
                </a>
            </div>

            <div class="relative z-10 mt-4">
                <h1 class="text-lg font-display font-bold text-white tracking-tight">Deploy Employer Hub</h1>
                <p class="text-xs text-slate-300 font-normal mt-0.5">Provision instant listing commands &amp; telemetry monitoring.</p>
            </div>
        </div>

        <!-- Perfectly Proportioned Multi-Column Form Stack -->
        <div class="px-8 py-6">

            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 bg-rose-50 border border-rose-100 rounded-xl text-xs text-rose-700 font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-rose-600 shrink-0">error</span>
                    <div class="leading-tight"><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($successMessage)): ?>
                <div class="mb-4 p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-xs text-emerald-800 font-bold flex items-center gap-2 shadow-2xs">
                    <span class="material-symbols-outlined text-[16px] text-emerald-600 shrink-0 animate-spin">autorenew</span>
                    <div><?php echo htmlspecialchars($successMessage); ?></div>
                </div>
                <script>
                    setTimeout(() => {
                        window.location.href = "<?php echo URLROOT; ?>/company/dashboard";
                    }, 1400);
                </script>
            <?php endif; ?>

            <form action="<?php echo URLROOT; ?>/company/index" method="POST" class="space-y-3.5" <?php echo !empty($successMessage) ? 'style="opacity:0.4; pointer-events:none;"' : ''; ?>>
                
                <!-- Row 1: Company Name & Operator Email side-by-side -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Firm Registered Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="company_name" value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>" required placeholder="Acme Corp" 
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-medium placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Official Operator Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required placeholder="hr@acme.com" 
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-medium placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Row 2: Secure Password & Industry side-by-side -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Security Key <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="••••••••" 
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-medium placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Industry Area</label>
                        <select name="industry" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all">
                            <option value="Technology &amp; Software">Tech &amp; Software</option>
                            <option value="Financial Services">Finance Services</option>
                            <option value="Healthcare &amp; Life">Health &amp; Sciences</option>
                            <option value="Creative Consulting">Consulting Group</option>
                        </select>
                    </div>
                </div>

                <!-- Row 3: Scale & Pointer Domain side-by-side -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Workforce Scale</label>
                        <select name="size" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all">
                            <option value="1-10 employees">1-10 employees</option>
                            <option value="11-50 employees" selected>11-50 employees</option>
                            <option value="51-200 employees">51-200 employees</option>
                            <option value="200+ employees">200+ scale</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Web Pointer Domain (Optional)</label>
                        <input type="url" name="website" value="<?php echo htmlspecialchars($_POST['website'] ?? ''); ?>" placeholder="https://domain.com" 
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-medium placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 rounded-xl shadow-md transition-all flex items-center justify-center gap-1.5 group">
                        <span>Provision Console Access</span>
                        <span class="material-symbols-outlined text-[16px] transform group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </div>

                <p class="text-[10px] text-slate-400 text-center mt-2 leading-tight">
                    Submittal guarantees operator authenticity credentials.
                </p>

            </form>

        </div>

    </div>

</body>
</html>
