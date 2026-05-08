<?php
class Post extends Model {
    public function getPosts() {
        $this->db->query("SELECT p.*, u.full_name, u.role as user_role, u.profile_pic, 
                          (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.post_id) as reaction_count 
                          FROM posts p 
                          JOIN users u ON p.user_id = u.user_id 
                          ORDER BY p.created_at DESC");
        return $this->db->resultSet();
    }

    public function addPost($data) {
        $this->db->query("INSERT INTO posts (user_id, content, post_image) VALUES (:user_id, :content, :post_image)");
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':post_image', $data['post_image'] ?? null);

        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function getPostById($id) {
        $this->db->query("SELECT p.*, u.full_name, u.role as user_role, u.profile_pic FROM posts p JOIN users u ON p.user_id = u.user_id WHERE p.post_id = :post_id");
        $this->db->bind(':post_id', $id);
        return $this->db->single();
    }

    public function addReaction($post_id, $user_id, $type = 'like') {
        // Check if already reacted
        $this->db->query("SELECT * FROM post_reactions WHERE post_id = :post_id AND user_id = :user_id");
        $this->db->bind(':post_id', $post_id);
        $this->db->bind(':user_id', $user_id);
        $row = $this->db->single();

        if ($row) {
            $this->db->query("DELETE FROM post_reactions WHERE reaction_id = :id");
            $this->db->bind(':id', $row['reaction_id']);
        } else {
            $this->db->query("INSERT INTO post_reactions (post_id, user_id, type) VALUES (:post_id, :user_id, :type)");
            $this->db->bind(':post_id', $post_id);
            $this->db->bind(':user_id', $user_id);
            $this->db->bind(':type', $type);
        }
        return $this->db->execute();
    }

    public function getReactionCount($post_id) {
        $this->db->query("SELECT COUNT(*) as count FROM post_reactions WHERE post_id = :post_id");
        $this->db->bind(':post_id', $post_id);
        $res = $this->db->single();
        return $res ? $res['count'] : 0;
    }
}
