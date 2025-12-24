<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';
requireLogin();

$user = getCurrentUser();
$stats = getUserStats($user['id']);
$modules = getUserModules($user['id']);
$recentNotes = getUserNotes($user['id']);
$recentNotes = array_slice($recentNotes, 0, 6);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Welcome back, <?= e($user['username']) ?>!</h1>
        <p class="page-subtitle">Here's your study overview</p>
    </div>
    <div class="page-actions">
        <a href="notes.php?action=new" class="btn btn-primary"><i data-feather="plus"></i> New Note</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary"><i data-feather="file-text"></i></div>
        </div>
        <div class="stat-value"><?= $stats['total_notes'] ?></div>
        <div class="stat-label">Total Notes</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success"><i data-feather="folder"></i></div>
        </div>
        <div class="stat-value"><?= $stats['total_modules'] ?></div>
        <div class="stat-label">Modules</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning"><i data-feather="clock"></i></div>
        </div>
        <div class="stat-value"><?= round($stats['study_time_week'] / 60, 1) ?>h</div>
        <div class="stat-label">Study Time This Week</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon info"><i data-feather="zap"></i></div>
        </div>
        <div class="stat-value"><?= $stats['study_streak'] ?></div>
        <div class="stat-label">Day Streak</div>
    </div>
</div>

<div class="page-header">
    <h2 class="page-title">Recent Notes</h2>
    <a href="notes.php" class="btn btn-ghost">View All</a>
</div>

<?php if (empty($recentNotes)): ?>
<div class="empty-state">
    <div class="empty-state-icon"><i data-feather="file-text"></i></div>
    <h3 class="empty-state-title">No notes yet</h3>
    <p class="empty-state-description">Create your first note to get started</p>
    <a href="notes.php?action=new" class="btn btn-primary">Create Note</a>
</div>
<?php else: ?>
<div class="notes-grid">
    <?php foreach ($recentNotes as $note): ?>
    <div class="card note-card">
        <div class="card-body">
            <div class="note-card-header">
                <div>
                    <h3 class="note-title"><?= e($note['title']) ?></h3>
                    <span class="note-module">
                        <span class="note-module-dot" style="background-color: <?= e($note['module_color']) ?>"></span>
                        <?= e($note['module_name']) ?>
                    </span>
                </div>
            </div>
            <p class="note-content"><?= e(truncate($note['content'], 120)) ?></p>
            <div class="note-footer">
                <span><?= timeAgo($note['updated_at']) ?></span>
                <div class="note-actions">
                    <a href="notes.php?action=edit&id=<?= $note['id'] ?>" class="btn btn-icon btn-ghost"><i data-feather="edit-2"></i></a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
