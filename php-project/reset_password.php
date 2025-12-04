<?php
/**
 * Reset Password Page
 * StudentHub - Student Notes Manager
 * 
 * Verifies token and allows password reset
 */

require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$errors = [];
$success = false;
$validToken = false;

$email = $_GET['email'] ?? $_POST['email'] ?? '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

// Verify token
if (!empty($email) && !empty($token)) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT * FROM password_resets 
        WHERE email = ? AND token = ? AND expires_at > NOW() AND used = 0
    ");
    $stmt->execute([$email, $token]);
    $reset = $stmt->fetch();
    
    if ($reset) {
        $validToken = true;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate password
        if (empty($password)) {
            $errors[] = 'Password is required';
        } else {
            list($valid, $message) = validatePassword($password);
            if (!$valid) {
                $errors[] = $message;
            }
        }
        
        // Confirm password
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        // If no errors, update password
        if (empty($errors)) {
            try {
                $db = getDB();
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Update user password
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->execute([$hashedPassword, $email]);
                
                // Mark token as used
                $stmt = $db->prepare("UPDATE password_resets SET used = 1 WHERE email = ? AND token = ?");
                $stmt->execute([$email, $token]);
                
                $success = true;
            } catch (PDOException $e) {
                $errors[] = 'An error occurred. Please try again.';
            }
        }
    }
}

$pageTitle = 'Reset Password';
$bodyClass = 'auth-page';
require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="card auth-card">
        <div class="auth-header">
            <a href="index.php" class="logo">
                <div class="logo-icon">📚</div>
                <span>StudentHub</span>
            </a>
            <?php if ($success): ?>
                <h1>Password Updated!</h1>
                <p>Your password has been successfully changed</p>
            <?php elseif ($validToken): ?>
                <h1>Create New Password</h1>
                <p>Enter your new password below</p>
            <?php else: ?>
                <h1>Invalid Link</h1>
                <p>This reset link is invalid or has expired</p>
            <?php endif; ?>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <p style="margin: 0;">Your password has been reset successfully. You can now log in with your new password.</p>
            </div>
            <a href="login.php" class="btn btn-primary btn-lg" style="width: 100%;">
                Go to Login
            </a>
        <?php elseif ($validToken): ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= escape($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="reset_password.php" data-validate>
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="email" value="<?= escape($email) ?>">
                <input type="hidden" name="token" value="<?= escape($token) ?>">
                
                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Create a strong password"
                        required
                        autocomplete="new-password"
                    >
                    <div class="form-hint">At least 8 characters with uppercase, lowercase, and number</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-input" 
                        placeholder="Confirm your password"
                        required
                        autocomplete="new-password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                    Reset Password
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-error">
                <p style="margin: 0;">This password reset link is invalid or has expired. Please request a new one.</p>
            </div>
            <a href="forgot_password.php" class="btn btn-primary btn-lg" style="width: 100%;">
                Request New Link
            </a>
        <?php endif; ?>
        
        <div class="auth-footer">
            <a href="login.php">← Back to Login</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
