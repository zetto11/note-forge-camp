-- StudyMate Database Schema
-- Enhanced Student Learning Platform
-- Run this in phpMyAdmin or MySQL CLI

-- Create database
CREATE DATABASE IF NOT EXISTS studymate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studymate;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    study_goal_hours INT DEFAULT 2,
    email_notifications TINYINT(1) DEFAULT 1,
    theme VARCHAR(20) DEFAULT 'light',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Modules table
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    user_id INT NOT NULL,
    color VARCHAR(7) DEFAULT '#1E40AF',
    icon VARCHAR(50) DEFAULT 'book',
    is_shared TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- Tags table
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(7) DEFAULT '#6B7280',
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_tag_user (name, user_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- Notes table
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    module_id INT NOT NULL,
    user_id INT NOT NULL,
    is_favorite TINYINT(1) DEFAULT 0,
    is_archived TINYINT(1) DEFAULT 0,
    view_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_module_id (module_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    FULLTEXT idx_search (title, content)
) ENGINE=InnoDB;

-- Note-Tags pivot table
CREATE TABLE IF NOT EXISTS note_tags (
    note_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (note_id, tag_id),
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Flashcards table
CREATE TABLE IF NOT EXISTS flashcards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    note_id INT DEFAULT NULL,
    module_id INT NOT NULL,
    user_id INT NOT NULL,
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    times_reviewed INT DEFAULT 0,
    times_correct INT DEFAULT 0,
    last_reviewed DATETIME DEFAULT NULL,
    next_review DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE SET NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_module (user_id, module_id),
    INDEX idx_next_review (next_review)
) ENGINE=InnoDB;

-- Study sessions table (Pomodoro tracking)
CREATE TABLE IF NOT EXISTS study_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    module_id INT DEFAULT NULL,
    duration_minutes INT NOT NULL,
    session_type ENUM('pomodoro', 'short_break', 'long_break', 'free_study') DEFAULT 'pomodoro',
    notes TEXT DEFAULT NULL,
    started_at DATETIME NOT NULL,
    ended_at DATETIME DEFAULT NULL,
    completed TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_started_at (started_at)
) ENGINE=InnoDB;

-- Study goals table
CREATE TABLE IF NOT EXISTS study_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    target_hours INT NOT NULL,
    current_hours DECIMAL(5,2) DEFAULT 0,
    deadline DATE DEFAULT NULL,
    status ENUM('active', 'completed', 'abandoned') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- Shared notes table (Collaboration)
CREATE TABLE IF NOT EXISTS shared_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    shared_by INT NOT NULL,
    shared_with INT NOT NULL,
    permission ENUM('view', 'edit') DEFAULT 'view',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_with) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_share (note_id, shared_with),
    INDEX idx_shared_with (shared_with)
) ENGINE=InnoDB;

-- Study groups table
CREATE TABLE IF NOT EXISTS study_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    owner_id INT NOT NULL,
    invite_code VARCHAR(20) UNIQUE NOT NULL,
    is_public TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_invite_code (invite_code)
) ENGINE=InnoDB;

