<?php
/**
 * Database Configuration
 * StudentHub - Student Notes Manager
 * 
 * Configure your database and email settings here.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'studenthub');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default for XAMPP, change if needed

// Email Configuration (Gmail SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com'); // Replace with your Gmail
define('SMTP_PASS', 'your-app-password');     // Replace with App Password
define('SMTP_FROM_NAME', 'StudentHub');

// Site Configuration
define('SITE_URL', 'http://localhost/studenthub');
define('SITE_NAME', 'StudentHub');

// Token expiration time (in seconds) - 1 hour
define('TOKEN_EXPIRY', 3600);

/**
 * Database Connection Class using PDO
 */
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Singleton pattern - get instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Get connection
    public function getConnection() {
        return $this->conn;
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Get database connection
 * @return PDO
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
?>
