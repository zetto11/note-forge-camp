<?php
/**
 * Helper Functions
 * StudentHub - Student Notes Manager
 */

require_once __DIR__ . '/../config/db.php';

/**
 * Sanitize output to prevent XSS
 * @param string $data
 * @return string
 */
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate random token for password reset
 * @return string
 */
function generateToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username
 * @return string|null
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Redirect with message
 * @param string $url
 * @param string $message
 * @param string $type (success, error, warning, info)
 */
function redirect($url, $message = '', $type = 'info') {
    if (!empty($message)) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

/**
 * Display flash message
 * @return string HTML
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = escape($_SESSION['flash_message']);
        $type = $_SESSION['flash_type'] ?? 'info';
        
        $classes = [
            'success' => 'alert-success',
            'error' => 'alert-error',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        $class = $classes[$type] ?? 'alert-info';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return "<div class='alert $class'>$message</div>";
    }
    return '';
}

/**
 * Validate email format
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * @param string $password
 * @return array [bool valid, string message]
 */
function validatePassword($password) {
    if (strlen($password) < 8) {
        return [false, 'Password must be at least 8 characters long'];
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return [false, 'Password must contain at least one uppercase letter'];
    }
    if (!preg_match('/[a-z]/', $password)) {
        return [false, 'Password must contain at least one lowercase letter'];
    }
    if (!preg_match('/[0-9]/', $password)) {
        return [false, 'Password must contain at least one number'];
    }
    return [true, ''];
}

/**
 * Get user by email
 * @param string $email
 * @return array|false
 */
function getUserByEmail($email) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

/**
 * Get user by ID
 * @param int $id
 * @return array|false
 */
function getUserById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Get all modules for a user
 * @param int $userId
 * @return array
 */
function getUserModules($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM modules WHERE user_id = ? ORDER BY name");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Get all notes for a user (optionally filtered by module)
 * @param int $userId
 * @param int|null $moduleId
 * @return array
 */
function getUserNotes($userId, $moduleId = null) {
    $db = getDB();
    
    if ($moduleId) {
        $stmt = $db->prepare("
            SELECT n.*, m.name as module_name, m.color as module_color 
            FROM notes n 
            JOIN modules m ON n.module_id = m.id 
            WHERE n.user_id = ? AND n.module_id = ? 
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([$userId, $moduleId]);
    } else {
        $stmt = $db->prepare("
            SELECT n.*, m.name as module_name, m.color as module_color 
            FROM notes n 
            JOIN modules m ON n.module_id = m.id 
            WHERE n.user_id = ? 
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([$userId]);
    }
    
    return $stmt->fetchAll();
}

/**
 * Get note by ID (with ownership check)
 * @param int $noteId
 * @param int $userId
 * @return array|false
 */
function getNoteById($noteId, $userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$noteId, $userId]);
    return $stmt->fetch();
}

/**
 * Get module by ID (with ownership check)
 * @param int $moduleId
 * @param int $userId
 * @return array|false
 */
function getModuleById($moduleId, $userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM modules WHERE id = ? AND user_id = ?");
    $stmt->execute([$moduleId, $userId]);
    return $stmt->fetch();
}

/**
 * Format date for display
 * @param string $date
 * @return string
 */
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Format datetime for display
 * @param string $datetime
 * @return string
 */
function formatDateTime($datetime) {
    return date('M d, Y H:i', strtotime($datetime));
}

/**
 * Truncate text
 * @param string $text
 * @param int $length
 * @return string
 */
function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Count notes for a module
 * @param int $moduleId
 * @return int
 */
function countModuleNotes($moduleId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM notes WHERE module_id = ?");
    $stmt->execute([$moduleId]);
    return $stmt->fetchColumn();
}
?>
