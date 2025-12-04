<?php
/**
 * Landing Page
 * StudentHub - Student Notes Manager
 */

require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$pageTitle = 'Welcome';
$bodyClass = 'landing-page';
require_once 'includes/header.php';
?>

<nav class="landing-nav">
    <a href="index.php" class="logo">
        <div class="logo-icon">📚</div>
        <span>StudentHub</span>
    </a>
    <div class="nav-links">
        <a href="login.php" class="btn btn-ghost">Log In</a>
        <a href="register.php" class="btn btn-primary">Get Started</a>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <h1>Organize Your Learning Journey</h1>
        <p>StudentHub helps you manage your notes, organize by module, and supercharge your studying with smart organization tools. Stay on top of your courses and ace your exams.</p>
        <div class="hero-buttons">
            <a href="register.php" class="btn btn-primary btn-lg">Start Free Today</a>
            <a href="login.php" class="btn btn-secondary btn-lg">I Have an Account</a>
        </div>
    </div>
</section>

<section class="features">
    <div class="feature-card card">
        <div class="feature-icon">📝</div>
        <h3>Smart Notes</h3>
        <p>Create, edit, and organize your notes with a clean, distraction-free interface. Never lose an important concept again.</p>
    </div>
    
    <div class="feature-card card">
        <div class="feature-icon">📁</div>
        <h3>Module Organization</h3>
        <p>Group your notes by subject or module. Filter and find exactly what you need when studying for exams.</p>
    </div>
    
    <div class="feature-card card">
        <div class="feature-icon">🔒</div>
        <h3>Secure & Private</h3>
        <p>Your notes are protected with secure authentication. Only you can access your study materials.</p>
    </div>
    
    <div class="feature-card card">
        <div class="feature-icon">📱</div>
        <h3>Access Anywhere</h3>
        <p>Responsive design means you can access your notes from any device - desktop, tablet, or mobile.</p>
    </div>
    
    <div class="feature-card card">
        <div class="feature-icon">🤖</div>
        <h3>AI-Ready</h3>
        <p>Coming soon: AI-powered summaries and smart quizzes based on your notes to enhance your learning.</p>
    </div>
    
    <div class="feature-card card">
        <div class="feature-icon">⚡</div>
        <h3>Fast & Simple</h3>
        <p>No complex features to learn. Just sign up and start taking notes immediately.</p>
    </div>
</section>

<footer style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.875rem;">
    <p>&copy; <?= date('Y') ?> StudentHub. Built for students, by students.</p>
</footer>

<?php require_once 'includes/footer.php'; ?>
