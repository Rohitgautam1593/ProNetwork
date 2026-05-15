<?php
class Education extends Model {
    public function getEducationByUserId($user_id) {
        $this->db->query("SELECT * FROM user_education WHERE user_id = :user_id ORDER BY start_year DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function addEducation($data) {
        $this->db->query("INSERT INTO user_education (user_id, institution, degree, field, start_year, end_year, description) 
                          VALUES (:user_id, :institution, :degree, :field, :start_year, :end_year, :description)");
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':institution', $data['institution']);
        $this->db->bind(':degree', $data['degree'] ?? '');
        $this->db->bind(':field', $data['field'] ?? '');
        $this->db->bind(':start_year', $data['start_year'] ?? null);
        $this->db->bind(':end_year', $data['end_year'] ?? null);
        $this->db->bind(':description', $data['description'] ?? '');
        return $this->db->execute();
    }

    public function getEducationByIdForUser($edu_id, $user_id) {
        $this->db->query("SELECT * FROM user_education WHERE edu_id = :edu_id AND user_id = :user_id LIMIT 1");
        $this->db->bind(':edu_id', $edu_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function updateEducation($edu_id, $user_id, $data) {
        $this->db->query("UPDATE user_education
                          SET institution = :institution,
                              degree = :degree,
                              field = :field,
                              start_year = :start_year,
                              end_year = :end_year,
                              description = :description
                          WHERE edu_id = :edu_id AND user_id = :user_id");
        $this->db->bind(':edu_id', $edu_id);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':institution', $data['institution']);
        $this->db->bind(':degree', $data['degree'] ?? '');
        $this->db->bind(':field', $data['field'] ?? '');
        $this->db->bind(':start_year', $data['start_year'] ?? null);
        $this->db->bind(':end_year', $data['end_year'] ?? null);
        $this->db->bind(':description', $data['description'] ?? '');
        return $this->db->execute();
    }

    public function deleteEducation($edu_id, $user_id) {
        $this->db->query("DELETE FROM user_education WHERE edu_id = :edu_id AND user_id = :user_id");
        $this->db->bind(':edu_id', $edu_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }
}
