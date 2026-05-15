<?php
class Experience extends Model {
    public function getExperienceByUserId($user_id) {
        $this->db->query("SELECT * FROM user_experience WHERE user_id = :user_id ORDER BY start_date DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function addExperience($data) {
        $this->db->query("INSERT INTO user_experience (user_id, job_title, company, emp_type, start_date, end_date, is_current, location, description) 
                          VALUES (:user_id, :job_title, :company, :emp_type, :start_date, :end_date, :is_current, :location, :description)");
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':job_title', $data['job_title']);
        $this->db->bind(':company', $data['company']);
        $this->db->bind(':emp_type', $data['emp_type'] ?? 'Full-time');
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date'] ?? null);
        $this->db->bind(':is_current', $data['is_current'] ?? 0);
        $this->db->bind(':location', $data['location'] ?? '');
        $this->db->bind(':description', $data['description'] ?? '');
        return $this->db->execute();
    }

    public function getExperienceByIdForUser($exp_id, $user_id) {
        $this->db->query("SELECT * FROM user_experience WHERE exp_id = :exp_id AND user_id = :user_id LIMIT 1");
        $this->db->bind(':exp_id', $exp_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function updateExperience($exp_id, $user_id, $data) {
        $this->db->query("UPDATE user_experience
                          SET job_title = :job_title,
                              company = :company,
                              emp_type = :emp_type,
                              start_date = :start_date,
                              end_date = :end_date,
                              is_current = :is_current,
                              location = :location,
                              description = :description
                          WHERE exp_id = :exp_id AND user_id = :user_id");
        $this->db->bind(':exp_id', $exp_id);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':job_title', $data['job_title']);
        $this->db->bind(':company', $data['company']);
        $this->db->bind(':emp_type', $data['emp_type'] ?? 'Full-time');
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', !empty($data['is_current']) ? null : ($data['end_date'] ?? null));
        $this->db->bind(':is_current', !empty($data['is_current']) ? 1 : 0);
        $this->db->bind(':location', $data['location'] ?? '');
        $this->db->bind(':description', $data['description'] ?? '');
        return $this->db->execute();
    }

    public function deleteExperience($exp_id, $user_id) {
        $this->db->query("DELETE FROM user_experience WHERE exp_id = :exp_id AND user_id = :user_id");
        $this->db->bind(':exp_id', $exp_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }
}
