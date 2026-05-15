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
        if (isLoggedIn()) {
            if (hasRole('Company') && $_SESSION['user_name'] === $company['company_name']) {
                $isOwner = true;
            } elseif ($company['user_id'] == $_SESSION['user_id']) {
                $isOwner = true;
            }
        }

        $isFollowing = false;
        if (isLoggedIn() && !$isOwner) {
            $isFollowing = $this->companyModel->isFollowing($id, $_SESSION['user_id']);
        }

        $this->view('company/dashboard', [
            'company' => $company,
            'jobs' => $this->jobModel->getJobsByCompany($id),
            'posts' => $this->postModel->getPosts(),
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

            $companyName = trim($input['company_name'] ?? '');
            $email = strtolower(trim($input['email'] ?? ''));
            $password = trim($input['password'] ?? '');
            $industry = trim($input['industry'] ?? 'Technology & Systems');
            $size = trim($input['size'] ?? '11-50 employees');
            $website = trim($input['website'] ?? '');
            $description = trim($input['description'] ?? '');

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

                $db->query('SELECT user_id FROM users WHERE email = :email');
                $db->bind(':email', $email);
                if ($db->single()) {
                    $error = 'Specified email identifier is already attached to an active ProNetwork account profile.';
                } else {
                    $db->query('SELECT company_id FROM companies WHERE name = :name');
                    $db->bind(':name', $companyName);
                    if ($db->single()) {
                        $error = 'Corporate identity tag already registered within our system network.';
                    } else {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

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

                            $db->query(
                                'INSERT INTO companies (name, industry, description, website, size, founded_year, followers, user_id) 
                                 VALUES (:name, :industry, :description, :website, :size, :founded_year, 1, :user_id)'
                            );
                            $db->bind(':name', $companyName);
                            $db->bind(':industry', $industry);
                            $db->bind(':description', empty($description) ? 'Pioneering sophisticated platform capabilities and unlocking premium enterprise distributed network connections.' : $description);
                            $db->bind(':website', $website);
                            $db->bind(':size', $size);
                            $db->bind(':founded_year', date('Y'));
                            $db->bind(':user_id', $newUserId);
                            $db->execute();

                            session_regenerate_id(true);
                            $_SESSION['user_id'] = $newUserId;
                            $_SESSION['user_name'] = $companyName;
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
                        if ($user['role'] === 'Company' || stripos((string) $user['role'], 'company') !== false) {
                            session_regenerate_id(true);
                            $_SESSION['user_id'] = $user['user_id'];
                            $_SESSION['user_name'] = $user['full_name'];
                            $_SESSION['role'] = $user['role'];
                            $_SESSION['is_admin'] = (($user['role'] ?? '') === 'Admin');

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
            header('Location: ' . URLROOT . '/company');
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