-- Study group members table
CREATE TABLE IF NOT EXISTS study_group_members (
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('owner', 'admin', 'member') DEFAULT 'member',
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (group_id, user_id),
    FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Group shared modules
CREATE TABLE IF NOT EXISTS group_modules (
    group_id INT NOT NULL,
    module_id INT NOT NULL,
    shared_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (group_id, module_id),
    FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Password resets table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB;

-- User activity log
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT DEFAULT NULL,
    details TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_created (user_id, created_at)
) ENGINE=InnoDB;

-- Reminders table
CREATE TABLE IF NOT EXISTS reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    remind_at DATETIME NOT NULL,
    is_sent TINYINT(1) DEFAULT 0,
    module_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE SET NULL,
    INDEX idx_remind_at (remind_at),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- Insert sample data for testing
-- Password is: password123 (hashed with bcrypt)
INSERT INTO users (username, email, password, bio, study_goal_hours) VALUES 
('johndoe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science student passionate about learning.', 4),
('janedoe', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Engineering student who loves mathematics.', 3);

-- Sample modules
INSERT INTO modules (name, description, user_id, color, icon) VALUES 
('Web Development', 'HTML, CSS, JavaScript, and frameworks', 1, '#1E40AF', 'code'),
('Object-Oriented Programming', 'OOP concepts and design patterns', 1, '#059669', 'boxes'),
('Mathematics', 'Calculus, Linear Algebra, and Statistics', 1, '#D97706', 'calculator'),
('Database Systems', 'SQL, NoSQL, and database design', 1, '#DC2626', 'database'),
('Algorithms', 'Data structures and algorithms', 2, '#7C3AED', 'git-branch');

-- Sample tags
INSERT INTO tags (name, color, user_id) VALUES 
('Important', '#DC2626', 1),
('Review', '#D97706', 1),
('Exam', '#7C3AED', 1),
('Practice', '#059669', 1),
('Concept', '#1E40AF', 1),
('Formula', '#EC4899', 2);

-- Sample notes
INSERT INTO notes (title, content, module_id, user_id, is_favorite) VALUES 
('HTML5 Fundamentals', '# HTML5 Basics\n\nHTML (HyperText Markup Language) is the foundation of web pages.\n\n## Key Elements\n- `<!DOCTYPE html>` - Document declaration\n- `<html>` - Root element\n- `<head>` - Meta information container\n- `<body>` - Visible content\n\n## Semantic Elements\n- `<header>` - Page header\n- `<nav>` - Navigation links\n- `<main>` - Main content\n- `<article>` - Self-contained content\n- `<section>` - Thematic grouping\n- `<footer>` - Page footer', 1, 1, 1),
('CSS Flexbox Guide', '# Flexbox Layout\n\nFlexbox is a one-dimensional layout method.\n\n## Container Properties\n```css\n.container {\n  display: flex;\n  flex-direction: row | column;\n  justify-content: center;\n  align-items: center;\n  flex-wrap: wrap;\n}\n```\n\n## Item Properties\n```css\n.item {\n  flex-grow: 1;\n  flex-shrink: 0;\n  flex-basis: 200px;\n  order: 1;\n}\n```', 1, 1, 0),
('OOP Four Pillars', '# Object-Oriented Programming\n\n## 1. Encapsulation\nBundling data and methods that operate on that data within a single unit.\n\n## 2. Abstraction\nHiding complex implementation details and showing only necessary features.\n\n## 3. Inheritance\nCreating new classes based on existing classes.\n\n## 4. Polymorphism\nObjects of different classes can be treated as objects of a common parent class.', 2, 1, 1),
('Linear Algebra Basics', '# Linear Algebra\n\n## Vectors\nA vector is an ordered list of numbers.\n\n## Matrix Operations\n- Addition: A + B\n- Scalar multiplication: kA\n- Matrix multiplication: AB\n\n## Key Concepts\n- Determinants\n- Eigenvalues\n- Eigenvectors\n- Linear transformations', 3, 1, 0);

-- Sample note tags
INSERT INTO note_tags (note_id, tag_id) VALUES 
(1, 1), (1, 5),
(2, 4),
(3, 1), (3, 3),
(4, 5);

-- Sample flashcards
INSERT INTO flashcards (question, answer, module_id, user_id, difficulty) VALUES 
('What does HTML stand for?', 'HyperText Markup Language', 1, 1, 'easy'),
('What is the difference between == and === in JavaScript?', '== compares values with type coercion, === compares values and types strictly', 1, 1, 'medium'),
('Name the four pillars of OOP', 'Encapsulation, Abstraction, Inheritance, Polymorphism', 2, 1, 'medium'),
('What is a primary key in databases?', 'A unique identifier for each record in a table that ensures no duplicate rows', 4, 1, 'easy');

-- Sample study group
INSERT INTO study_groups (name, description, owner_id, invite_code) VALUES 
('CS Study Group', 'A group for Computer Science students to collaborate and share notes', 1, 'CS2024STUDY');

INSERT INTO study_group_members (group_id, user_id, role) VALUES 
(1, 1, 'owner'),
(1, 2, 'member');

-- Sample study sessions
INSERT INTO study_sessions (user_id, module_id, duration_minutes, session_type, started_at, ended_at, completed) VALUES 
(1, 1, 25, 'pomodoro', DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_SUB(NOW(), INTERVAL 95 MINUTE), 1),
(1, 1, 5, 'short_break', DATE_SUB(NOW(), INTERVAL 95 MINUTE), DATE_SUB(NOW(), INTERVAL 90 MINUTE), 1),
(1, 2, 25, 'pomodoro', DATE_SUB(NOW(), INTERVAL 90 MINUTE), DATE_SUB(NOW(), INTERVAL 65 MINUTE), 1);
