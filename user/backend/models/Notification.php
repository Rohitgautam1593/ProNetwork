<?php
class Notification extends Model {
    public function getNotifications($user_id) {
        $this->db->query("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function markAsRead($user_id) {
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    public function addNotification($user_id, $type, $message, $source_id = null, $source_type = null) {
        $this->db->query("INSERT INTO notifications (user_id, type, source_id, source_type, message) VALUES (:user_id, :type, :source_id, :source_type, :message)");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':type', $type);
        $this->db->bind(':source_id', $source_id);
        $this->db->bind(':source_type', $source_type);
        $this->db->bind(':message', $message);
        return $this->db->execute();
    }
}
