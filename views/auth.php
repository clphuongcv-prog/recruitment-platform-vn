<?php
/**
 * Auth Views - Login, Register, Forgot Password
 */

$action = $routes[1] ?? 'login';

if ($action === 'login') {
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Nền Tảng Tuyển Dụng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563EB;
            --secondary: #7C3AED;
            --dark-bg: #0F172A;
            --dark-surface: #111827;
            --text-light: #F8FAFC;
            --text-muted: #94A3B8;
        }

        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1a1f3a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-light);
        }

        .auth-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 50px 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-header p {
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            color: var(--text-light);
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-group input::placeholder {
            color: var(--text-muted);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--text-muted);
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #86efac;
        }

        .error-text {
            color: #fca5a5;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Đăng Nhập</h2>
                <p>Trở lại nền tảng tuyển dụng</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo APP_URL; ?>/auth/login" id="loginForm">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="your@email.com" required value="<?php echo $_POST['email'] ?? ''; ?>">
                    <?php if (isset($_SESSION['errors']['Email'])): ?>
                        <div class="error-text"><?php echo $_SESSION['errors']['Email'][0]; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Mật Khẩu</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                    <?php if (isset($_SESSION['errors']['Mật khẩu'])): ?>
                        <div class="error-text"><?php echo $_SESSION['errors']['Mật khẩu'][0]; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group" style="display: flex; align-items: center;">
                    <input type="checkbox" id="remember" name="remember" style="width: auto; margin-right: 10px;">
                    <label for="remember" style="margin: 0; cursor: pointer;">Ghi nhở tài khoản</label>
                </div>

                <button type="submit" class="btn-submit">Đăng Nhập</button>
            </form>

            <div class="form-footer">
                <p>
                    Chưa có tài khoản?
                    <a href="<?php echo APP_URL; ?>/auth/register">Đăng Ký Ngay</a>
                </p>
                <p>
                    <a href="<?php echo APP_URL; ?>/auth/forgot-password">Quên Mật Khẩu?</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
} elseif ($action === 'register') {
    $role = intval($_GET['role'] ?? ROLE_CANDIDATE);
    $roleText = $role === ROLE_EMPLOYER ? 'Nhà Tuyển Dụng' : 'Üng Viên';
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Nền Tảng Tuyển Dụng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563EB;
            --secondary: #7C3AED;
            --dark-bg: #0F172A;
            --dark-surface: #111827;
            --text-light: #F8FAFC;
            --text-muted: #94A3B8;
        }

        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1a1f3a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-light);
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 500px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            color: var(--text-light);
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            outline: none;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            font-size: 14px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .form-footer {
            text-align: center;
            margin-top: 15px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .error-text {
            color: #fca5a5;
            font-size: 12px;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Đăng Ký <?php echo $roleText; ?></h2>
                <p>Tạo tài khoản mới trong 1 phút</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" style="margin-bottom: 15px;">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo APP_URL; ?>/auth/register">
                <input type="hidden" name="role" value="<?php echo $role; ?>">

                <div class="form-group">
                    <label>Họ Tên</label>
                    <input type="text" name="name" placeholder="Nhập họ tên" required value="<?php echo $_POST['name'] ?? ''; ?>">
                    <?php if (isset($_SESSION['errors']['Họ tên'])): ?>
                        <div class="error-text"><?php echo $_SESSION['errors']['Họ tên'][0]; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="your@email.com" required value="<?php echo $_POST['email'] ?? ''; ?>">
                    <?php if (isset($_SESSION['errors']['Email'])): ?>
                        <div class="error-text"><?php echo $_SESSION['errors']['Email'][0]; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Điện Thoại</label>
                    <input type="tel" name="phone" placeholder="0901234567" value="<?php echo $_POST['phone'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Mật Khẩu</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                    <?php if (isset($_SESSION['errors']['Mật khẩu'])): ?>
                        <div class="error-text"><?php echo $_SESSION['errors']['Mật khẩu'][0]; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Xác Nhận Mật Khẩu</label>
                    <input type="password" name="password_confirm" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-submit">Đăng Ký</button>
            </form>

            <div class="form-footer">
                <p>
                    Đã có tài khoản?
                    <a href="<?php echo APP_URL; ?>/auth/login">Đăng Nhập</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
}
?>
