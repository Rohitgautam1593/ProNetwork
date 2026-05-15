<?php
class PostController extends Controller {
    private $postModel;
    private $notificationModel;
    private const MAX_UPLOAD_BYTES = 10485760;
    private const ALLOWED_MEDIA_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'video/mp4' => 'mp4',
        'video/webm' => 'webm',
        'video/ogg' => 'ogv'
    ];

    public function __construct() {
        $this->postModel = $this->model('Post');
        $this->notificationModel = $this->model('Notification');
    }

    // Endpoint for GET and POST on /post
    public function index() {
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $posts = $this->postModel->getPosts($_SESSION['user_id'] ?? null);
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

        if(isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = $this->storeUploadedMedia($_FILES['media'], 'uploads/posts/');
            if (!$upload['success']) {
                echo json_encode(['success' => false, 'message' => $upload['message']]);
                return;
            }
            $fileName = $upload['fileName'];
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
            $newPost = $this->postModel->getPostById($postId, (int) $_SESSION['user_id']);
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

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
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
                    "$userName liked your post",
                    $post_id,
                    'post',
                    (int)$_SESSION['user_id']
                );
            }

            echo json_encode(['success' => true, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to react']);
        }
    }

    public function report($post_id) {
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

            $reason = trim($data['reason'] ?? 'Inappropriate content');
            if ($reason === '') {
                $reason = 'Inappropriate content';
            }

            $reportData = [
                'reporter_id' => $_SESSION['user_id'],
                'target_id' => $post_id,
                'reason' => $reason
            ];

            if($this->postModel->reportPost($reportData)) {
                echo json_encode(['success' => true, 'message' => 'Report submitted. Admin will review it.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save report to database.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }

    public function delete($post_id) {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        // Only allow deleting own posts
        $post = $this->postModel->getPostById($post_id);
        if (!$post || $post['user_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Not authorized to delete this post.']);
            return;
        }

        $db = Database::getInstance();
        $db->query("DELETE FROM posts WHERE post_id = :id AND user_id = :uid");
        $db->bind(':id', $post_id);
        $db->bind(':uid', $_SESSION['user_id']);
        
        if($db->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete post.']);
        }
    }

    /**
     * GET /post/comments/{id} — list comments
     * POST /post/comments/{id} — add comment (JSON or form: content)
     */
    public function comments($post_id) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $post_id = (int) $post_id;
        if ($post_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid post.']);
            return;
        }

        $post = $this->postModel->getPostById($post_id);
        if (!$post) {
            echo json_encode(['success' => false, 'message' => 'Post not found.']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $comments = $this->postModel->getCommentsForPost($post_id);
            echo json_encode([
                'success' => true,
                'comments' => $comments,
                'count' => count($comments)
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = $_POST;
            }
            $content = isset($input['content']) ? trim((string) $input['content']) : '';
            $len = function_exists('mb_strlen') ? mb_strlen($content) : strlen($content);
            if ($content === '') {
                echo json_encode(['success' => false, 'message' => 'Comment cannot be empty.']);
                return;
            }
            if ($len > 2000) {
                echo json_encode(['success' => false, 'message' => 'Comment must be 2000 characters or less.']);
                return;
            }

            $commentId = $this->postModel->addComment($post_id, (int) $_SESSION['user_id'], $content);
            if (!$commentId) {
                echo json_encode(['success' => false, 'message' => 'Could not save comment.']);
                return;
            }

            $comment = $this->postModel->getCommentById($commentId);
            $count = $this->postModel->getCommentCount($post_id);

            if ((int) $post['user_id'] !== (int) $_SESSION['user_id']) {
                $userName = $_SESSION['user_name'] ?? 'Someone';
                $preview = $len > 100 ? (function_exists('mb_substr') ? mb_substr($content, 0, 100) : substr($content, 0, 100)) . '…' : $content;
                $this->notificationModel->addNotification(
                    (int) $post['user_id'],
                    'Comment',
                    $userName . ' commented: ' . $preview,
                    $post_id,
                    'post',
                    (int)$_SESSION['user_id']
                );
            }

            echo json_encode(['success' => true, 'comment' => $comment, 'count' => $count]);
            return;
        }

        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    }

    public function detail($post_id) {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $post = $this->postModel->getPostById((int) $post_id, (int) ($_SESSION['user_id'] ?? 0));
        if($post) {
            echo json_encode(['success' => true, 'post' => $post]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Post not found']);
        }
    }

    /**
     * GET /post/reactions/{id} — users who reacted (feed modal)
     */
    public function reactions($post_id) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $post_id = (int) $post_id;
        if ($post_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid post']);
            return;
        }
        $reactions = $this->postModel->getReactionsForPost($post_id);
        echo json_encode(['success' => true, 'reactions' => $reactions]);
    }

    /**
     * Single post page (was named "view" but that overrides Controller::view() and fatals on PHP 8+).
     * Route: /post/show/{id}
     */
    public function show($post_id = null) {
        if(!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $post_id = (int) $post_id;
        $post = $this->postModel->getPostById($post_id, (int) ($_SESSION['user_id'] ?? 0));
        if(!$post) {
            header('Location: ' . URLROOT . '/user/feed');
            exit;
        }
        $this->view('users/post_view', ['post' => $post]);
    }

    /**
     * POST /post/deleteComment/{id}
     * Authorized for the comment author OR the parent post owner
     */
    public function deleteComment($comment_id) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            return;
        }

        $comment = $this->postModel->getCommentById($comment_id);
        if (!$comment) {
            echo json_encode(['success' => false, 'message' => 'Comment not found']);
            return;
        }

        $post = $this->postModel->getPostById($comment['post_id']);
        $currentUserId = (int) $_SESSION['user_id'];
        
        // Allowed if user authored the comment OR if user owns the parent post
        $isCommentor = ((int) $comment['user_id'] === $currentUserId);
        $isPostOwner = ($post && (int) $post['user_id'] === $currentUserId);

        if (!$isCommentor && !$isPostOwner) {
            echo json_encode(['success' => false, 'message' => 'Not authorized to delete this comment.']);
            return;
        }

        if ($this->postModel->deleteComment($comment_id)) {
            $count = $this->postModel->getCommentCount($comment['post_id']);
            echo json_encode(['success' => true, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete comment']);
        }
    }

    private function storeUploadedMedia($file, $uploadDir) {
        if($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload failed.'];
        }

        if($file['size'] > self::MAX_UPLOAD_BYTES) {
            return ['success' => false, 'message' => 'File must be 10 MB or smaller.'];
        }

        $mimeType = mime_content_type($file['tmp_name']);
        if(!isset(self::ALLOWED_MEDIA_TYPES[$mimeType])) {
            return ['success' => false, 'message' => 'Unsupported media type.'];
        }

        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . self::ALLOWED_MEDIA_TYPES[$mimeType];
        if(!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            return ['success' => false, 'message' => 'File upload failed.'];
        }

        return ['success' => true, 'fileName' => $fileName];
    }
}
