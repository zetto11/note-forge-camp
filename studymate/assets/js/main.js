/**
 * StudyMate - Main JavaScript
 * Handles UI interactions, modals, forms, and dynamic features
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeSidebar();
    initializeDropdowns();
    initializeModals();
    initializeTabs();
    initializeThemeToggle();
    initializeMobileNav();
    initializeFormValidation();
    initializeDynamicForms();
    initializePomodoro();
    initializeFlashcards();
    
    // Re-initialize Feather icons after dynamic content
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});

/**
 * Sidebar Toggle
 */
function initializeSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobileNavOverlay');
    
    function toggleSidebar() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
    }
    
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', toggleSidebar);
    }
    
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    });
}

/**
 * Dropdown Menus
 */
function initializeDropdowns() {
    const dropdowns = document.querySelectorAll('.user-dropdown');
    
    dropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('.user-btn');
        
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        dropdowns.forEach(dropdown => {
            dropdown.classList.remove('active');
        });
    });
}

/**
 * Modal System
 */
function initializeModals() {
    // Open modal buttons
    document.querySelectorAll('[data-modal-open]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-open');
            openModal(modalId);
        });
    });
    
    // Close modal buttons
    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal-backdrop');
            closeModal(modal);
        });
    });
    
    // Close on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal-backdrop.active');
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

/**
 * Tabs
 */
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            const tabContainer = this.closest('.tabs').parentElement;
            
            // Deactivate all tabs
            tabContainer.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            tabContainer.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Activate selected tab
            this.classList.add('active');
            const tabContent = tabContainer.querySelector(`#${tabId}`);
            if (tabContent) {
                tabContent.classList.add('active');
            }
        });
    });
}

/**
 * Theme Toggle (Light/Dark)
 */
function initializeThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark');
            
            const isDark = document.body.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            // Update icon
            const icon = this.querySelector('svg');
            if (icon) {
                icon.setAttribute('data-feather', isDark ? 'sun' : 'moon');
                feather.replace();
            }
            
            // Save preference via AJAX if logged in
            saveThemePreference(isDark ? 'dark' : 'light');
        });
    }
    
    // Load saved theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark');
    }
}

function saveThemePreference(theme) {
    // AJAX call to save theme preference
    fetch('profile.php?action=update_theme', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `theme=${theme}&ajax=1`
    }).catch(err => console.log('Theme preference not saved'));
}

/**
 * Mobile Navigation
 */
function initializeMobileNav() {
    const mobileNavToggle = document.getElementById('mobileNavToggle');
    const guestNav = document.querySelector('.guest-nav');
    
    if (mobileNavToggle && guestNav) {
        mobileNavToggle.addEventListener('click', function() {
            guestNav.classList.toggle('open');
            
            const icon = this.querySelector('svg');
            if (icon) {
                const isOpen = guestNav.classList.contains('open');
                icon.setAttribute('data-feather', isOpen ? 'x' : 'menu');
                feather.replace();
            }
        });
    }
}

/**
 * Form Validation
 */
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous errors
            form.querySelectorAll('.form-error').forEach(el => el.remove());
            form.querySelectorAll('.is-error').forEach(el => el.classList.remove('is-error'));
            
            // Validate required fields
            form.querySelectorAll('[required]').forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    showFieldError(input, 'This field is required');
                }
            });
            
            // Validate email fields
            form.querySelectorAll('input[type="email"]').forEach(input => {
                if (input.value && !isValidEmail(input.value)) {
                    isValid = false;
                    showFieldError(input, 'Please enter a valid email address');
                }
            });
            
            // Validate password confirmation
            const password = form.querySelector('input[name="password"]');
            const confirmPassword = form.querySelector('input[name="confirm_password"]');
            if (password && confirmPassword && password.value !== confirmPassword.value) {
                isValid = false;
                showFieldError(confirmPassword, 'Passwords do not match');
            }
            
            // Validate password strength for registration
            if (password && form.getAttribute('data-validate') === 'register') {
                const passwordErrors = validatePassword(password.value);
                if (passwordErrors.length > 0) {
                    isValid = false;
                    showFieldError(password, passwordErrors[0]);
                }
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
}

