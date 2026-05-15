<?php
class UserController extends Controller {
    private $userModel;
    private $experienceModel;
    private $educationModel;
    private $settingsModel;
    private const MAX_IMAGE_BYTES = 5242880;
    private const ALLOWED_IMAGE_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    public function __construct() {
        $this->userModel = $this->model('User');
        $this->experienceModel = $this->model('Experience');
        $this->educationModel = $this->model('Education');
        $this->settingsModel = $this->model('UserSettings');
    }

    public function feed() {
        if(!isLoggedIn()) { header('Location: ' . URLROOT . '/auth/login'); exit; }
        $postModel = $this->model('Post');
        $trending = $postModel->getDynamicTrendingTopics();
        $recents = $postModel->getDynamicRecents();
        $this->view('users/feed', [
            'trending' => $trending,
            'recents' => $recents
        ]);
    }

    public function profile() {
        if(!isLoggedIn()) { header('Location: ' . URLROOT . '/auth/login'); exit; }
        $this->view('users/profile');
    }

    public function network() {
        if(!isLoggedIn()) { header('Location: ' . URLROOT . '/auth/login'); exit; }
        $this->view('users/network');
    }

    public function messaging() {
        if(!isLoggedIn()) { header('Location: ' . URLROOT . '/auth/login'); exit; }
        $this->view('users/messaging');
    }

    public function jobs() {
        if(!isLoggedIn()) { header('Location: ' . URLROOT . '/auth/login'); exit; }
        $this->view('users/jobs');
    }

    public function notifications() {
        if(!isLoggedIn()) { header('Location: ' . URLROOT . '/auth/login'); exit; }
        $this->view('users/notifications');
    }

    public function settings() {
        if(!isLoggedIn()) { header('Location: ' . URLROOT . '/auth/login'); exit; }
        $this->view('users/settings');
    }

    public function settings_data() {
        if(!isLoggedIn()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
        $userId = (int) $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        $settings = $this->settingsModel->getSettings($userId);
        echo json_encode([
            'success' => true,
            'user' => $user,
            'settings' => $settings,
            'session' => [
                'started' => session_id() ? true : false,
                'role' => $_SESSION['role'] ?? 'Professional'
            ]
        ]);
    }

    public function save_settings() {
        if(!isLoggedIn()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Invalid settings data']);
            return;
        }
        $this->settingsModel->saveSettings((int) $_SESSION['user_id'], $data);
        echo json_encode([
            'success' => true,
            'message' => 'Settings saved',
            'settings' => $this->settingsModel->getSettings((int) $_SESSION['user_id'])
        ]);
    }

    public function update_email() {
        if(!isLoggedIn()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        $email = strtolower(trim($data['email'] ?? ''));
        $password = trim($data['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Enter a valid email address']);
            return;
        }
        if (!$this->userModel->verifyPassword($_SESSION['user_id'], $password)) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            return;
        }
        if ($this->userModel->findUserByEmailExceptId($email, $_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'That email is already in use']);
            return;
        }

        if ($this->userModel->updateEmail($_SESSION['user_id'], $email)) {
            echo json_encode(['success' => true, 'message' => 'Email updated', 'user' => $this->userModel->getUserById($_SESSION['user_id'])]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update email']);
        }
    }

    public function change_password() {
        if(!isLoggedIn()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        $current = trim($data['current_password'] ?? '');
        $next = trim($data['new_password'] ?? '');

        if (strlen($next) < 6) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
            return;
        }
        if (!$this->userModel->verifyPassword($_SESSION['user_id'], $current)) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            return;
        }

