<?php
class Search extends Model {
    public function globalSearch($term) {
        $query = '%' . $term . '%';

        $this->db->query("SELECT user_id, full_name, headline, location, profile_pic
                          FROM users
                          WHERE full_name LIKE :query OR headline LIKE :query OR location LIKE :query OR industry LIKE :query
                          ORDER BY full_name ASC
                          LIMIT 6");
        $this->db->bind(':query', $query);
        $people = $this->db->resultSet();

        $this->db->query("SELECT j.job_id, j.title, j.location, j.job_type, c.name as company_name, c.logo_path as logo
                          FROM jobs j
                          JOIN companies c ON j.company_id = c.company_id
                          WHERE j.status = 'Live'
                          AND (j.title LIKE :query OR j.location LIKE :query OR j.description LIKE :query OR c.name LIKE :query)
                          ORDER BY j.posted_at DESC
                          LIMIT 5");
        $this->db->bind(':query', $query);
        $jobs = $this->db->resultSet();

        $this->db->query("SELECT company_id, name as company_name, industry, logo_path as logo
                          FROM companies
                          WHERE name LIKE :query OR industry LIKE :query OR description LIKE :query
                          ORDER BY name ASC
                          LIMIT 5");
        $this->db->bind(':query', $query);
        $companies = $this->db->resultSet();

        $this->db->query("SELECT p.post_id, p.content, p.created_at, u.full_name
                          FROM posts p
                          JOIN users u ON p.user_id = u.user_id
                          WHERE p.content LIKE :query OR u.full_name LIKE :query
                          ORDER BY p.created_at DESC
                          LIMIT 5");
        $this->db->bind(':query', $query);
        $posts = $this->db->resultSet();

        return [
            'people' => $people,
            'jobs' => $jobs,
            'companies' => $companies,
            'posts' => $posts
        ];
    }
}
