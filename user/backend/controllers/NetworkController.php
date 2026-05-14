<?php
class NetworkController extends Controller {
    private $networkModel;
    private $notificationModel;

    public function __construct() {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        $this->networkModel = $this->model('Network');
        $this->notificationModel = $this->model('Notification');
    }

    public function index() {
        $this->view('users/network');
    }

    public function suggestions() {
        $suggestions = $this->networkModel->getSuggestions($_SESSION['user_id']);
        echo json_encode(['success' => true, 'suggestions' => $suggestions]);
    }

    public function connections() {
        $connections = $this->networkModel->getConnections($_SESSION['user_id']);
        echo json_encode(['success' => true, 'connections' => $connections]);
    }

    public function send_request() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }
        $target_user_id = $data['user_id'] ?? null;

        if($target_user_id) {
            if($this->networkModel->sendRequest($_SESSION['user_id'], $target_user_id)) {
                $userName = $_SESSION['user_name'] ?? 'Someone';
                $this->notificationModel->addNotification(
                    $target_user_id,
                    'Connection_Request',
                    "$userName sent you a connection request"
                );
                echo json_encode(['success' => true, 'message' => 'Request sent']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Request already exists or is invalid']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        }
    }
    public function pending() {
        $pending = $this->networkModel->getPendingRequests($_SESSION['user_id']);
        echo json_encode(['success' => true, 'requests' => $pending]);
    }

    public function accept() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }
        $sender_id = $data['user_id'] ?? null;

        if($sender_id) {
            if($this->networkModel->acceptRequest($sender_id, $_SESSION['user_id'])) {
                $userName = $_SESSION['user_name'] ?? 'Someone';
                $this->notificationModel->addNotification(
                    $sender_id,
                    'Connection_Accepted',
                    "$userName accepted your connection request"
                );
                echo json_encode(['success' => true, 'message' => 'Request accepted']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to accept request']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        }
    }
}
