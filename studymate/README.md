# StudyMate - Enhanced Student Learning Platform

A comprehensive PHP-based web application designed to help students organize their studies, take notes, create flashcards, track study sessions, and collaborate with peers.

## 🚀 Features

### Core Features
- **User Authentication**: Secure login, registration, and password reset via email
- **Notes Management**: Create, edit, delete, and organize notes by modules
- **Module Management**: Organize your subjects with custom colors and icons
- **Search & Filter**: Full-text search and filter notes by module or tags

### Enhanced Features
- **User Profiles**: Customizable profile with avatar, bio, and preferences
- **Tags & Categories**: Color-coded tags for better note organization
- **Study Tools**:
  - Flashcards with spaced repetition
  - Pomodoro timer for focused study sessions
  - Progress tracking and statistics
  - Study goals and reminders
- **Collaboration**:
  - Share notes with other users
  - Create and join study groups
  - Collaborative module sharing

### Design
- Professional/Corporate design theme
- Fully responsive (mobile, tablet, desktop)
- Clean and intuitive user interface
- Dark/Light mode support

## 📁 Project Structure

```
studymate/
├── config/
│   └── db.php                 # Database configuration
├── includes/
│   ├── functions.php          # Helper functions
│   ├── header.php             # Common header
│   └── footer.php             # Common footer
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── main.js            # JavaScript functionality
├── sql/
│   └── database.sql           # Database schema
├── uploads/
│   └── avatars/               # User avatar uploads
├── index.php                  # Landing page
├── register.php               # User registration
├── login.php                  # User login
├── logout.php                 # User logout
├── forgot_password.php        # Forgot password
├── reset_password.php         # Reset password
├── dashboard.php              # User dashboard
├── modules.php                # Module management
├── notes.php                  # Notes management
├── profile.php                # User profile settings
├── flashcards.php             # Flashcard study tool
├── study_timer.php            # Pomodoro timer
├── progress.php               # Progress tracking
├── collaboration.php          # Study groups & sharing
└── README.md
```

## 🛠 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for PHPMailer)

### Step-by-Step Setup

1. **Clone or download the project**
   ```bash
   # Copy the studymate folder to your web server directory
   # For XAMPP: C:/xampp/htdocs/studymate
   # For WAMP: C:/wamp64/www/studymate
   # For MAMP: /Applications/MAMP/htdocs/studymate
   ```

2. **Create the database**
   ```bash
   # Open phpMyAdmin or MySQL CLI
   # Import sql/database.sql
   mysql -u root -p < sql/database.sql
   ```

3. **Configure database connection**
   ```php
   // Edit config/db.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'studymate');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Your MySQL password
   ```

4. **Install PHPMailer via Composer**
   ```bash
   cd studymate
   composer require phpmailer/phpmailer
   ```

5. **Configure email settings**
   ```php
   // Edit includes/functions.php - Update SMTP settings
   $mail->Host = 'smtp.gmail.com';
   $mail->Username = 'your-email@gmail.com';
   $mail->Password = 'your-app-password';  // Gmail App Password
   ```

6. **Set up file permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/avatars/
   ```

7. **Access the application**
   ```
   http://localhost/studymate/
   ```

## 🔐 Gmail SMTP Setup

1. Enable 2-Step Verification on your Google account
2. Go to Google Account → Security → App Passwords
3. Generate an App Password for "Mail"
4. Use this password in the SMTP configuration

## 📊 Database Schema

### Tables
- **users**: User accounts and preferences
- **modules**: Study subjects/courses
- **notes**: User notes with full-text search
- **tags**: Color-coded tags
- **note_tags**: Note-tag relationships
- **flashcards**: Flashcard study cards
- **study_sessions**: Pomodoro/study time tracking
- **study_goals**: Learning objectives
- **shared_notes**: Note sharing between users
- **study_groups**: Collaborative study groups
- **study_group_members**: Group membership
- **group_modules**: Shared modules in groups
- **password_resets**: Password reset tokens
- **activity_log**: User activity tracking
- **reminders**: Study reminders

## 🔒 Security Features

- Password hashing with `password_hash()` (bcrypt)
- Password verification with `password_verify()`
- Prepared statements (PDO) for all queries
- CSRF token protection on all forms
- XSS prevention with `htmlspecialchars()`
- Secure session management
- Token expiration for password resets
- Input validation and sanitization

## 🎨 Design Features

- Professional color scheme (Navy, Gray, White)
- Responsive grid layout
- Card-based UI components
- Smooth animations and transitions
- Accessible form elements
- Mobile-first approach

## 📱 Responsive Breakpoints

- Mobile: < 768px
- Tablet: 768px - 1024px
- Desktop: > 1024px

## 🔮 Future AI Integration

The application is designed to support future AI features:
- Automatic note summarization
- Smart quiz generation from notes
- Study recommendations
- Flashcard suggestions
- Progress predictions

## 🧪 Test Accounts

```
Email: john@example.com
Password: password123

Email: jane@example.com
Password: password123
```

## 📝 License

This project is open source and available for educational purposes.

## 🤝 Contributing

Feel free to fork and enhance this project for your learning needs!
