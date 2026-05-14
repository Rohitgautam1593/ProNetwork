<?php
class User extends Model {
    private $safeUserColumns = "user_id, full_name, email, role, headline, location, industry, bio, phone, website, profile_pic, cover_image, is_admin, status, created_at";

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check row
        if($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Register user
    public function register($data) {
        $this->db->query("INSERT INTO users (full_name, email, password, role, headline, location, bio) VALUES (:full_name, :email, :password, :role, :headline, :location, :bio)");
        
        // Bind values
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':headline', $data['headline']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':bio', $data['bio']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Login user
    public function login($email, $password) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if ($row) {
            $hashed_password = $row['password'];
            if(password_verify($password, $hashed_password)) {
                unset($row['password']);
                return $row;
            } else {
                return false;
            }
        }
        return false;
    }

    public function getUserById($id) {
        $this->db->query("SELECT {$this->safeUserColumns} FROM users WHERE user_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateProfile($id, $data) {
        $this->db->query("UPDATE users SET full_name = :full_name, headline = :headline, location = :location, industry = :industry, bio = :bio WHERE user_id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':headline', $data['headline']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':industry', $data['industry'] ?? null);
        $this->db->bind(':bio', $data['bio']);
        return $this->db->execute();
    }
    public function updateProfilePic($id, $fileName) {
        $this->db->query("UPDATE users SET profile_pic = :profile_pic WHERE user_id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':profile_pic', $fileName);
        return $this->db->execute();
    }
    public function updateCoverImage($id, $fileName) {
        $this->db->query("UPDATE users SET cover_image = :cover_image WHERE user_id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':cover_image', $fileName);
        return $this->db->execute();
    }
}
