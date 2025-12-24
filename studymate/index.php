<?php
/**
 * StudyMate Landing Page
 */
$pageTitle = 'Welcome';
require_once 'includes/header.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>

<section class="hero">
    <div class="hero-container">
        <span class="hero-badge">
            <span>✨</span>
            <span>Your Learning Journey Starts Here</span>
        </span>
        
        <h1 class="hero-title">
            Master Your Studies with <span class="hero-title-accent">StudyMate</span>
        </h1>
        
        <p class="hero-description">
            The professional learning platform that helps you organize notes, create flashcards, 
            track study sessions, and collaborate with peers. Built for serious students.
        </p>
        
        <div class="hero-actions">
            <a href="register.php" class="btn btn-primary btn-lg">
                Get Started Free
                <i data-feather="arrow-right"></i>
            </a>
            <a href="login.php" class="btn btn-secondary btn-lg">
                Sign In
            </a>
        </div>
    </div>
</section>

<section class="features">
    <div class="section-header">
        <p class="section-label">Features</p>
        <h2 class="section-title">Everything You Need to Succeed</h2>
        <p class="section-description">
            Powerful tools designed to enhance your learning experience and boost productivity.
        </p>
    </div>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon" style="background-color: var(--primary-600);">
                <i data-feather="file-text"></i>
            </div>
            <h3 class="feature-title">Smart Notes</h3>
            <p class="feature-description">
                Organize your notes by modules with tags, search, and markdown support. 
                Never lose track of important information again.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon" style="background-color: var(--success-500);">
                <i data-feather="layers"></i>
            </div>
            <h3 class="feature-title">Flashcards</h3>
            <p class="feature-description">
                Create flashcards from your notes with spaced repetition. 
                Optimize your memory retention with smart review schedules.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon" style="background-color: var(--warning-500);">
                <i data-feather="clock"></i>
            </div>
            <h3 class="feature-title">Pomodoro Timer</h3>
            <p class="feature-description">
                Stay focused with built-in study timer. Track your sessions 
                and maintain productivity with structured breaks.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon" style="background-color: var(--error-500);">
                <i data-feather="trending-up"></i>
            </div>
            <h3 class="feature-title">Progress Tracking</h3>
            <p class="feature-description">
                Visualize your study habits with detailed statistics. 
                Set goals and track your improvement over time.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon" style="background-color: #7C3AED;">
                <i data-feather="users"></i>
            </div>
            <h3 class="feature-title">Study Groups</h3>
            <p class="feature-description">
                Collaborate with classmates by sharing notes and modules. 
                Learn together and help each other succeed.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon" style="background-color: #EC4899;">
                <i data-feather="shield"></i>
            </div>
            <h3 class="feature-title">Secure & Private</h3>
            <p class="feature-description">
                Your data is encrypted and secure. Control what you share 
                and keep your study materials private.
            </p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
