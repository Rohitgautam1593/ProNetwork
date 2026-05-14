<?php
class CompanyController extends Controller {
    private $companyModel;
    private $jobModel;
    private $postModel;

    public function __construct() {
        $this->companyModel = $this->model('Company');
        $this->jobModel = $this->model('Job');
        $this->postModel = $this->model('Post');
    }

    /**
     * Employer registration (GET/POST).
     */
    public function index() {
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
                                'INSERT INTO companies (name, industry, description, website, size, founded_year, followers) 
                                 VALUES (:name, :industry, :description, :website, :size, :founded_year, 1)'
                            );
                            $db->bind(':name', $companyName);
                            $db->bind(':industry', $industry);
                            $db->bind(':description', empty($description) ? 'Pioneering sophisticated platform capabilities and unlocking premium enterprise distributed network connections.' : $description);
                            $db->bind(':website', $website);
                            $db->bind(':size', $size);
                            $db->bind(':founded_year', date('Y'));
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

        $company = $this->companyModel->getPrimaryCompany();
        $companyId = $company ? $company['company_id'] : 1;

        $this->view('company/dashboard', [
            'company' => $company,
            'jobs' => $this->jobModel->getJobsByCompany($companyId),
            'posts' => $this->postModel->getPosts(),
            'isOwner' => hasRole('Company'),
        ]);
    }

    public function update_profile() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/company/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && hasRole('Company')) {
            $company = $this->companyModel->getPrimaryCompany();
            if ($company) {
                $this->companyModel->updateCompanyDetails($company['company_id'], $_POST);
            }
            header('Location: ' . URLROOT . '/company/dashboard');
            exit;
        }
        header('Location: ' . URLROOT . '/company/dashboard');
    }

    public function update_job($job_id) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && hasRole('Company')) {
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

        if (hasRole('Company')) {
            $applicants = $this->jobModel->getApplicantsForJob($job_id);
            echo json_encode(['success' => true, 'applicants' => $applicants]);
            exit;
        }
        echo json_encode(['success' => false]);
    }

    public function add_job() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/company/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && hasRole('Company')) {
            $company = $this->companyModel->getPrimaryCompany();
            if ($company) {
                $_POST['company_id'] = $company['company_id'];
                $this->jobModel->addJob($_POST);
            }
            header('Location: ' . URLROOT . '/company/dashboard');
            exit;
        }
        header('Location: ' . URLROOT . '/company/dashboard');
    }
}