        if ($this->userModel->updatePassword($_SESSION['user_id'], password_hash($next, PASSWORD_DEFAULT))) {
            echo json_encode(['success' => true, 'message' => 'Password changed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to change password']);
        }
    }

    public function export_data() {
        if(!isLoggedIn()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
        $userId = (int) $_SESSION['user_id'];
        $networkModel = $this->model('Network');
        $postModel = $this->model('Post');
        $payload = [
            'exported_at' => date('c'),
            'profile' => $this->userModel->getUserById($userId),
            'settings' => $this->settingsModel->getSettings($userId),
            'experience' => $this->experienceModel->getExperienceByUserId($userId),
            'education' => $this->educationModel->getEducationByUserId($userId),
            'connections' => $networkModel->getConnections($userId),
            'posts' => array_values(array_filter($postModel->getPosts($userId), function ($post) use ($userId) {
                return empty($post['is_company_activity']) && (int)($post['user_id'] ?? 0) === $userId;
            }))
        ];

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="pronetwork-data-' . $userId . '.json"');
        echo json_encode($payload, JSON_PRETTY_PRINT);
    }

    public function me() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        echo json_encode(['success' => true, 'user' => $user]);
    }

    public function data($id) {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }
        echo json_encode([
            'success' => true,
            'user' => $user,
            'settings' => $this->settingsModel->getSettings((int) $id)
        ]);
    }

    public function experience() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $userId = isset($_GET['id']) ? (int) $_GET['id'] : (int) $_SESSION['user_id'];
        $experience = $this->experienceModel->getExperienceByUserId($userId);
        echo json_encode(['success' => true, 'data' => $experience]);
    }

    public function education() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $userId = isset($_GET['id']) ? (int) $_GET['id'] : (int) $_SESSION['user_id'];
        $education = $this->educationModel->getEducationByUserId($userId);
        echo json_encode(['success' => true, 'data' => $education]);
    }
    public function update() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) $data = $_POST;
        $currentUser = $this->userModel->getUserById($_SESSION['user_id']);

        $updateData = [
            'full_name' => trim($data['fullName'] ?? $data['full_name'] ?? $currentUser['full_name'] ?? ''),
            'headline' => trim($data['headline'] ?? $currentUser['headline'] ?? ''),
            'location' => trim($data['location'] ?? $currentUser['location'] ?? ''),
            'industry' => trim($data['industry'] ?? $currentUser['industry'] ?? ''),
            'bio' => trim($data['bio'] ?? $currentUser['bio'] ?? ''),
            'phone' => trim($data['phone'] ?? $currentUser['phone'] ?? ''),
            'website' => trim($data['website'] ?? $currentUser['website'] ?? '')
        ];

        if($this->userModel->updateProfile($_SESSION['user_id'], $updateData)) {
            $updatedUser = $this->userModel->getUserById($_SESSION['user_id']);
            echo json_encode(['success' => true, 'message' => 'Profile updated!', 'user' => $updatedUser]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
    }

    public function update_experience() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || empty($data['exp_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }
        if (empty($data['job_title']) || empty($data['company']) || empty($data['start_date'])) {
            echo json_encode(['success' => false, 'message' => 'Job title, company, and start date are required']);
            return;
        }
        if (!$this->experienceModel->getExperienceByIdForUser((int)$data['exp_id'], $_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Experience not found']);
            return;
        }
        if($this->experienceModel->updateExperience((int)$data['exp_id'], $_SESSION['user_id'], $data)) {
            echo json_encode(['success' => true, 'message' => 'Experience updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update experience']);
        }
    }

    public function delete_experience() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        $expId = (int)($data['exp_id'] ?? 0);
        if (!$expId) {
            echo json_encode(['success' => false, 'message' => 'Invalid experience']);
            return;
        }
        if($this->experienceModel->deleteExperience($expId, $_SESSION['user_id'])) {
            echo json_encode(['success' => true, 'message' => 'Experience deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete experience']);
        }
    }

    public function add_experience() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }
        if (empty($data['job_title']) || empty($data['company']) || empty($data['start_date'])) {
            echo json_encode(['success' => false, 'message' => 'Job title, company, and start date are required']);
            return;
        }
        $data['user_id'] = $_SESSION['user_id'];
        if($this->experienceModel->addExperience($data)) {
            echo json_encode(['success' => true, 'message' => 'Experience added!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add experience']);
        }
    }

    public function update_education() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || empty($data['edu_id']) || empty($data['institution'])) {
            echo json_encode(['success' => false, 'message' => 'Institution is required']);
            return;
        }
        if (!$this->educationModel->getEducationByIdForUser((int)$data['edu_id'], $_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Education not found']);
            return;
        }
        if($this->educationModel->updateEducation((int)$data['edu_id'], $_SESSION['user_id'], $data)) {
            echo json_encode(['success' => true, 'message' => 'Education updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update education']);
        }
    }

    public function delete_education() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        $eduId = (int)($data['edu_id'] ?? 0);
        if (!$eduId) {
            echo json_encode(['success' => false, 'message' => 'Invalid education']);
            return;
        }
        if($this->educationModel->deleteEducation($eduId, $_SESSION['user_id'])) {
            echo json_encode(['success' => true, 'message' => 'Education deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete education']);
        }
    }

    public function add_education() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }
        if (empty($data['institution'])) {
            echo json_encode(['success' => false, 'message' => 'Institution is required']);
            return;
        }
        $data['user_id'] = $_SESSION['user_id'];
        if($this->educationModel->addEducation($data)) {
            echo json_encode(['success' => true, 'message' => 'Education added!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add education']);
        }
    }
    public function upload_pic() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }

        if(isset($_FILES['profile_pic'])) {
            $upload = $this->storeUploadedImage($_FILES['profile_pic'], 'uploads/profiles/');
            if($upload['success']) {
                if($this->userModel->updateProfilePic($_SESSION['user_id'], $upload['fileName'])) {
                    echo json_encode(['success' => true, 'fileName' => $upload['fileName']]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database update failed']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => $upload['message']]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        }
    }

    public function upload_cover() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }

        if(isset($_FILES['cover_image'])) {
            $upload = $this->storeUploadedImage($_FILES['cover_image'], 'uploads/covers/');
            if($upload['success']) {
                if($this->userModel->updateCoverImage($_SESSION['user_id'], $upload['fileName'])) {
                    echo json_encode(['success' => true, 'fileName' => $upload['fileName']]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database update failed']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => $upload['message']]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        }
    }

    public function report($id) {
        if(!isLoggedIn()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $targetId = (int) $id;
        if ($targetId <= 0 || $targetId === (int) $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Invalid profile report']);
            return;
        }

        $target = $this->userModel->getUserById($targetId);
        if (!$target) {
            echo json_encode(['success' => false, 'message' => 'Profile not found']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $reason = trim($data['reason'] ?? 'Profile concern');
        if ($reason === '') {
            $reason = 'Profile concern';
        }

        if($this->userModel->reportUser([
            'reporter_id' => $_SESSION['user_id'],
            'target_id' => $targetId,
            'reason' => $reason
        ])) {
            echo json_encode(['success' => true, 'message' => 'Profile report submitted. Admin will review it.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save report']);
        }
    }

    private function storeUploadedImage($file, $uploadDir) {
        if($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload failed.'];
        }

        if($file['size'] > self::MAX_IMAGE_BYTES) {
            return ['success' => false, 'message' => 'Image must be 5 MB or smaller.'];
        }

        $mimeType = mime_content_type($file['tmp_name']);
        if(!isset(self::ALLOWED_IMAGE_TYPES[$mimeType])) {
            return ['success' => false, 'message' => 'Unsupported image type.'];
        }

        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . self::ALLOWED_IMAGE_TYPES[$mimeType];
        if(!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            return ['success' => false, 'message' => 'File upload failed.'];
        }

        return ['success' => true, 'fileName' => $fileName];
    }
}
