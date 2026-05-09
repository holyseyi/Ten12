<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Handle form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../backend/config/database.php';
    require_once __DIR__ . '/../backend/models/User.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $user = new User($db);
        $auth_user = $user->verifyPassword($_POST['username'], $_POST['password']);
        
        if ($auth_user) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $auth_user['id'];
            $_SESSION['admin_username'] = $auth_user['username'];
            $_SESSION['admin_email'] = $auth_user['email'];
            $_SESSION['admin_role'] = $auth_user['role'];
            
            // Redirect based on role
            $redirect = $_SESSION['admin_role'] === 'admin' ? 'dashboard.php' : 'dashboard.php';
            header('Location: ' . $redirect);
            exit();
        } else {
            $error = 'Invalid credentials';
        }
    } else {
        $error = 'Database connection failed';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Ten12</title>
    <link rel="icon" type="image/png" href="../frontend/assets/images/favicon.png">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-login">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Ten12 Admin</h1>
                <p>Sign in to manage your portfolio</p>
            </div>
            
            <div class="admin-nav-top">
                <div class="admin-menu-toggle">
                    <button class="admin-toggle-btn" id="adminToggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
                
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
                
                <nav class="admin-nav-menu" id="adminNavMenu">
                    <div class="admin-site-nav">
                        <a href="/frontend/index.php" class="nav-link">View Site</a>
                        <a href="/frontend/projects.php" class="nav-link">Projects</a>
                        <a href="/frontend/about.php" class="nav-link">About</a>
                        <a href="/frontend/contact.php" class="nav-link">Contact</a>
                    </div>
                </nav>
            </div>
            
            <?php if ($error): ?>
            <div class="login-message error" style="display: block;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Enter your username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>
        </div>
    </div>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>