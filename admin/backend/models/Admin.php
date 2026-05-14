<?php
class Admin extends Model {
    public function getAllUsers() {
        $this->db->query("SELECT u.user_id, u.full_name, u.email, u.role, u.is_admin, u.status, u.profile_pic, u.created_at,
                          (SELECT COUNT(*) FROM reports r WHERE r.target_type = 'User' AND r.target_id = u.user_id AND r.status = 'Pending') as active_report_count
                          FROM users u ORDER BY u.created_at DESC");
        return $this->db->resultSet();
    }

    public function getUserById($id) {
        $this->db->query("SELECT user_id, full_name, email, role, status FROM users WHERE user_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getAllPosts() {
        $this->db->query("SELECT p.*, u.full_name as author_name, u.email as author_email,
                          (SELECT COUNT(*) FROM reports r WHERE r.target_type = 'Post' AND r.target_id = p.post_id AND r.status = 'Pending') as active_report_count,
                          (SELECT COUNT(*) FROM reports r WHERE r.target_type = 'Post' AND r.target_id = p.post_id) as total_report_count
                          FROM posts p 
                          JOIN users u ON p.user_id = u.user_id 
                          ORDER BY p.created_at DESC");
        return $this->db->resultSet();
    }

    public function updateRole($user_id, $role) {
        $allowedRoles = ['Student', 'Professional', 'Company', 'Admin'];
        if (!in_array($role, $allowedRoles, true)) {
            return false;
        }

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
        $allowedRoles = ['Student', 'Professional', 'Company', 'Admin'];
        if (!in_array($data['role'], $allowedRoles, true)) {
            return false;
        }

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
        $this->db->query("SELECT j.*, c.name as company_name,
                          (SELECT COUNT(*) FROM reports r WHERE r.target_type = 'Job' AND r.target_id = j.job_id AND r.status = 'Pending') as active_report_count,
                          (SELECT COUNT(*) FROM reports r WHERE r.target_type = 'Job' AND r.target_id = j.job_id) as total_report_count
                          FROM jobs j JOIN companies c ON j.company_id = c.company_id ORDER BY j.posted_at DESC");
        return $this->db->resultSet();
    }

    public function getReportsWithContext() {
        $this->db->query("
            SELECT r.*, reporter.full_name as reporter_name, reporter.email as reporter_email,
            CASE 
                WHEN r.target_type = 'Post' THEN (SELECT content FROM posts WHERE post_id = r.target_id)
                WHEN r.target_type = 'Job' THEN (SELECT title FROM jobs WHERE job_id = r.target_id)
                WHEN r.target_type = 'User' THEN (SELECT full_name FROM users WHERE user_id = r.target_id)
                ELSE 'Unknown'
            END as target_preview,
            (SELECT COUNT(*) FROM reports ar WHERE ar.target_type = r.target_type AND ar.target_id = r.target_id AND ar.status = 'Pending') as active_report_count,
            (SELECT COUNT(*) FROM reports tr WHERE tr.target_type = r.target_type AND tr.target_id = r.target_id) as total_report_count
            FROM reports r
            JOIN users reporter ON r.reporter_id = reporter.user_id
            ORDER BY active_report_count DESC, r.created_at DESC
        ");
        return $this->db->resultSet();
    }

    public function getReportById($id) {
        $this->db->query("SELECT r.*, u.full_name as reporter_name, u.email as reporter_email FROM reports r JOIN users u ON r.reporter_id = u.user_id WHERE r.report_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getReportsForTarget($targetType, $targetId) {
        $this->db->query("SELECT r.*, u.full_name as reporter_name, u.email as reporter_email
                          FROM reports r
                          JOIN users u ON r.reporter_id = u.user_id
                          WHERE r.target_type = :target_type AND r.target_id = :target_id
                          ORDER BY FIELD(r.status, 'Pending', 'Resolved', 'Dismissed'), r.created_at DESC");
        $this->db->bind(':target_type', $targetType);
        $this->db->bind(':target_id', $targetId);
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

    public function toggleJobStatus($id) {
        $this->db->query("UPDATE jobs SET status = CASE WHEN status = 'Live' THEN 'Closed' ELSE 'Live' END WHERE job_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function approveUser($id) {
        $this->db->query("UPDATE users SET status = 'Approved' WHERE user_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function rejectUser($id) {
        $this->db->query("UPDATE users SET status = 'Rejected' WHERE user_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function resolveReport($id) {
        $this->db->query("UPDATE reports SET status = 'Resolved' WHERE report_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function dismissReport($id) {
        $this->db->query("UPDATE reports SET status = 'Dismissed' WHERE report_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function deleteReport($id) {
        $this->db->query("DELETE FROM reports WHERE report_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function resolveTargetReports($targetType, $targetId) {
        $this->db->query("UPDATE reports SET status = 'Resolved' WHERE target_type = :target_type AND target_id = :target_id AND status = 'Pending'");
        $this->db->bind(':target_type', $targetType);
        $this->db->bind(':target_id', $targetId);
        return $this->db->execute();
    }

    public function dismissTargetReports($targetType, $targetId) {
        $this->db->query("UPDATE reports SET status = 'Dismissed' WHERE target_type = :target_type AND target_id = :target_id AND status = 'Pending'");
        $this->db->bind(':target_type', $targetType);
        $this->db->bind(':target_id', $targetId);
        return $this->db->execute();
    }

    public function getPendingUsersCount() {
        $this->db->query("SELECT COUNT(*) as count FROM users WHERE status = 'Pending'");
        return (int)$this->db->single()['count'];
    }

    public function addUser($data) {
        $allowedRoles = ['Student', 'Professional', 'Company', 'Admin'];
        if (!in_array($data['role'], $allowedRoles, true)) {
            return false;
        }

        $isAdmin = ($data['role'] === 'Admin') ? 1 : 0;
        $this->db->query("INSERT INTO users (full_name, email, password, role, is_admin, headline, location, bio, status) VALUES (:full_name, :email, :password, :role, :is_admin, :headline, :location, :bio, :status)");
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':is_admin', $isAdmin);
        $this->db->bind(':headline', 'Networking on ProNetwork');
        $this->db->bind(':location', 'Global');
        $this->db->bind(':bio', '');
        $this->db->bind(':status', 'Approved');
        return $this->db->execute();
    }
    public function getCombinedActivity($limit, $offset = 0) {
        $this->db->query("
            SELECT * FROM (
                (SELECT 'User' as type, user_id as id, full_name as main_text, email as sub_text, created_at 
                 FROM users)
                UNION ALL
                (SELECT 'Post' as type, p.post_id as id, u.full_name as main_text, LEFT(p.content, 50) as sub_text, p.created_at 
                 FROM posts p JOIN users u ON p.user_id = u.user_id)
                UNION ALL
                (SELECT 'Report' as type, r.report_id as id, u.full_name as main_text, CONCAT('Reported a ', LOWER(r.target_type)) as sub_text, r.created_at 
                 FROM reports r JOIN users u ON r.reporter_id = u.user_id)
            ) as activity
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $this->db->bind(':limit', (int)$limit);
        $this->db->bind(':offset', (int)$offset);
        return $this->db->resultSet();
    }
}
