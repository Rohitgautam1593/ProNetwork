<?php
class Admin extends Model {
    public function getAllUsers() {
        $this->db->query("SELECT user_id, full_name, email, role, is_admin, profile_pic, created_at FROM users ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function getAllPosts() {
        $this->db->query("SELECT p.*, u.full_name as author_name, u.email as author_email 
                          FROM posts p 
                          JOIN users u ON p.user_id = u.user_id 
                          ORDER BY p.created_at DESC");
        return $this->db->resultSet();
    }

    public function updateRole($user_id, $role) {
        if ($role === 'Admin') {
            $this->db->query("UPDATE users SET is_admin = 1, role = 'Professional' WHERE user_id = :user_id");
        } else {
            $this->db->query("UPDATE users SET role = :role, is_admin = 0 WHERE user_id = :user_id");
            $this->db->bind(':role', $role);
        }
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    public function deleteUser($user_id) {
        $this->db->query("DELETE FROM users WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    public function updateUser($data) {
        $passwordUpdate = "";
        if (!empty($data['password'])) {
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            $passwordUpdate = ", password = :password";
        }

        if ($data['role'] === 'Admin') {
            $this->db->query("UPDATE users SET full_name = :name, email = :email, is_admin = 1, role = 'Professional' $passwordUpdate WHERE user_id = :id");
        } else {
            $this->db->query("UPDATE users SET full_name = :name, email = :email, role = :role, is_admin = 0 $passwordUpdate WHERE user_id = :id");
            $this->db->bind(':role', $data['role']);
        }
        
        $this->db->bind(':name', $data['full_name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':id', $data['user_id']);
        
        if (!empty($data['password'])) {
            $this->db->bind(':password', $hashed_password);
        }
        
        return $this->db->execute();
    }

    public function deletePost($post_id) {
        $this->db->query("DELETE FROM posts WHERE post_id = :post_id");
        $this->db->bind(':post_id', $post_id);
        return $this->db->execute();
    }

    public function getAllCompanies() {
        $this->db->query("SELECT * FROM companies ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function getAllJobs() {
        $this->db->query("SELECT j.*, c.name as company_name FROM jobs j JOIN companies c ON j.company_id = c.company_id ORDER BY j.posted_at DESC");
        return $this->db->resultSet();
    }

    public function deleteCompany($id) {
        $this->db->query("DELETE FROM companies WHERE company_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function deleteJob($id) {
        $this->db->query("DELETE FROM jobs WHERE job_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
