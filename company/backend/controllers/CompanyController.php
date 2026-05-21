<?php
class CompanyController extends Controller {
    private $companyModel;
    private $jobModel;
    private $postModel;
    private const MAX_LOGO_BYTES = 5242880;
    private const ALLOWED_LOGO_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    public function __construct() {
        $this->companyModel = $this->model('Company');
        $this->jobModel = $this->model('Job');
        $this->postModel = $this->model('Post');
    }

    /**
     * Public directory of all company pages.
     */
    public function index() {
        $companies = $this->companyModel->getCompanies();
        $this->view('company/index', [
            'companies' => $companies
        ]);
    }

    /**
     * Show a specific company page
     */
    public function show($id) {
        $company = $this->companyModel->getCompanyById($id);
        if (!$company) {
            header('Location: ' . URLROOT . '/company');
            exit;
        }

        $isOwner = false;
        if (isLoggedIn() && $company['user_id'] == $_SESSION['user_id']) {
            $isOwner = true;
        }

        $isFollowing = false;
        if (isLoggedIn() && !$isOwner) {
            $isFollowing = $this->companyModel->isFollowing($id, $_SESSION['user_id']);
        }

        $this->view('company/dashboard', [
            'company' => $company,
            'jobs' => $this->jobModel->getJobsByCompany($id),
            'posts' => $this->postModel->getPostsByUser($company['user_id'], isLoggedIn() ? $_SESSION['user_id'] : null),
            'isOwner' => $isOwner,
            'isFollowing' => $isFollowing
        ]);
    }

    /**
     * Create a new company page (for regular users)
     */
    public function create() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'industry' => trim($_POST['industry'] ?? ''),
                'size' => trim($_POST['size'] ?? ''),
                'website' => trim($_POST['website'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'user_id' => $_SESSION['user_id']
            ];

            if ($this->companyModel->createCompany($data)) {
                $db = Database::getInstance();
                $newCompanyId = $db->lastInsertId();
                header('Location: ' . URLROOT . '/company/show/' . $newCompanyId);
                exit;
            }
        }

