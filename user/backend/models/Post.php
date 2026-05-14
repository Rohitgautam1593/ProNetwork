<?php
class Post extends Model {
    public function getPosts() {
        $this->db->query("SELECT p.*, u.full_name, u.role as user_role, u.profile_pic, 
                          (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.post_id) as reaction_count,
                          (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id) as comment_count
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
        $this->db->query("SELECT p.*, u.full_name, u.role as user_role, u.profile_pic,
                          (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.post_id) as reaction_count,
                          (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id) as comment_count
                          FROM posts p JOIN users u ON p.user_id = u.user_id WHERE p.post_id = :post_id");
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

    public function reportPost($data) {
        $this->db->query("INSERT INTO reports (reporter_id, target_type, target_id, reason, status) VALUES (:reporter_id, 'Post', :target_id, :reason, 'Pending')");
        $this->db->bind(':reporter_id', $data['reporter_id']);
        $this->db->bind(':target_id', $data['target_id']);
        $this->db->bind(':reason', $data['reason']);
        return $this->db->execute();
    }

    public function getCommentsForPost($post_id) {
        $this->db->query(
            "SELECT c.comment_id, c.post_id, c.user_id, c.content, c.created_at, u.full_name, u.profile_pic
             FROM comments c
             JOIN users u ON c.user_id = u.user_id
             WHERE c.post_id = :post_id
             ORDER BY c.created_at ASC"
        );
        $this->db->bind(':post_id', $post_id);
        return $this->db->resultSet();
    }

    public function getCommentCount($post_id) {
        $this->db->query("SELECT COUNT(*) as cnt FROM comments WHERE post_id = :post_id");
        $this->db->bind(':post_id', $post_id);
        $row = $this->db->single();
        return $row ? (int) $row['cnt'] : 0;
    }

    public function addComment($post_id, $user_id, $content) {
        $this->db->query("INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)");
        $this->db->bind(':post_id', $post_id);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':content', $content);
        if ($this->db->execute()) {
            return (int) $this->db->lastInsertId();
        }
        return false;
    }

    public function getCommentById($comment_id) {
        $this->db->query(
            "SELECT c.comment_id, c.post_id, c.user_id, c.content, c.created_at, u.full_name, u.profile_pic
             FROM comments c
             JOIN users u ON c.user_id = u.user_id
             WHERE c.comment_id = :id"
        );
        $this->db->bind(':id', $comment_id);
        return $this->db->single();
    }
}
