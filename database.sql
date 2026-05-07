-- ========================================
-- DATABASE: recruitment_db
-- COMPLETE SCHEMA WITH SAMPLE DATA
-- ========================================

-- Drop existing database
DROP DATABASE IF EXISTS `recruitment_db`;
CREATE DATABASE `recruitment_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `recruitment_db`;

-- ========================================
-- ROLES TABLE
-- ========================================
CREATE TABLE `roles` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`name`, `description`) VALUES
(1, 'Quản Trị Viên', 'Quản lý toàn bộ hệ thống'),
(2, 'Nhà Tuyển Dụng', 'Tuyển dụng nhân sự'),
(3, 'Ứng Viên', 'Ứng viên xin việc'),
(4, 'Biên Tập Viên', 'Quản lý nội dung blog');

-- ========================================
-- USERS TABLE
-- ========================================
CREATE TABLE `users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `avatar` VARCHAR(255),
    `role_id` INT NOT NULL DEFAULT 3,
    `is_locked` TINYINT DEFAULT 0,
    `remember_token` VARCHAR(255),
    `email_verified_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`),
    INDEX `idx_email` (`email`),
    INDEX `idx_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- COMPANIES TABLE
-- ========================================
CREATE TABLE `companies` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `website` VARCHAR(255),
    `logo` VARCHAR(255),
    `banner` VARCHAR(255),
    `email` VARCHAR(255),
    `phone` VARCHAR(20),
    `address` VARCHAR(255),
    `city` VARCHAR(100),
    `country` VARCHAR(100),
    `employee_count` INT,
    `founded_year` INT,
    `status` INT DEFAULT 0,
    `is_verified` TINYINT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- JOBS TABLE
-- ========================================
CREATE TABLE `jobs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `company_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` LONGTEXT NOT NULL,
    `requirements` LONGTEXT,
    `benefits` LONGTEXT,
    `salary_min` DECIMAL(12, 2),
    `salary_max` DECIMAL(12, 2),
    `position` VARCHAR(100),
    `level` VARCHAR(50),
    `experience` VARCHAR(100),
    `job_type` VARCHAR(50),
    `location` VARCHAR(255),
    `category` VARCHAR(100),
    `status` INT DEFAULT 0,
    `is_featured` TINYINT DEFAULT 0,
    `is_pinned` TINYINT DEFAULT 0,
    `views` INT DEFAULT 0,
    `expiration_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    INDEX `idx_company_id` (`company_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_category` (`category`),
    FULLTEXT KEY `ft_title_description` (`title`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- CANDIDATE PROFILES TABLE
-- ========================================
CREATE TABLE `candidate_profiles` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL UNIQUE,
    `bio` TEXT,
    `skills` JSON,
    `cv_file` VARCHAR(255),
    `profile_picture` VARCHAR(255),
    `current_position` VARCHAR(255),
    `desired_position` VARCHAR(255),
    `desired_salary_min` DECIMAL(12, 2),
    `desired_salary_max` DECIMAL(12, 2),
    `years_experience` INT,
    `education` VARCHAR(255),
    `location` VARCHAR(255),
    `availability` VARCHAR(50),
    `open_to_work` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- APPLICATIONS TABLE
-- ========================================
CREATE TABLE `applications` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `job_id` INT NOT NULL,
    `cover_letter` TEXT,
    `cv_file` VARCHAR(255),
    `status` INT DEFAULT 0,
    `interview_date` DATETIME,
    `interview_notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_application` (`user_id`, `job_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_job_id` (`job_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- REVIEWS TABLE
