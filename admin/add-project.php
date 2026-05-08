<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project - Ten12 Admin</title>
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
                <li><a href="/admin/dashboard.php">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Dashboard
                </a></li>
                <li><a href="/admin/add-project.php" class="active">
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
                        <div class="admin-user-name">Admin</div>
                        <div class="admin-user-role">Super Admin</div>
                    </div>
                    <a href="/admin/login.php?logout=1" class="btn-logout">Logout</a>
                </div>
            </div>
        </header>
        
        <div class="admin-content">
            <div id="adminMessage" class="admin-message"></div>
            
            <section class="admin-section">
                <div class="section-header">
                    <h2>Project Details</h2>
                </div>
                <div class="section-content">
                    <form id="projectForm" class="project-form">
                        <input type="hidden" id="projectId" name="id" value="">
                        <input type="hidden" id="thumbnail" name="thumbnail" value="">
                        <input type="hidden" id="images" name="images" value="[]">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="title">Project Title *</label>
                                <input type="text" id="title" name="title" required placeholder="Enter project title">
                            </div>
                            <div class="form-group">
                                <label for="category">Category *</label>
                                <select id="category" name="category" required>
                                    <option value="">Select category</option>
                                    <option value="Web">Web Design</option>
                                    <option value="Mobile">Mobile Apps</option>
                                    <option value="Design">UI/UX Design</option>
                                    <option value="Brand">Brand Identity</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Short Description *</label>
                            <textarea id="description" name="description" required placeholder="Brief description of the project" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">Full Description *</label>
                            <textarea id="content" name="content" required placeholder="Detailed description of the project" rows="8"></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tags">Tags</label>
                                <input type="text" id="tags" name="tags" placeholder="HTML, CSS, JavaScript (comma separated)">
                            </div>
                            <div class="form-group">
                                <label for="published">Status</label>
                                <select id="published" name="published">
                                    <option value="1">Published</option>
                                    <option value="0">Draft</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="live_url">Live Demo URL</label>
                                <input type="url" id="live_url" name="live_url" placeholder="https://example.com">
                            </div>
                            <div class="form-group">
                                <label for="github_url">GitHub URL</label>
                                <input type="url" id="github_url" name="github_url" placeholder="https://github.com/username/repo">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Thumbnail Image</label>
                            <div class="image-upload" id="thumbnailUpload">
                                <input type="file" id="thumbnailUploadInput" accept="image/*" style="display: none;">
                                <div class="upload-content">
                                    <img id="thumbnailPreview" style="display: none; max-width: 200px; max-height: 200px; border-radius: 8px;">
                                    <div class="upload-text">
                                        <p>Click to upload thumbnail image</p>
                                        <p class="upload-hint">JPG, PNG, GIF, WebP (max 5MB)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Project Images</label>
                            <div class="multiple-image-upload" id="multipleImageUpload">
                                <input type="file" id="multipleImageUploadInput" accept="image/*" multiple style="display: none;">
                                <div class="upload-content">
                                    <div class="upload-text">
                                        <p>Click to upload project images</p>
                                        <p class="upload-hint">JPG, PNG, GIF, WebP (max 5MB each, multiple files allowed)</p>
                                    </div>
                                </div>
                                <div id="imagePreviewContainer" class="image-preview-container" style="display: none;">
                                    <div class="preview-grid" id="previewGrid"></div>
                                    <button type="button" id="clearImages" class="btn btn-secondary btn-small">Clear All</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='/admin/dashboard.php'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Project</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>
    
    <script src="assets/js/admin.js"></script>
    <script>
        // Initialize image upload
        document.addEventListener('DOMContentLoaded', function() {
            const thumbnailUpload = document.getElementById('thumbnailUpload');
            const imageUpload = document.getElementById('imageUpload');
            const imagePreview = document.getElementById('imagePreview');
            
            thumbnailUpload.addEventListener('click', function() {
                imageUpload.click();
            });
        });
    </script>
</body>
</html>
