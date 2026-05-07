<?php
/**
 * Review Model
 * Quản lý đánh giá công ty
 */

class Review {
    private $pdo;
    private $table = 'reviews';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get review by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT r.*, u.name as user_name FROM {$this->table} r 
             JOIN users u ON r.user_id = u.id 
             WHERE r.id = ? AND r.deleted_at IS NULL"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get reviews by company
     */
    public function getByCompanyId($companyId, $approved_only = true) {
        $query = "SELECT r.*, u.name as user_name FROM {$this->table} r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.company_id = ? AND r.deleted_at IS NULL";
        
        if ($approved_only) {
            $query .= " AND r.is_approved = 1";
        }
        
        $query .= " ORDER BY r.created_at DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll();
    }

    /**
     * Create review
     */
    public function create($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} (company_id, user_id, rating, title, content, interview_experience, work_environment, management_style, salary_level, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );

        return $stmt->execute([
            $data['company_id'],
            $data['user_id'],
            $data['rating'],
            $data['title'] ?? null,
            $data['content'] ?? null,
            $data['interview_experience'] ?? null,
            $data['work_environment'] ?? null,
            $data['management_style'] ?? null,
            $data['salary_level'] ?? null
        ]);
    }

    /**
     * Get average rating for company
     */
    public function getAverageRating($companyId) {
        $stmt = $this->pdo->prepare(
            "SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews FROM {$this->table} 
             WHERE company_id = ? AND is_approved = 1 AND deleted_at IS NULL"
        );
        $stmt->execute([$companyId]);
        return $stmt->fetch();
    }

    /**
     * Get pending reviews for approval
     */
    public function getPending() {
        $stmt = $this->pdo->prepare(
            "SELECT r.*, u.name as user_name, c.name as company_name FROM {$this->table} r 
             JOIN users u ON r.user_id = u.id 
             JOIN companies c ON r.company_id = c.id 
             WHERE r.is_approved = 0 AND r.deleted_at IS NULL 
             ORDER BY r.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Approve review
     */
    public function approve($id) {
        $stmt = $this->pdo->prepare(
            "UPDATE {$this->table} SET is_approved = 1, updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Reject/Delete review
     */
    public function reject($id) {
        $stmt = $this->pdo->prepare(
            "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Check if user already reviewed
     */
    public function hasReviewed($userId, $companyId) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND company_id = ? AND deleted_at IS NULL"
        );
        $stmt->execute([$userId, $companyId]);
        return $stmt->fetch()['count'] > 0;
    }
}
?>
