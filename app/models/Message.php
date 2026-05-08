<?php
class Message extends Model {
    public function getConversations($user_id) {
        $this->db->query("SELECT DISTINCT u.user_id, u.full_name, u.profile_pic, 
                          m2.message_text as last_message,
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
                          ORDER BY last_message_time DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function getChatHistory($user_id, $other_user_id) {
        $this->db->query("SELECT m.*, u.profile_pic as sender_pic, u.full_name as sender_name FROM messages m
                          JOIN users u ON m.sender_id = u.user_id
                          WHERE (sender_id = :user_id AND receiver_id = :other_user_id)
                          OR (sender_id = :other_user_id AND receiver_id = :user_id)
                          ORDER BY sent_at ASC");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':other_user_id', $other_user_id);
        return $this->db->resultSet();
    }

    public function sendMessage($sender_id, $receiver_id, $message_text) {
        if ((int)$sender_id === (int)$receiver_id) {
            return false;
        }

        $this->db->query("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (:sender_id, :receiver_id, :message_text)");
        $this->db->bind(':sender_id', $sender_id);
        $this->db->bind(':receiver_id', $receiver_id);
        $this->db->bind(':message_text', $message_text);
        return $this->db->execute();
    }
}
