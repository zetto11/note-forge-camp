<?php
/**
 * StudyMate Helper Functions
 * Contains all utility functions used throughout the application
 */

require_once __DIR__ . '/../config/db.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require user to be logged in, redirect to login if not
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        $_SESSION['flash_message'] = ['type' => 'warning', 'message' => 'Please log in to continue.'];
        header('Location: login.php');
        exit;
    }
}

/**
 * Get current logged in user data
 */
function getCurrentUser(): ?array {
    global $pdo;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Sanitize output to prevent XSS
 */
function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF input field
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

/**
 * Set flash message
 */
function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlashMessage(): ?array {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Display flash message HTML
 */
function displayFlashMessage(): void {
    $flash = getFlashMessage();
    if ($flash) {
        $typeClasses = [
            'success' => 'alert-success',
            'error' => 'alert-error',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        $class = $typeClasses[$flash['type']] ?? 'alert-info';
        echo '<div class="alert ' . $class . '">';
        echo '<span>' . e($flash['message']) . '</span>';
        echo '<button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>';
        echo '</div>';
    }
}

/**
 * Validate email format
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 */
function isValidPassword(string $password): array {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    return $errors;
}

/**
 * Generate random token
 */
function generateToken(int $length = 32): string {
    return bin2hex(random_bytes($length));
}

/**
 * Hash password
 */
function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password
 */
function verifyPassword(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Send email using PHPMailer
 */
function sendEmail(string $to, string $subject, string $body, bool $isHTML = true): bool {
    // Check if PHPMailer is available
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        error_log("PHPMailer not installed. Run: composer require phpmailer/phpmailer");
        return false;
    }
    
    require_once $autoloadPath;
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send password reset email
 */
function sendPasswordResetEmail(string $email, string $token): bool {
    $resetLink = APP_URL . "/reset_password.php?email=" . urlencode($email) . "&token=" . $token;
    
    $subject = "Reset Your " . APP_NAME . " Password";
    
    $body = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #1F2937; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #1E40AF; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #F9FAFB; padding: 30px; border: 1px solid #E5E7EB; }
            .button { display: inline-block; background: #1E40AF; color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; margin: 20px 0; font-weight: bold; }
            .footer { text-align: center; padding: 20px; color: #6B7280; font-size: 12px; }
            .warning { background: #FEF3C7; padding: 15px; border-radius: 6px; margin-top: 20px; border-left: 4px solid #D97706; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>' . APP_NAME . '</h1>
                <p>Password Reset Request</p>
            </div>
            <div class="content">
                <h2>Hello,</h2>
                <p>We received a request to reset your password. Click the button below to create a new password:</p>
                <p style="text-align: center;">
                    <a href="' . $resetLink . '" class="button">Reset Password</a>
                </p>
                <p>Or copy and paste this link into your browser:</p>
                <p style="word-break: break-all; color: #1E40AF;">' . $resetLink . '</p>
                <div class="warning">
                    <strong>⚠️ Important:</strong> This link will expire in 1 hour. If you did not request this reset, please ignore this email.
                </div>
            </div>
            <div class="footer">
                <p>&copy; ' . date('Y') . ' ' . APP_NAME . '. All rights reserved.</p>
                <p>This is an automated message, please do not reply.</p>
            </div>
        </div>
    </body>
    </html>';
    
    return sendEmail($email, $subject, $body);
}

/**
 * Create password reset token
 */
function createPasswordResetToken(string $email): ?string {
    global $pdo;
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if (!$stmt->fetch()) {
        return null;
    }
    
    // Delete any existing tokens for this email
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$email]);
    
    // Create new token
    $token = generateToken();
    $hashedToken = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', time() + TOKEN_EXPIRY);
    
    $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$email, $hashedToken, $expiresAt]);
    
    return $token;
}

/**
 * Verify password reset token
 */
function verifyPasswordResetToken(string $email, string $token): bool {
    global $pdo;
    
    $hashedToken = hash('sha256', $token);
    
    $stmt = $pdo->prepare("
        SELECT * FROM password_resets 
        WHERE email = ? AND token = ? AND expires_at > NOW() AND used = 0
    ");
    $stmt->execute([$email, $hashedToken]);
    
    return $stmt->fetch() !== false;
}

/**
 * Mark password reset token as used
 */
function markTokenAsUsed(string $email, string $token): void {
    global $pdo;
    
    $hashedToken = hash('sha256', $token);
    
    $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE email = ? AND token = ?");
    $stmt->execute([$email, $hashedToken]);
}

/**
 * Get user's modules
 */
function getUserModules(int $userId): array {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT m.*, COUNT(n.id) as note_count 
        FROM modules m 
        LEFT JOIN notes n ON m.id = n.module_id 
        WHERE m.user_id = ? 
        GROUP BY m.id 
        ORDER BY m.name ASC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Get user's tags
 */
function getUserTags(int $userId): array {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM tags WHERE user_id = ? ORDER BY name ASC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Get note's tags
 */
function getNoteTags(int $noteId): array {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT t.* FROM tags t 
        INNER JOIN note_tags nt ON t.id = nt.tag_id 
        WHERE nt.note_id = ?
    ");
    $stmt->execute([$noteId]);
    return $stmt->fetchAll();
}

/**
 * Get user's notes with optional filters
 */
function getUserNotes(int $userId, ?int $moduleId = null, ?int $tagId = null, ?string $search = null): array {
    global $pdo;
    
    $sql = "
        SELECT n.*, m.name as module_name, m.color as module_color 
        FROM notes n 
        INNER JOIN modules m ON n.module_id = m.id 
        WHERE n.user_id = ? AND n.is_archived = 0
    ";
    $params = [$userId];
    
    if ($moduleId) {
        $sql .= " AND n.module_id = ?";
        $params[] = $moduleId;
    }
    
    if ($tagId) {
        $sql .= " AND n.id IN (SELECT note_id FROM note_tags WHERE tag_id = ?)";
        $params[] = $tagId;
    }
    
    if ($search) {
        $sql .= " AND (n.title LIKE ? OR n.content LIKE ?)";
        $searchTerm = '%' . $search . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY n.updated_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Format date for display
 */
function formatDate(string $date, string $format = 'M j, Y'): string {
    return date($format, strtotime($date));
}

/**
 * Format relative time
 */
function timeAgo(string $datetime): string {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return formatDate($datetime);
    }
}

/**
 * Log user activity
 */
function logActivity(int $userId, string $action, string $entityType, ?int $entityId = null, ?string $details = null): void {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO activity_log (user_id, action, entity_type, entity_id, details) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $action, $entityType, $entityId, $details]);
}

/**
 * Get user's study statistics
 */
function getUserStats(int $userId): array {
    global $pdo;
    
    $stats = [];
    
    // Total notes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['total_notes'] = $stmt->fetchColumn();
    
    // Total modules
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['total_modules'] = $stmt->fetchColumn();
    
    // Total flashcards
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM flashcards WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['total_flashcards'] = $stmt->fetchColumn();
    
    // Study time this week (in minutes)
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(duration_minutes), 0) 
        FROM study_sessions 
        WHERE user_id = ? AND started_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND completed = 1
    ");
    $stmt->execute([$userId]);
    $stats['study_time_week'] = $stmt->fetchColumn();
    
    // Study streak (consecutive days)
    $stmt = $pdo->prepare("
        SELECT DATE(started_at) as study_date 
        FROM study_sessions 
        WHERE user_id = ? AND completed = 1 
        GROUP BY DATE(started_at) 
        ORDER BY study_date DESC
    ");
    $stmt->execute([$userId]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $streak = 0;
    $currentDate = new DateTime();
    foreach ($dates as $date) {
        $studyDate = new DateTime($date);
        $diff = $currentDate->diff($studyDate)->days;
        
        if ($diff <= 1) {
            $streak++;
            $currentDate = $studyDate;
        } else {
            break;
        }
    }
    $stats['study_streak'] = $streak;
    
    return $stats;
}

/**
 * Get user's flashcards for review
 */
function getFlashcardsForReview(int $userId, ?int $moduleId = null, int $limit = 10): array {
    global $pdo;
    
    $sql = "
        SELECT f.*, m.name as module_name, m.color as module_color 
        FROM flashcards f 
        INNER JOIN modules m ON f.module_id = m.id 
        WHERE f.user_id = ? AND (f.next_review IS NULL OR f.next_review <= NOW())
    ";
    $params = [$userId];
    
    if ($moduleId) {
        $sql .= " AND f.module_id = ?";
        $params[] = $moduleId;
    }
    
    $sql .= " ORDER BY f.next_review ASC, RAND() LIMIT ?";
    $params[] = $limit;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Update flashcard after review
 */
function updateFlashcardReview(int $flashcardId, bool $correct): void {
    global $pdo;
    
    // Get current flashcard data
    $stmt = $pdo->prepare("SELECT times_reviewed, times_correct, difficulty FROM flashcards WHERE id = ?");
    $stmt->execute([$flashcardId]);
    $card = $stmt->fetch();
    
    if (!$card) return;
    
    $timesReviewed = $card['times_reviewed'] + 1;
    $timesCorrect = $correct ? $card['times_correct'] + 1 : $card['times_correct'];
    
    // Calculate next review date based on spaced repetition
    $successRate = $timesReviewed > 0 ? $timesCorrect / $timesReviewed : 0;
    
    if ($correct) {
        if ($successRate >= 0.8) {
            $nextReview = date('Y-m-d H:i:s', strtotime('+7 days'));
        } elseif ($successRate >= 0.6) {
            $nextReview = date('Y-m-d H:i:s', strtotime('+3 days'));
        } else {
            $nextReview = date('Y-m-d H:i:s', strtotime('+1 day'));
        }
    } else {
        $nextReview = date('Y-m-d H:i:s', strtotime('+4 hours'));
    }
    
    $stmt = $pdo->prepare("
        UPDATE flashcards 
        SET times_reviewed = ?, times_correct = ?, last_reviewed = NOW(), next_review = ? 
        WHERE id = ?
    ");
    $stmt->execute([$timesReviewed, $timesCorrect, $nextReview, $flashcardId]);
}

/**
 * Get shared notes with user
 */
function getSharedNotes(int $userId): array {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT n.*, m.name as module_name, m.color as module_color, 
               u.username as shared_by_name, sn.permission, sn.created_at as shared_at
        FROM shared_notes sn
        INNER JOIN notes n ON sn.note_id = n.id
        INNER JOIN modules m ON n.module_id = m.id
        INNER JOIN users u ON sn.shared_by = u.id
        WHERE sn.shared_with = ?
        ORDER BY sn.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Get user's study groups
 */
function getUserStudyGroups(int $userId): array {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT g.*, gm.role, u.username as owner_name,
               (SELECT COUNT(*) FROM study_group_members WHERE group_id = g.id) as member_count
        FROM study_groups g
        INNER JOIN study_group_members gm ON g.id = gm.group_id
        INNER JOIN users u ON g.owner_id = u.id
        WHERE gm.user_id = ?
        ORDER BY g.name ASC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Upload file with validation
 */
function uploadFile(array $file, string $destination, array $allowedTypes = ALLOWED_EXTENSIONS, int $maxSize = UPLOAD_MAX_SIZE): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedTypes)) {
        return null;
    }
    
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = $destination . $filename;
    
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $filename;
    }
    
    return null;
}

/**
 * Truncate text to a specific length
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Convert markdown to HTML (simple conversion)
 */
function markdownToHtml(string $text): string {
    // Headers
    $text = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $text);
    
    // Bold and italic
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
    
    // Code blocks
    $text = preg_replace('/```(.+?)```/s', '<pre><code>$1</code></pre>', $text);
    $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);
    
    // Lists
    $text = preg_replace('/^- (.+)$/m', '<li>$1</li>', $text);
    $text = preg_replace('/(<li>.+<\/li>\n?)+/', '<ul>$0</ul>', $text);
    
    // Line breaks
    $text = nl2br($text);
    
    return $text;
}