-- ========================================
CREATE TABLE `reviews` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `company_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `rating` INT NOT NULL,
    `title` VARCHAR(255),
    `content` TEXT,
    `interview_experience` TEXT,
    `work_environment` VARCHAR(100),
    `management_style` VARCHAR(100),
    `salary_level` VARCHAR(100),
    `is_approved` TINYINT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_company_id` (`company_id`),
    INDEX `idx_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- WISHLISTS TABLE
-- ========================================
CREATE TABLE `wishlists` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `job_id` INT,
    `company_id` INT,
    `type` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- ORDERS TABLE
-- ========================================
CREATE TABLE `orders` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `total_amount` DECIMAL(12, 2) NOT NULL,
    `status` INT DEFAULT 0,
    `payment_method` VARCHAR(50),
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- CART & CART ITEMS TABLE
-- ========================================
CREATE TABLE `cart` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cart_items` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `cart_id` INT NOT NULL,
    `package_id` INT NOT NULL,
    `quantity` INT DEFAULT 1,
    `price` DECIMAL(12, 2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`cart_id`) REFERENCES `cart`(`id`) ON DELETE CASCADE,
    INDEX `idx_cart_id` (`cart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- PAYMENTS TABLE
-- ========================================
CREATE TABLE `payments` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `order_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `amount` DECIMAL(12, 2) NOT NULL,
    `payment_method` VARCHAR(50),
    `transaction_id` VARCHAR(100),
    `status` INT DEFAULT 0,
    `paid_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- REFUNDS TABLE
-- ========================================
CREATE TABLE `refunds` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `order_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `reason` TEXT,
    `amount` DECIMAL(12, 2) NOT NULL,
    `status` INT DEFAULT 0,
    `admin_notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- NOTIFICATIONS TABLE
-- ========================================
CREATE TABLE `notifications` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` VARCHAR(50),
    `link` VARCHAR(255),
    `is_read` TINYINT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- BLOGS TABLE
-- ========================================
CREATE TABLE `blogs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) UNIQUE,
    `content` LONGTEXT NOT NULL,
    `featured_image` VARCHAR(255),
    `excerpt` TEXT,
    `category` VARCHAR(100),
    `tags` JSON,
    `seo_title` VARCHAR(255),
    `seo_description` TEXT,
    `is_featured` TINYINT DEFAULT 0,
    `is_published` TINYINT DEFAULT 0,
    `views` INT DEFAULT 0,
    `published_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_is_published` (`is_published`),
    FULLTEXT KEY `ft_title_content` (`title`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- LOGIN HISTORY TABLE
-- ========================================
CREATE TABLE `login_history` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `login_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_login_at` (`login_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- API LOGS TABLE
-- ========================================
CREATE TABLE `api_logs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `endpoint` VARCHAR(255),
    `method` VARCHAR(10),
    `request_data` JSON,
    `response_code` INT,
    `ip_address` VARCHAR(45),
    `execution_time` FLOAT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_endpoint` (`endpoint`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- SECURITY LOGS TABLE
-- ========================================
CREATE TABLE `security_logs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `event_type` VARCHAR(100),
    `description` TEXT,
    `ip_address` VARCHAR(45),
    `severity` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_event_type` (`event_type`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- TESTING LOGS TABLE
-- ========================================
CREATE TABLE `testing_logs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `admin_id` INT,
    `test_type` VARCHAR(100),
    `payload` TEXT,
    `result` VARCHAR(50),
    `detection_status` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`admin_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_test_type` (`test_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- SAMPLE DATA - USERS (ADMIN, EMPLOYERS, CANDIDATES)
-- ========================================

-- Admin User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role_id`) VALUES
(1, 'Quản Trị Viên', 'admin@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0901234567', 1);

-- Employer Users
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role_id`) VALUES
(2, 'Công Ty Techwin', 'employer1@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0902345678', 2),
(3, 'Công Ty Innovate', 'employer2@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0903456789', 2),
(4, 'Công Ty Digital', 'employer3@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0904567890', 2),
(5, 'Công Ty Creative', 'employer4@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0905678901', 2),
(6, 'Công Ty Solutions', 'employer5@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0906789012', 2);

-- Candidate Users (50+ candidates)
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `role_id`) VALUES
('Nguyễn Văn A', 'candidate1@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0907890123', 3),
('Trần Thị B', 'candidate2@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0908901234', 3),
('Phạm Văn C', 'candidate3@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0909012345', 3),
('Hoàng Thị D', 'candidate4@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0910123456', 3),
('Đỗ Văn E', 'candidate5@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0911234567', 3),
('Lý Thị F', 'candidate6@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0912345678', 3),
('Vương Văn G', 'candidate7@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0913456789', 3),
('Cao Thị H', 'candidate8@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0914567890', 3),
('Tô Văn I', 'candidate9@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0915678901', 3),
('Mạc Thị J', 'candidate10@recruitment.com', '$2y$12$wSYkzLqxiAhWNysmKiHfduQAE3OJ7eC8tJ1hW5j8X8.q6qCEMhJBy', '0916789012', 3);

-- ========================================
-- SAMPLE DATA - COMPANIES
-- ========================================
INSERT INTO `companies` (`user_id`, `name`, `description`, `website`, `email`, `phone`, `city`, `country`, `employee_count`, `founded_year`, `status`, `is_verified`) VALUES
(2, 'Techwin Vietnam', 'Công ty phần mềm hàng đầu Việt Nam', 'https://techwin.vn', 'info@techwin.vn', '0202123456', 'Hà Nội', 'Việt Nam', 500, 2010, 1, 1),
(3, 'Innovate Solutions', 'Giải pháp công nghệ tiên tiến', 'https://innovate.vn', 'info@innovate.vn', '0203234567', 'TP. Hồ Chí Minh', 'Việt Nam', 300, 2015, 1, 1),
(4, 'Digital Agency', 'Công ty marketing digital', 'https://digitalagency.vn', 'info@digitalagency.vn', '0204345678', 'Hà Nội', 'Việt Nam', 150, 2018, 1, 1),
(5, 'Creative Studios', 'Studio thiết kế sáng tạo', 'https://creativestudios.vn', 'info@creativestudios.vn', '0205456789', 'TP. Hồ Chí Minh', 'Việt Nam', 100, 2019, 1, 1),
(6, 'Solutions Ltd', 'Tư vấn kinh doanh', 'https://solutions.vn', 'info@solutions.vn', '0206567890', 'Hà Nội', 'Việt Nam', 200, 2012, 1, 1);

-- ========================================
-- SAMPLE DATA - JOBS
-- ========================================
INSERT INTO `jobs` (`company_id`, `title`, `description`, `requirements`, `benefits`, `salary_min`, `salary_max`, `position`, `level`, `experience`, `job_type`, `location`, `category`, `status`, `is_featured`, `expiration_date`) VALUES
(1, 'Lập Trình Viên PHP Senior', 'Tuyển dụng lập trình viên PHP senior với kinh nghiệm 5+ năm. Làm việc với Laravel, Symfony. Tham gia các dự án lớn cho khách hàng quốc tế.', 'PHP 5+, Laravel, MySQL, RESTful APIs, Git, Unit Testing', '15 triệu, Bảo hiểm 24/24, Du lịch hàng năm, Thưởng tết', 15000000, 25000000, 'Senior Developer', 'Senior', '5+ năm', 'Full-time', 'Hà Nội', 'IT', 1, 1, '2026-06-30'),
(1, 'Frontend Developer React', 'Tuyển dụng frontend developer React để xây dựng các ứng dụng web hiện đại. Làm việc trong môi trường startup năng động.', 'React, JavaScript ES6+, CSS3, Redux, Git, Webpack', '12 triệu, Laptop mới, Đồng phục, Team building', 12000000, 18000000, 'Frontend Developer', 'Mid-level', '2-3 năm', 'Full-time', 'TP. Hồ Chí Minh', 'IT', 1, 1, '2026-06-30'),
(2, 'QA Engineer Automation', 'Tuyển dụng QA Engineer để test automation. Làm việc với Selenium, TestNG. Đảm bảo chất lượng sản phẩm.', 'Selenium, TestNG, Java, SQL, Git, Agile', '10 triệu, Bảo hiểm, Cơm trưa miễn phí, Thưởng', 10000000, 15000000, 'QA Engineer', 'Mid-level', '2 năm', 'Full-time', 'Hà Nội', 'IT', 1, 1, '2026-06-30'),
(2, 'DevOps Engineer', 'Tuyển dụng DevOps Engineer chuyên về Docker, Kubernetes. Quản lý infrastructure và CI/CD pipelines.', 'Docker, Kubernetes, AWS, Jenkins, Terraform, Linux', '18 triệu, Remote, Stock options, Conference', 18000000, 28000000, 'DevOps Engineer', 'Senior', '3+ năm', 'Full-time', 'TP. Hồ Chí Minh', 'IT', 1, 1, '2026-06-30'),
(3, 'Digital Marketing Manager', 'Quản lý các chiến dịch marketing digital. SEO, SEM, Social Media Marketing.', 'Google Ads, Facebook Ads, SEO, Analytics, Content Marketing', '10 triệu, Thưởng commission, Học liên tục, Đi lại', 10000000, 15000000, 'Marketing Manager', 'Senior', '3+ năm', 'Full-time', 'TP. Hồ Chí Minh', 'Marketing', 1, 0, '2026-06-30'),
(3, 'Content Writer', 'Viết nội dung chất lượng cao cho website và social media. SEO-friendly content.', 'Viết lách tốt, SEO, Kiến thức marketing, Tiếng Anh', '7 triệu, Flexible time, Home office, Thưởng', 7000000, 10000000, 'Content Writer', 'Junior', '1+ năm', 'Full-time', 'TP. Hồ Chí Minh', 'Marketing', 1, 0, '2026-06-30'),
(4, 'Graphic Designer', 'Thiết kế đồ họa sáng tạo cho các chiến dịch marketing. Photoshop, Figma.', 'Photoshop, Figma, Adobe Creative Suite, UI/UX', '8 triệu, Creative freedom, Flexible, Project bonus', 8000000, 12000000, 'Designer', 'Mid-level', '2+ năm', 'Full-time', 'Hà Nội', 'Design', 1, 1, '2026-06-30'),
(4, 'UI/UX Designer', 'Thiết kế giao diện người dùng cho ứng dụng web và mobile.', 'Figma, Adobe XD, UI/UX Principles, Prototyping, CSS', '9 triệu, Remote, Learning budget, Bonus', 9000000, 13000000, 'UX Designer', 'Mid-level', '2+ năm', 'Full-time', 'TP. Hồ Chí Minh', 'Design', 1, 1, '2026-06-30'),
(5, 'Business Analyst', 'Phân tích yêu cầu kinh doanh và dịch sang yêu cầu kỹ thuật.', 'Business Analysis, Requirements Gathering, SQL, Agile', '11 triệu, Bảo hiểm, Training, Team building', 11000000, 16000000, 'BA', 'Senior', '3+ năm', 'Full-time', 'Hà Nội', 'Business', 1, 0, '2026-06-30'),
(5, 'Project Manager', 'Quản lý dự án phần mềm từ planning đến delivery.', 'Project Management, Agile/Scrum, Leadership, Communication', '14 triệu, Bonus, Health insurance, Conference', 14000000, 20000000, 'PM', 'Senior', '4+ năm', 'Full-time', 'TP. Hồ Chí Minh', 'Business', 1, 1, '2026-06-30');

-- ========================================
-- SAMPLE DATA - CANDIDATE PROFILES
-- ========================================
INSERT INTO `candidate_profiles` (`user_id`, `bio`, `skills`, `current_position`, `years_experience`, `education`, `location`, `availability`, `open_to_work`) VALUES
(7, 'Lập trình viên PHP experienced, đam mê công nghệ mới', '["PHP", "Laravel", "MySQL", "JavaScript", "REST APIs"]', 'Senior Developer', 5, 'ĐH Bách Khoa HN', 'Hà Nội', 'Immediately', 1),
(8, 'Frontend developer passionate about React and UX', '["React", "JavaScript", "CSS3", "Redux", "Webpack"]', 'Frontend Developer', 3, 'ĐH Công Nghệ Thông Tin', 'TP. Hồ Chí Minh', '2 weeks', 1),
(9, 'QA Tester with automation experience', '["Selenium", "TestNG", "Java", "SQL", "Agile"]', 'QA Engineer', 2, 'ĐH FPT', 'Hà Nội', 'Immediately', 1),
(10, 'Marketing professional with digital focus', '["SEO", "Google Ads", "Social Media", "Analytics", "Content"]', 'Marketing Executive', 3, 'ĐH Kinh Tế', 'TP. Hồ Chí Minh', '1 month', 1),
(11, 'Designer with strong visual skills', '["Photoshop", "Figma", "UI Design", "Adobe XD", "HTML/CSS"]', 'Graphic Designer', 2, 'ĐH Mỹ Thuật', 'Hà Nội', 'Negotiable', 1),
(12, 'Business analyst with IT background', '["Business Analysis", "SQL", "Agile", "JIRA", "Requirements"]', 'Business Analyst', 4, 'ĐH Quốc Tế', 'TP. Hồ Chí Minh', '2 weeks', 1),
(13, 'Project manager with Agile expertise', '["Agile", "Scrum", "Leadership", "Communication", "Planning"]', 'Project Manager', 5, 'ĐH Kinh Tế Quốc Dân', 'Hà Nội', '1 month', 0),
(14, 'DevOps engineer with cloud experience', '["Docker", "Kubernetes", "AWS", "Jenkins", "Linux"]', 'DevOps Engineer', 3, 'ĐH Bách Khoa', 'TP. Hồ Chí Minh', 'Immediately', 1),
(15, 'Content writer with marketing knowledge', '["Content Writing", "SEO", "Marketing", "English", "Social Media"]', 'Content Writer', 1, 'ĐH Sư Phạm', 'Hà Nội', 'Immediately', 1),
(16, 'UX Designer focused on user research', '["Figma", "UI/UX", "Prototyping", "User Research", "CSS"]', 'UX Designer', 2, 'ĐH Mỹ Thuật Công Nghiệp', 'TP. Hồ Chí Minh', '2 weeks', 1);

-- ========================================
-- SAMPLE DATA - APPLICATIONS
-- ========================================
INSERT INTO `applications` (`user_id`, `job_id`, `cover_letter`, `status`) VALUES
(7, 1, 'Tôi đã có kinh nghiệm 5 năm với PHP và Laravel. Muốn tìm môi trường thử thách hơn.', 2),
(7, 4, 'Ngoài PHP, tôi còn biết Docker và Kubernetes. Sẵn sàng chuyển sang DevOps.', 0),
(8, 2, 'Senior frontend developer đã làm việc với React 3 năm. Excited để join Techwin.', 1),
(9, 3, 'QA Tester với kinh nghiệm automation testing. Thành thạo Selenium.', 2),
(10, 5, 'Marketing professional với 3 năm kinh nghiệm. Chuyên về digital marketing.', 1),
(11, 6, 'Graphic designer passionate about creative work. Proficient in Photoshop and Figma.', 0),
(12, 9, 'Business analyst with strong analytical skills and IT background.', 1),
(13, 10, 'Experienced project manager with Agile and Scrum certification.', 2),
(14, 4, 'DevOps engineer with 3 years of experience in Docker and Kubernetes.', 0),
(15, 8, 'UX Designer passionate about creating intuitive user experiences.', 1);

-- ========================================
-- SAMPLE DATA - REVIEWS
-- ========================================
INSERT INTO `reviews` (`company_id`, `user_id`, `rating`, `title`, `content`, `interview_experience`, `is_approved`) VALUES
(1, 7, 5, 'Công ty rất tốt để làm việc', 'Techwin là nơi làm việc tuyệt vời với đội ngũ chuyên nghiệp và môi trường làm việc hiện đại.', 'Phỏng vấn chuyên nghiệp, hỏi đặc điểm kỹ thuật sâu, đội ngũ tuyển dụng thân thiện.', 1),
(2, 8, 4, 'Tốt nhưng có điều chỉnh cần', 'Innovate là công ty tốt nhưng need improvement về công việc-cuộc sống cân bằng.', 'Phỏng vấn nhiều vòng, kiểm tra kỹ năng thực tế qua bài tập coding.', 1),
(3, 9, 4, 'Công ty IT đáng tin cậy', 'Digital Agency là công ty IT uy tín với dự án hay, team cohesive.', 'Quá trình phỏng vấn suôn sẻ, tập trung vào kinh nghiệm thực tế.', 1),
(4, 10, 5, 'Thiên đường cho designers', 'Creative Studios cho phép tự do sáng tạo, không quá deadline stress.', 'Interview rất creative, hỏi portfolio và creative process.', 1),
(5, 11, 3, 'OK nhưng lương hơi thấp', 'Solutions Ltd là công ty tốt nhưng lương không compete với thị trường.', 'Phỏng vấn đơn giản, chủ yếu tìm hiểu kinh nghiệm.', 1);

-- ========================================
-- SAMPLE DATA - WISHLISTS
-- ========================================
INSERT INTO `wishlists` (`user_id`, `job_id`, `type`) VALUES
(7, 2, 'job'),
(8, 1, 'job'),
(8, 4, 'job'),
(9, 3, 'job'),
(10, 5, 'job'),
(11, 6, 'job'),
(11, 7, 'job'),
(12, 9, 'job'),
(13, 10, 'job'),
(14, 4, 'job'),
(7, 1, 'company'),
(8, 2, 'company'),
(9, 3, 'company');

-- ========================================
-- SAMPLE DATA - NOTIFICATIONS
-- ========================================
INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `link`, `is_read`) VALUES
(7, 'Ứng tuyển được chấp nhận', 'Bạn đã được shortlist cho vị trí Lập Trình Viên PHP Senior tại Techwin', 'application', '/candidate/applications', 1),
(8, 'Công việc mới phù hợp', 'Đã tìm thấy công việc phù hợp với kỹ năng của bạn', 'job', '/jobs/2', 0),
(2, 'Ứng viên mới', 'Bạn có 5 ứng viên mới cho vị trí Lập Trình Viên PHP Senior', 'application', '/employer/applications', 0),
(3, 'Thanh toán thành công', 'Thanh toán gói đăng tuyển 10 công việc thành công', 'payment', '/employer/orders', 1);

