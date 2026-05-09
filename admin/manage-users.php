<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['admin_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/models/User.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Ten12 Admin</title>
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
                <li><a href="/admin/manage-users.php" class="active">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                    Manage Users
                </a></li>
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
            
            <!-- Page Header -->
            <div class="section-header">
                <h2>Manage Users</h2>
                <button class="btn btn-primary" onclick="openAddUserModal()">Add New User</button>
            </div>
            
            <!-- Users Table -->
            <section class="admin-section">
                <div class="section-content">
                    <table class="projects-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTable">
                            <tr>
                                <td colspan="6" class="empty-state">Loading users...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
    
    <!-- Add/Edit User Modal -->
    <div id="userModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New User</h3>
                <button class="modal-close" onclick="closeUserModal()">&times;</button>
            </div>
            <form id="userForm" class="modal-body">
                <input type="hidden" id="userId" name="id">
                <div class="form-group">
                    <label for="userUsername">Username *</label>
                    <input type="text" id="userUsername" name="username" required>
                </div>
                <div class="form-group">
                    <label for="userEmail">Email *</label>
                    <input type="email" id="userEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="userPassword">Password *</label>
                    <input type="password" id="userPassword" name="password">
                    <small class="form-help">Leave empty to keep current password when editing</small>
                </div>
                <div class="form-group">
                    <label for="userRole">Role *</label>
                    <select id="userRole" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeUserModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/js/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
            
            const userForm = document.getElementById('userForm');
            if (userForm) {
                userForm.addEventListener('submit', handleUserSubmit);
            }
        });
        
        async function loadUsers() {
            const container = document.getElementById('usersTable');
            if (!container) return;
            
            try {
                const response = await fetch('/backend/api/admin/users.php');
                const data = await response.json();
                
                if (data.success && data.users.length > 0) {
                    container.innerHTML = data.users.map(user => `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.username}</td>
                            <td>${user.email}</td>
                            <td><span class="project-status ${user.role === 'admin' ? 'published' : 'draft'}">${user.role}</span></td>
                            <td>${new Date(user.created_at).toLocaleDateString()}</td>
                            <td class="project-actions">
                                <button class="btn-sm btn-edit" onclick="editUser(${user.id})">Edit</button>
                                <button class="btn-sm btn-delete" onclick="deleteUser(${user.id})">Delete</button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    container.innerHTML = '<tr><td colspan="6" class="empty-state">No users found</td></tr>';
                }
            } catch (error) {
                console.error('Failed to load users:', error);
                container.innerHTML = '<tr><td colspan="6" class="empty-state">Failed to load users</td></tr>';
            }
        }
        
        function openAddUserModal() {
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('userPassword').required = true;
            document.getElementById('userModal').style.display = 'flex';
        }
        
        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
        }
        
        async function handleUserSubmit(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const userId = formData.get('id');
            const isEdit = !!userId;
            
            const userData = {
                username: formData.get('username'),
                email: formData.get('email'),
                role: formData.get('role')
            };
            
            if (formData.get('password')) {
                userData.password = formData.get('password');
            }
            
            try {
                const response = await fetch(`/backend/api/admin/users.php${isEdit ? '?id=' + userId : ''}`, {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(userData)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    showAdminMessage(result.message || 'User saved successfully.', 'success');
                    closeUserModal();
                    loadUsers();
                } else {
                    showAdminMessage(result.message || 'Failed to save user.', 'error');
                }
            } catch (error) {
                console.error('User submit error:', error);
                showAdminMessage('Failed to save user. Please try again.', 'error');
            }
        }
        
        async function editUser(id) {
            try {
                const response = await fetch('/backend/api/admin/users.php?id=' + id);
                const result = await response.json();
                
                if (result.success) {
                    const user = result.user;
                    document.getElementById('modalTitle').textContent = 'Edit User';
                    document.getElementById('userId').value = user.id;
                    document.getElementById('userUsername').value = user.username;
                    document.getElementById('userEmail').value = user.email;
                    document.getElementById('userRole').value = user.role;
                    document.getElementById('userPassword').required = false;
                    document.getElementById('userModal').style.display = 'flex';
                }
            } catch (error) {
                console.error('Edit user error:', error);
                showAdminMessage('Failed to load user.', 'error');
            }
        }
        
async function deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user?')) {
                return;
            }
            
            try {
                const response = await fetch('/backend/api/admin/users.php?id=' + id, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    showAdminMessage(result.message, 'success');
                    loadUsers();
                } else {
                    showAdminMessage(result.message || 'Failed to delete user.', 'error');
                }
            } catch (error) {
                console.error('Delete user error:', error);
                showAdminMessage('Failed to delete user. Please try again.', 'error');
            }
        }
    </script>
</body>
</html>