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
                <button class="theme-toggle-btn" id="themeToggle" aria-label="Toggle dark mode">
                    <svg class="sun-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 17.5C9.5 17.5 7.5 15.5 7.5 13S9.5 8.5 12 8.5 16.5 10.5 16.5 13 14.5 17.5 12 17.5M12 7C8.7 7 6 9.7 6 13S8.7 19 12 19 18 16.3 18 13 15.3 7 12 7M12 2L14.4 6.4L19 5.7L16.2 9.8L18.6 14L14 12.7L9.4 14L11.8 9.8L9 5.7L13.6 6.4L12 2Z"/>
                    </svg>
                    <svg class="moon-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"/>
                    </svg>
                </button>
            </div>
            <div class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
