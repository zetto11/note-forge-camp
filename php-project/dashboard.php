<?php
/**
 * Dashboard Page
 * StudentHub - Student Notes Manager
 * 
 * Main user dashboard with sidebar navigation
 */

require_once 'includes/functions.php';
requireLogin();

$userId = getCurrentUserId();
$username = getCurrentUsername();

// Get filter
$filterModule = isset($_GET['module']) ? (int)$_GET['module'] : null;

// Get user's modules and notes
$modules = getUserModules($userId);
$notes = getUserNotes($userId, $filterModule);

// Count stats
$totalNotes = count(getUserNotes($userId));
$totalModules = count($modules);

$pageTitle = 'Dashboard';
$bodyClass = 'dashboard-layout';
require_once 'includes/header.php';
?>

<!-- Mobile Menu Toggle -->
<button class="mobile-menu-toggle btn btn-primary">☰</button>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="logo">
            <div class="logo-icon">📚</div>
            <span>StudentHub</span>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <div class="sidebar-section">
            <div class="sidebar-section-title">Menu</div>
            <a href="dashboard.php" class="sidebar-link active">
                <span class="sidebar-link-icon">🏠</span>
                <span>Dashboard</span>
            </a>
            <a href="notes.php" class="sidebar-link">
                <span class="sidebar-link-icon">📝</span>
                <span>All Notes</span>
            </a>
            <a href="modules.php" class="sidebar-link">
                <span class="sidebar-link-icon">📁</span>
                <span>Modules</span>
            </a>
        </div>
        
        <div class="sidebar-section">
            <div class="sidebar-section-title">Modules</div>
            <a href="dashboard.php" class="sidebar-link <?= !$filterModule ? 'active' : '' ?>">
                <span class="sidebar-link-icon">📋</span>
                <span>All Modules</span>
            </a>
            <?php foreach ($modules as $module): ?>
                <a href="dashboard.php?module=<?= $module['id'] ?>" 
                   class="sidebar-link <?= $filterModule == $module['id'] ? 'active' : '' ?>">
                    <span class="sidebar-link-icon" style="color: <?= escape($module['color']) ?>">●</span>
                    <span><?= escape($module['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-menu">
            <div class="user-avatar">
                <?= strtoupper(substr($username, 0, 1)) ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?= escape($username) ?></div>
                <div class="user-email"><?= escape($_SESSION['email']) ?></div>
            </div>
        </div>
        <a href="logout.php" class="btn btn-ghost btn-sm" style="width: 100%; margin-top: 0.5rem;">
            Logout
        </a>
    </div>
</aside>

<!-- Main Content -->
<main class="main-content">
    <header class="content-header">
        <div>
            <h1>
                <?php if ($filterModule): ?>
                    <?php 
                        $currentModule = array_filter($modules, fn($m) => $m['id'] == $filterModule);
                        $currentModule = reset($currentModule);
                    ?>
                    <?= escape($currentModule['name'] ?? 'Notes') ?>
                <?php else: ?>
                    Dashboard
                <?php endif; ?>
            </h1>
        </div>
        <div class="flex gap-2">
            <a href="modules.php" class="btn btn-secondary">+ New Module</a>
            <a href="notes.php?action=add" class="btn btn-primary">+ New Note</a>
        </div>
    </header>
    
    <div class="content-body">
        <?= displayFlashMessage() ?>
        
        <!-- Stats Cards -->
        <?php if (!$filterModule): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="card" style="padding: 1.5rem;">
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);"><?= $totalNotes ?></div>
                <div style="color: var(--text-muted); font-size: 0.875rem;">Total Notes</div>
            </div>
            <div class="card" style="padding: 1.5rem;">
                <div style="font-size: 2rem; font-weight: 700; color: var(--success);"><?= $totalModules ?></div>
                <div style="color: var(--text-muted); font-size: 0.875rem;">Modules</div>
            </div>
            <div class="card" style="padding: 1.5rem;">
                <div style="font-size: 2rem; font-weight: 700; color: var(--warning);">🚀</div>
                <div style="color: var(--text-muted); font-size: 0.875rem;">AI Coming Soon</div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Notes Section -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.25rem;">
                <?= $filterModule ? 'Notes' : 'Recent Notes' ?>
            </h2>
            <?php if (!$filterModule): ?>
                <a href="notes.php" style="font-size: 0.875rem;">View all →</a>
            <?php endif; ?>
        </div>
        
        <?php if (empty($notes)): ?>
            <div class="empty-state card">
                <div class="empty-state-icon">📝</div>
                <h3>No notes yet</h3>
                <p>Start by creating your first note to organize your studies.</p>
                <a href="notes.php?action=add" class="btn btn-primary">Create Note</a>
            </div>
        <?php else: ?>
            <div class="notes-grid">
                <?php 
                $displayNotes = $filterModule ? $notes : array_slice($notes, 0, 6);
                foreach ($displayNotes as $note): 
                ?>
                    <div class="card note-card">
                        <div class="card-header">
                            <h3 class="note-title"><?= escape($note['title']) ?></h3>
                            <span class="module-pill" style="background-color: <?= escape($note['module_color']) ?>">
                                <?= escape($note['module_name']) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="note-content"><?= escape(truncateText($note['content'], 150)) ?></p>
                        </div>
                        <div class="card-footer note-meta">
                            <span class="note-date"><?= formatDate($note['created_at']) ?></span>
                            <div class="note-actions">
                                <a href="notes.php?action=edit&id=<?= $note['id'] ?>" class="btn btn-ghost btn-sm">Edit</a>
                                <a href="notes.php?action=view&id=<?= $note['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Quick Add Module Section (if no modules) -->
        <?php if (empty($modules)): ?>
            <div style="margin-top: 2rem;">
                <div class="empty-state card">
                    <div class="empty-state-icon">📁</div>
                    <h3>No modules yet</h3>
                    <p>Create modules to organize your notes by subject.</p>
                    <a href="modules.php" class="btn btn-secondary">Create Module</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
