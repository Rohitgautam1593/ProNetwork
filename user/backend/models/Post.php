<?php
class Post extends Model {
    public function getPosts($userId = null) {
        $viewer = (int) ($userId ?? 0);
        $this->db->query("SELECT p.*, u.full_name, u.role as user_role, u.profile_pic, 
                          (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.post_id) as reaction_count,
                          (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id) as comment_count,
                          (SELECT COUNT(*) FROM post_reactions pr WHERE pr.post_id = p.post_id AND pr.user_id = :viewer_id) as user_has_liked
                          FROM posts p 
                          JOIN users u ON p.user_id = u.user_id 
                          ORDER BY p.created_at DESC");
        $this->db->bind(':viewer_id', $viewer);
        $posts = $this->db->resultSet();

        if ($userId) {
            $posts = array_merge($posts, $this->getFollowedCompanyActivity((int) $userId));
            usort($posts, function ($a, $b) {
                return strtotime($b['created_at'] ?? 'now') <=> strtotime($a['created_at'] ?? 'now');
            });
        }

        return $posts;
    }

    private function getFollowedCompanyActivity($userId) {
        $this->db->query("SELECT j.job_id, j.title, j.location, j.job_type, j.posted_at, c.company_id,
                                 c.name as company_name, c.industry, c.logo_path as logo, c.banner_path as banner
                          FROM company_followers f
                          JOIN companies c ON c.company_id = f.company_id
                          JOIN jobs j ON j.company_id = c.company_id
                          WHERE f.user_id = :user_id
                          ORDER BY RAND()
                          LIMIT 4");
        $this->db->bind(':user_id', $userId);
        $jobs = $this->db->resultSet();

        $activity = [];
        foreach ($jobs as $job) {
            $activity[] = [
                'post_id' => 'company-job-' . $job['job_id'],
                'created_at' => $job['posted_at'],
                'is_company_activity' => true,
                'activity_type' => 'job',
                'job_id' => $job['job_id'],
                'company_id' => $job['company_id'],
                'company_name' => $job['company_name'],
                'full_name' => $job['company_name'],
                'user_role' => 'Company Page',
                'logo' => $job['logo'],
                'banner' => $job['banner'],
                'title' => $job['title'],
                'location' => $job['location'],
                'job_type' => $job['job_type'],
                'content' => $job['company_name'] . ' is hiring for ' . $job['title'] . '.'
            ];
        }

        $this->db->query("SELECT c.company_id, c.name as company_name, c.industry, c.description,
                                 c.logo_path as logo, c.banner_path as banner, c.created_at
                          FROM company_followers f
                          JOIN companies c ON c.company_id = f.company_id
                          WHERE f.user_id = :user_id
                          ORDER BY RAND()
                          LIMIT 3");
        $this->db->bind(':user_id', $userId);
        foreach ($this->db->resultSet() as $company) {
            $activity[] = [
                'post_id' => 'company-post-' . $company['company_id'],
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . random_int(1, 72) . ' hours')),
                'is_company_activity' => true,
                'activity_type' => 'update',
                'company_id' => $company['company_id'],
                'company_name' => $company['company_name'],
                'full_name' => $company['company_name'],
                'user_role' => 'Company Page',
                'logo' => $company['logo'],
                'banner' => $company['banner'],
                'title' => $company['industry'] ?: 'Company update',
                'content' => $company['description'] ?: ($company['company_name'] . ' shared a new company update.')
            ];
        }

        shuffle($activity);
        return $activity;
    }

    public function addPost($data) {
        $this->db->query("INSERT INTO posts (user_id, content, post_image) VALUES (:user_id, :content, :post_image)");
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':post_image', $data['post_image'] ?? null);

        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function getPostById($id, $viewerId = null) {
        $viewer = (int) ($viewerId ?? 0);
        $this->db->query("SELECT p.*, u.full_name, u.role as user_role, u.profile_pic,
                          (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.post_id) as reaction_count,
                          (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id) as comment_count,
                          (SELECT COUNT(*) FROM post_reactions pr WHERE pr.post_id = p.post_id AND pr.user_id = :viewer_id) as user_has_liked
                          FROM posts p JOIN users u ON p.user_id = u.user_id WHERE p.post_id = :post_id");
        $this->db->bind(':post_id', $id);
        $this->db->bind(':viewer_id', $viewer);
        return $this->db->single();
    }

    public function addReaction($post_id, $user_id, $type = 'like') {
        // Check if already reacted
        $this->db->query("SELECT * FROM post_reactions WHERE post_id = :post_id AND user_id = :user_id");
        $this->db->bind(':post_id', $post_id);
        $this->db->bind(':user_id', $user_id);
        $row = $this->db->single();

        if ($row) {
            $this->db->query("DELETE FROM post_reactions WHERE reaction_id = :id");
            $this->db->bind(':id', $row['reaction_id']);
        } else {
            $this->db->query("INSERT INTO post_reactions (post_id, user_id, type) VALUES (:post_id, :user_id, :type)");
            $this->db->bind(':post_id', $post_id);
            $this->db->bind(':user_id', $user_id);
            $this->db->bind(':type', $type);
        }
        return $this->db->execute();
    }

    public function getReactionCount($post_id) {
        $this->db->query("SELECT COUNT(*) as count FROM post_reactions WHERE post_id = :post_id");
        $this->db->bind(':post_id', $post_id);
        $res = $this->db->single();
        return $res ? $res['count'] : 0;
    }

    public function reportPost($data) {
        $this->db->query("INSERT INTO reports (reporter_id, target_type, target_id, reason, status) VALUES (:reporter_id, 'Post', :target_id, :reason, 'Pending')");
        $this->db->bind(':reporter_id', $data['reporter_id']);
        $this->db->bind(':target_id', $data['target_id']);
        $this->db->bind(':reason', $data['reason']);
        return $this->db->execute();
    }

    public function getCommentsForPost($post_id) {
        $this->db->query(
            "SELECT c.comment_id, c.post_id, c.user_id, c.content, c.created_at, u.full_name, u.profile_pic, u.role as user_role, p.user_id as post_owner_id
             FROM comments c
             JOIN users u ON c.user_id = u.user_id
             JOIN posts p ON c.post_id = p.post_id
             WHERE c.post_id = :post_id
             ORDER BY c.created_at ASC"
        );
        $this->db->bind(':post_id', $post_id);
        return $this->db->resultSet();
    }

    public function getReactionsForPost($post_id) {
        $this->db->query(
            "SELECT r.reaction_id, r.user_id, r.type, r.created_at, u.full_name, u.profile_pic, u.headline, u.role as user_role
             FROM post_reactions r
             JOIN users u ON r.user_id = u.user_id
             WHERE r.post_id = :post_id
             ORDER BY r.created_at DESC"
        );
        $this->db->bind(':post_id', $post_id);
        return $this->db->resultSet();
    }

    public function getCommentCount($post_id) {
        $this->db->query("SELECT COUNT(*) as cnt FROM comments WHERE post_id = :post_id");
        $this->db->bind(':post_id', $post_id);
        $row = $this->db->single();
        return $row ? (int) $row['cnt'] : 0;
    }

    public function addComment($post_id, $user_id, $content) {
        $this->db->query("INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)");
        $this->db->bind(':post_id', $post_id);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':content', $content);
        if ($this->db->execute()) {
            return (int) $this->db->lastInsertId();
        }
        return false;
    }

    public function getCommentById($comment_id) {
        $this->db->query(
            "SELECT c.comment_id, c.post_id, c.user_id, c.content, c.created_at, u.full_name, u.profile_pic, u.role as user_role, p.user_id as post_owner_id
             FROM comments c
             JOIN users u ON c.user_id = u.user_id
             JOIN posts p ON c.post_id = p.post_id
             WHERE c.comment_id = :id"
        );
        $this->db->bind(':id', $comment_id);
        return $this->db->single();
    }

    public function deleteComment($comment_id) {
        $this->db->query("DELETE FROM comments WHERE comment_id = :id");
        $this->db->bind(':id', $comment_id);
        return $this->db->execute();
    }

    public function getDynamicTrendingTopics() {
        $this->db->query("SELECT p.post_id, p.content, p.created_at,
                                 (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.post_id) +
                                 (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) as score
                          FROM posts p
                          ORDER BY score DESC, p.created_at DESC
                          LIMIT 10");
        $results = $this->db->resultSet();
        $trending = [];
        
        foreach ($results as $row) {
            $content = strtolower($row['content']);
            $topic = "Tech & Product Strategy";
            if (strpos($content, 'dashboard') !== false || strpos($content, 'tailwind') !== false) $topic = "Enterprise Dashboards";
            elseif (strpos($content, 'design') !== false || strpos($content, 'typography') !== false) $topic = "Design Systems Frameworks";
            elseif (strpos($content, 'database') !== false || strpos($content, 'serverless') !== false) $topic = "Distributed Databases scaling";
            elseif (strpos($content, 'hiring') !== false || strpos($content, 'mvc') !== false) $topic = "Core Engineering scaling";
            elseif (strpos($content, 'compute') !== false || strpos($content, 'grid') !== false) $topic = "Cloud Compute Optimization";
            elseif (strpos($content, 'deep learning') !== false || strpos($content, 'tensor') !== false) $topic = "Deep Learning Matrix Ops";
            elseif (strpos($content, 'api') !== false || strpos($content, 'jwt') !== false) $topic = "Enterprise API Cryptography";
            
            $exists = false;
            foreach ($trending as $t) {
                if ($t['title'] === $topic) { $exists = true; break; }
            }
            if (!$exists) {
                $readers = ($row['score'] * 450) + random_int(3100, 18400);
                $trending[] = [
                    'title' => $topic,
                    'readers' => number_format($readers) . ' readers',
                    'time' => 'Trending activity',
                    'post_id' => $row['post_id']
                ];
            }
        }
        
        if (count($trending) < 3) {
            $trending[] = ['title' => 'Remote Work Innovation', 'readers' => '12,430 readers', 'time' => 'Recently active', 'post_id' => 1];
            $trending[] = ['title' => 'Scalable UI Architectures', 'readers' => '8,920 readers', 'time' => 'Recently active', 'post_id' => 2];
        }
        
        return array_slice($trending, 0, 4);
    }

    public function getDynamicRecents() {
        $this->db->query("SELECT post_id, content FROM posts ORDER BY created_at DESC LIMIT 12");
        $results = $this->db->resultSet();
        $recents = [];
        
        foreach ($results as $row) {
            $c = strtolower($row['content']);
            $title = '';
            $icon = 'tag';
            if (strpos($c, 'mvc') !== false) { $title = 'PHP MVC Scaling'; $icon = 'group'; }
            elseif (strpos($c, 'summit') !== false || strpos($c, 'event') !== false) { $title = 'Global Tech Summit'; $icon = 'event'; }
            elseif (strpos($c, 'tailwind') !== false) { $title = '#TailwindCSS'; $icon = 'tag'; }
            elseif (strpos($c, 'database') !== false) { $title = 'DB Replication Test'; $icon = 'article'; }
            elseif (strpos($c, 'deep learning') !== false) { $title = 'AI Models Pod'; $icon = 'lightbulb'; }
            elseif (strpos($c, 'api') !== false) { $title = 'API Secure Group'; $icon = 'group'; }
            
            if ($title && !isset($recents[$title])) {
                $recents[$title] = ['title' => $title, 'icon' => $icon, 'post_id' => $row['post_id']];
            }
        }
        
        if (count($recents) < 3) {
            $recents['UX Patterns'] = ['title' => 'UX Design Patterns', 'icon' => 'group', 'post_id' => 1];
            $recents['Leadership Pod'] = ['title' => '#Leadership2026', 'icon' => 'tag', 'post_id' => 2];
            $recents['DevOps Sync'] = ['title' => 'Cloud Scalability sync', 'icon' => 'event', 'post_id' => 3];
        }
        
        return array_values(array_slice($recents, 0, 4));
    }
}
