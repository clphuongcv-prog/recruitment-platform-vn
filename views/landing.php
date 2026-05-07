<?php
/**
 * Landing Page
 * Trang chủ của hệ thống
 */

if (!isset($pdo)) {
    require_once __DIR__ . '/../config/Database.php';
    require_once __DIR__ . '/../config/Constants.php';
    require_once __DIR__ . '/../helpers/HelperFunctions.php';
    require_once __DIR__ . '/../models/Job.php';
    require_once __DIR__ . '/../models/Company.php';
}

$jobModel = new Job($pdo);
$companyModel = new Company($pdo);

$featuredJobs = $jobModel->getFeatured(6);
$hotJobs = $jobModel->getHot(6);
$topCompanies = $companyModel->getApproved(5);

$totalJobs = $jobModel->countTotal();
$stmt = $pdo->prepare('SELECT COUNT(*) as count FROM users WHERE role_id = ?');
$stmt->execute([ROLE_CANDIDATE]);
$totalCandidates = $stmt->fetch()['count'];
$totalCompanies = $companyModel->countTotal();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nền Tảng Tuyển Dụng Chuyên Nghiệp - Việc Làm Hàng Đầu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <style>
        :root {
            --primary: #2563EB;
            --secondary: #7C3AED;
            --accent: #06B6D4;
            --dark-bg: #0F172A;
            --dark-surface: #111827;
            --text-light: #F8FAFC;
            --text-muted: #94A3B8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1a1f3a 100%);
            color: var(--text-light);
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--dark-surface) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 60px 20px;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0.15;
            top: -100px;
            right: -100px;
            animation: float 6s ease-in-out infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, var(--secondary) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0.15;
            bottom: -50px;
            left: -50px;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(20px); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 800px;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-size: 56px;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #60A5FA 0%, #06B6D4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .hero p {
            font-size: 20px;
            color: var(--text-muted);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .search-box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 20px 30px;
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            flex-wrap: wrap;
        }

        .search-box input,
        .search-box select {
            background: transparent;
            border: none;
            color: var(--text-light);
            flex: 1;
            min-width: 150px;
            font-size: 16px;
            outline: none;
        }

        .search-box input::placeholder,
        .search-box select option {
            color: var(--text-muted);
        }

        .btn-search {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.6);
        }

        /* Stats Section */
        .stats {
            padding: 80px 20px;
            background: var(--dark-surface);
        }

        .stat-card {
            text-align: center;
            padding: 40px 20px;
            animation: slideInUp 0.6s ease;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-number {
            font-size: 48px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 16px;
        }

        /* Featured Jobs Section */
        .featured-section {
            padding: 80px 20px;
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--dark-surface) 100%);
        }

        .section-title {
            font-size: 40px;
            font-weight: 800;
            margin-bottom: 50px;
            text-align: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .job-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .job-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 8px 32px rgba(37, 99, 235, 0.2);
        }

        .job-card h5 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-light);
        }

        .company-name {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .job-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .meta-item i {
            color: var(--accent);
        }

        .salary {
            color: #10B981;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .job-description {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .job-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-apply {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
            color: white;
        }

        .btn-save {
            background: transparent;
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            background: var(--accent);
            color: var(--dark-bg);
        }

        /* Footer */
        .footer {
            background: var(--dark-surface);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 50px 20px 30px;
            margin-top: 80px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h6 {
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 12px;
        }

        .footer-section a {
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-section a:hover {
            color: var(--primary);
            padding-left: 5px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-muted);
        }

        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }

            .hero p {
                font-size: 16px;
            }

            .search-box {
                flex-direction: column;
            }

            .stat-number {
                font-size: 36px;
            }

            .section-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-5">
            <a class="navbar-brand" href="<?php echo APP_URL; ?>">
                <i class="fas fa-briefcase"></i>
                Việc Làm
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/jobs">Tìm Việc</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/companies">Công Ty</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Blog</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo (
                                getCurrentUser()['role_id'] === ROLE_ADMIN ? APP_ADMIN_URL :
                                (getCurrentUser()['role_id'] === ROLE_EMPLOYER ? APP_EMPLOYER_URL :
                                APP_CANDIDATE_URL)
                            ); ?>/dashboard">
                                <i class="fas fa-user-circle"></i> Thành Viên
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL; ?>/auth/logout">Thoát</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL; ?>/auth/login">Tham Gia</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn-primary-custom ms-2" href="<?php echo APP_URL; ?>/auth/register?role=<?php echo ROLE_CANDIDATE; ?>">Cho Üng Viên</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
            <h1>Tìm Việc Làm Mơ Ước Ngay Hôm Nay</h1>
            <p>Hàng nghàn công việc tuyệt vời đại bằng những công ty hàng đầu</p>

            <form method="GET" action="<?php echo APP_URL; ?>/jobs" class="search-box">
                <input type="text" name="title" placeholder="Chức danh hoặc kỹ năng..." required>
                <input type="text" name="location" placeholder="Địa điểm...">
                <select name="category">
                    <option value="">Tất cả lĩnh vực</option>
                    <option value="IT">IT</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Design">Design</option>
                    <option value="Business">Business</option>
                </select>
                <button type="submit" class="btn-search"><i class="fas fa-search"></i> Tìm Kiếm</button>
            </form>

            <p>
                <a href="<?php echo APP_URL; ?>/auth/register?role=<?php echo ROLE_EMPLOYER; ?>" class="btn-primary-custom me-3">
                    Tuyển Dụng Ngay
                </a>
            </p>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="stats">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 stat-card">
                    <div class="stat-number"><?php echo number_format($totalJobs); ?>+</div>
                    <div class="stat-label">Việc Làm Đăng Tuyển</div>
                </div>
                <div class="col-md-3 stat-card">
                    <div class="stat-number"><?php echo number_format($totalCandidates); ?>+</div>
                    <div class="stat-label">Üng Viên Đăng Ký</div>
                </div>
                <div class="col-md-3 stat-card">
                    <div class="stat-number"><?php echo number_format($totalCompanies); ?>+</div>
                    <div class="stat-label">Công Ty Đăng Hợp Tác</div>
                </div>
                <div class="col-md-3 stat-card">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Tỷ Lệ Hài Lòng</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Jobs Section -->
    <div class="featured-section">
        <div class="container-fluid px-5">
            <h2 class="section-title">Việc Làm Nổi Bật</h2>
            <div class="row">
                <?php foreach ($featuredJobs as $job): ?>
                    <div class="col-lg-6 col-md-12">
                        <div class="job-card">
                            <h5><?php echo htmlspecialchars($job['title']); ?></h5>
                            <div class="company-name">
                                <i class="fas fa-building me-2"></i>
                                <?php echo htmlspecialchars($job['company_name']); ?>
                            </div>
                            <div class="job-meta">
                                <span class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($job['location']); ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-briefcase"></i>
                                    <?php echo htmlspecialchars($job['job_type']); ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-layer-group"></i>
                                    <?php echo htmlspecialchars($job['level']); ?>
                                </span>
                            </div>
                            <div class="salary">
                                <?php echo formatCurrency($job['salary_min']); ?> - <?php echo formatCurrency($job['salary_max']); ?>
                            </div>
                            <div class="job-description">
                                <?php echo htmlspecialchars(substr($job['description'], 0, 150)); ?>...
                            </div>
                            <div class="job-footer">
                                <a href="<?php echo APP_URL; ?>/jobs?id=<?php echo $job['id']; ?>" class="btn-apply">Xem Chi Tiết</a>
                                <?php if (isLoggedIn() && getCurrentUser()['role_id'] === ROLE_CANDIDATE): ?>
                                    <button class="btn-save" onclick="saveJob(<?php echo $job['id']; ?>, this)">
                                        <i class="fas fa-heart"></i> Lưu
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Hot Jobs Section -->
    <div class="featured-section">
        <div class="container-fluid px-5">
            <h2 class="section-title">Việc Làm Hot Nhất</h2>
            <div class="row">
                <?php foreach ($hotJobs as $job): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="job-card">
                            <h5><?php echo htmlspecialchars($job['title']); ?></h5>
                            <div class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></div>
                            <div class="salary"><?php echo formatCurrency($job['salary_min']); ?> - <?php echo formatCurrency($job['salary_max']); ?></div>
                            <div class="job-meta">
                                <span class="meta-item"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                            </div>
                            <div class="job-footer">
                                <a href="<?php echo APP_URL; ?>/jobs?id=<?php echo $job['id']; ?>" class="btn-apply">Xem</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="featured-section" style="text-align: center;">
        <div class="container-fluid px-5">
            <h2 class="section-title" style="margin-bottom: 30px;">Bạn Là Nhà Tuyển Dụng?</h2>
            <p style="color: var(--text-muted); font-size: 18px; margin-bottom: 30px;">Tuyển dụng nhân tài tấn công với chi phí thấp nhất</p>
            <a href="<?php echo APP_URL; ?>/auth/register?role=<?php echo ROLE_EMPLOYER; ?>" class="btn-primary-custom">Bắt Đầu Tuyển Dụng</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid px-5">
            <div class="footer-content">
                <div class="footer-section">
                    <h6><i class="fas fa-briefcase"></i> Việc Làm</h6>
                    <ul>
                        <li><a href="#">Tìm Việc</a></li>
                        <li><a href="#">Công Ty</a></li>
                        <li><a href="#">Đăng Ký Üng Viên</a></li>
                        <li><a href="#">Upload CV</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h6>Cho Nhà Tuyển Dụng</h6>
                    <ul>
                        <li><a href="#">Đăng Ký Công Ty</a></li>
                        <li><a href="#">Đăng Tin Tuyển Dụng</a></li>
                        <li><a href="#">Quản Lý Ứng Viên</a></li>
                        <li><a href="#">Gói Có Sắn</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h6>Về Chúng Tôi</h6>
                    <ul>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Tuyển Dụng</a></li>
                        <li><a href="#">Liên Hệ</a></li>
                        <li><a href="#">Điều Khoản</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h6>Kết Nối Với Chúng Tôi</h6>
                    <div style="display: flex; gap: 15px;">
                        <a href="#" style="color: var(--primary); font-size: 24px;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: var(--primary); font-size: 24px;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: var(--primary); font-size: 24px;"><i class="fab fa-linkedin"></i></a>
                        <a href="#" style="color: var(--primary); font-size: 24px;"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Nền Tảng Tuyển Dụng Chuyên Nghiệp. Tất cả quyền được bảo vệ.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function saveJob(jobId, btn) {
            fetch('<?php echo APP_URL; ?>/api/wishlist/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({job_id: jobId})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.style.background = 'var(--accent)';
                    btn.style.color = 'var(--dark-bg)';
                }
            });
        }
    </script>
</body>
</html>
