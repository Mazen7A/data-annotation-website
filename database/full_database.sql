-- ========================================================
-- Saudi Culture Annotation Platform - Full Database Schema & Seed Data
-- ========================================================
DROP DATABASE IF EXISTS saudi_culture;

CREATE DATABASE saudi_culture CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE saudi_culture;

-- ========================================================
-- 1. Table Definitions
-- ========================================================
-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'manager') DEFAULT 'user',
    theme_preference ENUM('light', 'dark', 'auto') DEFAULT 'auto',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Settings Table
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'color', 'boolean', 'json') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Projects Table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    summary TEXT,
    description TEXT,
    category VARCHAR(255),
    image_url VARCHAR(255) NULL,
    -- Location Data Columns
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    location_name VARCHAR(255) NULL,
    created_by INT NOT NULL,
    total_questions INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_category (category),
    INDEX idx_created_by (created_by),
    INDEX idx_location (latitude, longitude)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Project Commits Table
CREATE TABLE project_commits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Questions Table
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('mcq', 'true_false', 'open', 'list') NOT NULL,
    category VARCHAR(255) NULL,
    media_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_question_type (question_type)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Question Options Table
CREATE TABLE question_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    is_correct BOOLEAN DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_question_id (question_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Answers Table
CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_text TEXT NULL,
    selected_options JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_question (user_id, question_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Sessions Table
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_id INT NOT NULL,
    progress FLOAT DEFAULT 0,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_project (user_id, project_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Reviews Table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    answer_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    score INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (answer_id) REFERENCES answers(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_answer_review (answer_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Contact Messages Table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    type ENUM(
        'technical_issue',
        'feedback',
        'project_question',
        'feature_request',
        'bug_report',
        'other'
    ) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'in_progress', 'resolved') DEFAULT 'pending',
    admin_reply TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE
    SET
        NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Project Comments Table
CREATE TABLE project_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ========================================================
-- 2. Seed Data
-- ========================================================
-- --------------------------------------------------------
-- Default Settings
-- --------------------------------------------------------
INSERT INTO
    settings (setting_key, setting_value, setting_type)
VALUES
    ('default_theme', 'purple', 'text'),
    ('default_mode', 'auto', 'text'),
    (
        'available_themes',
        '["purple","blue","green","orange","gold","night"]',
        'json'
    ),
    ('enable_animations', 'true', 'boolean'),
    ('primary_color', '#667eea', 'color'),
    ('secondary_color', '#764ba2', 'color');

-- --------------------------------------------------------
-- Test Users
-- --------------------------------------------------------
INSERT INTO
    users (
        id,
        name,
        email,
        password,
        role,
        theme_preference
    )
VALUES
    (
        1,
        'مازن',
        'mazen@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'manager',
        'auto'
    ),
    (
        2,
        'فاطمة السعيد',
        'manager@test.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'manager',
        'auto'
    ),
    (
        3,
        'خالد العتيبي',
        'khalid@test.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'user',
        'light'
    ),
    (
        4,
        'نورة القحطاني',
        'noura@test.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'manager',
        'dark'
    );

-- --------------------------------------------------------
-- Projects (With Location Data Added)
-- --------------------------------------------------------
-- INSERT INTO projects (id, name, summary, description, category, image_url, latitude, longitude, location_name, created_by, total_questions) VALUES
-- (
--     1,
--     'الأزياء التقليدية السعودية',
--     'توثيق الأزياء التقليدية في مناطق المملكة المختلفة',
--     'مشروع شامل لتوثيق وتصنيف الأزياء التقليدية السعودية عبر المناطق المختلفة، يشمل الثوب، الشماغ، البشت، والعباءة وغيرها من الملابس التراثية.',
--     'تراث',
--     'uploads/projects/traditional_clothing.jpg',
--     24.7136, 
--     46.6753, 
--     'المتحف الوطني، الرياض',
--     2,
--     5
-- ),
-- (
--     2,
--     'الأطعمة الشعبية السعودية',
--     'جمع معلومات عن الأطباق التقليدية في المناطق السعودية',
--     'مشروع لتوثيق الأطباق الشعبية السعودية مثل الكبسة، المندي، الجريش، القرصان، والعديد من الأطباق الأخرى مع تفاصيل المكونات وطرق التحضير.',
--     'طعام',
--     'uploads/projects/traditional_food.jpg',
--     21.4901, 
--     39.1862, 
--     'جدة التاريخية (البلد)',
--     2,
--     4
-- ),
-- (
--     3,
--     'المعالم التراثية في المملكة',
--     'توثيق المواقع والمعالم التراثية والأثرية',
--     'مشروع لتوثيق المعالم التراثية والأثرية في المملكة العربية السعودية، بما في ذلك القلاع، الحصون، القرى التراثية، والمواقع الأثرية.',
--     'معالم',
--     'uploads/projects/heritage_sites.jpg',
--     26.6173, 
--     37.9309, 
--     'مدائن صالح (الحجر)، العلا',
--     4,
--     6
-- );
-- --------------------------------------------------------
-- Project Commits
-- --------------------------------------------------------
-- INSERT INTO project_commits (project_id, user_id, message) VALUES
-- (1, 2, 'إنشاء المشروع: الأزياء التقليدية السعودية'),
-- (2, 2, 'إنشاء المشروع: الأطعمة الشعبية السعودية'),
-- (3, 4, 'إنشاء المشروع: المعالم التراثية في المملكة'),
-- (1, 2, 'إضافة 5 أسئلة للمشروع'),
-- (2, 2, 'إضافة 4 أسئلة للمشروع');
-- --------------------------------------------------------
-- Questions & Options
-- --------------------------------------------------------
-- -- PROJECT 1: CLOTHING --
-- -- Q1
-- INSERT INTO questions (id, project_id, question_text, question_type, category) VALUES 
-- (1, 1, 'ما هو الزي التقليدي الرجالي الأكثر شيوعاً في المملكة؟', 'mcq', 'أزياء رجالية');
-- INSERT INTO question_options (question_id, option_text, is_correct) VALUES
-- (1, 'الثوب والشماغ', 1),
-- (1, 'البنطلون والقميص', 0),
-- (1, 'الجلباب والطاقية', 0),
-- (1, 'السروال والقفطان', 0);
-- -- Q2
-- INSERT INTO questions (id, project_id, question_text, question_type, category) VALUES 
-- (2, 1, 'يعتبر البشت زياً يُلبس في المناسبات الرسمية فقط؟', 'true_false', 'أزياء رجالية');
-- INSERT INTO question_options (question_id, option_text, is_correct) VALUES
-- (2, 'صحيح', 1),
-- (2, 'خطأ', 0);
-- -- PROJECT 2: FOOD --
-- Q3
-- INSERT INTO questions (id, project_id, question_text, question_type, category) VALUES 
-- (3, 2, 'ما هو الطبق الوطني الأكثر شهرة في المملكة؟', 'mcq', 'أطباق رئيسية');
-- INSERT INTO question_options (question_id, option_text, is_correct) VALUES
-- (3, 'الكبسة', 1),
-- (3, 'المكرونة', 0),
-- (3, 'البيتزا', 0),
-- (3, 'البرجر', 0);
-- -- Q4
-- INSERT INTO questions (id, project_id, question_text, question_type, category) VALUES 
-- (4, 2, 'الجريش يُصنع من القمح المجروش؟', 'true_false', 'أطباق رئيسية');
-- INSERT INTO question_options (question_id, option_text, is_correct) VALUES
-- (4, 'صحيح', 1),
-- (4, 'خطأ', 0);
-- -- Q5 (Open Ended)
-- INSERT INTO questions (id, project_id, question_text, question_type, category) VALUES 
-- (5, 2, 'صف طريقة تحضير القهوة العربية التقليدية', 'open', 'مشروبات');
-- -- PROJECT 3: HERITAGE --
-- Q6
-- INSERT INTO questions (id, project_id, question_text, question_type, category) VALUES 
-- (6, 3, 'أي من هذه المواقع مدرج في قائمة اليونسكو للتراث العالمي؟', 'mcq', 'مواقع أثرية');
-- INSERT INTO question_options (question_id, option_text, is_correct) VALUES
-- (6, 'مدائن صالح', 1),
-- (6, 'برج إيفل', 0),
-- (6, 'تاج محل', 0),
-- (6, 'الكولوسيوم', 0);
-- -- Q7
-- INSERT INTO questions (id, project_id, question_text, question_type, category, media_url) VALUES 
-- (7, 3, 'ما اسم هذا الموقع الأثري؟', 'mcq', 'مواقع أثرية', 'uploads/questions/mada_saleh.jpg');
-- INSERT INTO question_options (question_id, option_text, is_correct) VALUES
-- (7, 'مدائن صالح (الحِجر)', 1),
-- (7, 'قصر المصمك', 0),
-- (7, 'برج المملكة', 0),
-- (7, 'قلعة تبوك', 0);
-- -- --------------------------------------------------------
-- -- Sessions
-- -- --------------------------------------------------------
-- INSERT INTO sessions (user_id, project_id, progress, started_at, completed_at) VALUES
-- (1, 1, 100, '2025-11-25 10:30:00', '2025-11-25 11:15:00'),
-- (1, 2, 50, '2025-11-26 14:20:00', NULL),
-- (3, 1, 80, '2025-11-27 09:00:00', NULL),
-- (3, 3, 33.33, '2025-11-28 16:45:00', NULL);
-- -- --------------------------------------------------------
-- -- Answers (Updated to match Question IDs)
-- -- --------------------------------------------------------
-- INSERT INTO answers (user_id, question_id, answer_text, selected_options) VALUES
-- -- User 1 Answers
-- (1, 1, NULL, '[1]'), -- Answered Q1 Correctly
-- (1, 2, NULL, '[5]'), -- Answered Q2 Correctly
-- (1, 3, NULL, '[9]'), -- Answered Q3 Correctly
-- (1, 5, 'يتم تحميص البن ثم طحنه واضافة الهيل والزعفران ويغلى في الدلة', NULL), -- Answered Q5
-- -- User 3 Answers
-- (3, 1, NULL, '[1]'), 
-- (3, 6, NULL, '[14]');
-- -- --------------------------------------------------------
-- -- Reviews
-- -- --------------------------------------------------------
-- INSERT INTO reviews (answer_id, reviewer_id, score, comment) VALUES
-- (4, 2, 10, 'إجابة صحيحة ومباشرة'), -- Reviewing User 1's Open Answer (ID 4 is the ID of the Answer row)
-- (1, 2, 10, 'إجابة صحيحة');
-- --------------------------------------------------------
-- Contact Messages
-- --------------------------------------------------------
INSERT INTO
    contact_messages (
        user_id,
        name,
        email,
        phone,
        type,
        subject,
        message,
        status,
        admin_reply
    )
VALUES
    (
        1,
        'أحمد محمد',
        'user@test.com',
        '0501234567',
        'feedback',
        'تجربة رائعة',
        'المنصة سهلة الاستخدام وممتعة، شكراً لكم',
        'resolved',
        'شكراً لك على ملاحظاتك الإيجابية!'
    ),
    (
        NULL,
        'زائر مهتم',
        'visitor@example.com',
        '0509876543',
        'project_question',
        'استفسار عن المشاريع',
        'هل يمكنني المشاركة في أكثر من مشروع في نفس الوقت؟',
        'in_progress',
        NULL
    ),
    (
        3,
        'خالد العتيبي',
        'khalid@test.com',
        NULL,
        'technical_issue',
        'مشكلة في رفع الصورة',
        'واجهت مشكلة عند محاولة رفع صورة في أحد الأسئلة',
        'pending',
        NULL
    );

-- Password Reset Table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_code (code),
    INDEX idx_expires (expires_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Bank Questions Tables
CREATE TABLE IF NOT EXISTS bank_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    question_type ENUM('mcq', 'true_false', 'open', 'list') NOT NULL,
    category VARCHAR(255) NULL,
    media_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_bq_type (question_type),
    INDEX idx_bq_category (category)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bank_question_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_question_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    is_correct BOOLEAN DEFAULT 0,
    FOREIGN KEY (bank_question_id) REFERENCES bank_questions(id) ON DELETE CASCADE,
    INDEX idx_bq_id (bank_question_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;