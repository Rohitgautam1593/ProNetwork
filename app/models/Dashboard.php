<?php
class Dashboard extends Model {
    public function getAdminStats() {
        $stats = [];
        $tables = [
            'users' => 'users',
            'posts' => 'posts',
            'connections' => 'connections',
            'messages' => 'messages',
            'jobs' => 'jobs',
            'companies' => 'companies'
        ];

        foreach ($tables as $key => $table) {
            $this->db->query("SELECT COUNT(*) as count FROM {$table}");
            $row = $this->db->single();
            $stats[$key] = $row ? (int)$row['count'] : 0;
        }

        // Add Unread Counts
        $this->db->query("SELECT COUNT(*) as count FROM reports WHERE status = 'Pending'");
        $stats['unread_reports'] = (int)$this->db->single()['count'];

        $this->db->query("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0 AND user_id = :user_id");
        $this->db->bind(':user_id', $_SESSION['user_id']);
        $stats['unread_notifications'] = (int)$this->db->single()['count'];

        return $stats;
    }
}
