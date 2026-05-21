<?php
class Network extends Model {
    /**
     * Users to suggest (no existing connection row in either direction).
     */
    public function getSuggestions($user_id, int $limit = 12) {
        $limit = max(1, min(50, $limit));
        $this->db->query("SELECT user_id, full_name, headline, profile_pic, industry, location
                          FROM users
                          WHERE user_id != :uid
                          AND user_id NOT IN (
                              SELECT receiver_id FROM connections WHERE sender_id = :uid_as_sender
                              UNION
                              SELECT sender_id FROM connections WHERE receiver_id = :uid_as_receiver
                          )
                          ORDER BY user_id DESC
                          LIMIT :result_limit");
        $this->db->bind(':uid', $user_id);
        $this->db->bind(':uid_as_sender', $user_id);
        $this->db->bind(':uid_as_receiver', $user_id);
        $this->db->bind(':result_limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Get current connections
    public function getConnections($user_id) {
        $this->db->query("SELECT u.user_id, u.full_name, u.headline, u.profile_pic 
                          FROM users u 
                          JOIN connections c ON (u.user_id = c.sender_id OR u.user_id = c.receiver_id)
                          WHERE (c.sender_id = :user_id OR c.receiver_id = :user_id)
                          AND u.user_id != :user_id
                          AND c.status = 'Accepted'");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }
    // Get pending connection requests received by user
    public function getPendingRequests($user_id) {
        $this->db->query("SELECT u.user_id, u.full_name, u.headline, u.profile_pic 
                          FROM users u 
                          JOIN connections c ON u.user_id = c.sender_id 
                          WHERE c.receiver_id = :user_id 
                          AND c.status = 'Pending'");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    // Send connection request
    public function sendRequest($sender_id, $receiver_id) {
        if ((int)$sender_id === (int)$receiver_id) {
            return false;
        }

        $this->db->query("SELECT user_id FROM users WHERE user_id = :receiver_id LIMIT 1");
        $this->db->bind(':receiver_id', $receiver_id);
        if (!$this->db->single()) {
            return false;
        }

        $this->db->query("SELECT connection_id FROM connections 
                          WHERE (sender_id = :sender_id AND receiver_id = :receiver_id)
                          OR (sender_id = :receiver_id AND receiver_id = :sender_id)
                          LIMIT 1");
        $this->db->bind(':sender_id', $sender_id);
        $this->db->bind(':receiver_id', $receiver_id);
        if ($this->db->single()) {
            return false;
        }

        $this->db->query("INSERT INTO connections (sender_id, receiver_id, status) VALUES (:sender_id, :receiver_id, 'Pending')");
        $this->db->bind(':sender_id', $sender_id);
        $this->db->bind(':receiver_id', $receiver_id);
        return $this->db->execute();
    }

    // Accept connection request
    public function acceptRequest($sender_id, $receiver_id) {
        $this->db->query("UPDATE connections SET status = 'Accepted' WHERE sender_id = :sender_id AND receiver_id = :receiver_id");
        $this->db->bind(':sender_id', $sender_id);
        $this->db->bind(':receiver_id', $receiver_id);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function areConnected($user_id, $other_user_id) {
        $this->db->query("SELECT connection_id FROM connections
                          WHERE status = 'Accepted'
                          AND ((sender_id = :user_id AND receiver_id = :other_user_id)
                          OR (sender_id = :other_user_id AND receiver_id = :user_id))
                          LIMIT 1");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':other_user_id', $other_user_id);
        return (bool)$this->db->single();
    }

    public function getConnectionStatus($user_id, $other_user_id) {
        if ((int)$user_id === (int)$other_user_id) {
            return ['state' => 'self', 'direction' => 'self'];
        }

        $this->db->query("SELECT sender_id, receiver_id, status FROM connections
                          WHERE (sender_id = :user_id AND receiver_id = :other_user_id)
                          OR (sender_id = :other_user_id AND receiver_id = :user_id)
                          LIMIT 1");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':other_user_id', $other_user_id);
        $row = $this->db->single();

        if (!$row) {
            return ['state' => 'none', 'direction' => 'none'];
        }

        $direction = ((int)$row['sender_id'] === (int)$user_id) ? 'sent' : 'received';
        return ['state' => $row['status'], 'direction' => $direction];
    }

    // Get company pages followed by user
    public function getFollowedPages($user_id) {
        $this->db->query("SELECT c.company_id, c.name as company_name, c.industry, c.logo_path
                          FROM companies c
                          JOIN company_followers f ON c.company_id = f.company_id
                          WHERE f.user_id = :user_id
                          ORDER BY c.name ASC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    // Reject/Ignore connection request
    public function rejectRequest($sender_id, $receiver_id) {
        $this->db->query("DELETE FROM connections WHERE sender_id = :sender_id AND receiver_id = :receiver_id AND status = 'Pending'");
        $this->db->bind(':sender_id', $sender_id);
        $this->db->bind(':receiver_id', $receiver_id);
        return $this->db->execute();
    }

    public function getPendingRequestsCount($user_id) {
        $this->db->query("SELECT COUNT(*) AS cnt FROM connections WHERE receiver_id = :user_id AND status = 'Pending'");
        $this->db->bind(':user_id', $user_id);
        $row = $this->db->single();
        return (int)($row['cnt'] ?? 0);
    }
}
