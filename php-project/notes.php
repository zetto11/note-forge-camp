<?php
/**
 * Notes Management Page
 * StudentHub - Student Notes Manager
 * 
 * Full CRUD for notes with filtering
 */

require_once 'includes/functions.php';
requireLogin();

$userId = getCurrentUserId();
$username = getCurrentUsername();
$errors = [];

// Get action
$action = $_GET['action'] ?? 'list';
$noteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get modules for dropdowns
$modules = getUserModules($userId);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $postAction = $_POST['action'] ?? '';
        $db = getDB();
        
        switch ($postAction) {
            case 'add':
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                $moduleId = (int)($_POST['module_id'] ?? 0);
                
                if (empty($title)) {
                    $errors[] = 'Title is required';
                }
                if (empty($content)) {
                    $errors[] = 'Content is required';
                }
                if (empty($moduleId)) {
                    $errors[] = 'Please select a module';
                } else {
                    // Verify module belongs to user
                    $module = getModuleById($moduleId, $userId);
                    if (!$module) {
                        $errors[] = 'Invalid module selected';
                    }
                }
                
                if (empty($errors)) {
                    $stmt = $db->prepare("INSERT INTO notes (title, content, module_id, user_id) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$title, $content, $moduleId, $userId]);
                    redirect('notes.php', 'Note created successfully!', 'success');
                }
                break;
                
            case 'edit':
                $noteId = (int)($_POST['note_id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                $moduleId = (int)($_POST['module_id'] ?? 0);
                
                if (empty($title)) {
                    $errors[] = 'Title is required';
                }
                if (empty($content)) {
                    $errors[] = 'Content is required';
                }
                
                // Verify ownership
                $note = getNoteById($noteId, $userId);
                if (!$note) {
                    $errors[] = 'Note not found';
                }
                
                // Verify module belongs to user
                $module = getModuleById($moduleId, $userId);
                if (!$module) {
                    $errors[] = 'Invalid module selected';
                }
                
                if (empty($errors)) {
                    $stmt = $db->prepare("UPDATE notes SET title = ?, content = ?, module_id = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$title, $content, $moduleId, $noteId, $userId]);
                    redirect('notes.php', 'Note updated successfully!', 'success');
                }
                break;
                
            case 'delete':
                $noteId = (int)($_POST['note_id'] ?? 0);
                
                // Verify ownership
                $note = getNoteById($noteId, $userId);
                if ($note) {
                    $stmt = $db->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
                    $stmt->execute([$noteId, $userId]);
                    redirect('notes.php', 'Note deleted successfully!', 'success');
                } else {
                    $errors[] = 'Note not found';
                }
                break;
        }
    }
}

// Get filter
$filterModule = isset($_GET['module']) ? (int)$_GET['module'] : null;

// Get notes
$notes = getUserNotes($userId, $filterModule);

// If viewing/editing a note, get it
$currentNote = null;
if (($action === 'edit' || $action === 'view') && $noteId) {
    $currentNote = getNoteById($noteId, $userId);
    if (!$currentNote) {
        redirect('notes.php', 'Note not found', 'error');
    }
}

$pageTitle = 'Notes';
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
            <a href="dashboard.php" class="sidebar-link">
                <span class="sidebar-link-icon">🏠</span>
                <span>Dashboard</span>
            </a>
            <a href="notes.php" class="sidebar-link active">
                <span class="sidebar-link-icon">📝</span>
                <span>All Notes</span>
            </a>
            <a href="modules.php" class="sidebar-link">
                <span class="sidebar-link-icon">📁</span>
                <span>Modules</span>
            </a>
        </div>
        
        <div class="sidebar-section">
            <div class="sidebar-section-title">Filter by Module</div>
            <a href="notes.php" class="sidebar-link <?= !$filterModule ? 'active' : '' ?>">
                <span class="sidebar-link-icon">📋</span>
                <span>All Notes</span>
            </a>
            <?php foreach ($modules as $module): ?>
                <a href="notes.php?module=<?= $module['id'] ?>" 
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
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <!-- Add/Edit Note Form -->
        <header class="content-header">
            <h1><?= $action === 'add' ? 'Add New Note' : 'Edit Note' ?></h1>
            <a href="notes.php" class="btn btn-secondary">← Back to Notes</a>
        </header>
        
        <div class="content-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= escape($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (empty($modules)): ?>
                <div class="alert alert-warning">
                    <p style="margin: 0;">You need to create a module first before adding notes.</p>
                </div>
                <a href="modules.php" class="btn btn-primary">Create Module</a>
            <?php else: ?>
                <div class="card" style="max-width: 800px;">
                    <div class="card-body">
                        <form method="POST" action="notes.php">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <input type="hidden" name="action" value="<?= $action ?>">
                            <?php if ($action === 'edit' && $currentNote): ?>
                                <input type="hidden" name="note_id" value="<?= $currentNote['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label class="form-label" for="title">Title</label>
                                <input 
                                    type="text" 
                                    id="title" 
                                    name="title" 
                                    class="form-input" 
                                    placeholder="Enter note title"
                                    value="<?= escape($currentNote['title'] ?? $_POST['title'] ?? '') ?>"
                                    required
                                    maxlength="255"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="module_id">Module</label>
                                <select id="module_id" name="module_id" class="form-select" required>
                                    <option value="">Select a module</option>
                                    <?php foreach ($modules as $module): ?>
                                        <option 
                                            value="<?= $module['id'] ?>"
                                            <?= ($currentNote['module_id'] ?? $_POST['module_id'] ?? '') == $module['id'] ? 'selected' : '' ?>
                                        >
                                            <?= escape($module['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="content">Content</label>
                                <textarea 
                                    id="content" 
                                    name="content" 
                                    class="form-textarea" 
                                    placeholder="Write your note content here..."
                                    required
                                    style="min-height: 300px;"
                                ><?= escape($currentNote['content'] ?? $_POST['content'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <?= $action === 'add' ? 'Create Note' : 'Save Changes' ?>
                                </button>
                                <a href="notes.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
    <?php elseif ($action === 'view' && $currentNote): ?>
        <!-- View Note -->
        <header class="content-header">
            <div>
                <a href="notes.php" style="color: var(--text-muted); font-size: 0.875rem;">← Back to Notes</a>
                <h1 style="margin-top: 0.5rem;"><?= escape($currentNote['title']) ?></h1>
            </div>
            <div class="flex gap-2">
                <a href="notes.php?action=edit&id=<?= $currentNote['id'] ?>" class="btn btn-primary">Edit Note</a>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="note_id" value="<?= $currentNote['id'] ?>">
                    <button type="submit" class="btn btn-danger" data-confirm="Delete this note? This cannot be undone.">Delete</button>
                </form>
            </div>
        </header>
        
        <div class="content-body">
            <?php 
                $noteModule = getModuleById($currentNote['module_id'], $userId);
            ?>
            <div class="card" style="max-width: 800px;">
                <div class="card-header">
                    <span class="module-pill" style="background-color: <?= escape($noteModule['color'] ?? '#3B82F6') ?>">
                        <?= escape($noteModule['name'] ?? 'Unknown') ?>
                    </span>
                    <span class="note-date">Created <?= formatDateTime($currentNote['created_at']) ?></span>
                </div>
                <div class="card-body">
                    <div style="white-space: pre-wrap; line-height: 1.8;">
                        <?= escape($currentNote['content']) ?>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Notes List -->
        <header class="content-header">
            <h1>
                <?php if ($filterModule): ?>
                    <?php 
                        $currentModule = array_filter($modules, fn($m) => $m['id'] == $filterModule);
                        $currentModule = reset($currentModule);
                    ?>
                    <?= escape($currentModule['name'] ?? 'Notes') ?>
                <?php else: ?>
                    All Notes
                <?php endif; ?>
            </h1>
            <div class="flex gap-2">
                <input type="text" id="noteSearch" class="form-input" placeholder="Search notes..." style="width: 200px;">
                <a href="notes.php?action=add" class="btn btn-primary">+ New Note</a>
            </div>
        </header>
        
        <div class="content-body">
            <?= displayFlashMessage() ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p style="margin: 0;"><?= escape($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($notes)): ?>
                <div class="empty-state card">
                    <div class="empty-state-icon">📝</div>
                    <h3>No notes found</h3>
                    <p>
                        <?= $filterModule ? 'No notes in this module yet.' : 'Start by creating your first note.' ?>
                    </p>
                    <?php if (!empty($modules)): ?>
                        <a href="notes.php?action=add" class="btn btn-primary">Create Note</a>
                    <?php else: ?>
                        <a href="modules.php" class="btn btn-secondary">Create Module First</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="notes-grid">
                    <?php foreach ($notes as $note): ?>
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
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="note_id" value="<?= $note['id'] ?>">
                                        <button type="submit" class="btn btn-ghost btn-sm" data-confirm="Delete this note?">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>
