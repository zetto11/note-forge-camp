<?php
/**
 * StudyMate Header Template
 * Professional/Corporate Design
 */

require_once __DIR__ . '/functions.php';

$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="StudyMate - Your Professional Learning Companion. Organize notes, create flashcards, track study progress, and collaborate with peers.">
    <meta name="keywords" content="study, notes, flashcards, learning, education, collaboration, pomodoro">
    <meta name="author" content="StudyMate">
    
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?><?= APP_NAME ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📚</text></svg>">
    
    <!-- Google Fonts - Professional Stack -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= $currentUser && isset($currentUser['theme']) ? e($currentUser['theme']) : 'light' ?>">
    <?php if (isLoggedIn()): ?>
    <!-- Authenticated Layout -->
    <div class="app-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="logo">
                    <span class="logo-icon">📚</span>
                    <span class="logo-text"><?= APP_NAME ?></span>
                </a>
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i data-feather="menu"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <span class="nav-section-title">Main</span>
                    <a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        <i data-feather="home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="notes.php" class="nav-link <?= $currentPage === 'notes' ? 'active' : '' ?>">
                        <i data-feather="file-text"></i>
                        <span>Notes</span>
                    </a>
                    <a href="modules.php" class="nav-link <?= $currentPage === 'modules' ? 'active' : '' ?>">
                        <i data-feather="folder"></i>
                        <span>Modules</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <span class="nav-section-title">Study Tools</span>
                    <a href="flashcards.php" class="nav-link <?= $currentPage === 'flashcards' ? 'active' : '' ?>">
                        <i data-feather="layers"></i>
                        <span>Flashcards</span>
                    </a>
                    <a href="study_timer.php" class="nav-link <?= $currentPage === 'study_timer' ? 'active' : '' ?>">
                        <i data-feather="clock"></i>
                        <span>Study Timer</span>
                    </a>
                    <a href="progress.php" class="nav-link <?= $currentPage === 'progress' ? 'active' : '' ?>">
                        <i data-feather="trending-up"></i>
                        <span>Progress</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <span class="nav-section-title">Collaborate</span>
                    <a href="collaboration.php" class="nav-link <?= $currentPage === 'collaboration' ? 'active' : '' ?>">
                        <i data-feather="users"></i>
                        <span>Study Groups</span>
                    </a>
                    <a href="collaboration.php?tab=shared" class="nav-link">
                        <i data-feather="share-2"></i>
                        <span>Shared Notes</span>
                    </a>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <a href="profile.php" class="nav-link <?= $currentPage === 'profile' ? 'active' : '' ?>">
                    <i data-feather="settings"></i>
                    <span>Settings</span>
                </a>
                <a href="logout.php" class="nav-link logout-link">
                    <i data-feather="log-out"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                        <i data-feather="menu"></i>
                    </button>
                    <div class="breadcrumb">
                        <span class="breadcrumb-item"><?= ucfirst($currentPage) ?></span>
                    </div>
                </div>
                
                <div class="header-right">
                    <div class="search-box">
                        <form action="notes.php" method="GET">
                            <i data-feather="search"></i>
                            <input type="text" name="search" placeholder="Search notes..." class="search-input">
                        </form>
                    </div>
                    
                    <div class="header-actions">
                        <button class="icon-btn" id="themeToggle" aria-label="Toggle theme">
                            <i data-feather="moon"></i>
                        </button>
                        
                        <div class="user-dropdown" id="userDropdown">
                            <button class="user-btn">
                                <?php if ($currentUser && $currentUser['avatar']): ?>
                                    <img src="uploads/avatars/<?= e($currentUser['avatar']) ?>" alt="Avatar" class="user-avatar">
                                <?php else: ?>
                                    <div class="user-avatar-placeholder">
                                        <?= $currentUser ? strtoupper(substr($currentUser['username'], 0, 1)) : 'U' ?>
                                    </div>
                                <?php endif; ?>
                                <span class="user-name"><?= $currentUser ? e($currentUser['username']) : 'User' ?></span>
                                <i data-feather="chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="profile.php" class="dropdown-item">
                                    <i data-feather="user"></i>
                                    <span>Profile</span>
                                </a>
                                <a href="profile.php#preferences" class="dropdown-item">
                                    <i data-feather="settings"></i>
                                    <span>Preferences</span>
                                </a>
                                <hr class="dropdown-divider">
                                <a href="logout.php" class="dropdown-item text-danger">
                                    <i data-feather="log-out"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content Container -->
            <div class="content-container">
                <?php displayFlashMessage(); ?>
    
    <?php else: ?>
    <!-- Guest Layout -->
    <div class="guest-layout">
        <header class="guest-header">
            <div class="container">
                <a href="index.php" class="logo">
                    <span class="logo-icon">📚</span>
                    <span class="logo-text"><?= APP_NAME ?></span>
                </a>
                
                <nav class="guest-nav">
                    <a href="index.php" class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>">Home</a>
                    <a href="login.php" class="nav-link <?= $currentPage === 'login' ? 'active' : '' ?>">Login</a>
                    <a href="register.php" class="btn btn-primary">Get Started</a>
                </nav>
                
                <button class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle navigation">
                    <i data-feather="menu"></i>
                </button>
            </div>
        </header>
        
        <main class="guest-main">
            <?php displayFlashMessage(); ?>
    <?php endif; ?>
