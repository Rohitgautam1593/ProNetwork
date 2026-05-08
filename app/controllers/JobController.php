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
        $jobs = $this->jobModel->getJobs();
        echo json_encode(['success' => true, 'jobs' => $jobs]);
    }

    public function detail($id) {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $job = $this->jobModel->getJobById($id);
        echo json_encode(['success' => true, 'job' => $job]);
    }
}
