<?php
class JobController extends Controller {
    private $jobModel;

    public function __construct() {
        $this->jobModel = $this->model('Job');
    }

    public function index() {
        if(!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $this->view('users/jobs');
    }

    public function fetch() {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $filters = [];
        $q = trim($_GET['q'] ?? '');
        if ($q !== '') {
            $filters['q'] = $q;
        }
        $jobType = trim($_GET['job_type'] ?? '');
        if ($jobType !== '') {
            $filters['job_type'] = $jobType;
        }
        if (!empty($_GET['with_salary'])) {
            $filters['with_salary'] = true;
        }
        if (!empty($_GET['applied_only'])) {
            $filters['applied_user_id'] = $_SESSION['user_id'];
        }
        if (!empty($_GET['ids'])) {
            $ids = array_filter(array_map('intval', explode(',', $_GET['ids'])));
            if ($ids) {
                $filters['job_ids'] = $ids;
            }
        }

        $jobs = $this->jobModel->getJobs($filters);
        $userId = $_SESSION['user_id'];
        foreach ($jobs as &$job) {
            $job['has_applied'] = $this->jobModel->hasApplied($job['job_id'], $userId);
        }
        unset($job);

        echo json_encode(['success' => true, 'jobs' => $jobs, 'count' => count($jobs)]);
    }

    public function applications() {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $apps = $this->jobModel->getMyApplications($_SESSION['user_id']);
        echo json_encode(['success' => true, 'applications' => $apps]);
    }

    public function detail($id) {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $job = $this->jobModel->getJobById($id);
        if(!$job) {
            echo json_encode(['success' => false, 'message' => 'Job not found']);
            return;
        }
        $job['has_applied'] = $this->jobModel->hasApplied($id, $_SESSION['user_id']);
        echo json_encode(['success' => true, 'job' => $job]);
    }

    public function apply($job_id) {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $job = $this->jobModel->getJobById($job_id);
            if(!$job) {
                echo json_encode(['success' => false, 'message' => 'Job listing not found.']);
                return;
            }

            if($job['status'] === 'Closed') {
                echo json_encode(['success' => false, 'message' => 'This job listing is closed and no longer accepting applications.']);
                return;
            }

            if(!empty($job['applicant_limit']) && (int)$job['applicant_count'] >= (int)$job['applicant_limit']) {
                echo json_encode(['success' => false, 'message' => 'Application limit reached. This position is full.']);
                return;
            }

            if($this->jobModel->hasApplied($job_id, $_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'You have already submitted an application for this job.']);
                return;
            }

            // Support multipart form submission or JSON payload
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $coverLetter = trim($_POST['cover_letter'] ?? '');
            $resumePath = '';

            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            if ($data) {
                $firstName = trim($data['first_name'] ?? $firstName);
                $lastName = trim($data['last_name'] ?? $lastName);
                $phone = trim($data['phone'] ?? $phone);
                $coverLetter = trim($data['cover_letter'] ?? $coverLetter);
                $resumePath = trim($data['resume_path'] ?? '');
            }

            if (empty($firstName)) {
                $parts = explode(' ', $_SESSION['user_name'] ?? 'Applicant');
                $firstName = $parts[0];
                $lastName = $parts[1] ?? 'User';
            }

            // Handle file upload if present
            if(isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(APPROOT) . '/public/uploads/resumes/';
                if(!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
                $filename = 'resume_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                if(move_uploaded_file($_FILES['resume']['tmp_name'], $uploadDir . $filename)) {
                    $resumePath = 'uploads/resumes/' . $filename;
                }
            }

            if(empty($resumePath)) {
                $resumePath = 'uploads/resumes/default_resume.pdf'; // fallback if no file attached
            }

            $applyData = [
                'job_id' => $job_id,
                'user_id' => $_SESSION['user_id'],
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'resume_path' => $resumePath,
                'cover_letter' => $coverLetter
            ];

            if($this->jobModel->applyJob($applyData)) {
                $this->jobModel->incrementApplicantCount($job_id);
                $this->jobModel->checkAndCloseJobLimit($job_id);
                echo json_encode(['success' => true, 'message' => 'Application submitted successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Could not submit application.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }

    public function report($job_id) {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Invalid data format.']);
                return;
            }

            $reason = trim($data['reason'] ?? 'Inappropriate job listing');
            if ($reason === '') {
                $reason = 'Inappropriate job listing';
            }

            $reportData = [
                'reporter_id' => $_SESSION['user_id'],
                'target_id' => $job_id,
                'reason' => $reason
            ];

            if($this->jobModel->reportJob($reportData)) {
                echo json_encode(['success' => true, 'message' => 'Job report submitted.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save job report to database.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
}
