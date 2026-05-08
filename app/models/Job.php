<?php
class Job extends Model {
    public function getJobs() {
        $this->db->query("SELECT j.*, c.name as company_name, c.logo_path as logo FROM jobs j JOIN companies c ON j.company_id = c.company_id ORDER BY j.posted_at DESC");
        return $this->db->resultSet();
    }

    public function getJobById($id) {
        $this->db->query("SELECT j.*, c.name as company_name, c.logo_path as logo, c.description as company_description FROM jobs j JOIN companies c ON j.company_id = c.company_id WHERE j.job_id = :job_id");
        $this->db->bind(':job_id', $id);
        return $this->db->single();
    }

    public function searchJobs($query) {
        $this->db->query("SELECT j.*, c.name as company_name, c.logo_path as logo FROM jobs j JOIN companies c ON j.company_id = c.company_id 
                          WHERE j.title LIKE :query OR j.location LIKE :query OR c.name LIKE :query
                          ORDER BY j.posted_at DESC");
        $this->db->bind(':query', '%' . $query . '%');
        return $this->db->resultSet();
    }
}
