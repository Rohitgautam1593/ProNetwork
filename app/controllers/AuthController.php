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
            $email = trim($data['email'] ?? '');
            $password = trim($data['password'] ?? '');
            $role = trim($data['role'] ?? 'Professional');
            $allowedRoles = ['Student', 'Professional', 'Recruiter'];

            if(empty($fullName) || empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
                return;
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Please enter a valid email']);
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
                echo json_encode(['success' => true, 'message' => 'Account created successfully! Redirecting to sign in...']);
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

            $email = trim($data['email'] ?? '');
            $password = trim($data['password'] ?? '');

            if(empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
                return;
            }

            $loggedInUser = $this->userModel->login($email, $password);

            if($loggedInUser) {
                // Create Session
                $this->createUserSession($loggedInUser);
                $redirect = $loggedInUser['role'] === 'Recruiter' ? URLROOT . '/company/dashboard' : URLROOT . '/user/feed';
                if(!empty($loggedInUser['is_admin'])) {
                    $redirect = URLROOT . '/admin/dashboard';
                }
                echo json_encode(['success' => true, 'message' => 'Login successful!', 'user' => $loggedInUser, 'redirect' => $redirect]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Password incorrect']);
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
}