function showFieldError(input, message) {
    input.classList.add('is-error');
    const error = document.createElement('div');
    error.className = 'form-error';
    error.textContent = message;
    input.parentNode.appendChild(error);
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validatePassword(password) {
    const errors = [];
    if (password.length < 8) {
        errors.push('Password must be at least 8 characters long');
    }
    if (!/[A-Z]/.test(password)) {
        errors.push('Password must contain at least one uppercase letter');
    }
    if (!/[a-z]/.test(password)) {
        errors.push('Password must contain at least one lowercase letter');
    }
    if (!/[0-9]/.test(password)) {
        errors.push('Password must contain at least one number');
    }
    return errors;
}

/**
 * Dynamic Forms (Add/Edit Notes, Modules, etc.)
 */
function initializeDynamicForms() {
    // Tag input handling
    const tagInputs = document.querySelectorAll('.tag-input-container');
    
    tagInputs.forEach(container => {
        const input = container.querySelector('.tag-input');
        const tagsContainer = container.querySelector('.tags-selected');
        const hiddenInput = container.querySelector('input[type="hidden"]');
        
        if (input) {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    const value = this.value.trim();
                    if (value) {
                        addTag(value, tagsContainer, hiddenInput);
                        this.value = '';
                    }
                }
            });
        }
    });
    
    // Note preview toggle
    const previewToggle = document.querySelector('[data-toggle-preview]');
    if (previewToggle) {
        previewToggle.addEventListener('click', function() {
            const editor = document.getElementById('noteContent');
            const preview = document.getElementById('notePreview');
            
            if (preview.classList.contains('d-none')) {
                preview.innerHTML = parseMarkdown(editor.value);
                preview.classList.remove('d-none');
                editor.classList.add('d-none');
                this.textContent = 'Edit';
            } else {
                preview.classList.add('d-none');
                editor.classList.remove('d-none');
                this.textContent = 'Preview';
            }
        });
    }
    
    // Color picker preview
    const colorPickers = document.querySelectorAll('input[type="color"]');
    colorPickers.forEach(picker => {
        picker.addEventListener('input', function() {
            const preview = this.parentElement.querySelector('.color-preview');
            if (preview) {
                preview.style.backgroundColor = this.value;
            }
        });
    });
}

function addTag(value, container, hiddenInput) {
    const tag = document.createElement('span');
    tag.className = 'tag';
    tag.innerHTML = `${escapeHtml(value)} <span class="tag-remove" onclick="this.parentElement.remove(); updateTagsInput();">&times;</span>`;
    container.appendChild(tag);
    updateTagsInput();
}

