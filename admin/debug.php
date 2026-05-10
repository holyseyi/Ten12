<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if debug access is allowed
$debug_token = isset($_GET['token']) ? $_GET['token'] : '';
$valid_token = md5('ten12-debug-access');

if ($debug_token !== $valid_token && !isset($_SERVER['HTTP_X_DEBUG_MODE'])) {
    // Allow local access without token
    if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== 'localhost') {
        header('HTTP/1.1 403 Forbidden');
        echo "Access denied. Use ?token=" . $valid_token;
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ten12 Admin - Debug Report</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        body.debug-page {
            background: #f5f5f5;
            padding: 20px;
        }
        .debug-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .debug-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        .debug-section.error {
            border-left-color: #f44336;
            background: #ffebee;
        }
        .debug-section.success {
            border-left-color: #4CAF50;
            background: #f1f8e9;
        }
        .debug-section h2 {
            margin-top: 0;
            color: #333;
            font-size: 18px;
        }
        .debug-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .debug-item:last-child {
            border-bottom: none;
        }
        .debug-label {
            font-weight: 600;
            width: 200px;
            color: #666;
        }
        .debug-value {
            flex: 1;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-badge.success {
            background: #4CAF50;
            color: white;
        }
        .status-badge.error {
            background: #f44336;
            color: white;
        }
        .status-badge.warning {
            background: #ff9800;
            color: white;
        }
        .debug-warning {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        table.debug-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.debug-table th,
        table.debug-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        table.debug-table th {
            background: #f5f5f5;
            font-weight: 600;
            color: #666;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body class="debug-page">
    <div class="debug-container">
        <h1>Ten12 Admin - Debug Report</h1>
        <p style="color: #666; margin-bottom: 30px;">Generated: <?php echo date('Y-m-d H:i:s'); ?></p>

        <!-- Server Configuration -->
        <div class="debug-section">
            <h2>🖥️ Server Configuration</h2>
            <div class="debug-item">
                <div class="debug-label">PHP Version:</div>
                <div class="debug-value"><?php echo phpversion(); ?></div>
            </div>
            <div class="debug-item">
                <div class="debug-label">Server Software:</div>
                <div class="debug-value"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></div>
            </div>
            <div class="debug-item">
                <div class="debug-label">Operating System:</div>
                <div class="debug-value"><?php echo php_uname(); ?></div>
            </div>
            <div class="debug-item">
                <div class="debug-label">Remote Address:</div>
                <div class="debug-value"><?php echo $_SERVER['REMOTE_ADDR']; ?></div>
            </div>
            <div class="debug-item">
                <div class="debug-label">Document Root:</div>
                <div class="debug-value"><?php echo $_SERVER['DOCUMENT_ROOT']; ?></div>
            </div>
        </div>

        <!-- Database Configuration -->
        <div class="debug-section">
            <h2>🗄️ Database Configuration</h2>
            <div class="debug-item">
                <div class="debug-label">DATABASE_URL Env:</div>
                <div class="debug-value">
                    <?php
                    $db_url = getenv('DATABASE_URL') ?: getenv('POSTGRES_URL') ?: getenv('MYSQL_URL');
                    if ($db_url) {
                        echo '<span class="status-badge success">SET</span><br>';
                        $masked = preg_replace('/([^:]*):([^@]*)@/', '$1:***@', $db_url);
                        echo htmlspecialchars($masked);
                    } else {
                        echo '<span class="status-badge warning">NOT SET - Using In-Memory SQLite</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="debug-warning">
                <strong>⚠️ Important:</strong> In-memory SQLite databases do <strong>NOT persist data</strong> between requests. 
                This means login data and user accounts are lost on every request. 
                <br><br>
                <strong>Solution:</strong> Set DATABASE_URL environment variable to use a persistent database (PostgreSQL, MySQL, or file-based SQLite).
            </div>
        </div>

        <!-- Database Connection Test -->
        <div class="debug-section <?php echo (test_database_connection() ? 'success' : 'error'); ?>">
            <h2>🔌 Database Connection</h2>
            <?php
            function test_database_connection() {
                try {
                    require_once __DIR__ . '/../backend/config/database.php';
                    $database = new Database();
                    $db = $database->getConnection();
                    
                    if ($db) {
                        // Test query
                        $result = $db->query("SELECT 1");
                        if ($result) {
                            echo '<div class="debug-item">';
                            echo '<div class="debug-label">Connection Status:</div>';
                            echo '<div class="debug-value"><span class="status-badge success">Connected</span></div>';
                            echo '</div>';
                            return true;
                        }
                    }
                } catch (Exception $e) {
                    echo '<div class="debug-item">';
                    echo '<div class="debug-label">Error:</div>';
                    echo '<div class="debug-value">' . htmlspecialchars($e->getMessage()) . '</div>';
                    echo '</div>';
                    return false;
                }
                
                echo '<div class="debug-item">';
                echo '<div class="debug-label">Connection Status:</div>';
                echo '<div class="debug-value"><span class="status-badge error">Failed</span></div>';
                echo '</div>';
                return false;
            }
            ?>
        </div>

        <!-- Admin Users Table -->
        <div class="debug-section">
            <h2>👥 Admin Users</h2>
            <?php
            try {
                require_once __DIR__ . '/../backend/config/database.php';
                $database = new Database();
                $db = $database->getConnection();
                
                if ($db) {
                    $result = $db->query("SELECT id, username, email, role, created_at FROM admin_users");
                    $users = $result->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($users) > 0) {
                        echo '<p><span class="status-badge success">' . count($users) . ' user(s) found</span></p>';
                        echo '<table class="debug-table">';
                        echo '<thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created At</th></tr></thead>';
                        echo '<tbody>';
                        foreach ($users as $user) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($user['id']) . '</td>';
                            echo '<td><code>' . htmlspecialchars($user['username']) . '</code></td>';
                            echo '<td><code>' . htmlspecialchars($user['email']) . '</code></td>';
                            echo '<td>' . htmlspecialchars($user['role']) . '</td>';
                            echo '<td>' . htmlspecialchars($user['created_at']) . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<div class="debug-warning">';
                        echo '<strong>⚠️ No admin users found!</strong><br>';
                        echo 'Run the database setup script or create an admin user.';
                        echo '</div>';
                    }
                }
            } catch (Exception $e) {
                echo '<p><span class="status-badge error">Error: ' . htmlspecialchars($e->getMessage()) . '</span></p>';
            }
            ?>
        </div>

        <!-- File Permissions -->
        <div class="debug-section">
            <h2>📁 File Permissions</h2>
            <?php
            $paths = [
                'Backend logs' => __DIR__ . '/../backend/logs',
                'Uploads' => __DIR__ . '/../backend/uploads',
                'Config' => __DIR__ . '/../backend/config',
            ];
            
            echo '<table class="debug-table">';
            echo '<thead><tr><th>Path</th><th>Writable</th><th>Exists</th><th>Permissions</th></tr></thead>';
            echo '<tbody>';
            
            foreach ($paths as $name => $path) {
                $exists = file_exists($path);
                $writable = is_writable($path);
                $perms = $exists ? substr(sprintf('%o', fileperms($path)), -3) : 'N/A';
                
                echo '<tr>';
                echo '<td><code>' . htmlspecialchars($path) . '</code></td>';
                echo '<td><span class="status-badge ' . ($writable ? 'success' : 'error') . '">' . ($writable ? 'Yes' : 'No') . '</span></td>';
                echo '<td><span class="status-badge ' . ($exists ? 'success' : 'error') . '">' . ($exists ? 'Yes' : 'No') . '</span></td>';
                echo '<td>' . $perms . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
            ?>
        </div>

        <!-- Session Information -->
        <div class="debug-section">
            <h2>🔐 Session Information</h2>
            <div class="debug-item">
                <div class="debug-label">Session Status:</div>
                <div class="debug-value">
                    <?php
                    if (session_status() === PHP_SESSION_NONE) {
                        echo '<span class="status-badge error">Not Started</span>';
                    } elseif (session_status() === PHP_SESSION_ACTIVE) {
                        echo '<span class="status-badge success">Active</span>';
                    } else {
                        echo '<span class="status-badge warning">Disabled</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="debug-item">
                <div class="debug-label">Session ID:</div>
                <div class="debug-value"><code><?php echo session_id(); ?></code></div>
            </div>
            <div class="debug-item">
                <div class="debug-label">Session Path:</div>
                <div class="debug-value"><code><?php echo session_save_path(); ?></code></div>
            </div>
            <div class="debug-item">
                <div class="debug-label">Logged In:</div>
                <div class="debug-value">
                    <?php
                    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
                        echo '<span class="status-badge success">Yes</span><br>';
                        echo 'Username: <code>' . htmlspecialchars($_SESSION['admin_username'] ?? 'Unknown') . '</code>';
                    } else {
                        echo '<span class="status-badge error">No</span>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Log Files -->
        <div class="debug-section">
            <h2>📝 Recent Log Entries</h2>
            <?php
            $log_file = __DIR__ . '/../backend/logs/login.log';
            if (file_exists($log_file)) {
                $lines = array_reverse(file($log_file));
                $recent = array_slice($lines, 0, 20);
                
                echo '<pre style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px;">';
                foreach ($recent as $line) {
                    echo htmlspecialchars(trim($line)) . "\n";
                }
                echo '</pre>';
            } else {
                echo '<p><span class="status-badge warning">No log file yet</span></p>';
            }
            ?>
        </div>

        <!-- Troubleshooting Guide -->
        <div class="debug-section">
            <h2>🔧 Troubleshooting Guide</h2>
            <ol style="line-height: 1.8;">
                <li><strong>Database Issue:</strong> Check if DATABASE_URL is properly set. Without it, all data is lost between requests.</li>
                <li><strong>No Admin Users:</strong> Run the setup script or create an admin user using the database interface.</li>
                <li><strong>Session Issues:</strong> Ensure session path is writable and sessions are enabled in PHP.</li>
                <li><strong>Password Verification:</strong> Check that password_hash() and password_verify() are supported.</li>
                <li><strong>File Permissions:</strong> Ensure logs and uploads directories are writable by the web server.</li>
            </ol>
        </div>

        <div style="text-align: center; margin-top: 30px; color: #666;">
            <p>
                <a href="/admin/login.php" style="color: #2196F3; text-decoration: none;">Back to Login</a> | 
                <a href="/admin/login.php?debug=1" style="color: #2196F3; text-decoration: none;">Login with Debug</a>
            </p>
            <p style="font-size: 12px; margin-top: 20px;">
                Token: <code><?php echo $valid_token; ?></code>
            </p>
        </div>
    </div>
</body>
</html>
