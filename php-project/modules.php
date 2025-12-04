<?php
/**
 * Modules Management Page
 * StudentHub - Student Notes Manager
 * 
 * Full CRUD for modules with modal edit
 */

require_once 'includes/functions.php';
requireLogin();

$userId = getCurrentUserId();
$username = getCurrentUsername();
$errors = [];
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        $db = getDB();
        
        switch ($action) {
            case 'add':
                $name = trim($_POST['name'] ?? '');
                $color = $_POST['color'] ?? '#3B82F6';
                
                if (empty($name)) {
                    $errors[] = 'Module name is required';
                } elseif (strlen($name) > 100) {
                    $errors[] = 'Module name must be less than 100 characters';
                } else {
                    $stmt = $db->prepare("INSERT INTO modules (name, color, user_id) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $color, $userId]);
                    redirect('modules.php', 'Module created successfully!', 'success');
                }
                break;
                
            case 'edit':
                $moduleId = (int)($_POST['module_id'] ?? 0);
                $name = trim($_POST['name'] ?? '');
                $color = $_POST['color'] ?? '#3B82F6';
                
                if (empty($name)) {
                    $errors[] = 'Module name is required';
                } else {
                    // Verify ownership
                    $module = getModuleById($moduleId, $userId);
                    if ($module) {
                        $stmt = $db->prepare("UPDATE modules SET name = ?, color = ? WHERE id = ? AND user_id = ?");
                        $stmt->execute([$name, $color, $moduleId, $userId]);
                        redirect('modules.php', 'Module updated successfully!', 'success');
                    } else {
                        $errors[] = 'Module not found';
                    }
                }
                break;
                
            case 'delete':
                $moduleId = (int)($_POST['module_id'] ?? 0);
                
                // Verify ownership
                $module = getModuleById($moduleId, $userId);
                if ($module) {
                    // Notes will be deleted via CASCADE
                    $stmt = $db->prepare("DELETE FROM modules WHERE id = ? AND user_id = ?");
                    $stmt->execute([$moduleId, $userId]);
                    redirect('modules.php', 'Module deleted successfully!', 'success');
                } else {
                    $errors[] = 'Module not found';
                }
                break;
        }
    }
}

// Get user's modules
$modules = getUserModules($userId);

$pageTitle = 'Modules';
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
            <a href="notes.php" class="sidebar-link">
                <span class="sidebar-link-icon">📝</span>
                <span>All Notes</span>
            </a>
            <a href="modules.php" class="sidebar-link active">
                <span class="sidebar-link-icon">📁</span>
                <span>Modules</span>
            </a>
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
        <h1>Modules</h1>
        <button class="btn btn-primary" data-modal-open="addModuleModal">+ New Module</button>
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
        
        <?php if (empty($modules)): ?>
            <div class="empty-state card">
                <div class="empty-state-icon">📁</div>
                <h3>No modules yet</h3>
                <p>Create your first module to start organizing your notes by subject.</p>
                <button class="btn btn-primary" data-modal-open="addModuleModal">Create Module</button>
            </div>
        <?php else: ?>
            <div class="modules-grid">
                <?php foreach ($modules as $module): ?>
                    <div class="card module-card">
                        <div class="module-color" style="background-color: <?= escape($module['color']) ?>"></div>
                        <div class="module-info">
                            <h3 class="module-name"><?= escape($module['name']) ?></h3>
                            <p class="module-count"><?= countModuleNotes($module['id']) ?> notes</p>
                        </div>
                        <div class="module-actions">
                            <button 
                                class="btn btn-ghost btn-icon" 
                                onclick="editModule(<?= $module['id'] ?>, '<?= escape(addslashes($module['name'])) ?>', '<?= escape($module['color']) ?>')"
                                title="Edit"
                            >✏️</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="module_id" value="<?= $module['id'] ?>">
                                <button 
                                    type="submit" 
                                    class="btn btn-ghost btn-icon"
                                    data-confirm="Delete '<?= escape($module['name']) ?>'? All notes in this module will also be deleted."
                                    title="Delete"
                                >🗑️</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Add Module Modal -->
<div class="modal-overlay" id="addModuleModal">
    <div class="modal">
        <div class="modal-header">
            <h2>Add New Module</h2>
            <button class="modal-close" data-modal-close>&times;</button>
        </div>
        <form method="POST" action="modules.php">
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label class="form-label" for="add_name">Module Name</label>
                    <input 
                        type="text" 
                        id="add_name" 
                        name="name" 
                        class="form-input" 
                        placeholder="e.g., Web Development"
                        required
                        maxlength="100"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="add_color">Color</label>
                    <input 
                        type="color" 
                        id="add_color" 
                        name="color" 
                        value="#3B82F6"
                        style="width: 100%; height: 40px; cursor: pointer;"
                    >
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Add Module</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Module Modal -->
<div class="modal-overlay" id="editModuleModal">
    <div class="modal">
        <div class="modal-header">
            <h2>Edit Module</h2>
            <button class="modal-close" data-modal-close>&times;</button>
        </div>
        <form method="POST" action="modules.php">
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="module_id" id="edit_module_id">
                
                <div class="form-group">
                    <label class="form-label" for="edit_module_name">Module Name</label>
                    <input 
                        type="text" 
                        id="edit_module_name" 
                        name="name" 
                        class="form-input" 
                        required
                        maxlength="100"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit_module_color">Color</label>
                    <input 
                        type="color" 
                        id="edit_module_color" 
                        name="color" 
                        style="width: 100%; height: 40px; cursor: pointer;"
                    >
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
