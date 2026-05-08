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
}
