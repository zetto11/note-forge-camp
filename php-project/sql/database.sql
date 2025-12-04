-- StudentHub Database Schema
-- Run this in phpMyAdmin or MySQL CLI

-- Create database
CREATE DATABASE IF NOT EXISTS studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Modules table
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    user_id INT NOT NULL,
    color VARCHAR(7) DEFAULT '#3B82F6',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- Notes table
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    module_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_module_id (module_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
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

-- Insert sample data for testing (optional)
-- Password is: password123 (hashed with bcrypt)
INSERT INTO users (username, email, password) VALUES 
('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample modules for test user
INSERT INTO modules (name, user_id, color) VALUES 
('Web Development', 1, '#3B82F6'),
('Object-Oriented Programming', 1, '#10B981'),
('Mathematics', 1, '#F59E0B'),
('Database Systems', 1, '#EF4444');

-- Sample notes
INSERT INTO notes (title, content, module_id, user_id) VALUES 
('HTML Basics', 'HTML (HyperText Markup Language) is the standard markup language for creating web pages. Key elements include:\n\n- <!DOCTYPE html> - Declaration\n- <html> - Root element\n- <head> - Meta information\n- <body> - Page content\n\nSemantic tags: header, nav, main, section, article, footer', 1, 1),
('CSS Flexbox', 'Flexbox is a one-dimensional layout method for arranging items in rows or columns.\n\nContainer properties:\n- display: flex\n- flex-direction\n- justify-content\n- align-items\n- flex-wrap\n\nItem properties:\n- flex-grow\n- flex-shrink\n- flex-basis', 1, 1),
('OOP Principles', 'The four pillars of OOP:\n\n1. Encapsulation - Bundling data and methods\n2. Abstraction - Hiding complexity\n3. Inheritance - Code reuse through parent-child relationships\n4. Polymorphism - Same interface, different implementations', 2, 1),
('Linear Algebra', 'Key concepts:\n\n- Vectors and vector spaces\n- Matrix operations (addition, multiplication)\n- Determinants\n- Eigenvalues and eigenvectors\n- Linear transformations', 3, 1);
