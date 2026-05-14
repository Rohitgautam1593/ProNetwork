<?php
class UserController extends Controller {
    private $userModel;
    private $experienceModel;
    private $educationModel;
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
    }

    public function feed() {
        if(!isLoggedIn()) { header('Location: ' . URLROOT . '/auth/login'); exit; }
        $this->view('users/feed');
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

    public function me() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        echo json_encode(['success' => true, 'user' => $user]);
    }

    public function data($id) {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $user = $this->userModel->getUserById($id);
        echo json_encode(['success' => true, 'user' => $user]);
    }

    public function experience() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $experience = $this->experienceModel->getExperienceByUserId($_SESSION['user_id']);
        echo json_encode(['success' => true, 'data' => $experience]);
    }

    public function education() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }
        $education = $this->educationModel->getEducationByUserId($_SESSION['user_id']);
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
            'bio' => trim($data['bio'] ?? $currentUser['bio'] ?? '')
        ];

        if($this->userModel->updateProfile($_SESSION['user_id'], $updateData)) {
            $updatedUser = $this->userModel->getUserById($_SESSION['user_id']);
            echo json_encode(['success' => true, 'message' => 'Profile updated!', 'user' => $updatedUser]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
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
