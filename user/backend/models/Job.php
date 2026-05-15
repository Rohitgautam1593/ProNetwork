<?php
class Job extends Model {
    public function getJobs($filters = []) {
        $sql = "SELECT j.*, c.name as company_name, c.logo_path as logo, c.banner_path as banner
                FROM jobs j JOIN companies c ON j.company_id = c.company_id WHERE 1=1";
        $params = [];

        if (!empty($filters['q'])) {
            $sql .= " AND (j.title LIKE :q OR j.location LIKE :q OR c.name LIKE :q OR j.description LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['job_type'])) {
            $sql .= " AND j.job_type = :job_type";
            $params[':job_type'] = $filters['job_type'];
        }
        if (!empty($filters['with_salary'])) {
            $sql .= " AND j.salary_range IS NOT NULL AND TRIM(j.salary_range) <> ''";
        }
        if (!empty($filters['job_ids']) && is_array($filters['job_ids'])) {
            $ids = array_values(array_filter(array_map('intval', $filters['job_ids'])));
            if ($ids) {
                $placeholders = [];
                foreach ($ids as $i => $id) {
                    $key = ':jid' . $i;
                    $placeholders[] = $key;
                    $params[$key] = $id;
                }
                $sql .= ' AND j.job_id IN (' . implode(',', $placeholders) . ')';
            } else {
                return [];
            }
        }
        if (!empty($filters['applied_user_id'])) {
            $sql .= " AND j.job_id IN (SELECT job_id FROM applications WHERE user_id = :applied_user_id)";
            $params[':applied_user_id'] = (int)$filters['applied_user_id'];
        }

        $sql .= " ORDER BY j.posted_at DESC";
        $this->db->query($sql);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        return $this->db->resultSet();
    }

    public function getMyApplications($user_id) {
        $this->db->query("SELECT a.*, j.title, j.location, j.job_type, j.status as job_status, j.posted_at,
                          c.name as company_name, c.logo_path as logo, c.company_id
                          FROM applications a
                          JOIN jobs j ON a.job_id = j.job_id
                          JOIN companies c ON j.company_id = c.company_id
                          WHERE a.user_id = :user_id
                          ORDER BY a.applied_at DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function getJobById($id) {
        $this->db->query("SELECT j.*, c.name as company_name, c.logo_path as logo, c.banner_path as banner, c.description as company_description FROM jobs j JOIN companies c ON j.company_id = c.company_id WHERE j.job_id = :job_id");
        $this->db->bind(':job_id', $id);
        return $this->db->single();
    }

    public function searchJobs($query) {
        $this->db->query("SELECT j.*, c.name as company_name, c.logo_path as logo, c.banner_path as banner FROM jobs j JOIN companies c ON j.company_id = c.company_id 
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
        $this->db->query("SELECT j.*, c.name as company_name, c.logo_path as logo, c.banner_path as banner FROM jobs j JOIN companies c ON j.company_id = c.company_id WHERE j.company_id = :company_id ORDER BY j.posted_at DESC");
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
