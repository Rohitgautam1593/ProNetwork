<?php
class PostController extends Controller {
    private $postModel;
    private $notificationModel;

    public function __construct() {
        $this->postModel = $this->model('Post');
        $this->notificationModel = $this->model('Notification');
    }

    // Endpoint for GET and POST on /post
    public function index() {
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $posts = $this->postModel->getPosts();
            echo json_encode(['success' => true, 'posts' => $posts]);
        } else if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->create();
        }
    }

    public function create() {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        // Support multipart/form-data for file uploads
        $content = trim($_POST['content'] ?? '');
        $fileName = null;

        if(isset($_FILES['media'])) {
            $file = $_FILES['media'];
            $fileName = time() . '_' . $file['name'];
            $uploadDir = 'uploads/posts/';
            $uploadFile = $uploadDir . $fileName;

            if(move_uploaded_file($file['tmp_name'], $uploadFile)) {
                // Success
            } else {
                $fileName = null;
            }
        }

        if(empty($content) && !$fileName) {
            echo json_encode(['success' => false, 'message' => 'Post content or media cannot be empty.']);
            return;
        }

        $postData = [
            'user_id' => $_SESSION['user_id'],
            'content' => $content,
            'post_image' => $fileName
        ];

        $postId = $this->postModel->addPost($postData);

        if($postId) {
            $newPost = $this->postModel->getPostById($postId);
            echo json_encode(['success' => true, 'message' => 'Post created!', 'post' => $newPost]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save post']);
        }
    }

    public function react($post_id) {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if($this->postModel->addReaction($post_id, $_SESSION['user_id'])) {
            $count = $this->postModel->getReactionCount($post_id);
            
            // Notify post owner
            $post = $this->postModel->getPostById($post_id);
            if ($post && $post['user_id'] != $_SESSION['user_id']) {
                $userName = $_SESSION['user_name'] ?? 'Someone';
                $this->notificationModel->addNotification(
                    $post['user_id'], 
                    'Like', 
                    "$userName liked your post"
                );
            }

            echo json_encode(['success' => true, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to react']);
        }
    }
}
