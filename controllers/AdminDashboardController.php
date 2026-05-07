<?php
/**
 * Admin Dashboard Controller
 * Quản lý admin
 */

class AdminDashboardController {
    private $pdo;
    private $user;
    private $company;
    private $job;
    private $application;
    private $payment;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->user = new User($pdo);
        $this->company = new Company($pdo);
        $this->job = new Job($pdo);
        $this->application = new Application($pdo);
        $this->payment = new Payment($pdo);
    }

    /**
     * Show dashboard
     */
    public function dashboard() {
        // Get statistics
        $totalUsers = $this->user->countTotal();
        $totalCompanies = $this->company->countTotal();
        $totalJobs = $this->job->countTotal();
        
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as count FROM applications');
        $stmt->execute();
        $totalApplications = $stmt->fetch()['count'];

        $stmt = $this->pdo->prepare('SELECT COUNT(*) as count FROM orders WHERE status = ?');
        $stmt->execute([PAYMENT_SUCCESS]);
        $totalOrders = $stmt->fetch()['count'];

        $paymentStats = $this->payment->getStatistics();
        $totalRevenue = $paymentStats['total_revenue'] ?? 0;

        // Get recent data
        $recentUsers = $this->user->getAll(10);
        $recentCompanies = $this->company->getAll(10);
        $recentJobs = $this->job->getActive(10);

        include __DIR__ . '/../admin/views/dashboard.php';
    }

    /**
     * Manage users
     */
    public function manageUsers() {
        $page = intval($_GET['page'] ?? 1);
        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $users = $this->user->getAll($limit, $offset);
        $total = $this->user->countTotal();
        $total_pages = ceil($total / $limit);

        include __DIR__ . '/../admin/views/manage-users.php';
    }

    /**
     * Lock user
     */
    public function lockUser() {
        $userId = intval($_POST['user_id'] ?? 0);

        if (!$userId) {
            echo json_encode(['success' => false]);
            exit;
        }

        if ($this->user->lock($userId)) {
            SecurityHelper::logSecurityEvent('User locked', ['admin_id' => getCurrentUserId(), 'user_id' => $userId]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    /**
     * Unlock user
     */
    public function unlockUser() {
        $userId = intval($_POST['user_id'] ?? 0);

        if (!$userId) {
            echo json_encode(['success' => false]);
            exit;
        }

        if ($this->user->unlock($userId)) {
            SecurityHelper::logSecurityEvent('User unlocked', ['admin_id' => getCurrentUserId(), 'user_id' => $userId]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    /**
     * Delete user
     */
    public function deleteUser() {
        $userId = intval($_POST['user_id'] ?? 0);

        if (!$userId || $userId === getCurrentUserId()) {
            echo json_encode(['success' => false, 'message' => 'Lỗi']);
            exit;
        }

        if ($this->user->delete($userId)) {
            SecurityHelper::logSecurityEvent('User deleted', ['admin_id' => getCurrentUserId(), 'user_id' => $userId]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    /**
     * Manage companies
     */
    public function manageCompanies() {
        $page = intval($_GET['page'] ?? 1);
        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $companies = $this->company->getAll($limit, $offset);
        $total = $this->company->countTotal();
        $total_pages = ceil($total / $limit);

        include __DIR__ . '/../admin/views/manage-companies.php';
    }

    /**
     * Approve company
     */
    public function approveCompany() {
        $companyId = intval($_POST['company_id'] ?? 0);

        if (!$companyId) {
            echo json_encode(['success' => false]);
            exit;
        }

        if ($this->company->approve($companyId)) {
            SecurityHelper::logSecurityEvent('Company approved', ['admin_id' => getCurrentUserId(), 'company_id' => $companyId]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    /**
     * Manage jobs
     */
    public function manageJobs() {
        $page = intval($_GET['page'] ?? 1);
        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $jobs = $this->job->getActive($limit, $offset);
        $total = $this->job->countTotal();
        $total_pages = ceil($total / $limit);

        include __DIR__ . '/../admin/views/manage-jobs.php';
    }

    /**
     * Feature job
     */
    public function featureJob() {
        $jobId = intval($_POST['job_id'] ?? 0);

        if (!$jobId) {
            echo json_encode(['success' => false]);
            exit;
        }

        if ($this->job->feature($jobId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}
?>
