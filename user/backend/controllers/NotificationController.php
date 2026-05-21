<?php
class NotificationController extends Controller {
    private $notificationModel;

    public function __construct() {
        $this->notificationModel = $this->model('Notification');
    }

    public function index() {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $this->view('users/notifications');
    }

    public function fetch() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $userId = $_SESSION['user_id'];
        $notifications = $this->notificationModel->getNotifications($userId);
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $this->notificationModel->getUnreadCount($userId)
        ]);
    }

    public function unread_count() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'count' => 0]);
            exit;
        }
        echo json_encode([
            'success' => true,
            'count' => $this->notificationModel->getUnreadCount($_SESSION['user_id'])
        ]);
    }

    public function mark_read() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $ok = $this->notificationModel->markAsRead($_SESSION['user_id']);
        echo json_encode(['success' => $ok, 'unread_count' => 0]);
    }

    public function mark_one($id = null) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid id']);
            return;
        }
        $ok = $this->notificationModel->markOneAsRead((int)$id, $_SESSION['user_id']);
        echo json_encode([
            'success' => $ok,
            'unread_count' => $this->notificationModel->getUnreadCount($_SESSION['user_id'])
        ]);
    }

    public function dismiss($id = null) {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid id']);
            return;
        }
        $ok = $this->notificationModel->dismiss((int)$id, $_SESSION['user_id']);
        echo json_encode([
            'success' => $ok,
            'unread_count' => $this->notificationModel->getUnreadCount($_SESSION['user_id'])
        ]);
    }

    public function clear_all() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $ok = $this->notificationModel->clearAll($_SESSION['user_id']);
        echo json_encode(['success' => $ok, 'unread_count' => 0]);
    }

    public function badge_counts() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'notifications' => 0, 'messages' => 0, 'network' => 0]);
            exit;
        }
        $userId = $_SESSION['user_id'];
        
        $messageModel = $this->model('Message');
        $networkModel = $this->model('Network');
        
        $notifCount = $this->notificationModel->getUnreadCount($userId);
        $msgCount = $messageModel->getUnreadMessagesCount($userId);
        $netCount = $networkModel->getPendingRequestsCount($userId);
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifCount,
            'messages' => $msgCount,
            'network' => $netCount
        ]);
        exit;
    }
}
