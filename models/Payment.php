<?php
/**
 * Payment Model
 * Quản lý thanh toán
 */

class Payment {
    private $pdo;
    private $table = 'payments';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create payment
     */
    public function create($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} (order_id, user_id, amount, payment_method, transaction_id, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );

        return $stmt->execute([
            $data['order_id'],
            $data['user_id'],
            $data['amount'],
            $data['payment_method'] ?? null,
            $data['transaction_id'] ?? null,
            $data['status'] ?? PAYMENT_PENDING
        ]);
    }

    /**
     * Get payment by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get payments by user
     */
    public function getByUserId($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, o.order_number FROM {$this->table} p 
             JOIN orders o ON p.order_id = o.id 
             WHERE p.user_id = ? ORDER BY p.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Update payment status
     */
    public function updateStatus($id, $status, $transactionId = null) {
        $query = "UPDATE {$this->table} SET status = ?";
        $params = [$status];

        if ($transactionId) {
            $query .= ", transaction_id = ?";
            $params[] = $transactionId;
        }

        if ($status === PAYMENT_SUCCESS) {
            $query .= ", paid_at = NOW()";
        }

        $query .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Get payment statistics
     */
    public function getStatistics() {
        $stmt = $this->pdo->prepare(
            "SELECT 
                COUNT(*) as total_payments,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as successful_payments,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed_payments,
                SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as total_revenue
             FROM {$this->table}"
        );
        $stmt->execute([PAYMENT_SUCCESS, PAYMENT_FAILED, PAYMENT_SUCCESS]);
        return $stmt->fetch();
    }
}
?>
