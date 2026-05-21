<?php
class Message extends Model {
    
    public function isBlocked($user1, $user2) {
        $this->db->query("SELECT id FROM blocked_users WHERE (blocker_id = :u1 AND blocked_id = :u2) OR (blocker_id = :u2 AND blocked_id = :u1)");
        $this->db->bind(':u1', $user1);
        $this->db->bind(':u2', $user2);
        return $this->db->single() !== false;
    }

    public function blockUser($blocker_id, $blocked_id) {
        if ($this->isBlocked($blocker_id, $blocked_id)) return true; // Already blocked
        $this->db->query("INSERT INTO blocked_users (blocker_id, blocked_id) VALUES (:blocker, :blocked)");
        $this->db->bind(':blocker', $blocker_id);
        $this->db->bind(':blocked', $blocked_id);
        return $this->db->execute();
    }

    public function unblockUser($blocker_id, $blocked_id) {
        $this->db->query("DELETE FROM blocked_users WHERE blocker_id = :blocker AND blocked_id = :blocked");
        $this->db->bind(':blocker', $blocker_id);
        $this->db->bind(':blocked', $blocked_id);
        return $this->db->execute();
    }

    public function getConversations($user_id) {
        // Exclude conversations where users have blocked each other
        $this->db->query("SELECT DISTINCT u.user_id, u.full_name, u.profile_pic, 
                          CASE 
                            WHEN m2.is_deleted = 1 THEN 'This message was deleted' 
                            WHEN m2.media_path IS NOT NULL AND m2.media_path != '' THEN 'Sent an attachment'
                            ELSE m2.message_text 
                          END as last_message,
                          m2.sent_at as last_message_time
                          FROM users u
                          JOIN messages m ON (u.user_id = m.sender_id OR u.user_id = m.receiver_id)
                          LEFT JOIN (
                              SELECT m3.*
                              FROM messages m3
                              INNER JOIN (
                                  SELECT 
                                      CASE WHEN sender_id = :user_id THEN receiver_id ELSE sender_id END as other_id,
                                      MAX(sent_at) as max_sent
                                  FROM messages
                                  WHERE sender_id = :user_id OR receiver_id = :user_id
                                  GROUP BY other_id
                              ) m4 ON (
                                  (m3.sender_id = :user_id AND m3.receiver_id = m4.other_id AND m3.sent_at = m4.max_sent) OR
                                  (m3.receiver_id = :user_id AND m3.sender_id = m4.other_id AND m3.sent_at = m4.max_sent)
                              )
                          ) m2 ON u.user_id = m2.sender_id OR u.user_id = m2.receiver_id
                          WHERE (m.sender_id = :user_id OR m.receiver_id = :user_id)
                          AND u.user_id != :user_id
                          AND NOT EXISTS (SELECT 1 FROM blocked_users b WHERE (b.blocker_id = :user_id AND b.blocked_id = u.user_id) OR (b.blocker_id = u.user_id AND b.blocked_id = :user_id))
                          ORDER BY last_message_time DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function getChatHistory($user_id, $other_user_id) {
        if ($this->isBlocked($user_id, $other_user_id)) {
            return []; // Return empty if blocked
        }

        $this->db->query("SELECT m.message_id, m.sender_id, m.receiver_id, m.sent_at, m.updated_at, m.is_edited, m.is_deleted, m.media_path,
                          CASE WHEN m.is_deleted = 1 THEN 'This message was deleted' ELSE m.message_text END as message_text,
                          u.profile_pic as sender_pic, u.full_name as sender_name 
                          FROM messages m
                          JOIN users u ON m.sender_id = u.user_id
                          WHERE (sender_id = :user_id AND receiver_id = :other_user_id)
                          OR (sender_id = :other_user_id AND receiver_id = :user_id)
                          ORDER BY sent_at ASC");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':other_user_id', $other_user_id);
        return $this->db->resultSet();
    }

    public function sendMessage($sender_id, $receiver_id, $message_text, $media_path = null) {
        if ((int)$sender_id === (int)$receiver_id) return false;
        if ($this->isBlocked($sender_id, $receiver_id)) return false;

        if ($message_text === '' && $media_path) {
            $message_text = ' ';
        }

        $this->db->query("INSERT INTO messages (sender_id, receiver_id, message_text, media_path) VALUES (:sender_id, :receiver_id, :message_text, :media_path)");
        $this->db->bind(':sender_id', $sender_id);
        $this->db->bind(':receiver_id', $receiver_id);
        $this->db->bind(':message_text', $message_text);
        $this->db->bind(':media_path', $media_path);
        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function getBlockStatus($user_id, $other_user_id) {
        $this->db->query("SELECT blocker_id FROM blocked_users WHERE
            (blocker_id = :me AND blocked_id = :other) OR (blocker_id = :other AND blocked_id = :me)");
        $this->db->bind(':me', $user_id);
        $this->db->bind(':other', $other_user_id);
        $row = $this->db->single();
        if (!$row) {
            return ['blocked' => false, 'blocked_by_me' => false, 'blocked_by_them' => false];
        }
        $blockedByMe = (int)$row['blocker_id'] === (int)$user_id;
        return [
            'blocked' => true,
            'blocked_by_me' => $blockedByMe,
            'blocked_by_them' => !$blockedByMe
        ];
    }

    public function getMessagesSince($user_id, $other_user_id, $since = null) {
        if ($this->isBlocked($user_id, $other_user_id)) {
            return [];
        }
        $sql = "SELECT m.message_id, m.sender_id, m.receiver_id, m.sent_at, m.updated_at, m.is_edited, m.is_deleted, m.media_path,
                CASE WHEN m.is_deleted = 1 THEN 'This message was deleted' ELSE m.message_text END as message_text,
                u.profile_pic as sender_pic, u.full_name as sender_name
                FROM messages m
                JOIN users u ON m.sender_id = u.user_id
                WHERE ((sender_id = :user_id AND receiver_id = :other) OR (sender_id = :other AND receiver_id = :user_id))";
        if ($since) {
            $sql .= " AND m.sent_at > :since";
        }
        $sql .= " ORDER BY sent_at ASC";
        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':other', $other_user_id);
        if ($since) {
            $this->db->bind(':since', $since);
        }
        return $this->db->resultSet();
    }

    public function markThreadRead($user_id, $other_user_id) {
        $this->db->query("UPDATE messages SET is_read = 1
            WHERE receiver_id = :user_id AND sender_id = :other AND is_read = 0");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':other', $other_user_id);
        return $this->db->execute();
    }

    public function getMessageById($message_id) {
        $this->db->query("SELECT * FROM messages WHERE message_id = :id");
        $this->db->bind(':id', $message_id);
        return $this->db->single();
    }

    public function editMessage($message_id, $sender_id, $new_text) {
        $msg = $this->getMessageById($message_id);
        if (!$msg || $msg['sender_id'] != $sender_id || $msg['is_deleted'] == 1) return false;

        // Check if within 2 minutes
        $sentTime = strtotime($msg['sent_at']);
        $currentTime = time();
        if (($currentTime - $sentTime) > 120) {
            return false; // Past 2 minutes
        }

        $this->db->query("UPDATE messages SET message_text = :text, is_edited = 1, updated_at = NOW() WHERE message_id = :id");
        $this->db->bind(':text', $new_text);
        $this->db->bind(':id', $message_id);
        return $this->db->execute();
    }

    public function deleteMessage($message_id, $sender_id) {
        $msg = $this->getMessageById($message_id);
        if (!$msg || $msg['sender_id'] != $sender_id) return false;

        $this->db->query("UPDATE messages SET is_deleted = 1, message_text = '', media_path = NULL WHERE message_id = :id");
        $this->db->bind(':id', $message_id);
        return $this->db->execute();
    }

    public function deleteConversation($user1, $user2) {
        // Hard delete conversation for both users
        $this->db->query("DELETE FROM messages WHERE (sender_id = :u1 AND receiver_id = :u2) OR (sender_id = :u2 AND receiver_id = :u1)");
        $this->db->bind(':u1', $user1);
        $this->db->bind(':u2', $user2);
        return $this->db->execute();
    }

    public function getUnreadMessagesCount($user_id) {
        $this->db->query("SELECT COUNT(*) AS cnt FROM messages m 
                          WHERE m.receiver_id = :user_id 
                          AND m.is_read = 0 
                          AND m.is_deleted = 0
                          AND NOT EXISTS (
                              SELECT 1 FROM blocked_users b 
                              WHERE (b.blocker_id = :user_id AND b.blocked_id = m.sender_id)
                              OR (b.blocker_id = m.sender_id AND b.blocked_id = :user_id)
                          )");
        $this->db->bind(':user_id', $user_id);
        $row = $this->db->single();
        return (int)($row['cnt'] ?? 0);
    }
}
