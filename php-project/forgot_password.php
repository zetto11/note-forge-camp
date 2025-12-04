<?php
/**
 * Forgot Password Page
 * StudentHub - Student Notes Manager
 * 
 * Sends password reset email via PHPMailer + Gmail SMTP
 */

require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$errors = [];
$success = false;
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            $errors[] = 'Please enter your email address';
        } elseif (!isValidEmail($email)) {
            $errors[] = 'Please enter a valid email address';
        } else {
            // Check if user exists
            $user = getUserByEmail($email);
            
            // Always show success message to prevent email enumeration
            $success = true;
            
            if ($user) {
                try {
                    $db = getDB();
                    
                    // Generate token
                    $token = generateToken();
                    $expiresAt = date('Y-m-d H:i:s', time() + TOKEN_EXPIRY);
                    
                    // Delete any existing tokens for this email
                    $stmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
                    $stmt->execute([$email]);
                    
                    // Insert new token
                    $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
                    $stmt->execute([$email, $token, $expiresAt]);
                    
                    // Send email with PHPMailer
                    require_once 'vendor/autoload.php';
                    
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host = SMTP_HOST;
                        $mail->SMTPAuth = true;
                        $mail->Username = SMTP_USER;
                        $mail->Password = SMTP_PASS;
                        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = SMTP_PORT;
                        
                        // Recipients
                        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
                        $mail->addAddress($email, $user['username']);
                        
                        // Content
                        $resetLink = SITE_URL . "/reset_password.php?email=" . urlencode($email) . "&token=" . $token;
                        
                        $mail->isHTML(true);
                        $mail->Subject = 'Reset Your StudentHub Password';
                        $mail->Body = "
                            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                                <div style='background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); padding: 30px; text-align: center;'>
                                    <h1 style='color: white; margin: 0;'>StudentHub</h1>
                                </div>
                                <div style='padding: 30px; background: #f8fafc;'>
                                    <h2 style='color: #0f172a;'>Password Reset Request</h2>
                                    <p style='color: #475569;'>Hi {$user['username']},</p>
                                    <p style='color: #475569;'>We received a request to reset your password. Click the button below to create a new password:</p>
                                    <div style='text-align: center; margin: 30px 0;'>
                                        <a href='{$resetLink}' style='background-color: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block;'>Reset Password</a>
                                    </div>
                                    <p style='color: #475569; font-size: 14px;'>This link will expire in 1 hour.</p>
                                    <p style='color: #475569; font-size: 14px;'>If you didn't request this, you can safely ignore this email.</p>
                                    <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                                    <p style='color: #94a3b8; font-size: 12px;'>If the button doesn't work, copy and paste this link into your browser:</p>
                                    <p style='color: #94a3b8; font-size: 12px; word-break: break-all;'>{$resetLink}</p>
                                </div>
                            </div>
                        ";
                        $mail->AltBody = "Reset your password by visiting: {$resetLink}\n\nThis link expires in 1 hour.";
                        
                        $mail->send();
                    } catch (Exception $e) {
                        // Log error but don't expose to user
                        error_log("PHPMailer Error: " . $mail->ErrorInfo);
                    }
                } catch (PDOException $e) {
                    error_log("Database Error: " . $e->getMessage());
                }
            }
        }
    }
}

$pageTitle = 'Forgot Password';
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
            <h1>Forgot Password?</h1>
            <p>Enter your email and we'll send you a reset link</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <p style="margin: 0;"><strong>Check your email!</strong></p>
                <p style="margin: 0.5rem 0 0 0;">If an account exists with that email, we've sent password reset instructions.</p>
            </div>
            <div class="auth-footer">
                <a href="login.php">← Back to Login</a>
            </div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p style="margin: 0;"><?= escape($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="forgot_password.php" data-validate>
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
                
                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                    Send Reset Link
                </button>
            </form>
            
            <div class="auth-footer">
                Remember your password? <a href="login.php">Log in</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
