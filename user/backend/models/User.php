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

    public function findUserByEmailExceptId($email, $id) {
        $this->db->query("SELECT user_id FROM users WHERE email = :email AND user_id != :id LIMIT 1");
        $this->db->bind(':email', $email);
        $this->db->bind(':id', $id);
        return $this->db->single();
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

    public function verifyPassword($id, $password) {
        $this->db->query("SELECT password FROM users WHERE user_id = :id LIMIT 1");
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return $row && password_verify($password, $row['password']);
    }

    public function getUserById($id) {
        $this->db->query("SELECT {$this->safeUserColumns} FROM users WHERE user_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateProfile($id, $data) {
        $this->db->query("UPDATE users SET full_name = :full_name, headline = :headline, location = :location, industry = :industry, bio = :bio, phone = :phone, website = :website WHERE user_id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':headline', $data['headline']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':industry', $data['industry'] ?? null);
        $this->db->bind(':bio', $data['bio']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':website', $data['website'] ?? null);
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

    public function updateEmail($id, $email) {
        $this->db->query("UPDATE users SET email = :email WHERE user_id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }

    public function updatePassword($id, $passwordHash) {
        $this->db->query("UPDATE users SET password = :password WHERE user_id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':password', $passwordHash);
        return $this->db->execute();
    }

    private function ensurePasswordResetStorage() {
        $this->db->query("CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            otp VARCHAR(6) NOT NULL,
            token VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_otp (otp),
            INDEX idx_token (token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->db->execute();

        $this->db->query("SHOW COLUMNS FROM password_resets LIKE 'token'");
        $hasTokenColumn = $this->db->single();
        if (!$hasTokenColumn) {
            $this->db->query("ALTER TABLE password_resets ADD token VARCHAR(64) NOT NULL DEFAULT '' AFTER otp");
            $this->db->execute();
        }

        $this->db->query("SHOW INDEX FROM password_resets WHERE Key_name = 'idx_token'");
        $hasTokenIndex = $this->db->single();
        if (!$hasTokenIndex) {
            $this->db->query("ALTER TABLE password_resets ADD INDEX idx_token (token)");
            $this->db->execute();
        }
    }

    // Save password reset OTP and Token (deletes old one and inserts a new one)
    public function createPasswordReset($email, $otp, $token, $expiresAt) {
        $this->ensurePasswordResetStorage();

        // First delete any existing OTP for this email
        $this->db->query("DELETE FROM password_resets WHERE email = :email");
        $this->db->bind(':email', $email);
        $this->db->execute();

        // Insert new OTP and Token
        $this->db->query("INSERT INTO password_resets (email, otp, token, expires_at) VALUES (:email, :otp, :token, :expires_at)");
        $this->db->bind(':email', $email);
        $this->db->bind(':otp', $otp);
        $this->db->bind(':token', $token);
        $this->db->bind(':expires_at', $expiresAt);
        return $this->db->execute();
    }

    // Verify OTP exists and is not expired. Expiry is stored in PHP's UTC time.
    public function verifyOTP($email, $otp) {
        $this->db->query("SELECT * FROM password_resets WHERE email = :email AND otp = :otp AND expires_at > UTC_TIMESTAMP() LIMIT 1");
        $this->db->bind(':email', $email);
        $this->db->bind(':otp', $otp);
        $row = $this->db->single();
        return $row ? true : false;
    }

    // Verify Token exists and is not expired. Expiry is stored in PHP's UTC time.
    public function verifyToken($token) {
        $this->db->query("SELECT * FROM password_resets WHERE token = :token AND expires_at > UTC_TIMESTAMP() LIMIT 1");
        $this->db->bind(':token', $token);
        return $this->db->single();
    }

    // Delete OTP after successful use
    public function deleteOTP($email) {
        $this->db->query("DELETE FROM password_resets WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }

    public function reportUser($data) {
        $this->db->query("INSERT INTO reports (reporter_id, target_type, target_id, reason, status)
                          VALUES (:reporter_id, 'User', :target_id, :reason, 'Pending')");
        $this->db->bind(':reporter_id', $data['reporter_id']);
        $this->db->bind(':target_id', $data['target_id']);
        $this->db->bind(':reason', $data['reason']);
        return $this->db->execute();
    }
}
