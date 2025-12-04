<?php
/**
 * Common Header
 * StudentHub - Student Notes Manager
 */

require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="StudentHub - Organize your learning with smart note-taking">
    <title><?= isset($pageTitle) ? escape($pageTitle) . ' - ' : '' ?>StudentHub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="<?= isset($bodyClass) ? escape($bodyClass) : '' ?>">
