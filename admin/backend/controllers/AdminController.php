<?php
class AdminController extends Controller {
    private $dashboardModel = null;
    private $adminModel = null;
    private $db = null;

    private function requireAdmin() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/admin/login');
            exit;
        }

        if (!hasRole('Admin')) {
            if (hasRole('Company') || ($_SESSION['role'] ?? '') === 'Company') {
                header('Location: ' . URLROOT . '/company/dashboard');
            } else {
                header('Location: ' . URLROOT . '/user/feed');
            }
            exit;
        }

        if ($this->dashboardModel === null) {
            $this->db = Database::getInstance();
            $this->dashboardModel = $this->model('Dashboard');
            $this->adminModel = $this->model('admin/Admin');
        }
    }

    public function index() {
        if (isLoggedIn() && hasRole('Admin')) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }
        header('Location: ' . URLROOT . '/admin/login');
        exit;
    }

    /**
     * Admin login form + POST handler (no auth required).
     */
    public function login() {
        if (isLoggedIn() && hasRole('Admin')) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            $email = trim($input['email'] ?? '');
            $password = trim($input['password'] ?? '');

            if (empty($email) || empty($password)) {
                $error = 'Please fill in both the administrative email and password.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Provided address does not adhere to standard email structures.';
            } else {
                $db = Database::getInstance();
                $db->query('SELECT * FROM users WHERE email = :email');
                $db->bind(':email', $email);
                $user = $db->single();

                if ($user && password_verify($password, $user['password'])) {
                    if (!empty($user['is_admin']) || ($user['role'] ?? '') === 'Admin') {
                        if (($user['status'] ?? '') === 'Pending') {
                            $error = 'Administrative identity setup review pending execution.';
                        } elseif (($user['status'] ?? '') === 'Rejected') {
                            $error = 'System entry authorization revoked. Please contact core infrastructure personnel.';
                        } else {
                            session_regenerate_id(true);
                            $_SESSION['user_id'] = $user['user_id'];
                            $_SESSION['user_name'] = $user['full_name'];
                            $_SESSION['role'] = $user['role'];
                            $_SESSION['is_admin'] = true;

                            if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                                echo json_encode([
                                    'success' => true,
                                    'redirect' => URLROOT . '/admin/dashboard',
                                ]);
                                exit;
                            }

                            header('Location: ' . URLROOT . '/admin/dashboard');
                            exit;
                        }
                    } else {
                        $error = 'Access Verification Failed: Insufficient administrative command authorization clearance.';
                    }
                } elseif ($user) {
                    $error = 'Invalid credentials provided.';
                } else {
                    $error = 'No administrative identity matched the submitted parameters.';
                }
            }

            if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                echo json_encode([
                    'success' => false,
                    'message' => $error,
                ]);
                exit;
            }
        }

        $this->view('admin/gateway', ['error' => $error]);
    }

    public function dashboard() {
        $this->requireAdmin();
        $stats          = $this->dashboardModel->getAdminStats();
        $recentActivity = $this->adminModel->getCombinedActivity(5);
        $users          = $this->adminModel->getAllUsers();
        $posts          = $this->adminModel->getAllPosts();
        $companies      = $this->adminModel->getAllCompanies();
        $jobs           = $this->adminModel->getAllJobs();

        $this->view('admin/dashboard', [
            'stats'           => $stats,
            'recent_activity' => $recentActivity,
            'users'           => array_slice($users,     0, 5),
            'posts'           => array_slice($posts,     0, 5),
            'companies'       => array_slice($companies, 0, 5),
            'jobs'            => array_slice($jobs,      0, 5),
        ]);
    }

    public function activity_api() {
        $this->requireAdmin();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;
        
        $activities = $this->adminModel->getCombinedActivity($limit, $offset);
        
        $this->json([
            'success' => true,
            'activities' => $activities,
            'has_more' => count($activities) == $limit
        ]);
    }

    public function users() {
        $this->requireAdmin();
        $users = $this->adminModel->getAllUsers();
        $this->view('admin/users', [
            'users' => $users,
            'stats' => $this->dashboardModel->getAdminStats()
        ]);
    }

    public function update_role() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!is_array($data) || empty($data['user_id']) || empty($data['role'])) {
                $this->jsonError('Invalid data.');
                return;
            }

            $this->json(['success' => (bool)$this->adminModel->updateRole($data['user_id'], $data['role'])]);
        }
    }

    public function delete_user($id) {
        $this->requireAdmin();
        // Prevent self-deletion
        if ($id == $_SESSION['user_id']) {
            flash('admin_message', 'You cannot delete your own account.', 'bg-red-100 text-red-600');
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }

        // Fetch user to check if they are an admin
        $this->db->query("SELECT is_admin FROM users WHERE user_id = :id");
        $this->db->bind(':id', $id);
        $user = $this->db->single();

        if ($user && $user['is_admin']) {
            flash('admin_message', 'You cannot delete another administrator.', 'bg-red-100 text-red-600');
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }

        if ($this->adminModel->deleteUser($id)) {
            flash('admin_message', 'User deleted successfully');
            header('Location: ' . URLROOT . '/admin/users');
        }
    }

    public function posts() {
        $this->requireAdmin();
        $posts = $this->adminModel->getAllPosts();
        $this->view('admin/posts', [
            'posts' => $posts,
            'stats' => $this->dashboardModel->getAdminStats()
        ]);
    }

    public function delete_post($id) {
        $this->requireAdmin();
        if ($this->adminModel->deletePost($id)) {
            $this->jsonSuccess();
        } else {
            $this->jsonFailure();
        }
    }

    public function update_user() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!is_array($data) || empty($data['user_id'])) {
                $this->jsonError('Invalid data.');
                return;
            }
            
            // Basic validation
            if (empty($data['full_name']) || empty($data['email']) || empty($data['role'])) {
                $this->jsonError('Please fill in all fields.');
                return;
            }

            // Password validation if provided
            if (!empty($data['password']) && strlen($data['password']) < 6) {
                $this->jsonError('Password must be at least 6 characters.');
                return;
            }

            if ($this->adminModel->updateUser($data)) {
                $this->jsonSuccess();
            } else {
                $this->jsonError('Could not update user.');
            }
        }
    }

    public function stats_api() {
        $this->requireAdmin();
        $stats = $this->dashboardModel->getAdminStats();
        $this->json($stats);
    }

    public function companies() {
        $this->requireAdmin();
        $companies = $this->adminModel->getAllCompanies();
        $this->view('admin/companies', [
            'companies' => $companies,
            'stats' => $this->dashboardModel->getAdminStats()
        ]);
    }

    public function jobs() {
        $this->requireAdmin();

        $jobs = $this->adminModel->getAllJobs();
        $this->view('admin/jobs', [
            'jobs' => $jobs,
            'stats' => $this->dashboardModel->getAdminStats()
        ]);
    }

    public function reports() {
        $this->requireAdmin();
        $reports = $this->adminModel->getReportsWithContext();
        
        $this->view('admin/reports', [
            'reports' => $reports,
            'stats' => $this->dashboardModel->getAdminStats()
        ]);
    }

    public function get_report_details($id) {
        $this->requireAdmin();
        $report = $this->adminModel->getReportById($id);
        if (!$report) {
            $this->jsonFailure();
            return;
        }

        $content = '';
        $title = '';
        $target = [];

        if ($report['target_type'] === 'Post') {
            $this->db->query("SELECT p.*, u.full_name, u.email, u.role, u.profile_pic FROM posts p JOIN users u ON p.user_id = u.user_id WHERE p.post_id = :tid");
            $this->db->bind(':tid', $report['target_id']);
            $post = $this->db->single();
            if ($post) {
                $title = "Post by " . $post['full_name'];
                $content = $post['content'];
                $target = [
                    'Post ID' => $post['post_id'],
                    'Author' => $post['full_name'],
                    'Author Email' => $post['email'],
                    'Author Role' => $post['role'],
                    'Visibility' => $post['visibility'] ?? 'Public',
                    'Created' => date('M d, Y H:i', strtotime($post['created_at']))
                ];
                if ($post['post_image']) {
                    $content .= "\n\n[Image: " . URLROOT . "/uploads/posts/" . $post['post_image'] . "]";
                }
            }
        } elseif ($report['target_type'] === 'Job') {
            $this->db->query("SELECT j.*, c.name as company_name, c.industry as company_industry FROM jobs j JOIN companies c ON j.company_id = c.company_id WHERE j.job_id = :tid");
            $this->db->bind(':tid', $report['target_id']);
            $job = $this->db->single();
            if ($job) {
                $title = $job['title'] . " at " . $job['company_name'];
                $content = $job['description'];
                $target = [
                    'Job ID' => $job['job_id'],
                    'Title' => $job['title'],
                    'Company' => $job['company_name'],
                    'Industry' => $job['company_industry'] ?? '',
                    'Location' => $job['location'] ?? '',
                    'Type' => $job['job_type'] ?? '',
                    'Applicants' => $job['applicant_count'] ?? 0,
                    'Posted' => date('M d, Y H:i', strtotime($job['posted_at']))
                ];
            }
        } elseif ($report['target_type'] === 'User') {
            $this->db->query("SELECT user_id, full_name, email, role, headline, location, industry, bio, status, is_admin, created_at FROM users WHERE user_id = :tid");
            $this->db->bind(':tid', $report['target_id']);
            $user = $this->db->single();
            if ($user) {
                $title = "User: " . $user['full_name'];
                $content = "Email: " . $user['email'] . "\nBio: " . $user['bio'];
                $target = [
                    'User ID' => $user['user_id'],
                    'Name' => $user['full_name'],
                    'Email' => $user['email'],
                    'Role' => !empty($user['is_admin']) ? 'Admin' : $user['role'],
                    'Headline' => $user['headline'] ?? '',
                    'Location' => $user['location'] ?? '',
                    'Industry' => $user['industry'] ?? '',
                    'Status' => $user['status'],
                    'Joined' => date('M d, Y H:i', strtotime($user['created_at']))
                ];
            }
        }

        $relatedReports = $this->adminModel->getReportsForTarget($report['target_type'], $report['target_id']);
        $activeReportCount = 0;
        foreach ($relatedReports as $related) {
            if ($related['status'] === 'Pending') {
                $activeReportCount++;
            }
        }

        $this->json([
            'success' => true,
            'report_id' => (int)$report['report_id'],
            'target_type' => $report['target_type'],
            'target_id' => (int)$report['target_id'],
            'status' => $report['status'],
            'title' => $title,
            'content' => $content,
            'reason' => $report['reason'],
            'reporter_name' => $report['reporter_name'],
            'reporter_email' => $report['reporter_email'],
            'date' => date('M d, Y', strtotime($report['created_at'])),
            'target_url' => $report['target_type'] === 'Post' ? URLROOT . '/admin/posts' : ($report['target_type'] === 'Job' ? URLROOT . '/admin/jobs' : URLROOT . '/admin/users'),
            'target' => $target,
            'active_report_count' => $activeReportCount,
            'total_report_count' => count($relatedReports),
            'related_reports' => $relatedReports,
            'can_delete_target' => in_array($report['target_type'], ['Post', 'Job', 'User'], true)
        ]);
    }

    public function report_action($id, $action) {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonFailure('Invalid request method.');
            return;
        }

        if ($action === 'resolve') {
            $ok = $this->adminModel->resolveReport($id);
        } elseif ($action === 'dismiss') {
            $ok = $this->adminModel->dismissReport($id);
        } elseif ($action === 'delete') {
            $ok = $this->adminModel->deleteReport($id);
        } else {
            $this->jsonFailure('Unknown action.');
            return;
        }

        $this->json(['success' => (bool)$ok]);
    }

    public function target_reports_action($targetType, $targetId, $action) {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonFailure('Invalid request method.');
            return;
        }

        $targetType = ucfirst(strtolower($targetType));
        if (!in_array($targetType, ['Post', 'Job', 'User'], true)) {
            $this->jsonFailure('Invalid target type.');
            return;
        }

        if ($action === 'resolve') {
            $ok = $this->adminModel->resolveTargetReports($targetType, $targetId);
        } elseif ($action === 'dismiss') {
            $ok = $this->adminModel->dismissTargetReports($targetType, $targetId);
        } else {
            $this->jsonFailure('Unknown action.');
            return;
        }

        $this->json(['success' => (bool)$ok]);
    }

    public function delete_company($id) {
        $this->requireAdmin();
        if ($this->adminModel->deleteCompany($id)) {
            $this->jsonSuccess();
        } else {
            $this->jsonFailure();
        }
    }

    public function delete_job($id) {
        $this->requireAdmin();
        if ($this->adminModel->deleteJob($id)) {
            $this->jsonSuccess();
        } else {
            $this->jsonFailure();
        }
    }

    public function toggle_job_status($id) {
        $this->requireAdmin();
        if ($this->adminModel->toggleJobStatus($id)) {
            $this->jsonSuccess();
        } else {
            $this->jsonFailure();
        }
    }

    public function approve_user($id) {
        $this->requireAdmin();
        $user = $this->adminModel->getUserById($id);
        if ($this->adminModel->approveUser($id)) {
            $emailSent = $user ? MailHelper::sendApprovalNotification($user) : false;
            $message = $emailSent
                ? 'User approved successfully and approval email sent'
                : 'User approved successfully, but the approval email could not be sent';
            $class = $emailSent ? 'alert alert-success' : 'bg-yellow-100 text-yellow-700';
            flash('admin_message', $message, $class);
        } else {
            flash('admin_message', 'Could not approve user', 'bg-red-100 text-red-600');
        }
        header('Location: ' . URLROOT . '/admin/users');
    }

    public function reject_user($id) {
        $this->requireAdmin();
        if ($this->adminModel->rejectUser($id)) {
            flash('admin_message', 'User rejected successfully');
        } else {
            flash('admin_message', 'Could not reject user', 'bg-red-100 text-red-600');
        }
        header('Location: ' . URLROOT . '/admin/users');
    }

    public function resolve_report($id) {
        $this->requireAdmin();
        if ($this->adminModel->resolveReport($id)) {
            flash('admin_message', 'Report marked as resolved');
        } else {
            flash('admin_message', 'Could not resolve report', 'bg-red-100 text-red-600');
        }
        header('Location: ' . URLROOT . '/admin/reports');
    }

    public function dismiss_report($id) {
        $this->requireAdmin();
        if ($this->adminModel->dismissReport($id)) {
            flash('admin_message', 'Report dismissed');
        } else {
            flash('admin_message', 'Could not dismiss report', 'bg-red-100 text-red-600');
        }
        header('Location: ' . URLROOT . '/admin/reports');
    }

    public function delete_report($id) {
        $this->requireAdmin();
        if ($this->adminModel->deleteReport($id)) {
            flash('admin_message', 'Report deleted');
        } else {
            flash('admin_message', 'Could not delete report', 'bg-red-100 text-red-600');
        }
        header('Location: ' . URLROOT . '/admin/reports');
    }

    public function add_user() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!is_array($data)) {
                $this->jsonError('Invalid data.');
                return;
            }

            if (empty($data['full_name']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
                $this->jsonError('All fields are required.');
                return;
            }

            if (strlen($data['password']) < 6) {
                $this->jsonError('Password must be at least 6 characters.');
                return;
            }

            // Check if email already exists
            $this->db->query("SELECT user_id FROM users WHERE email = :email");
            $this->db->bind(':email', $data['email']);
            if ($this->db->single()) {
                $this->jsonError('Email already exists.');
                return;
            }

            if ($this->adminModel->addUser($data)) {
                $this->jsonSuccess();
            } else {
                $this->jsonError('Could not add user.');
            }
        }
    }

    public function get_entity_info($type, $id) {
        $this->requireAdmin();
        header('Content-Type: application/json');
        $type = ucfirst(strtolower($type));
        $id = (int)$id;
        $title = '';
        $subtitle = '';
        $content = '';
        $image = '';
        $meta = [];
        $actions = [];

        if ($type === 'User') {
            $this->db->query("SELECT * FROM users WHERE user_id = :id");
            $this->db->bind(':id', $id);
            $user = $this->db->single();
            if ($user) {
                $title = $user['full_name'];
                $subtitle = $user['email'];
                $content = !empty($user['bio']) ? $user['bio'] : 'No bio provided.';
                if (!empty($user['profile_pic'])) {
                    $baseDir = ($user['role'] === 'Company') ? '/uploads/companies/' : '/uploads/profiles/';
                    $image = strpos($user['profile_pic'], 'http') === 0 ? $user['profile_pic'] : URLROOT . $baseDir . $user['profile_pic'];
                }
                $meta = [
                    'User ID' => $user['user_id'],
                    'Platform Role' => !empty($user['is_admin']) ? 'Admin' : $user['role'],
                    'Status' => $user['status'],
                    'Headline' => !empty($user['headline']) ? $user['headline'] : 'N/A',
                    'Location' => !empty($user['location']) ? $user['location'] : 'N/A',
                    'Industry' => !empty($user['industry']) ? $user['industry'] : 'N/A',
                    'Phone' => !empty($user['phone']) ? $user['phone'] : 'N/A',
                    'Website' => !empty($user['website']) ? $user['website'] : 'N/A',
                    'Registered On' => date('M d, Y H:i', strtotime($user['created_at']))
                ];
                $actions = [
                    'manage_url' => URLROOT . '/admin/users',
                    'delete_type' => 'User',
                    'delete_id' => $user['user_id']
                ];
            }
        } elseif ($type === 'Company') {
            $this->db->query("SELECT * FROM companies WHERE company_id = :id");
            $this->db->bind(':id', $id);
            $comp = $this->db->single();
            if ($comp) {
                $title = $comp['name'];
                $subtitle = !empty($comp['industry']) ? $comp['industry'] : 'Company';
                $content = !empty($comp['description']) ? $comp['description'] : 'No detailed company description provided.';
                if (!empty($comp['logo_path'])) {
                    $image = strpos($comp['logo_path'], 'http') === 0 ? $comp['logo_path'] : URLROOT . '/uploads/companies/' . $comp['logo_path'];
                }
                $meta = [
                    'Company ID' => $comp['company_id'],
                    'Industry' => !empty($comp['industry']) ? $comp['industry'] : 'N/A',
                    'Company Size' => !empty($comp['size']) ? $comp['size'] : 'N/A',
                    'Founded Year' => !empty($comp['founded_year']) ? $comp['founded_year'] : 'N/A',
                    'Followers' => isset($comp['followers']) ? (int)$comp['followers'] : 0,
                    'Website' => !empty($comp['website']) ? $comp['website'] : 'N/A',
                    'Created On' => date('M d, Y H:i', strtotime($comp['created_at']))
                ];
                $actions = [
                    'manage_url' => URLROOT . '/admin/companies',
                    'delete_type' => 'Company',
                    'delete_id' => $comp['company_id']
                ];
            }
        } elseif ($type === 'Post') {
            $this->db->query("SELECT p.*, u.full_name, u.email, u.profile_pic,
                              (SELECT COUNT(*) FROM post_reactions pr WHERE pr.post_id = p.post_id) as likes_count,
                              (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id) as comments_count,
                              (SELECT COUNT(*) FROM reports r WHERE r.target_type = 'Post' AND r.target_id = p.post_id AND r.status = 'Pending') as active_report_count
                              FROM posts p JOIN users u ON p.user_id = u.user_id WHERE p.post_id = :id");
            $this->db->bind(':id', $id);
            $post = $this->db->single();
            if ($post) {
                $title = "Shared by " . $post['full_name'];
                $subtitle = $post['email'];
                $content = $post['content'];
                if (!empty($post['post_image'])) {
                    $image = strpos($post['post_image'], 'http') === 0 ? $post['post_image'] : URLROOT . '/uploads/posts/' . $post['post_image'];
                } elseif (!empty($post['profile_pic'])) {
                    $image = strpos($post['profile_pic'], 'http') === 0 ? $post['profile_pic'] : URLROOT . '/uploads/profiles/' . $post['profile_pic'];
                }
                $meta = [
                    'Post ID' => $post['post_id'],
                    'Author Name' => $post['full_name'],
                    'Visibility' => !empty($post['visibility']) ? $post['visibility'] : 'Public',
                    'Like Count' => isset($post['likes_count']) ? $post['likes_count'] : 0,
                    'Comment Count' => isset($post['comments_count']) ? $post['comments_count'] : 0,
                    'Active Reports' => isset($post['active_report_count']) ? (int)$post['active_report_count'] : 0,
                    'Published On' => date('M d, Y H:i', strtotime($post['created_at']))
                ];
                $actions = [
                    'manage_url' => URLROOT . '/admin/posts',
                    'delete_type' => 'Post',
                    'delete_id' => $post['post_id']
                ];
            }
        } elseif ($type === 'Job') {
            $this->db->query("SELECT j.*, c.name as company_name, c.industry, c.logo_path,
                              (SELECT COUNT(*) FROM reports r WHERE r.target_type = 'Job' AND r.target_id = j.job_id AND r.status = 'Pending') as active_report_count
                              FROM jobs j JOIN companies c ON j.company_id = c.company_id WHERE j.job_id = :id");
            $this->db->bind(':id', $id);
            $job = $this->db->single();
            if ($job) {
                $title = $job['title'];
                $subtitle = "At " . $job['company_name'];
                $content = !empty($job['description']) ? $job['description'] : 'No job description provided.';
                if (!empty($job['logo_path'])) {
                    $image = strpos($job['logo_path'], 'http') === 0 ? $job['logo_path'] : URLROOT . '/uploads/companies/' . $job['logo_path'];
                }
                $meta = [
                    'Job ID' => $job['job_id'],
                    'Company Name' => $job['company_name'],
                    'Industry Area' => !empty($job['industry']) ? $job['industry'] : 'N/A',
                    'Work Location' => !empty($job['location']) ? $job['location'] : 'N/A',
                    'Employment Type' => !empty($job['job_type']) ? $job['job_type'] : 'N/A',
                    'Salary Bracket' => !empty($job['salary_range']) ? $job['salary_range'] : 'Undisclosed',
                    'Experience Lvl' => !empty($job['experience_level']) ? $job['experience_level'] : 'N/A',
                    'Applicants' => isset($job['applicant_count']) ? (int)$job['applicant_count'] : 0,
                    'Applicant Limit' => isset($job['applicant_limit']) ? (int)$job['applicant_limit'] : 'No limit',
                    'Active Reports' => isset($job['active_report_count']) ? (int)$job['active_report_count'] : 0,
                    'Status' => !empty($job['status']) ? $job['status'] : 'N/A',
                    'Posted On' => date('M d, Y H:i', strtotime($job['posted_at']))
                ];
                $actions = [
                    'manage_url' => URLROOT . '/admin/jobs',
                    'delete_type' => 'Job',
                    'delete_id' => $job['job_id']
                ];
            }
        }

        if (empty($title)) {
            $this->jsonFailure('Record parameters not matched. Entity may have been expunged.');
            return;
        }

        $this->json([
            'success' => true,
            'type' => $type,
            'id' => $id,
            'title' => $title,
            'subtitle' => $subtitle,
            'content' => $content,
            'image' => $image,
            'meta' => $meta,
            'actions' => $actions
        ]);
    }

    private function json($data) {
        echo json_encode($data);
    }

    private function jsonSuccess() {
        $this->json(['success' => true]);
    }

    private function jsonFailure($message = null) {
        $data = ['success' => false];
        if ($message !== null) {
            $data['message'] = $message;
        }
        $this->json($data);
    }

    private function jsonError($error) {
        $this->json(['success' => false, 'error' => $error]);
    }
}
