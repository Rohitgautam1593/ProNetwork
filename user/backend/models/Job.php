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

    public function reportJob($data) {
        $this->db->query("INSERT INTO reports (reporter_id, target_type, target_id, reason, status) VALUES (:reporter_id, 'Job', :target_id, :reason, 'Pending')");
        $this->db->bind(':reporter_id', $data['reporter_id']);
        $this->db->bind(':target_id', $data['target_id']);
        $this->db->bind(':reason', $data['reason']);
        return $this->db->execute();
    }

    public function hasApplied($job_id, $user_id) {
        $this->db->query("SELECT application_id FROM applications WHERE job_id = :job_id AND user_id = :user_id");
        $this->db->bind(':job_id', $job_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->single() !== false;
    }

    public function applyJob($data) {
        $this->db->query("INSERT INTO applications (job_id, user_id, first_name, last_name, phone, resume_path, cover_letter, status) 
                          VALUES (:job_id, :user_id, :first_name, :last_name, :phone, :resume_path, :cover_letter, 'Pending')");
        $this->db->bind(':job_id', $data['job_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':first_name', $data['first_name']);
        $this->db->bind(':last_name', $data['last_name']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':resume_path', $data['resume_path']);
        $this->db->bind(':cover_letter', $data['cover_letter'] ?? null);
        return $this->db->execute();
    }

    public function incrementApplicantCount($job_id) {
        $this->db->query("UPDATE jobs SET applicant_count = applicant_count + 1 WHERE job_id = :job_id");
        $this->db->bind(':job_id', $job_id);
        return $this->db->execute();
    }

    public function checkAndCloseJobLimit($job_id) {
        $job = $this->getJobById($job_id);
        if ($job && !empty($job['applicant_limit'])) {
            if ((int)$job['applicant_count'] >= (int)$job['applicant_limit']) {
                $this->db->query("UPDATE jobs SET status = 'Closed' WHERE job_id = :job_id");
                $this->db->bind(':job_id', $job_id);
                $this->db->execute();
            }
        }
    }

    public function getJobsByCompany($company_id) {
        $this->db->query("SELECT j.*, c.name as company_name, c.logo_path as logo FROM jobs j JOIN companies c ON j.company_id = c.company_id WHERE j.company_id = :company_id ORDER BY j.posted_at DESC");
        $this->db->bind(':company_id', $company_id);
        return $this->db->resultSet();
    }

    public function getApplicantsForJob($job_id) {
        $this->db->query("SELECT a.*, u.email, u.profile_pic FROM applications a JOIN users u ON a.user_id = u.user_id WHERE a.job_id = :job_id ORDER BY a.applied_at DESC");
        $this->db->bind(':job_id', $job_id);
        return $this->db->resultSet();
    }

    public function updateJobSettings($job_id, $status, $limit) {
        $this->db->query("UPDATE jobs SET status = :status, applicant_limit = :limit WHERE job_id = :job_id");
        $this->db->bind(':status', $status);
        $this->db->bind(':limit', $limit === '' || $limit === null ? null : (int)$limit);
        $this->db->bind(':job_id', $job_id);
        return $this->db->execute();
    }

    public function addJob($data) {
        $this->db->query("INSERT INTO jobs (company_id, title, description, location, salary_range, job_type, experience_level, easy_apply, applicant_count, applicant_limit, status) 
                          VALUES (:company_id, :title, :description, :location, :salary_range, :job_type, :experience_level, 1, 0, :applicant_limit, 'Live')");
        $this->db->bind(':company_id', $data['company_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':salary_range', $data['salary_range']);
        $this->db->bind(':job_type', $data['job_type']);
        $this->db->bind(':experience_level', $data['experience_level']);
        $this->db->bind(':applicant_limit', $data['applicant_limit'] === '' || $data['applicant_limit'] === null ? null : (int)$data['applicant_limit']);
        return $this->db->execute();
    }
}
