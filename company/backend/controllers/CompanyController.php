<?php
class CompanyController extends Controller {
    private $companyModel;
    private $jobModel;
    private $postModel;

    public function __construct() {
        // Initialize underlying model interfaces securely
        $this->companyModel = $this->model('Company');
        $this->jobModel = $this->model('Job');
        $this->postModel = $this->model('Post');
    }

    /**
     * Default Controller Entry Method
     * Routes active firm identities to dashboard console or directs new requests to standalone setup gateway.
     */
    public function index() {
        if (isLoggedIn() && hasRole('Company')) {
            header('Location: ' . URLROOT . '/company/dashboard');
            exit;
        }
        // Process directly inline to circumvent rewrite cyclic routing limits
        require_once dirname(dirname(__DIR__)) . '/index.php';
        exit;
    }

    /**
     * Fallback Login Interceptor Method
     * Ensures clean routing targeting the standalone login sequence script.
     */
    public function login() {
        if (isLoggedIn() && hasRole('Company')) {
            header('Location: ' . URLROOT . '/company/dashboard');
            exit;
        }
        // Process directly inline to circumvent rewrite cyclic routing limits
        require_once dirname(dirname(__DIR__)) . '/login.php';
        exit;
    }

    public function dashboard() {
        // Enforce clearance authentication locks
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/company/login.php');
            exit;
        }

        $company = $this->companyModel->getPrimaryCompany();
        $companyId = $company ? $company['company_id'] : 1;
        
        $this->view('company/dashboard', [
            'company' => $company,
            'jobs' => $this->jobModel->getJobsByCompany($companyId),
            'posts' => $this->postModel->getPosts(),
            'isOwner' => hasRole('Company')
        ]);
    }

    public function update_profile() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/company/login.php');
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
            header('Location: ' . URLROOT . '/company/login.php');
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
