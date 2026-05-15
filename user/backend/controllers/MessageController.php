<?php
class MessageController extends Controller {
    private $messageModel;
    private $networkModel;

    private const MAX_MEDIA_BYTES = 15728640; // 15MB
    private const ALLOWED_MEDIA = [
        'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp',
        'video/mp4' => 'mp4', 'video/webm' => 'webm',
        'audio/mpeg' => 'mp3', 'audio/wav' => 'wav', 'audio/ogg' => 'ogg',
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
    ];

    public function __construct() {
        $this->messageModel = $this->model('Message');
        $this->networkModel = $this->model('Network');
    }

    public function index() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $this->view('users/messaging');
    }

    public function conversations() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $conversations = $this->messageModel->getConversations($_SESSION['user_id']);
        echo json_encode(['success' => true, 'conversations' => $conversations]);
    }

    public function history($id = null) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'User ID missing']);
            return;
        }
        $otherId = (int)$id;
        $status = $this->messageModel->getBlockStatus($_SESSION['user_id'], $otherId);
        $messages = $status['blocked']
            ? []
            : $this->messageModel->getChatHistory($_SESSION['user_id'], $otherId);
        if (!$status['blocked']) {
            $this->messageModel->markThreadRead($_SESSION['user_id'], $otherId);
        }
        echo json_encode([
            'success' => true,
            'messages' => $messages,
            'block_status' => $status
        ]);
    }

    public function poll($id = null) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        if (!$id) {
            echo json_encode(['success' => false]);
            return;
        }
        $otherId = (int)$id;
        $since = trim($_GET['since'] ?? '');
        $status = $this->messageModel->getBlockStatus($_SESSION['user_id'], $otherId);
        if ($status['blocked']) {
            echo json_encode(['success' => true, 'messages' => [], 'block_status' => $status]);
            return;
        }
        $messages = $since
            ? $this->messageModel->getMessagesSince($_SESSION['user_id'], $otherId, $since)
            : $this->messageModel->getChatHistory($_SESSION['user_id'], $otherId);
        if (!empty($messages)) {
            $this->messageModel->markThreadRead($_SESSION['user_id'], $otherId);
        }
        echo json_encode(['success' => true, 'messages' => $messages, 'block_status' => $status]);
    }

    public function status($id = null) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        if (!$id) {
            echo json_encode(['success' => false]);
            return;
        }
        echo json_encode([
            'success' => true,
            'block_status' => $this->messageModel->getBlockStatus($_SESSION['user_id'], (int)$id)
        ]);
    }

    public function send() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $receiver_id = null;
        $content = '';
        $media_path = null;
        $gif_url = null;

        if (isset($_POST['receiver_id'])) {
            $receiver_id = (int)$_POST['receiver_id'];
            $content = trim($_POST['text'] ?? '');
            $gif_url = trim($_POST['gif_url'] ?? '');

            if ($gif_url !== '' && filter_var($gif_url, FILTER_VALIDATE_URL)) {
                $media_path = 'gif:' . $gif_url;
                if ($content === '') {
                    $content = ' ';
                }
            } elseif (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->handleMediaUpload($_FILES['media']);
                if ($uploaded === false) {
                    echo json_encode(['success' => false, 'message' => 'Invalid or unsupported file (max 15MB).']);
                    return;
                }
                $media_path = $uploaded;
                if ($content === '') {
                    $content = ' ';
                }
            }
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
            if (is_array($data)) {
                $receiver_id = (int)($data['receiver_id'] ?? 0);
                $content = trim($data['text'] ?? '');
                $gif_url = trim($data['gif_url'] ?? '');
                if ($gif_url !== '' && filter_var($gif_url, FILTER_VALIDATE_URL)) {
                    $media_path = 'gif:' . $gif_url;
                    if ($content === '') {
                        $content = ' ';
                    }
                }
            }
        }

        if (!$receiver_id || ($content === '' && !$media_path)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data or empty message']);
            return;
        }

        if (!$this->networkModel->areConnected($_SESSION['user_id'], $receiver_id)) {
            echo json_encode(['success' => false, 'message' => 'You can only message your connections']);
            return;
        }

        if ($this->messageModel->isBlocked($_SESSION['user_id'], $receiver_id)) {
            echo json_encode(['success' => false, 'message' => 'You cannot message this user (blocked).']);
            return;
        }

        $messageId = $this->messageModel->sendMessage($_SESSION['user_id'], $receiver_id, $content, $media_path);
        if ($messageId) {
            $msg = $this->messageModel->getMessageById($messageId);
            echo json_encode(['success' => true, 'message' => 'Message sent', 'data' => $msg]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
    }

    private function handleMediaUpload($file) {
        if ($file['size'] > self::MAX_MEDIA_BYTES) {
            return false;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!isset(self::ALLOWED_MEDIA[$mime])) {
            return false;
        }
        $uploadDir = dirname(APPROOT) . '/public/uploads/messages/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . self::ALLOWED_MEDIA[$mime];
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return $filename;
        }
        return false;
    }

    public function edit() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $message_id = (int)($data['message_id'] ?? 0);
        $new_text = trim($data['new_text'] ?? '');

        if ($message_id && $new_text !== '') {
            if ($this->messageModel->editMessage($message_id, $_SESSION['user_id'], $new_text)) {
                echo json_encode(['success' => true, 'message' => 'Message updated']);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Messages can only be edited within 2 minutes of sending.'
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
        }
    }

    public function unsend() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $message_id = (int)($data['message_id'] ?? 0);

        if ($message_id) {
            if ($this->messageModel->deleteMessage($message_id, $_SESSION['user_id'])) {
                echo json_encode(['success' => true, 'message' => 'Message unsent']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to unsend message']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
        }
    }

    public function block() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $blocked_id = (int)($data['user_id'] ?? 0);

        if ($blocked_id) {
            if ($this->messageModel->blockUser($_SESSION['user_id'], $blocked_id)) {
                echo json_encode(['success' => true, 'message' => 'User blocked']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to block user']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
        }
    }

    public function unblock() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $blocked_id = (int)($data['user_id'] ?? 0);

        if ($blocked_id && $this->messageModel->unblockUser($_SESSION['user_id'], $blocked_id)) {
            echo json_encode(['success' => true, 'message' => 'User unblocked']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to unblock user']);
        }
    }

    public function clear_chat() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $other_user_id = (int)($data['user_id'] ?? 0);

        if ($other_user_id) {
            if ($this->messageModel->deleteConversation($_SESSION['user_id'], $other_user_id)) {
                echo json_encode(['success' => true, 'message' => 'Conversation deleted']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete conversation']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
        }
    }
}
