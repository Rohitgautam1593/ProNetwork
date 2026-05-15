<?php
class NetworkController extends Controller {
    private $networkModel;
    private $notificationModel;

    public function __construct() {
        if (!isLoggedIn()) {
            $path = trim((string) ($_GET['url'] ?? ''), '/');
            $parts = $path !== '' ? explode('/', $path) : [];
            $method = $parts[1] ?? 'index';
            $htmlMethods = ['index', 'connections_list', 'pages_list', 'invitations_list', 'suggestions_list'];
            if (($parts[0] ?? '') === 'network' && in_array($method, $htmlMethods, true)) {
                header('Location: ' . URLROOT . '/auth/login');
                exit;
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        $this->networkModel = $this->model('Network');
        $this->notificationModel = $this->model('Notification');
    }

    public function index() {
        $this->view('users/network');
    }

    public function connections_list() {
        $this->view('users/connections_list');
    }

    public function pages_list() {
        $this->view('users/pages_list');
    }

    public function invitations_list() {
        $this->view('users/invitations_list');
    }

    public function suggestions_list() {
        $this->view('users/suggestions_list');
    }

    public function suggestions() {
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 12;
        $suggestions = $this->networkModel->getSuggestions($_SESSION['user_id'], $limit);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'suggestions' => $suggestions]);
    }

    public function connections() {
        $userId = isset($_GET['id']) ? (int) $_GET['id'] : (int) $_SESSION['user_id'];
        $connections = $this->networkModel->getConnections($userId);
        echo json_encode(['success' => true, 'connections' => $connections]);
    }

    public function status() {
        $targetUserId = (int)($_GET['id'] ?? 0);
        if (!$targetUserId) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }
        $status = $this->networkModel->getConnectionStatus($_SESSION['user_id'], $targetUserId);
        echo json_encode(['success' => true] + $status);
    }

    public function pages() {
        $pages = $this->networkModel->getFollowedPages($_SESSION['user_id']);
        echo json_encode(['success' => true, 'pages' => $pages]);
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
                    "$userName sent you a connection request",
                    (int)$_SESSION['user_id'],
                    'user',
                    (int)$_SESSION['user_id']
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
                    "$userName accepted your connection request",
                    (int)$_SESSION['user_id'],
                    'user',
                    (int)$_SESSION['user_id']
                );
                echo json_encode(['success' => true, 'message' => 'Request accepted']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to accept request']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        }
    }

    public function summary() {
        $userId = $_SESSION['user_id'];
        $connections = count($this->networkModel->getConnections($userId));
        $pages = count($this->networkModel->getFollowedPages($userId));

        echo json_encode([
            'success' => true,
            'connections' => $connections,
            'pages' => $pages,
            'events' => 0
        ]);
    }

    public function reject() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }
        $sender_id = $data['user_id'] ?? null;

        if($sender_id) {
            if($this->networkModel->rejectRequest($sender_id, $_SESSION['user_id'])) {
                echo json_encode(['success' => true, 'message' => 'Request ignored']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to ignore request']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        }
    }
}
