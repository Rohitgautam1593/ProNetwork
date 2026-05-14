<?php
class MessageController extends Controller {
    private $messageModel;
    private $networkModel;

    public function __construct() {
        $this->messageModel = $this->model('Message');
        $this->networkModel = $this->model('Network');
    }

    public function index() {
        if(!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $this->view('users/messaging');
    }

    public function conversations() {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $conversations = $this->messageModel->getConversations($_SESSION['user_id']);
        echo json_encode(['success' => true, 'conversations' => $conversations]);
    }

    public function history($id = null) {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        if($id) {
            $messages = $this->messageModel->getChatHistory($_SESSION['user_id'], $id);
            echo json_encode(['success' => true, 'messages' => $messages]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User ID missing']);
        }
    }

    public function send() {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }
        $receiver_id = $data['receiver_id'] ?? null;
        $content = trim($data['text'] ?? '');

        if($receiver_id && !empty($content)) {
            if(!$this->networkModel->areConnected($_SESSION['user_id'], $receiver_id)) {
                echo json_encode(['success' => false, 'message' => 'You can only message your connections']);
                return;
            }

            if($this->messageModel->sendMessage($_SESSION['user_id'], $receiver_id, $content)) {
                echo json_encode(['success' => true, 'message' => 'Message sent']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
        }
    }
}
