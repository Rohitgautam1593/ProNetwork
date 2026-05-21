<?php
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function index() {
        // Landing page
        if(isLoggedIn()) {
            header('Location: ' . URLROOT . '/user/feed');
        }
        $this->view('auth/landing');
    }

    public function register() {
        // Check for POST
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            // Since the old system used JSON inputs via fetch, we can support JSON here
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                $data = $_POST; // Fallback to normal post if no json
            }

            $fullName = trim($data['fullName'] ?? '');
            $email = strtolower(trim($data['email'] ?? ''));
            $password = trim($data['password'] ?? '');
            $role = trim($data['role'] ?? 'Professional');
            $allowedRoles = ['Student', 'Professional', 'Company'];

            if(empty($fullName) || empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
                return;
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Please enter a valid email']);
                return;
            }

            if(strlen($password) < 6) {
                echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
                return;
            }

            if(!in_array($role, $allowedRoles, true)) {
                $role = 'Professional';
            }

            if($this->userModel->findUserByEmail($email)) {
                echo json_encode(['success' => false, 'message' => 'Email already taken']);
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $userData = [
                'full_name' => $fullName,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role,
                'headline' => 'Networking on ProNetwork',
                'location' => 'Global',
                'bio' => "I'm new here! Just joined the ProNetwork community."
            ];

            if($this->userModel->register($userData)) {
                // Send email notification to admin
                MailHelper::sendRegistrationAlert($userData);
                echo json_encode(['success' => true, 'message' => 'Account created successfully! Awaiting administrative approval.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Something went wrong']);
            }
        } else {
            // Load view
            $this->view('auth/register');
        }
    }

    public function login() {
        // Check for POST
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                $data = $_POST;
            }

            $email = strtolower(trim($data['email'] ?? ''));
            $password = trim($data['password'] ?? '');

            if(empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
                return;
            }

            $loggedInUser = $this->userModel->login($email, $password);

            if($loggedInUser) {
                // Prevent Admin login through the regular portal
                if (!empty($loggedInUser['is_admin']) || $loggedInUser['role'] === 'Admin') {
                    echo json_encode(['success' => false, 'message' => 'Access Denied: Administrative accounts must authenticate exclusively via the secure Admin Portal.']);
                    return;
                }

                // Check if user is approved
                if ($loggedInUser['status'] === 'Pending') {
                    echo json_encode(['success' => false, 'message' => 'Your account is awaiting administrative approval.']);
                    return;
                }
                if ($loggedInUser['status'] === 'Rejected') {
                    echo json_encode(['success' => false, 'message' => 'Your account has been rejected. Please contact support.']);
                    return;
                }

                // Create Session
                $this->createUserSession($loggedInUser);
                $redirect = $loggedInUser['role'] === 'Company' ? URLROOT . '/company/dashboard' : URLROOT . '/user/feed';
                echo json_encode(['success' => true, 'message' => 'Login successful!', 'user' => $loggedInUser, 'redirect' => $redirect]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Email or password incorrect']);
            }
        } else {
            // Load view
            $this->view('auth/login');
        }
    }

    public function createUserSession($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['is_admin'] = !empty($user['is_admin']);
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['role']);
        unset($_SESSION['is_admin']);
        session_destroy();
        header('Location: ' . URLROOT . '/auth/login');
    }

    /**
     * API: Send a 6-digit OTP and Token Link for Forgot Password
     */
    public function forgot_password() {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $data = $_POST;
        }

        $email = strtolower(trim($data['email'] ?? ''));
        $portal = trim($data['portal'] ?? 'user'); // user, company, or admin

        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Please enter your email address.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            return;
        }

        $user = $this->userModel->findUserByEmail($email);
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'No registered account found with that email address.']);
            return;
        }

        // Segment security check by portal role
        if ($portal === 'admin') {
            if (empty($user['is_admin']) && ($user['role'] ?? '') !== 'Admin') {
                echo json_encode(['success' => false, 'message' => 'Access Denied: Insufficient authorization level.']);
                return;
            }
        } elseif ($portal === 'company') {
            if ($user['role'] !== 'Company') {
                echo json_encode(['success' => false, 'message' => 'Access Denied: Target email is not registered under corporate registry.']);
                return;
            }
        } else {
            // portal is user
            if (!empty($user['is_admin']) || ($user['role'] ?? '') === 'Admin' || $user['role'] === 'Company') {
                echo json_encode(['success' => false, 'message' => 'Access Denied: Please use the appropriate secure portal.']);
                return;
            }
        }

        try {
            // Generate a secure 6-digit numeric OTP and a 64-character hex token
            $otp = (string)random_int(100000, 999999);
            $token = bin2hex(random_bytes(32));
            $expiresAt = gmdate('Y-m-d H:i:s', time() + 900); // 15 minutes from now, stored in UTC

            if ($this->userModel->createPasswordReset($email, $otp, $token, $expiresAt)) {
                // Construct instant verification link
                $link = URLROOT . '/auth/verify_reset_link?token=' . $token;

                // Dispatch email
                if (MailHelper::sendPasswordResetOTP($email, $user['full_name'], $otp, $link)) {
                    echo json_encode(['success' => true, 'message' => 'A 6-digit security code and quick verification link have been dispatched to your email.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Email dispatch failed. Please contact platform support.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Database transaction failed during code provisioning.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Security token generation failed.']);
        }
    }

    /**
     * View/GET: Verify Direct Link and Render Password Reset Form
     */
    public function verify_reset_link() {
        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            // Since this is a browser view, we redirect with an error message
            header('Location: ' . URLROOT . '/auth/login?error=Invalid reset link');
            return;
        }

        $reset = $this->userModel->verifyToken($token);
        if (!$reset) {
            header('Location: ' . URLROOT . '/auth/login?error=The password reset link has expired or is invalid');
            return;
        }

        // Token is valid! Render a premium, modern password reset page.
        $data = [
            'email' => $reset['email'],
            'otp' => $reset['otp'],
            'token' => $token
        ];
        
        $this->view('auth/reset_password', $data);
    }

    /**
     * API: Verify 6-digit OTP
     */
    public function verify_otp() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $data = $_POST;
        }

        $email = strtolower(trim($data['email'] ?? ''));
        $otp = trim($data['otp'] ?? '');

        if (empty($email) || empty($otp)) {
            echo json_encode(['success' => false, 'message' => 'Please provide both your email and the 6-digit code.']);
            return;
        }

        if ($this->userModel->verifyOTP($email, $otp)) {
            echo json_encode(['success' => true, 'message' => 'Security key accepted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired security key.']);
        }
    }

    /**
     * API: Reset old password to new password
     */
    public function reset_password() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $data = $_POST;
        }

        $email = strtolower(trim($data['email'] ?? ''));
        $otp = trim($data['otp'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($email) || empty($otp) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All parameters are strictly required.']);
            return;
        }

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be a minimum of 6 characters.']);
            return;
        }

        // Re-verify the OTP right before the commit (defense in depth)
        if (!$this->userModel->verifyOTP($email, $otp)) {
            echo json_encode(['success' => false, 'message' => 'Security code validation expired. Please try again.']);
            return;
        }

        $user = $this->userModel->findUserByEmail($email);
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Identity mapping failed.']);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($this->userModel->updatePassword($user['user_id'], $hashedPassword)) {
            // Delete the OTP code to prevent replay attacks
            $this->userModel->deleteOTP($email);

            // Log out any current session
            unset($_SESSION['user_id']);
            unset($_SESSION['user_name']);
            unset($_SESSION['role']);
            unset($_SESSION['is_admin']);

            echo json_encode(['success' => true, 'message' => 'Clearance credentials successfully updated!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Critical failure updating authentication parameters.']);
        }
    }
}
