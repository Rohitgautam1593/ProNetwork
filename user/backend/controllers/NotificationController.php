<?php
class NotificationController extends Controller {
    private $notificationModel;

    public function __construct() {
        $this->notificationModel = $this->model('Notification');
    }

    public function index() {
        if(!isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        $this->view('users/notifications');
    }

    public function fetch() {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        $notifications = $this->notificationModel->getNotifications($_SESSION['user_id']);
        echo json_encode(['success' => true, 'notifications' => $notifications]);
    }

    public function mark_read() {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        if($this->notificationModel->markAsRead($_SESSION['user_id'])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}