        $this->view('company/create');
    }

    /**
     * Follow a company
     */
    public function follow($id) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($this->companyModel->addFollower($id, $_SESSION['user_id'])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Unfollow a company
     */
    public function unfollow($id) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($this->companyModel->removeFollower($id, $_SESSION['user_id'])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function suggestions() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'companies' => $this->companyModel->getFollowSuggestions($_SESSION['user_id'], 4)
        ]);
        exit;
    }

    /**
     * Employer registration (GET/POST) - Enterprise Accounts
     */
    public function register() {
        if (isLoggedIn() && hasRole('Company')) {
            header('Location: ' . URLROOT . '/company/dashboard');
            exit;
        }

        $error = '';
        $successMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            $fullName = trim($input['full_name'] ?? '');
            $email = strtolower(trim($input['email'] ?? ''));
            $password = trim($input['password'] ?? '');

            if (empty($fullName) || empty($email) || empty($password)) {
                $error = 'Please complete all required fields: Full Name, Corporate Email, and Password.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please submit a standard, properly structured electronic mail address.';
            } elseif (strlen($password) < 6) {
                $error = 'Access clearance keys must be a minimum length of 6 characters.';
            } else {
                $db = Database::getInstance();

                $db->query('SELECT user_id FROM users WHERE email = :email');
                $db->bind(':email', $email);
                if ($db->single()) {
                    $error = 'Specified email identifier is already attached to an active ProNetwork account profile.';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $db->query(
                        "INSERT INTO users (full_name, email, password, role, headline, location, industry, bio, status) 
                         VALUES (:full_name, :email, :password, 'Company', :headline, 'Global Headquarters', 'Enterprise', :bio, 'Approved')"
                    );
                    $db->bind(':full_name', $fullName);
                    $db->bind(':email', $email);
                    $db->bind(':password', $hashedPassword);
                    $db->bind(':headline', 'Enterprise Operator');
                    $db->bind(':bio', 'Verified employer workspace terminal actively managed on ProNetwork.');

                    if ($db->execute()) {
                        $newUserId = $db->lastInsertId();

                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $newUserId;
                        $_SESSION['user_name'] = $fullName;
                        $_SESSION['role'] = 'Company';
                        $_SESSION['is_admin'] = false;

                        $successMessage = 'Workspace terminal provisioned successfully! Authorizing routing link...';

                        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                            echo json_encode([
                                'success' => true,
                                'message' => $successMessage,
                                'redirect' => URLROOT . '/company/dashboard',
                            ]);
                            exit;
                        }
                    } else {
                        $error = 'Critical database transaction failure during identity assignment.';
                    }
                }
            }

            if ($error !== '' && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                echo json_encode(['success' => false, 'message' => $error]);
                exit;
            }
        }

        $this->view('company/register', [
            'error' => $error,
            'successMessage' => $successMessage,
        ]);
    }

    /**
     * Company operator login (GET/POST).
     */
    public function login() {
        if (isLoggedIn() && hasRole('Company')) {
            header('Location: ' . URLROOT . '/company/dashboard');
            exit;
        }

        $error = '';
        $successMessage = '';

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
                $db->query('SELECT * FROM users WHERE email = :email');
                $db->bind(':email', $email);
                $user = $db->single();

                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        // Prevent Admin login through the Company operator portal
                        if (!empty($user['is_admin']) || ($user['role'] ?? '') === 'Admin') {
                            $error = 'Access Denied: Administrative accounts must authenticate exclusively via the secure Admin Portal.';
                        } elseif ($user['role'] === 'Company' || stripos((string) $user['role'], 'company') !== false) {
                            session_regenerate_id(true);
                            $_SESSION['user_id'] = $user['user_id'];
                            $_SESSION['user_name'] = $user['full_name'];
                            $_SESSION['role'] = $user['role'];
                            $_SESSION['is_admin'] = false; // strictly ensure false here for company workspace

                            $successMessage = 'Clearance granted successfully! Routing to management node...';

                            if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                                echo json_encode([
                                    'success' => true,
                                    'message' => $successMessage,
                                    'redirect' => URLROOT . '/company/dashboard',
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

            if ($error !== '' && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                echo json_encode(['success' => false, 'message' => $error]);
                exit;
            }
        }

        $this->view('company/login', [
            'error' => $error,
            'successMessage' => $successMessage,
        ]);
    }

    public function dashboard() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/company/login');
            exit;
        }

        $company = $this->companyModel->getCompanyForUser($_SESSION['user_id']);
        if (!$company) {
            header('Location: ' . URLROOT . '/company/create');
            exit;
        }
        
        // Redirect to standard show method for consistency
        header('Location: ' . URLROOT . '/company/show/' . $company['company_id']);
        exit;
    }

    public function update_profile() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/company/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $company = $this->companyModel->getCompanyForUser($_SESSION['user_id']);
            if ($company) {
                $data = $_POST;
                if (!empty($_FILES['logo']['name'])) {
                    $upload = $this->storeCompanyLogo($_FILES['logo']);
                    if ($upload['success']) {
                        $data['logo_path'] = $upload['fileName'];
                        // Sync with users table
                        $db = Database::getInstance();
                        $db->query('UPDATE users SET profile_pic = :pic WHERE user_id = :uid');
                        $db->bind(':pic', $upload['fileName']);
                        $db->bind(':uid', $_SESSION['user_id']);
                        $db->execute();
                    }
                }
                $this->companyModel->updateCompanyDetails($company['company_id'], $data);
                header('Location: ' . URLROOT . '/company/show/' . $company['company_id']);
                exit;
            }
        }
        header('Location: ' . URLROOT . '/company/dashboard');
    }

    public function update_job($job_id) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? 'Live';
            $limit = $_POST['applicant_limit'] ?? '';
            $this->jobModel->updateJobSettings($job_id, $status, $limit);
            echo json_encode(['success' => true]);
            exit;
        }
        echo json_encode(['success' => false]);
    }

    public function get_applicants($job_id) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $applicants = $this->jobModel->getApplicantsForJob($job_id);
        echo json_encode(['success' => true, 'applicants' => $applicants]);
        exit;
    }

    public function add_job() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/company/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $company = $this->companyModel->getCompanyForUser($_SESSION['user_id']);
            if ($company) {
                $_POST['company_id'] = $company['company_id'];
                $this->jobModel->addJob($_POST);
                header('Location: ' . URLROOT . '/company/show/' . $company['company_id']);
                exit;
            }
        }
        header('Location: ' . URLROOT . '/company/dashboard');
    }

    private function storeCompanyLogo($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Logo upload failed.'];
        }

        if ($file['size'] > self::MAX_LOGO_BYTES) {
            return ['success' => false, 'message' => 'Logo must be 5 MB or smaller.'];
        }

        $mimeType = mime_content_type($file['tmp_name']);
        if (!isset(self::ALLOWED_LOGO_TYPES[$mimeType])) {
            return ['success' => false, 'message' => 'Unsupported logo type.'];
        }

        $uploadDir = 'uploads/companies/logos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = time() . '_company_' . bin2hex(random_bytes(8)) . '.' . self::ALLOWED_LOGO_TYPES[$mimeType];
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            return ['success' => false, 'message' => 'Logo upload failed.'];
        }

        return ['success' => true, 'fileName' => 'logos/' . $fileName];
    }
}
