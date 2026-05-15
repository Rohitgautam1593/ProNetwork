<?php
class UserSettings extends Model {
    private const DEFAULTS = [
        'theme' => 'light',
        'language' => 'English (US)',
        'content_language' => 'English',
        'autoplay_videos' => '1',
        'profile_visibility' => 'public',
        'show_email' => '0',
        'show_phone' => '0',
        'allow_messages' => 'connections',
        'data_personalization' => '1',
        'search_visibility' => '1',
        'demographics' => '',
        'verification_note' => ''
    ];

    public function __construct() {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable() {
        $this->db->query("CREATE TABLE IF NOT EXISTS user_settings (
            setting_id INT NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            setting_key VARCHAR(80) NOT NULL,
            setting_value TEXT NULL,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (setting_id),
            UNIQUE KEY uk_user_setting (user_id, setting_key),
            CONSTRAINT fk_user_settings_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->db->execute();
    }

    public function getSettings($userId) {
        $settings = self::DEFAULTS;
        $this->db->query("SELECT setting_key, setting_value FROM user_settings WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        foreach ($this->db->resultSet() as $row) {
            if (array_key_exists($row['setting_key'], self::DEFAULTS)) {
                $settings[$row['setting_key']] = (string) $row['setting_value'];
            }
        }
        return $settings;
    }

    public function saveSettings($userId, array $settings) {
        $allowed = array_keys(self::DEFAULTS);
        foreach ($settings as $key => $value) {
            if (!in_array($key, $allowed, true)) {
                continue;
            }
            $this->db->query("INSERT INTO user_settings (user_id, setting_key, setting_value)
                              VALUES (:user_id, :setting_key, :setting_value)
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':setting_key', $key);
            $this->db->bind(':setting_value', is_bool($value) ? ($value ? '1' : '0') : (string) $value);
            $this->db->execute();
        }
        return true;
    }
}
