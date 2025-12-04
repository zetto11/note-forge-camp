<?php
/**
 * Registration Page
 * StudentHub - Student Notes Manager
 */

require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$errors = [];
$username = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate username
        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'Username must be between 3 and 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores';
        }
        
        // Validate email
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!isValidEmail($email)) {
            $errors[] = 'Please enter a valid email address';
        } else {
            // Check if email exists
            if (getUserByEmail($email)) {
                $errors[] = 'This email is already registered';
            }
        }
        
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
        
        // If no errors, create user
        if (empty($errors)) {
            try {
                $db = getDB();
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashedPassword]);
                
                redirect('login.php', 'Registration successful! Please log in.', 'success');
            } catch (PDOException $e) {
                $errors[] = 'An error occurred. Please try again.';
            }
        }
    }
}

$pageTitle = 'Register';
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
            <h1>Create an Account</h1>
            <p>Start organizing your study notes today</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= escape($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="register.php" data-validate>
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-input" 
                    placeholder="Choose a username"
                    value="<?= escape($username) ?>"
                    required
                    autocomplete="username"
                >
                <div class="form-hint">Letters, numbers, and underscores only</div>
            </div>
            
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
                    placeholder="Create a strong password"
                    required
                    autocomplete="new-password"
                >
                <div class="form-hint">At least 8 characters with uppercase, lowercase, and number</div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password</label>
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
                Create Account
            </button>
        </form>
        
        <div class="auth-footer">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
