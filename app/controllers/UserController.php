<?php
class UserController extends Controller {
    private $userModel;
    private $experienceModel;
    private $educationModel;

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
            $file = $_FILES['profile_pic'];
            $fileName = time() . '_' . $file['name'];
            $uploadDir = 'uploads/profiles/';
            $uploadFile = $uploadDir . $fileName;

            if(move_uploaded_file($file['tmp_name'], $uploadFile)) {
                if($this->userModel->updateProfilePic($_SESSION['user_id'], $fileName)) {
                    echo json_encode(['success' => true, 'fileName' => $fileName]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database update failed']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'File upload failed']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        }
    }

    public function upload_cover() {
        if(!isLoggedIn()) { echo json_encode(['success' => false]); exit; }

        if(isset($_FILES['cover_image'])) {
            $file = $_FILES['cover_image'];
            $fileName = time() . '_' . $file['name'];
            $uploadDir = 'uploads/covers/';
            $uploadFile = $uploadDir . $fileName;

            if(move_uploaded_file($file['tmp_name'], $uploadFile)) {
                if($this->userModel->updateCoverImage($_SESSION['user_id'], $fileName)) {
                    echo json_encode(['success' => true, 'fileName' => $fileName]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database update failed']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'File upload failed']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        }
    }
}
