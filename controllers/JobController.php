<?php
/**
 * Job Controller
 * Quản lý tìm kiếm và xem việc làm
 */

class JobController {
    private $pdo;
    private $job;
    private $application;
    private $wishlist;
    private $company;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->job = new Job($pdo);
        $this->application = new Application($pdo);
        $this->company = new Company($pdo);
    }

    /**
     * Show all jobs
     */
    public function listJobs() {
        $page = intval($_GET['page'] ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $title = $_GET['title'] ?? '';
        $location = $_GET['location'] ?? '';
        $category = $_GET['category'] ?? '';
        $salary_min = intval($_GET['salary_min'] ?? 0);
        $salary_max = intval($_GET['salary_max'] ?? 0);

        $jobs = $this->job->search($title, $location, $category, $salary_min, $salary_max);
        $total = count($jobs);
        $jobs = array_slice($jobs, $offset, $limit);
        $total_pages = ceil($total / $limit);

        include __DIR__ . '/../views/jobs-list.php';
    }

    /**
     * Show job detail
     */
    public function showDetail() {
        $jobId = intval($_GET['id'] ?? 0);
        
        if (!$jobId) {
            redirect(APP_URL . '/jobs');
        }

        $job = $this->job->getById($jobId);

        if (!$job) {
            redirect(APP_URL . '/jobs');
        }

        // Increment views
        $stmt = $this->pdo->prepare('UPDATE jobs SET views = views + 1 WHERE id = ?');
        $stmt->execute([$jobId]);

        // Get similar jobs
        $similarJobs = $this->job->getSimilar($jobId);

        // Get company
        $company = $this->company->getById($job['company_id']);

        // Check if applied
        $hasApplied = false;
        if (isLoggedIn()) {
            $hasApplied = $this->application->hasApplied(getCurrentUserId(), $jobId);
        }

        include __DIR__ . '/../views/job-detail.php';
    }

    /**
     * Apply for job
     */
    public function apply() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/auth/login');
        }

        if (getCurrentUser()['role_id'] !== ROLE_CANDIDATE) {
            redirect(APP_CANDIDATE_URL . '/dashboard');
        }

        $jobId = intval($_POST['job_id'] ?? 0);
        $coverLetter = $_POST['cover_letter'] ?? '';

        if (!$jobId) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: Công việc không tồn tại']);
            exit;
        }

        $job = $this->job->getById($jobId);
        if (!$job) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: Công việc không tồn tại']);
            exit;
        }

        if ($this->application->hasApplied(getCurrentUserId(), $jobId)) {
            echo json_encode(['success' => false, 'message' => 'Bạn đã ứng tuyển công việc này rồi']);
            exit;
        }

        $data = [
            'user_id' => getCurrentUserId(),
            'job_id' => $jobId,
            'cover_letter' => SecurityHelper::sanitize($coverLetter),
            'status' => APP_PENDING
        ];

        if ($this->application->create($data)) {
            echo json_encode(['success' => true, 'message' => 'Ứng tuyển thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ứng tuyển thất bại']);
        }
        exit;
    }

    /**
     * Search jobs AJAX
     */
    public function search() {
        header('Content-Type: application/json');

        $keyword = $_GET['keyword'] ?? '';
        $keyword = SecurityHelper::sanitize($keyword);

        if (strlen($keyword) < 2) {
            echo json_encode(['jobs' => []]);
            exit;
        }

        $jobs = $this->job->search($keyword);
        $jobs = array_slice($jobs, 0, 10);

        echo json_encode(['jobs' => $jobs]);
        exit;
    }
}
?>
