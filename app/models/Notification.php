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

    public function addNotification($user_id, $type, $message) {
        $this->db->query("INSERT INTO notifications (user_id, type, message) VALUES (:user_id, :type, :message)");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':type', $type);
        $this->db->bind(':message', $message);
        return $this->db->execute();
    }
}
