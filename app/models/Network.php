<?php
class Network extends Model {
    // Get connection suggestions (users not already connected)
    public function getSuggestions($user_id) {
        $this->db->query("SELECT user_id, full_name, headline, profile_pic FROM users 
                          WHERE user_id != :user_id 
                          AND user_id NOT IN (
                              SELECT receiver_id FROM connections WHERE sender_id = :user_id
                              UNION
                              SELECT sender_id FROM connections WHERE receiver_id = :user_id
                          )
                          LIMIT 10");
        $this->db->bind(':user_id', $user_id);
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
        return $this->db->execute();
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
}
