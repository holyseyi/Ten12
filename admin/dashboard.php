<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Include database
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/models/User.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ten12</title>
    <link rel="icon" type="image/png" href="../frontend/assets/images/favicon.png">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-dashboard">
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <h2>Ten12</h2>
            <p>Admin Dashboard</p>
        </div>
        
        <div class="admin-menu-toggle">
            <button class="admin-toggle-btn" id="adminToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
        
        <nav class="admin-nav-menu" id="adminNavMenu">
            <ul class="admin-nav">
                <li><a href="/admin/dashboard.php" class="active">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Dashboard
                </a></li>
                <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                <li><a href="/admin/manage-users.php">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                    Manage Users
                </a></li>
                <?php endif; ?>
                <li><a href="/admin/add-project.php">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                    Add Project
                </a></li>
                <li><a href="/admin/login.php?logout=1">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                    </svg>
                    Logout
                </a></li>
            </ul>
        </nav>
    </aside>
    
    <main class="admin-main">
        <header class="admin-header">
            <div class="admin-nav-top">
                <div class="admin-site-nav">
                    <a href="/frontend/index.php" class="nav-link">View Site</a>
                    <a href="/frontend/projects.php" class="nav-link">Projects</a>
                    <a href="/frontend/about.php" class="nav-link">About</a>
                    <a href="/frontend/contact.php" class="nav-link">Contact</a>
                </div>
                <div class="admin-user">
                    <div class="admin-user-info">
                        <div class="admin-user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
                        <div class="admin-user-role"><?php echo ucfirst($_SESSION['admin_role']); ?></div>
                    </div>
                    <a href="/admin/login.php?logout=1" class="btn-logout">Logout</a>
                </div>
            </div>
        </header>
        
        <div class="admin-content">
            <div id="adminMessage" class="admin-message"></div>
            
            <!-- Dashboard Stats -->
            <div class="admin-stats">
                <div class="stat-card">
                    <h3>Total Projects</h3>
                    <div class="stat-value" id="total-projects">0</div>
                    <div class="stat-change positive">All time</div>
                </div>
                <div class="stat-card">
                    <h3>Published</h3>
                    <div class="stat-value" id="published-projects">0</div>
                    <div class="stat-change positive">Live projects</div>
                </div>
                <div class="stat-card">
                    <h3>Drafts</h3>
                    <div class="stat-value" id="draft-projects">0</div>
                    <div class="stat-change negative">Unpublished</div>
                </div>
                <div class="stat-card">
                    <h3>Messages</h3>
                    <div class="stat-value" id="total-messages">0</div>
                    <div class="stat-change positive">Contact forms</div>
                </div>
            </div>
            
            <!-- Recent Projects -->
            <section class="admin-section">
                <div class="section-header">
                    <h2>Recent Projects</h2>
                    <a href="add-project.php" class="btn btn-primary">Add New Project</a>
                </div>
                <div class="section-content">
                    <table class="projects-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recentProjects">
                            <tr>
                                <td colspan="6" class="empty-state">Loading projects...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>
