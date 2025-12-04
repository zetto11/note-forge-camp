/**
 * StudentHub - Main JavaScript
 * Handles modals, mobile menu, and interactive features
 */

document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initModals();
    initDeleteConfirmations();
    initAlertDismiss();
    initFormValidation();
});

/**
 * Mobile Menu Toggle
 */
function initMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.mobile-menu-toggle');
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    
    if (!sidebar || !toggleBtn) return;
    
    // Add overlay for mobile
    document.body.appendChild(overlay);
    
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
        document.body.classList.toggle('sidebar-open');
    });
    
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
    });
    
    // Add styles for overlay
    const style = document.createElement('style');
    style.textContent = `
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 99;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        body.sidebar-open {
            overflow: hidden;
        }
    `;
    document.head.appendChild(style);
}

/**
 * Modal Management
 */
function initModals() {
    // Open modal buttons
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal-open');
            openModal(modalId);
        });
    });
    
    // Close modal buttons
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = this.closest('.modal-overlay');
            closeModal(modal);
        });
    });
    
    // Close on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal-overlay.active');
            if (activeModal) {
                closeModal(activeModal);
            }
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Focus first input
        const firstInput = modal.querySelector('input, textarea, select');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
}

function closeModal(modal) {
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        
        // Reset form if exists
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

// Global functions for use in onclick attributes
window.openModal = openModal;
window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
};

/**
 * Delete Confirmations
 */
function initDeleteConfirmations() {
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Alert Auto-dismiss
 */
function initAlertDismiss() {
    document.querySelectorAll('.alert').forEach(alert => {
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
        
        // Add close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.className = 'alert-close';
        closeBtn.style.cssText = `
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            opacity: 0.7;
        `;
        closeBtn.onclick = () => alert.remove();
        
        alert.style.position = 'relative';
        alert.appendChild(closeBtn);
    });
}

/**
 * Form Validation
 */
function initFormValidation() {
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous errors
            form.querySelectorAll('.form-error').forEach(el => el.remove());
            form.querySelectorAll('.form-input, .form-textarea').forEach(el => {
                el.classList.remove('error');
            });
            
            // Required fields
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    showFieldError(field, 'This field is required');
                }
            });
            
            // Email validation
            form.querySelectorAll('[type="email"]').forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    isValid = false;
                    showFieldError(field, 'Please enter a valid email address');
                }
            });
            
            // Password validation
            const password = form.querySelector('[name="password"]');
            const confirmPassword = form.querySelector('[name="confirm_password"]');
            
            if (password && password.value && password.value.length < 8) {
                isValid = false;
                showFieldError(password, 'Password must be at least 8 characters');
            }
            
            if (confirmPassword && password && confirmPassword.value !== password.value) {
                isValid = false;
                showFieldError(confirmPassword, 'Passwords do not match');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
}

function showFieldError(field, message) {
    field.classList.add('error');
    field.style.borderColor = 'var(--error)';
    
    const error = document.createElement('div');
    error.className = 'form-error';
    error.textContent = message;
    
    field.parentNode.appendChild(error);
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * Edit Module Modal - populate form
 */
function editModule(id, name, color) {
    document.getElementById('edit_module_id').value = id;
    document.getElementById('edit_module_name').value = name;
    document.getElementById('edit_module_color').value = color;
    openModal('editModuleModal');
}

/**
 * Edit Note Modal - populate form
 */
function editNote(id, title, content, moduleId) {
    document.getElementById('edit_note_id').value = id;
    document.getElementById('edit_note_title').value = title;
    document.getElementById('edit_note_content').value = content;
    document.getElementById('edit_note_module').value = moduleId;
    openModal('editNoteModal');
}

// Make functions globally available
window.editModule = editModule;
window.editNote = editNote;

/**
 * Character counter for textareas
 */
function initCharacterCounters() {
    document.querySelectorAll('[data-max-length]').forEach(textarea => {
        const maxLength = parseInt(textarea.getAttribute('data-max-length'));
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.cssText = 'text-align: right; font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;';
        textarea.parentNode.appendChild(counter);
        
        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${textarea.value.length}/${maxLength}`;
            counter.style.color = remaining < 50 ? 'var(--warning)' : 'var(--text-muted)';
            if (remaining < 0) {
                counter.style.color = 'var(--error)';
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });
}

/**
 * Search/Filter notes in real-time
 */
function initNoteSearch() {
    const searchInput = document.getElementById('noteSearch');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const notes = document.querySelectorAll('.note-card');
        
        notes.forEach(note => {
            const title = note.querySelector('.note-title').textContent.toLowerCase();
            const content = note.querySelector('.note-content').textContent.toLowerCase();
            
            if (title.includes(query) || content.includes(query)) {
                note.style.display = '';
            } else {
                note.style.display = 'none';
            }
        });
    });
}

// Initialize additional features
document.addEventListener('DOMContentLoaded', function() {
    initCharacterCounters();
    initNoteSearch();
});
