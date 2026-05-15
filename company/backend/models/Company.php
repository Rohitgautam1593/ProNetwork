<?php
class Company extends Model {
    public function getCompanyById($id) {
        $this->db->query("SELECT company_id, user_id, name as company_name, industry, logo_path as logo, banner_path as banner, description, website, size, founded_year, followers, created_at FROM companies WHERE company_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getCompanies() {
        $this->db->query("SELECT company_id, name as company_name, industry, logo_path as logo, banner_path as banner, description, website, size, founded_year, followers, created_at FROM companies ORDER BY name ASC");
        return $this->db->resultSet();
    }

    public function getPrimaryCompany() {
        $this->db->query("SELECT company_id, name as company_name, industry, logo_path as logo, banner_path as banner, description, website, size, founded_year, followers, created_at FROM companies ORDER BY company_id ASC LIMIT 1");
        return $this->db->single();
    }

    public function getCompanyForUser($userId) {
        $this->db->query("SELECT company_id, user_id, name as company_name, industry, logo_path as logo, banner_path as banner, description, website, size, founded_year, followers, created_at FROM companies WHERE user_id = :userId OR name = (SELECT full_name FROM users WHERE user_id = :userId) LIMIT 1");
        $this->db->bind(':userId', $userId);
        $row = $this->db->single();
        if ($row) {
            return $row;
        }
        return $this->getPrimaryCompany();
    }

    public function getFollowSuggestions($userId, $limit = 4) {
        $this->db->query("SELECT company_id, name as company_name, industry, logo_path as logo, banner_path as banner, followers
                          FROM companies
                          WHERE company_id NOT IN (
                              SELECT company_id FROM company_followers WHERE user_id = :user_id
                          )
                          ORDER BY RAND()
                          LIMIT :limit");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', (int) $limit);
        return $this->db->resultSet();
    }

    public function updateCompanyDetails($id, $data) {
        $logoSql = !empty($data['logo_path']) ? ', logo_path = :logo_path' : '';
        $this->db->query("UPDATE companies SET description = :description, website = :website, industry = :industry, size = :size{$logoSql} WHERE company_id = :id");
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':website', $data['website'] ?? '');
        $this->db->bind(':industry', $data['industry'] ?? '');
        $this->db->bind(':size', $data['size'] ?? '');
        if (!empty($data['logo_path'])) {
            $this->db->bind(':logo_path', $data['logo_path']);
        }
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function createCompany($data) {
        $this->db->query("INSERT INTO companies (name, industry, description, website, size, founded_year, followers, user_id) VALUES (:name, :industry, :description, :website, :size, :founded_year, 0, :user_id)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':industry', $data['industry'] ?? 'Technology & Services');
        $this->db->bind(':description', $data['description'] ?? 'Pioneering innovative frameworks and scaling next-generation platforms.');
        $this->db->bind(':website', $data['website'] ?? 'https://company.pronetwork');
        $this->db->bind(':size', $data['size'] ?? '11-50 employees');
        $this->db->bind(':founded_year', date('Y'));
        $this->db->bind(':user_id', $data['user_id'] ?? null);
        return $this->db->execute();
    }

    public function isFollowing($companyId, $userId) {
        $this->db->query("SELECT id FROM company_followers WHERE company_id = :company_id AND user_id = :user_id");
        $this->db->bind(':company_id', $companyId);
        $this->db->bind(':user_id', $userId);
        return $this->db->single() ? true : false;
    }

    public function addFollower($companyId, $userId) {
        if ($this->isFollowing($companyId, $userId)) return false;
        
        $this->db->query("INSERT INTO company_followers (company_id, user_id) VALUES (:company_id, :user_id)");
        $this->db->bind(':company_id', $companyId);
        $this->db->bind(':user_id', $userId);
        
        if ($this->db->execute()) {
            $this->db->query("UPDATE companies SET followers = followers + 1 WHERE company_id = :company_id");
            $this->db->bind(':company_id', $companyId);
            return $this->db->execute();
        }
        return false;
    }

    public function removeFollower($companyId, $userId) {
        if (!$this->isFollowing($companyId, $userId)) return false;
        
        $this->db->query("DELETE FROM company_followers WHERE company_id = :company_id AND user_id = :user_id");
        $this->db->bind(':company_id', $companyId);
        $this->db->bind(':user_id', $userId);
        
        if ($this->db->execute()) {
            $this->db->query("UPDATE companies SET followers = followers - 1 WHERE company_id = :company_id");
            $this->db->bind(':company_id', $companyId);
            return $this->db->execute();
        }
        return false;
    }
}
