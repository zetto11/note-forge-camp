# StudentHub - Complete PHP Student Notes Manager

## 📁 Project Structure

```
studenthub/
├── config/
│   └── db.php              # Database connection (PDO)
├── includes/
│   ├── header.php          # Common header
│   ├── footer.php          # Common footer
│   └── functions.php       # Helper functions
├── assets/
│   ├── css/
│   │   └── style.css       # Main stylesheet
│   └── js/
│       └── main.js         # Main JavaScript
├── vendor/                 # PHPMailer (install via composer)
├── sql/
│   └── database.sql        # Database schema
├── index.php               # Landing page
├── register.php            # User registration
├── login.php               # User login
├── forgot_password.php     # Password reset request
├── reset_password.php      # Password reset form
├── dashboard.php           # User dashboard
├── modules.php             # Module management
├── notes.php               # Notes management
└── logout.php              # Logout handler
```

## 🚀 Installation Instructions

### Prerequisites
- XAMPP, WAMP, MAMP, or any PHP server with MySQL
- PHP 7.4+ with PDO extension
- Composer (for PHPMailer)

### Step 1: Set Up Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `studenthub`
3. Import the SQL file: `sql/database.sql`

### Step 2: Configure Database Connection
1. Open `config/db.php`
2. Update the credentials if needed:
```php
$host = 'localhost';
$dbname = 'studenthub';
$username = 'root';
$password = '';  // Default for XAMPP
```

### Step 3: Install PHPMailer
```bash
cd studenthub
composer require phpmailer/phpmailer
```

### Step 4: Configure Gmail SMTP
1. Open `config/db.php`
2. Update email settings:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
```

**Important:** For Gmail, you need to:
1. Enable 2-Factor Authentication
2. Generate an App Password at https://myaccount.google.com/apppasswords
3. Use the App Password (not your regular password)

### Step 5: Run the Project
1. Copy the `studenthub` folder to your web server root:
   - XAMPP: `C:\xampp\htdocs\studenthub`
   - WAMP: `C:\wamp64\www\studenthub`
   - MAMP: `/Applications/MAMP/htdocs/studenthub`
2. Open browser: `http://localhost/studenthub`

## 🔐 Security Features

- **Password Hashing**: Using `password_hash()` with BCRYPT
- **Prepared Statements**: All SQL queries use PDO prepared statements
- **CSRF Protection**: All forms include CSRF tokens
- **XSS Prevention**: All output is escaped with `htmlspecialchars()`
- **Session Security**: Secure session handling with regeneration
- **Token Expiration**: Password reset tokens expire after 1 hour

## 📱 Features

- ✅ User Registration & Login
- ✅ Secure Password Reset via Email
- ✅ Module Management (CRUD)
- ✅ Notes Management (CRUD)
- ✅ Filter Notes by Module
- ✅ Responsive Design (Mobile-friendly)
- ✅ Clean Modern UI
- ✅ Ready for AI Integration

## 🎨 Design

- Sidebar navigation on dashboard
- Card-based notes display
- Modal popups for edit operations
- Smooth hover effects
- Mobile-responsive layout
- Modern color scheme

## 📧 Email Configuration Troubleshooting

If emails aren't sending:
1. Check if "Less secure app access" is needed (for older Gmail accounts)
2. Verify App Password is correct
3. Check PHP error logs
4. Ensure port 587 is not blocked by firewall

## 🔮 Future AI Integration

The codebase is prepared for AI features:
- Notes content is stored in TEXT format for easy processing
- User-module-note relationship allows contextual AI
- Add API endpoints in `/api/` folder for AI services
