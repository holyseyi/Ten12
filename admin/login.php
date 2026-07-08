<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Enable logging
ini_set('log_errors', 1);
$log_file = __DIR__ . '/../backend/logs/login.log';
if (!is_dir(dirname($log_file))) {
    mkdir(dirname($log_file), 0755, true);
}
ini_set('error_log', $log_file);

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
$debug_info = '';
$is_debug_mode = isset($_GET['debug']) && $_GET['debug'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once __DIR__ . '/../backend/config/database.php';
        require_once __DIR__ . '/../backend/models/User.php';
        
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Validate inputs
        if (empty($username) || empty($password)) {
            $error = 'Username and password are required';
            error_log("Login attempt with missing credentials");
            throw new Exception($error);
        }
        
        error_log("Login attempt for user: " . $username);
        
        // Get database connection
        $database = new Database();
        $db = $database->getConnection();
        
        if (!$db) {
            $error = 'Database connection failed';
            error_log("Database connection failed during login");
            throw new Exception($error);
        }
        
        error_log("Database connected successfully");
        
        // Verify user
        $user = new User($db);
        error_log("Attempting to verify password for: " . $username);
        
        $auth_user = $user->verifyPassword($username, $password);
        
        if ($auth_user) {
            error_log("Login successful for user: " . $username);
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
            error_log("Invalid credentials for user: " . $username);
            
            // Check if user exists
            $existing_user = $user->findByUsername($username);
            if (!$existing_user) {
                $error = 'User not found';
                error_log("User does not exist: " . $username);
            } else {
                $error = 'Invalid password';
                error_log("Password verification failed for user: " . $username);
            }
            
            throw new Exception($error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        $debug_info = "Error: " . $error . "\n";
        error_log("Login error: " . $error);
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
                
                <nav class="admin-nav-menu" id="adminNavMenu">
                    <div class="admin-site-nav">
                        <a href="/frontend/index.php" class="nav-link">View Site</a>
                        <a href="/frontend/projects.php" class="nav-link">Projects</a>
                        <a href="/frontend/about.php" class="nav-link">About</a>
                        <a href="/frontend/contact.php" class="nav-link">Contact</a>
                    </div>
                </nav>
            </div>
            
            <div id="loginMessage" class="login-message"></div>
            <?php if ($error): ?>
            <div class="login-message error" style="display: block;">
                <strong>Login Error:</strong> <?php echo htmlspecialchars($error); ?>
                <?php if ($is_debug_mode && !empty($debug_info)): ?>
                    <div style="margin-top: 10px; font-size: 12px; padding: 10px; background-color: rgba(0,0,0,0.1); border-radius: 4px; font-family: monospace;">
                        <strong>Debug Info:</strong><br>
                        <?php echo nl2br(htmlspecialchars($debug_info)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($is_debug_mode): ?>
            <div class="login-message info" style="display: block; background-color: #e3f2fd; border-color: #2196F3; color: #1976D2;">
                <strong>Debug Mode Enabled</strong><br>
                Check browser console and server logs for detailed information.<br>
                Log file: <code style="background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 3px;">backend/logs/login.log</code>
            </div>
            <?php endif; ?>
            
            <form id="loginForm" action="login.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Enter your username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary">Sign In</button>
                <div style="text-align: center; margin-top: 15px; font-size: 12px;">
                    <a href="login.php?debug=1" style="color: #666; text-decoration: none;">Enable Debug Mode</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/js/admin.js"></script>
    <script>
        // Client-side debugging
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const debugMode = urlParams.get('debug') === '1';
            
            if (debugMode) {
                console.log('%cDebug Mode Enabled', 'color: #2196F3; font-size: 14px; font-weight: bold;');
                console.log('Page URL:', window.location.href);
                console.log('Session info:', {
                    cookies: document.cookie,
                    localStorage: { ...localStorage }
                });
            }
            
            // Form submission debugging
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    if (debugMode) {
                        const formData = new FormData(this);
                        console.log('%cForm Submitted', 'color: #4CAF50; font-size: 12px; font-weight: bold;');
                        console.log('Username:', formData.get('username'));
                        console.log('Password length:', formData.get('password').length);
                        console.log('Submission time:', new Date().toISOString());
                    }
                });
            }
        });
    </script>
</body>
</html>