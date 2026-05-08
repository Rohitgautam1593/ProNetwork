<?php
class CompanyController extends Controller {
    private $companyModel;
    private $jobModel;

    public function __construct() {
        if(!isLoggedIn() || !hasRole('Recruiter')) {
            if(!isLoggedIn()) {
                header('Location: ' . URLROOT . '/auth/login');
                exit;
            }
            header('Location: ' . URLROOT . '/user/feed');
            exit;
        }

        $this->companyModel = $this->model('Company');
        $this->jobModel = $this->model('Job');
    }

    public function dashboard() {
        $this->view('company/dashboard', [
            'company' => $this->companyModel->getPrimaryCompany(),
            'jobs' => $this->jobModel->getJobs()
        ]);
    }
}