function updateTagsInput() {
    const container = document.querySelector('.tags-selected');
    const hiddenInput = document.querySelector('.tag-input-container input[type="hidden"]');
    
    if (container && hiddenInput) {
        const tags = Array.from(container.querySelectorAll('.tag')).map(tag => {
            return tag.textContent.replace('×', '').trim();
        });
        hiddenInput.value = tags.join(',');
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function parseMarkdown(text) {
    // Simple markdown parsing
    let html = escapeHtml(text);
    
    // Headers
    html = html.replace(/^### (.+)$/gm, '<h3>$1</h3>');
    html = html.replace(/^## (.+)$/gm, '<h2>$1</h2>');
    html = html.replace(/^# (.+)$/gm, '<h1>$1</h1>');
    
    // Bold and italic
    html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
    
    // Code
    html = html.replace(/`(.+?)`/g, '<code>$1</code>');
    
    // Lists
    html = html.replace(/^- (.+)$/gm, '<li>$1</li>');
    
    // Line breaks
    html = html.replace(/\n/g, '<br>');
    
    return html;
}

/**
 * Pomodoro Timer
 */
let pomodoroTimer = null;
let pomodoroTime = 25 * 60; // 25 minutes in seconds
let pomodoroRunning = false;
let pomodoroMode = 'work';
let pomodoroSessions = 0;

function initializePomodoro() {
    const timerDisplay = document.getElementById('timerDisplay');
    const startBtn = document.getElementById('timerStart');
    const pauseBtn = document.getElementById('timerPause');
    const resetBtn = document.getElementById('timerReset');
    const modeButtons = document.querySelectorAll('.timer-mode-btn');
    
    if (!timerDisplay) return;
    
    updateTimerDisplay();
    
    if (startBtn) {
        startBtn.addEventListener('click', startPomodoro);
    }
    
    if (pauseBtn) {
        pauseBtn.addEventListener('click', pausePomodoro);
    }
    
    if (resetBtn) {
        resetBtn.addEventListener('click', resetPomodoro);
    }
    
    modeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const mode = this.getAttribute('data-mode');
            setTimerMode(mode);
            
            modeButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

function startPomodoro() {
    if (pomodoroRunning) return;
    
    pomodoroRunning = true;
    document.getElementById('timerStart').classList.add('d-none');
    document.getElementById('timerPause').classList.remove('d-none');
    
    pomodoroTimer = setInterval(function() {
        pomodoroTime--;
        updateTimerDisplay();
        updateTimerProgress();
        
        if (pomodoroTime <= 0) {
            completePomodoro();
        }
    }, 1000);
}

function pausePomodoro() {
    pomodoroRunning = false;
    clearInterval(pomodoroTimer);
    
    document.getElementById('timerStart').classList.remove('d-none');
    document.getElementById('timerPause').classList.add('d-none');
}

function resetPomodoro() {
    pausePomodoro();
    setTimerMode(pomodoroMode);
}

function setTimerMode(mode) {
    pomodoroMode = mode;
    
    switch (mode) {
        case 'work':
            pomodoroTime = 25 * 60;
            break;
        case 'short':
            pomodoroTime = 5 * 60;
            break;
        case 'long':
            pomodoroTime = 15 * 60;
            break;
    }
    
    updateTimerDisplay();
    updateTimerProgress();
    
    const label = document.getElementById('timerLabel');
    if (label) {
        label.textContent = mode === 'work' ? 'Focus Time' : (mode === 'short' ? 'Short Break' : 'Long Break');
    }
}

function updateTimerDisplay() {
    const display = document.getElementById('timerDisplay');
    if (display) {
        const minutes = Math.floor(pomodoroTime / 60);
        const seconds = pomodoroTime % 60;
        display.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }
}

function updateTimerProgress() {
    const progressBar = document.querySelector('.timer-progress-bar');
    if (progressBar) {
        let totalTime;
        switch (pomodoroMode) {
            case 'work': totalTime = 25 * 60; break;
            case 'short': totalTime = 5 * 60; break;
            case 'long': totalTime = 15 * 60; break;
        }
        const progress = ((totalTime - pomodoroTime) / totalTime) * 100;
        progressBar.style.width = `${progress}%`;
    }
}

function completePomodoro() {
    pausePomodoro();
    
    // Play notification sound
    playNotificationSound();
    
    // Show notification
    if (Notification.permission === 'granted') {
        new Notification('StudyMate Timer', {
            body: pomodoroMode === 'work' ? 'Great work! Time for a break.' : 'Break is over! Ready to focus?',
            icon: '/favicon.ico'
        });
    }
    
    // Save session to database
    if (pomodoroMode === 'work') {
        pomodoroSessions++;
        saveStudySession();
    }
    
    // Auto-switch mode
    if (pomodoroMode === 'work') {
        if (pomodoroSessions % 4 === 0) {
            setTimerMode('long');
        } else {
            setTimerMode('short');
        }
    } else {
        setTimerMode('work');
    }
}

function playNotificationSound() {
    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH6DfnJkYmR5goOBenNxdH2Fg4R8dHN1fYSEhH14dXZ9g4SEfnl2d32Dg4N+enl4fYODg395eXl9goKCfnp5eX2CgoJ+enl5fYKCgn56eXl9goKCfnp5eX2CgoJ+');
    audio.play().catch(e => console.log('Audio playback failed'));
}

function saveStudySession() {
    const moduleSelect = document.getElementById('sessionModule');
    const moduleId = moduleSelect ? moduleSelect.value : null;
    
    fetch('study_timer.php?action=save_session', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `duration=25&module_id=${moduleId}&type=pomodoro&ajax=1`
    }).catch(err => console.log('Session not saved'));
}

/**
 * Flashcards
 */
let currentFlashcardIndex = 0;
let flashcards = [];

function initializeFlashcards() {
    const flashcard = document.querySelector('.flashcard');
    
    if (flashcard) {
        flashcard.addEventListener('click', function() {
            this.classList.toggle('flipped');
        });
    }
    
    // Navigation buttons
    const nextBtn = document.getElementById('flashcardNext');
    const prevBtn = document.getElementById('flashcardPrev');
    const correctBtn = document.getElementById('flashcardCorrect');
    const incorrectBtn = document.getElementById('flashcardIncorrect');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', showNextFlashcard);
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', showPrevFlashcard);
    }
    
    if (correctBtn) {
        correctBtn.addEventListener('click', function() {
            markFlashcard(true);
        });
    }
    
    if (incorrectBtn) {
        incorrectBtn.addEventListener('click', function() {
            markFlashcard(false);
        });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (document.querySelector('.flashcard')) {
            if (e.key === ' ' || e.key === 'Enter') {
                document.querySelector('.flashcard').classList.toggle('flipped');
            } else if (e.key === 'ArrowRight') {
                showNextFlashcard();
            } else if (e.key === 'ArrowLeft') {
                showPrevFlashcard();
            }
        }
    });
}

function showNextFlashcard() {
    currentFlashcardIndex++;
    loadFlashcard(currentFlashcardIndex);
}

function showPrevFlashcard() {
    if (currentFlashcardIndex > 0) {
        currentFlashcardIndex--;
        loadFlashcard(currentFlashcardIndex);
    }
}

function loadFlashcard(index) {
    // Reset flip state
    const flashcard = document.querySelector('.flashcard');
    if (flashcard) {
        flashcard.classList.remove('flipped');
    }
    
    // Load flashcard content via AJAX
    fetch(`flashcards.php?action=get&index=${index}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('flashcardQuestion').textContent = data.question;
                document.getElementById('flashcardAnswer').textContent = data.answer;
                document.getElementById('flashcardProgress').textContent = `${index + 1} / ${data.total}`;
            } else {
                // No more flashcards
                showFlashcardComplete();
            }
        })
        .catch(err => console.log('Failed to load flashcard'));
}

function markFlashcard(correct) {
    const flashcardId = document.getElementById('flashcardId')?.value;
    
    if (flashcardId) {
        fetch('flashcards.php?action=mark', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${flashcardId}&correct=${correct ? 1 : 0}&ajax=1`
        }).then(() => {
            showNextFlashcard();
        }).catch(err => console.log('Failed to mark flashcard'));
    } else {
        showNextFlashcard();
    }
}

function showFlashcardComplete() {
    const container = document.querySelector('.flashcard-container');
    if (container) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <h3 class="empty-state-title">All Done!</h3>
                <p class="empty-state-description">You've reviewed all flashcards for this session. Great job!</p>
                <a href="flashcards.php" class="btn btn-primary">Start New Session</a>
            </div>
        `;
    }
}

/**
 * Confirm Delete
 */
function confirmDelete(message, formId) {
    if (confirm(message || 'Are you sure you want to delete this?')) {
        document.getElementById(formId).submit();
    }
}

/**
 * Request Notification Permission
 */
if ('Notification' in window && Notification.permission === 'default') {
    // Request permission when user interacts with timer
    document.addEventListener('click', function requestPermission(e) {
        if (e.target.closest('.timer-container')) {
            Notification.requestPermission();
            document.removeEventListener('click', requestPermission);
        }
    });
}

/**
 * Auto-save draft (for notes)
 */
let autoSaveTimer = null;

function initializeAutoSave() {
    const noteContent = document.getElementById('noteContent');
    const noteTitle = document.getElementById('noteTitle');
    
    if (noteContent && noteTitle) {
        [noteContent, noteTitle].forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(saveDraft, 3000);
            });
        });
    }
}

function saveDraft() {
    const noteContent = document.getElementById('noteContent')?.value;
    const noteTitle = document.getElementById('noteTitle')?.value;
    const noteId = document.getElementById('noteId')?.value;
    
    if (noteContent || noteTitle) {
        const draft = {
            id: noteId || 'new',
            title: noteTitle,
            content: noteContent,
            timestamp: Date.now()
        };
        
        localStorage.setItem('noteDraft', JSON.stringify(draft));
        showAutoSaveIndicator();
    }
}

function showAutoSaveIndicator() {
    let indicator = document.getElementById('autoSaveIndicator');
    
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'autoSaveIndicator';
        indicator.style.cssText = 'position: fixed; bottom: 20px; right: 20px; background: var(--success-500); color: white; padding: 8px 16px; border-radius: 8px; font-size: 14px; opacity: 0; transition: opacity 0.3s;';
        document.body.appendChild(indicator);
    }
    
    indicator.textContent = 'Draft saved';
    indicator.style.opacity = '1';
    
    setTimeout(() => {
        indicator.style.opacity = '0';
    }, 2000);
}

/**
 * Share functionality
 */
function shareNote(noteId) {
    openModal('shareModal');
    document.getElementById('shareNoteId').value = noteId;
}

function copyShareLink() {
    const link = document.getElementById('shareLink');
    if (link) {
        link.select();
        document.execCommand('copy');
        
        const btn = document.querySelector('[onclick="copyShareLink()"]');
        if (btn) {
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(() => {
                btn.textContent = originalText;
            }, 2000);
        }
    }
}

/**
 * Search functionality
 */
function initializeSearch() {
    const searchInput = document.querySelector('.search-input');
    
    if (searchInput) {
        let searchTimer;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                if (this.value.length >= 2) {
                    performSearch(this.value);
                }
            }, 300);
        });
    }
}

function performSearch(query) {
    // For now, just submit the form
    const form = document.querySelector('.search-box form');
    if (form) {
        form.submit();
    }
}
