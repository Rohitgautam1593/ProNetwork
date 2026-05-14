<?php
/**
 * Standalone Enterprise Secure Sign-In Gateway
 * Direct session verification pipeline restricting clearance strictly to Company operator accounts.
 */

// Initialize session scope securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require master platform routing configs and PDO wrapper module
require_once dirname(__DIR__) . '/app/config/config.php';
require_once dirname(__DIR__) . '/app/core/Database.php';
require_once dirname(__DIR__) . '/app/helpers/session_helper.php';

// Bypass login interface if operator session is already verified
if (isLoggedIn() && hasRole('Company')) {
    header('Location: ' . URLROOT . '/company/dashboard');
    exit;
}

$error = '';
$successMessage = '';

// Process submission payload securely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    $email = strtolower(trim($input['email'] ?? ''));
    $password = trim($input['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Please enter both your authorized Operator Email and Secure Clearance Key.';
    } else {
        $db = Database::getInstance();
        $db->query("SELECT * FROM users WHERE email = :email");
        $db->bind(':email', $email);
        $user = $db->single();

        if ($user) {
            // Verify access cryptographically
            if (password_verify($password, $user['password'])) {
                // Ensure identity matches requested Corporate scope clearance
                if ($user['role'] === 'Company' || strpos(strtolower($user['role']), 'company') !== false) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['is_admin'] = ($user['role'] === 'Admin');

                    $successMessage = 'Clearance granted successfully! Routing to management node...';

                    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                        echo json_encode([
                            'success' => true,
                            'message' => $successMessage,
                            'redirect' => URLROOT . '/company/dashboard'
                        ]);
                        exit;
                    }
                } else {
                    $error = 'Access restricted: Account identifier lacks certified Enterprise Workspace authority permissions.';
                }
            } else {
                $error = 'Authentication denied: Invalid security credentials submitted.';
            }
        } else {
            $error = 'Authentication denied: Account profile identity not found inside corporate registry.';
        }
    }

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
    <title>ProNetwork Enterprise — Identity Sign-In</title>
    <!-- Tailwind CSS Engine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts Framework -->
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
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-x-hidden">

    <!-- Ambient floating network light rings -->
    <div class="absolute top-1/4 right-1/3 translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none animate-pulse duration-1000"></div>
    <div class="absolute bottom-1/4 left-1/3 -translate-x-1/2 translate-y-1/2 w-80 h-80 bg-blue-600/10 rounded-full blur-[100px] pointer-events-none"></div>

    <!-- Centered Single-Column Premium Expanded Sign-In Frame -->
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden relative z-10 my-4 animate-in fade-in zoom-in-95 duration-300">
        
        <!-- Integrated Secure Header Block -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 px-8 py-6 text-white relative">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_70%_70%,white_1px,transparent_1px)] bg-[size:16px_16px]"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center font-display font-extrabold text-sm text-white shadow-md shadow-indigo-600/30">
                        PN
                    </div>
                    <div>
                        <span class="font-display font-extrabold text-base tracking-tight text-white block">ProNetwork</span>
                        <span class="text-[9px] text-indigo-300 block uppercase tracking-widest font-black">Enterprise Access</span>
                    </div>
                </div>

                <a href="<?php echo URLROOT; ?>/company/index" class="text-xs text-indigo-300 hover:text-white font-bold bg-white/10 hover:bg-white/20 border border-white/10 px-3 py-1.5 rounded-xl transition-all backdrop-blur-md flex items-center gap-1">
                    <span>Register Company</span>
                    <span class="material-symbols-outlined text-[14px]">domain_add</span>
                </a>
            </div>

            <div class="relative z-10 mt-4">
                <h1 class="text-lg font-display font-bold text-white tracking-tight">Operator Node Authorization</h1>
                <p class="text-xs text-slate-300 font-normal mt-0.5">Enter terminal clearance identifiers to resume telemetry control.</p>
            </div>
        </div>

        <!-- Premium Sign-In Form Fields Stack -->
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

            <form action="<?php echo URLROOT; ?>/company/login" method="POST" class="space-y-4" <?php echo !empty($successMessage) ? 'style="opacity:0.4; pointer-events:none;"' : ''; ?>>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Official Operator Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required placeholder="hr@company.com" 
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 font-medium placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">Security Clearance Key <span class="text-rose-500">*</span></label>
                        <a href="<?php echo URLROOT; ?>/auth/forgot" class="text-[10px] text-indigo-600 hover:underline font-medium">Forgot key?</a>
                    </div>
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 font-medium placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-3 rounded-xl shadow-md transition-all flex items-center justify-center gap-1.5 group">
                        <span>Authorize Terminal Clearance</span>
                        <span class="material-symbols-outlined text-[16px] transform group-hover:translate-x-1 transition-transform">lock_open</span>
                    </button>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-center gap-1 text-[11px] text-slate-500">
                    <span>Not provisioned yet?</span>
                    <a href="<?php echo URLROOT; ?>/company/index" class="text-indigo-600 font-bold hover:underline">Deploy an account node</a>
                </div>

            </form>

        </div>

    </div>

</body>
</html>
