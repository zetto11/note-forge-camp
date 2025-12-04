<?php
/**
 * Logout Handler
 * StudentHub - Student Notes Manager
 * 
 * Destroys session and redirects to login
 */

require_once 'config/db.php';

// Destroy session
session_unset();
session_destroy();

// Start new session for flash message
session_start();
$_SESSION['flash_message'] = 'You have been logged out successfully.';
$_SESSION['flash_type'] = 'info';

// Redirect to login
header('Location: login.php');
exit();
?>
