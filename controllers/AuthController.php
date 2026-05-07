<?php
/**
 * Auth Controller
 * Quản lý đăng nhập, đăng ký
 */

class AuthController {
    private $pdo;
    private $user;
    private $validator;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->user = new User($pdo);
        $this->validator = new ValidationHelper();
    }

    /**
     * Show login form
     */
    public function showLogin() {
        if (isLoggedIn()) {
            redirect(APP_URL . '/dashboard');
        }
        include __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Process login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showLogin();
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validate
        $this->validator->validateRequired($email, 'Email');
        $this->validator->validateRequired($password, 'Mật khẩu');

        if ($this->validator->hasErrors()) {
            $_SESSION['errors'] = $this->validator->getErrors();
            return $this->showLogin();
        }

        // Check rate limiting
        if (!SecurityHelper::checkRateLimit('login_' . $email, 5, 900)) {
            $_SESSION['error'] = 'Quá nhiều lần đăng nhập thất bại. Vui lòng thử lại sau.';
            return $this->showLogin();
        }

        // Get user
        $user = $this->user->getByEmail(SecurityHelper::sanitizeDB($email));

        if (!$user || !$this->user->verifyPassword($password, $user['password'])) {
            SecurityHelper::logSecurityEvent('Failed login attempt', ['email' => $email]);
            $_SESSION['error'] = 'Email hoặc mật khẩu không chín xác.';
            return $this->showLogin();
        }

        if ($user['is_locked']) {
            $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa.';
            return $this->showLogin();
        }

        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['csrf_token'] = SecurityHelper::generateCSRFToken();

        // Remember me
        if ($remember) {
            $token = SecurityHelper::generateToken();
            setcookie('remember_token', $token, time() + REMEMBER_ME_DURATION, '/');
            $this->user->update($user['id'], ['remember_token' => $token]);
        }

        // Log login
        $this->user->logLogin($user['id']);

        SecurityHelper::logSecurityEvent('Successful login', ['user_id' => $user['id'], 'email' => $email]);

        // Redirect based on role
        switch ($user['role_id']) {
            case ROLE_ADMIN:
                redirect(APP_ADMIN_URL . '/dashboard');
                break;
            case ROLE_EMPLOYER:
                redirect(APP_EMPLOYER_URL . '/dashboard');
                break;
            case ROLE_CANDIDATE:
                redirect(APP_CANDIDATE_URL . '/dashboard');
                break;
            default:
                redirect(APP_URL);
        }
    }

    /**
     * Show register form
     */
    public function showRegister() {
        if (isLoggedIn()) {
            redirect(APP_URL . '/dashboard');
        }
        
        $role = $_GET['role'] ?? ROLE_CANDIDATE;
        include __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Process registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showRegister();
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $role = intval($_POST['role'] ?? ROLE_CANDIDATE);

        // Validate
        $this->validator->validateRequired($name, 'Họ tên');
        $this->validator->validateRequired($email, 'Email');
        $this->validator->validateEmail($email, 'Email');
        $this->validator->validateRequired($password, 'Mật khẩu');
        $this->validator->validateMinLength($password, 6, 'Mật khẩu');
        $this->validator->validateMatch($password, $password_confirm, 'Mật khẩu');

        if ($phone) {
            $this->validator->validatePhone($phone, 'Điện thoại');
        }

        // Check if email exists
        $this->validator->validateUnique($email, 'users', 'email', $this->pdo, 'Email');

        if ($this->validator->hasErrors()) {
            $_SESSION['errors'] = $this->validator->getErrors();
            return $this->showRegister();
        }

        // Create user
        $data = [
            'name' => SecurityHelper::sanitize($name),
            'email' => SecurityHelper::sanitizeDB($email),
            'password' => SecurityHelper::hashPassword($password),
            'phone' => SecurityHelper::sanitize($phone),
            'role_id' => $role
        ];

        if ($this->user->create($data)) {
            SecurityHelper::logSecurityEvent('New user registration', ['email' => $email, 'role_id' => $role]);
            $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
            redirect(APP_URL . '/auth/login');
        } else {
            $_SESSION['error'] = 'Đăng ký thất bại. Vui lòng thử lại.';
            return $this->showRegister();
        }
    }

    /**
     * Logout
     */
    public function logout() {
        SecurityHelper::logSecurityEvent('User logout', ['user_id' => getCurrentUserId()]);
        
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');
        
        redirect(APP_URL);
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword() {
        include __DIR__ . '/../views/auth/forgot-password.php';
    }

    /**
     * Process forgot password
     */
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showForgotPassword();
        }

        $email = $_POST['email'] ?? '';

        $this->validator->validateRequired($email, 'Email');
        $this->validator->validateEmail($email, 'Email');

        if ($this->validator->hasErrors()) {
            $_SESSION['errors'] = $this->validator->getErrors();
            return $this->showForgotPassword();
        }

        $user = $this->user->getByEmail($email);

        if ($user) {
            // Generate reset token
            $token = SecurityHelper::generateToken();
            $this->user->update($user['id'], ['remember_token' => $token]);
            
            // In production, send email
            $_SESSION['success'] = 'Hướng dẫn đặt lại mật khẩu đã được gửi đến email của bạn.';
        } else {
            $_SESSION['success'] = 'Hướng dẫn đặt lại mật khẩu đã được gửi đến email của bạn.';
        }

        redirect(APP_URL . '/auth/login');
    }
}
?>