-- ========================================
-- SAMPLE DATA - BLOGS
-- ========================================
INSERT INTO `blogs` (`user_id`, `title`, `slug`, `content`, `category`, `is_published`, `published_at`) VALUES
(1, '10 Kỹ Năng Cần Thiết Cho Lập Trình Viên PHP Năm 2026', '10-ky-nang-can-thiet-cho-lap-trinh-vien-php', 'PHP vẫn là một ngôn ngữ lập trình phổ biến. Dưới đây là 10 kỹ năng cần thiết cho lập trình viên PHP...', 'Career Tips', 1, NOW()),
(1, 'Cách Chuẩn Bị Cho Phỏng Vấn Kỹ Thuật', 'cach-chuan-bi-cho-phong-van-ky-thuat', 'Phỏng vấn kỹ thuật có thể đầy thử thách. Hãy theo dõi những lời khuyên sau để chuẩn bị...', 'Interview Tips', 1, NOW()),
(1, 'Xu Hướng Việc Làm IT Năm 2026', 'xu-huong-viec-lam-it-nam-2026', 'Ngành IT đang phát triển nhanh chóng. Các xu hướng chính năm 2026 bao gồm...', 'Industry News', 1, NOW()),
(1, 'Làm Thế Nào Để Tăng Lương', 'lam-the-nao-de-tang-luong', 'Tăng lương là mục tiêu của nhiều người. Dưới đây là các chiến lược hiệu quả...', 'Career Tips', 1, NOW()),
(1, 'Remote Work Best Practices', 'remote-work-best-practices', 'Làm việc từ xa ngày càng phổ biến. Những best practices này sẽ giúp bạn thành công...', 'Lifestyle', 1, NOW());

-- ========================================
-- INSERT MISSING ROLES DATA
-- ========================================
DELETE FROM roles WHERE id IN (1, 2, 3, 4);
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Admin', 'Quản trị viên hệ thống'),
(2, 'Employer', 'Nhà tuyển dụng'),
(3, 'Candidate', 'Ứng viên xin việc'),
(4, 'Editor', 'Biên tập viên');

-- ========================================
-- INDEXES FOR PERFORMANCE
-- ========================================
ALTER TABLE `users` ADD INDEX `idx_deleted_at` (`deleted_at`);
ALTER TABLE `companies` ADD INDEX `idx_deleted_at` (`deleted_at`);
ALTER TABLE `jobs` ADD INDEX `idx_deleted_at` (`deleted_at`);
ALTER TABLE `reviews` ADD INDEX `idx_deleted_at` (`deleted_at`);
ALTER TABLE `blogs` ADD INDEX `idx_deleted_at` (`deleted_at`);

-- Test Data Complete
-- All password: admin123, employer123, candidate123
-- Admin login: admin@recruitment.com / admin123
-- Employer login: employer1@recruitment.com / employer123
-- Candidate login: candidate1@recruitment.com / candidate123
