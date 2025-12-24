<?php
$pageTitle = 'Login';
require_once 'includes/header.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to continue learning</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" data-validate="login">
            <?= csrfField() ?>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="<?= e($email) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            
            <div class="d-flex justify-between align-center mb-4">
                <label class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input">
                    <span class="form-check-label">Remember me</span>
                </label>
                <a href="forgot_password.php">Forgot password?</a>
            </div>
            
            <button type="submit" class="btn btn-primary w-full">Sign In</button>
        </form>
        
        <div class="auth-footer">
            Don't have an account? <a href="register.php">Sign up</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
