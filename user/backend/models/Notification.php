<?php
class Notification extends Model {
    public function getNotifications($user_id, $limit = 80) {
        $this->db->query("SELECT n.*,
            u.full_name AS actor_name, u.profile_pic AS actor_pic, u.user_id AS actor_user_id
            FROM notifications n
            LEFT JOIN users u ON u.user_id = n.actor_id
            WHERE n.user_id = :user_id
            ORDER BY n.created_at DESC
            LIMIT " . (int)$limit);
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function getUnreadCount($user_id) {
        $this->db->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = :user_id AND is_read = 0");
        $this->db->bind(':user_id', $user_id);
        $row = $this->db->single();
        return (int)($row['cnt'] ?? 0);
    }

    public function markAsRead($user_id) {
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0");
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    public function markOneAsRead($notification_id, $user_id) {
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE notification_id = :id AND user_id = :user_id");
        $this->db->bind(':id', $notification_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    public function dismiss($notification_id, $user_id) {
        $this->db->query("DELETE FROM notifications WHERE notification_id = :id AND user_id = :user_id");
        $this->db->bind(':id', $notification_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    public function clearAll($user_id) {
        $this->db->query("DELETE FROM notifications WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    public function addNotification($user_id, $type, $message, $source_id = null, $source_type = null, $actor_id = null) {
        $this->db->query("INSERT INTO notifications (user_id, actor_id, type, source_id, source_type, message)
            VALUES (:user_id, :actor_id, :type, :source_id, :source_type, :message)");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':actor_id', $actor_id);
        $this->db->bind(':type', $type);
        $this->db->bind(':source_id', $source_id);
        $this->db->bind(':source_type', $source_type);
        $this->db->bind(':message', $message);
        return $this->db->execute();
    }
}
