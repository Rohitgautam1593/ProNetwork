<?php
class AdminController extends Controller {
    private $dashboardModel;
    private $adminModel;
    private $db;

    public function __construct() {
        if(!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if(!hasRole('Admin')) {
            header('Location: ' . URLROOT . '/user/feed');
            exit;
        }

        $this->db = Database::getInstance();
        $this->dashboardModel = $this->model('Dashboard');
        $this->adminModel = $this->model('admin/Admin');
    }

    public function dashboard() {
        $stats = $this->dashboardModel->getAdminStats();
        $recentUsers = $this->adminModel->getAllUsers();
        $recentPosts = $this->adminModel->getAllPosts();

        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recent_users' => array_slice($recentUsers, 0, 3),
            'recent_posts' => array_slice($recentPosts, 0, 3)
        ]);
    }

    public function users() {
        $users = $this->adminModel->getAllUsers();
        $this->view('admin/users', [
            'users' => $users,
            'stats' => $this->dashboardModel->getAdminStats()
        ]);
    }

    public function update_role() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($this->adminModel->updateRole($data['user_id'], $data['role'])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }

    public function delete_user($id) {
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
        $posts = $this->adminModel->getAllPosts();
        $this->view('admin/posts', [
            'posts' => $posts,
            'stats' => $this->dashboardModel->getAdminStats()
        ]);
    }

    public function delete_post($id) {
        if ($this->adminModel->deletePost($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function update_user() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Basic validation
            if (empty($data['full_name']) || empty($data['email'])) {
                echo json_encode(['success' => false, 'error' => 'Please fill in all fields.']);
                return;
            }

            if ($this->adminModel->updateUser($data)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Could not update user.']);
            }
        }
    }

    public function stats_api() {
        $stats = $this->dashboardModel->getAdminStats();
        echo json_encode($stats);
    }

    public function companies() {
        $companies = $this->adminModel->getAllCompanies();
        $this->view('admin/companies', [
            'companies' => $companies,
            'stats' => $this->dashboardModel->getAdminStats()
        ]);
    }

    public function jobs() {
        $jobs = $this->adminModel->getAllJobs();
        $this->view('admin/jobs', [
            'jobs' => $jobs,
            'stats' => $this->dashboardModel->getAdminStats()
        ]);
    }

    public function reports() {
        $this->db->query("SELECT r.*, u.full_name as reporter_name FROM reports r JOIN users u ON r.reporter_id = u.user_id ORDER BY r.created_at DESC");
        $reports = $this->db->resultSet();
        
        $this->view('admin/reports', [
            'reports' => $reports,
            'stats' => $this->dashboardModel->getAdminStats()
        ]);
    }

    public function delete_company($id) {
        if ($this->adminModel->deleteCompany($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function delete_job($id) {
        if ($this->adminModel->deleteJob($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}
