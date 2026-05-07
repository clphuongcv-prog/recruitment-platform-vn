<?php
/**
 * Notification Model
 * Quản lý thông báo
 */

class Notification {
    private $pdo;
    private $table = 'notifications';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create notification
     */
    public function create($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} (user_id, title, message, type, link, created_at) 
             VALUES (?, ?, ?, ?, ?, NOW())"
        );

        return $stmt->execute([
            $data['user_id'],
            $data['title'],
            $data['message'],
            $data['type'] ?? 'general',
            $data['link'] ?? null
        ]);
    }

    /**
     * Get notifications for user
     */
    public function getByUserId($userId, $limit = 10) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get unread notifications
     */
    public function getUnread($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Mark as read
     */
    public function markAsRead($id) {
        $stmt = $this->pdo->prepare(
            "UPDATE {$this->table} SET is_read = 1 WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead($userId) {
        $stmt = $this->pdo->prepare(
            "UPDATE {$this->table} SET is_read = 1 WHERE user_id = ?"
        );
        return $stmt->execute([$userId]);
    }

    /**
     * Count unread
     */
    public function countUnread($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND is_read = 0"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'];
    }
}
?>
