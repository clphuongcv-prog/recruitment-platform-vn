<?php
/**
 * Main Entry Point
 * Điểm vào chính của ứng dụng
 */

session_start();

// Load configuration
require_once __DIR__ . '/config/Constants.php';
require_once __DIR__ . '/config/Database.php';

// Load helpers
require_once __DIR__ . '/helpers/HelperFunctions.php';
require_once __DIR__ . '/helpers/SecurityHelper.php';
require_once __DIR__ . '/helpers/ValidationHelper.php';

// Load models
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Company.php';
require_once __DIR__ . '/models/Job.php';
require_once __DIR__ . '/models/Application.php';

// Create PDO instance
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => false
        ]
    );
} catch (PDOException $e) {
    die('Database Connection Error: ' . $e->getMessage());
}

// Route handling
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/recruitment-platform-vn';
$request_path = str_replace($base_path, '', $request_uri);
$request_path = trim($request_path, '/');

// Default route
if (empty($request_path) || $request_path === '') {
    include __DIR__ . '/views/landing.php';
    exit;
}

// Route to appropriate page/controller
$routes = explode('/', $request_path);
$module = $routes[0] ?? '';

switch ($module) {
    case 'admin':
        if (isset($_SESSION['role_id']) && $_SESSION['role_id'] === ROLE_ADMIN) {
            include __DIR__ . '/admin/index.php';
        } else {
            redirect(APP_URL . '/auth/login');
        }
        break;

    case 'candidate':
        if (isset($_SESSION['role_id']) && $_SESSION['role_id'] === ROLE_CANDIDATE) {
            include __DIR__ . '/candidate/index.php';
        } else {
            redirect(APP_URL . '/auth/login');
        }
        break;

    case 'employer':
        if (isset($_SESSION['role_id']) && $_SESSION['role_id'] === ROLE_EMPLOYER) {
            include __DIR__ . '/employer/index.php';
        } else {
            redirect(APP_URL . '/auth/login');
        }
        break;

    case 'auth':
        include __DIR__ . '/views/auth.php';
        break;

    case 'jobs':
        include __DIR__ . '/views/jobs.php';
        break;

    case 'companies':
        include __DIR__ . '/views/companies.php';
        break;

    case 'api':
        header('Content-Type: application/json');
        include __DIR__ . '/api/router.php';
        break;

    default:
        include __DIR__ . '/views/landing.php';
        break;
}
?>
