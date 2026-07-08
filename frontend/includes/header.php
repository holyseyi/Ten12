<?php
// Start session for any future functionality
session_start();

// Define base path for includes
define('BASE_PATH', __DIR__ . '/..');

// Include configuration if needed
// require_once BASE_PATH . '/backend/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
    <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php" class="logo-link">
                    <img src="assets/images/logo.png" alt="Ten12" class="logo-image logo-light" />
                    <img src="assets/images/primaryLogo.png" alt="Ten12" class="logo-image logo-dark" />
                </a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link <?php echo $current_page === 'home' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="projects.php" class="nav-link <?php echo $current_page === 'projects' ? 'active' : ''; ?>">Projects</a></li>
                <li><a href="about.php" class="nav-link <?php echo $current_page === 'about' ? 'active' : ''; ?>">About</a></li>
                <li><a href="contact.php" class="nav-link <?php echo $current_page === 'contact' ? 'active' : ''; ?>">Contact</a></li>
                <li><a href="../admin/login.php" class="nav-link">Admin</a></li>
            </ul>
            <div class="theme-toggle">
                <fieldset class="theme-radio-group" id="themeToggle" aria-label="Select theme">
                    <legend class="sr-only">Theme</legend>
                    <label class="theme-radio-label">
                        <input type="radio" name="theme" value="light" class="theme-radio-input" />
                        <span class="theme-radio-text">Light</span>
                    </label>
                    <label class="theme-radio-label">
                        <input type="radio" name="theme" value="dark" class="theme-radio-input" />
                        <span class="theme-radio-text">Dark</span>
                    </label>
                </fieldset>
            </div>
            <div class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
