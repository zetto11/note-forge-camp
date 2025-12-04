<?php
/**
 * Login Page
 * StudentHub - Student Notes Manager
 */

require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$errors = [];
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate inputs
        if (empty($email) || empty($password)) {
            $errors[] = 'Please enter both email and password';
        } else {
            // Get user by email
            $user = getUserByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                redirect('dashboard.php', 'Welcome back, ' . $user['username'] . '!', 'success');
            } else {
                $errors[] = 'Invalid email or password';
            }
        }
    }
}

$pageTitle = 'Login';
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
            <h1>Welcome Back</h1>
            <p>Log in to access your notes</p>
        </div>
        
        <?= displayFlashMessage() ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p style="margin: 0;"><?= escape($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php" data-validate>
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="you@example.com"
                    value="<?= escape($email) ?>"
                    required
                    autocomplete="email"
                >
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                >
            </div>
            
            <div style="text-align: right; margin-bottom: 1rem;">
                <a href="forgot_password.php" style="font-size: 0.875rem;">Forgot password?</a>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                Log In
            </button>
        </form>
        
        <div class="auth-footer">
            Don't have an account? <a href="register.php">Sign up</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
