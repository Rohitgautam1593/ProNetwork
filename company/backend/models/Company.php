<?php
class Company extends Model {
    public function getCompanyById($id) {
        $this->db->query("SELECT company_id, name as company_name, industry, logo_path as logo, description, website, size, founded_year, followers, created_at FROM companies WHERE company_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getCompanies() {
        $this->db->query("SELECT company_id, name as company_name, industry, logo_path as logo, description, website, size, founded_year, followers, created_at FROM companies ORDER BY name ASC");
        return $this->db->resultSet();
    }

    public function getPrimaryCompany() {
        $this->db->query("SELECT company_id, name as company_name, industry, logo_path as logo, description, website, size, founded_year, followers, created_at FROM companies ORDER BY company_id ASC LIMIT 1");
        return $this->db->single();
    }

    public function getCompanyForUser($userId) {
        $this->db->query("SELECT company_id, name as company_name, industry, logo_path as logo, description, website, size, founded_year, followers, created_at FROM companies WHERE name = (SELECT full_name FROM users WHERE user_id = :userId) LIMIT 1");
        $this->db->bind(':userId', $userId);
        $row = $this->db->single();
        if ($row) {
            return $row;
        }
        return $this->getPrimaryCompany();
    }

    public function updateCompanyDetails($id, $data) {
        $this->db->query("UPDATE companies SET description = :description, website = :website, industry = :industry, size = :size WHERE company_id = :id");
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':website', $data['website'] ?? '');
        $this->db->bind(':industry', $data['industry'] ?? '');
        $this->db->bind(':size', $data['size'] ?? '');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function createCompany($data) {
        $this->db->query("INSERT INTO companies (name, industry, description, website, size, founded_year, followers) VALUES (:name, :industry, :description, :website, :size, :founded_year, 0)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':industry', $data['industry'] ?? 'Technology & Services');
        $this->db->bind(':description', $data['description'] ?? 'Pioneering innovative frameworks and scaling next-generation platforms.');
        $this->db->bind(':website', $data['website'] ?? 'https://company.pronetwork');
        $this->db->bind(':size', $data['size'] ?? '11-50 employees');
        $this->db->bind(':founded_year', date('Y'));
        return $this->db->execute();
    }
}
