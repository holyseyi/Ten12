// Admin Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme toggle
    initThemeToggle();
    
    // Initialize hamburger menu
    initHamburgerMenu();
    
    // Check if we're on login page
    if (document.body.classList.contains('admin-login')) {
        initLoginPage();
    } else {
        initAdminDashboard();
    }
});

// Theme Toggle Functionality
function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    
    // Get saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
}

// Hamburger Menu Functionality
function initHamburgerMenu() {
    const adminToggle = document.getElementById('adminToggle');
    const adminNavMenu = document.getElementById('adminNavMenu');
    
    if (adminToggle && adminNavMenu) {
        adminToggle.addEventListener('click', function() {
            adminToggle.classList.toggle('active');
            adminNavMenu.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!adminToggle.contains(e.target) && !adminNavMenu.contains(e.target)) {
                adminToggle.classList.remove('active');
                adminNavMenu.classList.remove('active');
            }
        });
    }
}

// Login Page Functionality
function initLoginPage() {
    const loginForm = document.getElementById('loginForm');
    const messageDiv = document.getElementById('loginMessage');
    
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    // Check if already logged in via session
    // This is handled by PHP redirect
}

async function handleLogin(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const messageDiv = document.getElementById('loginMessage');
    
    // Disable submit button
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner"></span> Signing in...';
    
    try {
        const response = await fetch('/backend/api/admin/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username: formData.get('username'),
                password: formData.get('password')
            })
        });

        const result = await response.json();
        
        if (response.ok && result.success) {
            showMessage('Login successful! Redirecting...', 'success');
            
            // Redirect to dashboard
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 1000);
        } else {
            showMessage(result.message || 'Login failed. Please try again.', 'error');
        }
    } catch (error) {
        console.error('Login error:', error);
        showMessage('Login failed. Please try again.', 'error');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Sign In';
    }
}

// Admin Dashboard Functionality
function initAdminDashboard() {
    // Authentication is handled by PHP session
    // Load dashboard data
    loadDashboardStats();
    loadRecentProjects();
    
    // Initialize event listeners
    initEventListeners();
}

function initEventListeners() {
    // Navigation
    const navLinks = document.querySelectorAll('.admin-nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            navLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
        });
    });
    
    // Project form
    const projectForm = document.getElementById('projectForm');
    if (projectForm) {
        projectForm.addEventListener('submit', handleProjectSubmit);
    }
    
    // Initialize image uploads
    initImageUploads();
    
    // Delete project buttons
    const deleteButtons = document.querySelectorAll('.delete-project');
    deleteButtons.forEach(button => {
        button.addEventListener('click', handleProjectDelete);
    });
}

async function loadDashboardStats() {
    try {
        const response = await fetch('/backend/api/projects.php?admin=1');
        
        if (response.ok) {
            const data = await response.json();
            updateDashboardStats(data);
        }
    } catch (error) {
        console.error('Failed to load stats:', error);
    }
}

function updateDashboardStats(data) {
    const totalProjects = data.records ? data.records.length : 0;
    const publishedProjects = data.records ? data.records.filter(p => p.published === 1).length : 0;
    const draftProjects = totalProjects - publishedProjects;
    
    // Update stat cards
    updateStatCard('total-projects', totalProjects);
    updateStatCard('published-projects', publishedProjects);
    updateStatCard('draft-projects', draftProjects);
}

function updateStatCard(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

async function loadRecentProjects() {
    const container = document.getElementById('recentProjects');
    if (!container) return;
    
    try {
        const response = await fetch('/backend/api/projects.php?admin=1&limit=5');
        
        if (response.ok) {
            const data = await response.json();
            if (data.records && data.records.length > 0) {
                container.innerHTML = data.records.map(project => createProjectRow(project)).join('');
            } else {
                container.innerHTML = '<tr><td colspan="6" class="empty-state">No projects found</td></tr>';
            }
        }
    } catch (error) {
        console.error('Failed to load projects:', error);
        const container = document.getElementById('recentProjects');
        if (container) {
            container.innerHTML = '<tr><td colspan="6" class="empty-state">Failed to load projects</td></tr>';
        }
    }
}

function createProjectRow(project) {
    const statusClass = project.published === 1 ? 'published' : 'draft';
    const statusText = project.published === 1 ? 'Published' : 'Draft';
    
    return `
        <tr>
            <td class="project-title">${project.title}</td>
            <td>${project.category}</td>
            <td><span class="project-status ${statusClass}">${statusText}</span></td>
            <td>${new Date(project.created_at).toLocaleDateString()}</td>
            <td>${new Date(project.updated_at).toLocaleDateString()}</td>
            <td class="project-actions">
                <button class="btn-sm btn-edit" onclick="editProject(${project.id})">Edit</button>
                <button class="btn-sm btn-delete delete-project" data-id="${project.id}">Delete</button>
            </td>
        </tr>
    `;
}

async function handleProjectSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const projectId = formData.get('id');
    const isEdit = !!projectId;
    
    // Disable submit button
    submitButton.disabled = true;
    submitButton.textContent = isEdit ? 'Updating...' : 'Creating...';
    
    try {
        const projectData = {
            id: projectId || undefined,
            title: formData.get('title'),
            description: formData.get('description'),
            content: formData.get('content'),
            thumbnail: formData.get('thumbnail'),
            images: JSON.parse(formData.get('images') || '[]'),
            category: formData.get('category'),
            tags: formData.get('tags').split(',').map(tag => tag.trim()).filter(tag => tag),
            live_url: formData.get('live_url'),
            github_url: formData.get('github_url'),
            published: formData.get('published') === '1'
        };
        
        const response = await fetch(`../backend/api/projects.php${isEdit ? `?id=${projectId}` : ''}`, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(projectData)
        });

        const result = await response.json();
        
        if (response.ok) {
            showAdminMessage(result.message, 'success');
            if (!isEdit) {
                form.reset();
                // Reset image preview
                const imagePreview = document.getElementById('imagePreview');
                if (imagePreview) {
                    imagePreview.src = '';
                    imagePreview.style.display = 'none';
                }
            }
            // Refresh projects list
            loadRecentProjects();
            loadDashboardStats();
        } else {
            showAdminMessage(result.message || 'Failed to save project.', 'error');
        }
    } catch (error) {
        console.error('Project submission error:', error);
        showAdminMessage('Failed to save project. Please try again.', 'error');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = isEdit ? 'Update Project' : 'Create Project';
    }
}

async function handleProjectDelete(e) {
    const projectId = e.target.getAttribute('data-id');
    
    if (!confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`../backend/api/projects.php?id=${projectId}`, {
            method: 'DELETE'
        });

        const result = await response.json();
        
        if (response.ok) {
            showAdminMessage(result.message, 'success');
            // Remove project from DOM
            e.target.closest('tr').remove();
            // Refresh stats
            loadDashboardStats();
        } else {
            showAdminMessage(result.message || 'Failed to delete project.', 'error');
        }
    } catch (error) {
        console.error('Delete error:', error);
        showAdminMessage('Failed to delete project. Please try again.', 'error');
    }
}

async function handleImageUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    const formData = new FormData();
    formData.append('image', file);
    
    try {
        const response = await fetch('../backend/api/upload.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (response.ok) {
            // Show image preview
            const imagePreview = document.getElementById('imagePreview');
            if (imagePreview) {
                imagePreview.src = '../backend/' + result.url;
                imagePreview.style.display = 'block';
            }
            
            // Update hidden input with image URL
            const thumbnailInput = document.getElementById('thumbnail');
            if (thumbnailInput) {
                thumbnailInput.value = result.url;
            }
            
            // Update upload area
            const uploadArea = document.querySelector('.image-upload');
            if (uploadArea) {
                uploadArea.classList.add('has-image');
            }
            
            showAdminMessage('Image uploaded successfully.', 'success');
        } else {
            showAdminMessage(result.message || 'Failed to upload image.', 'error');
        }
    } catch (error) {
        console.error('Upload error:', error);
        showAdminMessage('Failed to upload image. Please try again.', 'error');
    }
}

function editProject(projectId) {
    // Redirect to edit page or open modal
    window.location.href = `add-project.php?id=${projectId}`;
}

function showAdminMessage(message, type) {
    const messageDiv = document.getElementById('adminMessage');
    if (!messageDiv) return;
    
    messageDiv.textContent = message;
    messageDiv.className = `admin-message ${type}`;
    messageDiv.style.display = 'block';
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

function showMessage(message, type) {
    const messageDiv = document.getElementById('loginMessage');
    if (!messageDiv) return;
    
    messageDiv.textContent = message;
    messageDiv.className = `login-message ${type}`;
    messageDiv.style.display = 'block';
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// Utility functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function truncateText(text, maxLength) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// Image Upload Functions
function initImageUploads() {
    // Thumbnail upload
    const thumbnailUpload = document.getElementById('thumbnailUpload');
    const thumbnailUploadInput = document.getElementById('thumbnailUploadInput');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    
    // Multiple images upload
    const multipleImageUpload = document.getElementById('multipleImageUpload');
    const multipleImageUploadInput = document.getElementById('multipleImageUploadInput');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const previewGrid = document.getElementById('previewGrid');
    const clearImagesBtn = document.getElementById('clearImages');
    
    let uploadedImages = [];
    
    // Thumbnail upload handlers
    if (thumbnailUpload && thumbnailUploadInput && thumbnailPreview) {
        thumbnailUpload.addEventListener('click', function() {
            thumbnailUploadInput.click();
        });
        
        thumbnailUploadInput.addEventListener('change', function(e) {
            handleThumbnailUpload(e.target.files[0]);
        });
    }
    
    // Multiple images upload handlers
    if (multipleImageUpload && multipleImageUploadInput && imagePreviewContainer && previewGrid) {
        multipleImageUpload.addEventListener('click', function() {
            multipleImageUploadInput.click();
        });
        
        multipleImageUploadInput.addEventListener('change', function(e) {
            handleMultipleImageUpload(e.target.files);
        });
        
        if (clearImagesBtn) {
            clearImagesBtn.addEventListener('click', function() {
                clearAllImages();
            });
        }
    }
}

function handleThumbnailUpload(file) {
    if (!file) return;
    
    // Validate file
    if (!file.type.startsWith('image/')) {
        showAdminMessage('Please select an image file', 'error');
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) { // 5MB
        showAdminMessage('File size must be less than 5MB', 'error');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const thumbnailPreview = document.getElementById('thumbnailPreview');
        thumbnailPreview.src = e.target.result;
        thumbnailPreview.style.display = 'block';
        
        // Store thumbnail data
        document.getElementById('thumbnail').value = e.target.result;
    };
    reader.readAsDataURL(file);
}

function handleMultipleImageUpload(files) {
    if (!files) return;
    
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const previewGrid = document.getElementById('previewGrid');
    
    imagePreviewContainer.style.display = 'block';
    
    Array.from(files).forEach(file => {
        // Validate file
        if (!file.type.startsWith('image/')) {
            showAdminMessage('Please select only image files', 'error');
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) { // 5MB
            showAdminMessage('File size must be less than 5MB', 'error');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            addImagePreview(e.target.result, file.name);
            uploadedImages.push({
                name: file.name,
                data: e.target.result
            });
        };
        reader.readAsDataURL(file);
    });
    
    // Update hidden input
    document.getElementById('images').value = JSON.stringify(uploadedImages);
}

function addImagePreview(src, filename) {
    const previewGrid = document.getElementById('previewGrid');
    
    const previewItem = document.createElement('div');
    previewItem.className = 'preview-item';
    
    const img = document.createElement('img');
    img.src = src;
    img.alt = filename;
    
    const removeBtn = document.createElement('button');
    removeBtn.className = 'remove-btn';
    removeBtn.innerHTML = '×';
    removeBtn.onclick = function() {
        removeImage(previewItem, src);
    };
    
    previewItem.appendChild(img);
    previewItem.appendChild(removeBtn);
    previewGrid.appendChild(previewItem);
}

function removeImage(previewItem, src) {
    previewItem.remove();
    uploadedImages = uploadedImages.filter(img => img.data !== src);
    document.getElementById('images').value = JSON.stringify(uploadedImages);
    
    // Hide container if no images
    if (uploadedImages.length === 0) {
        document.getElementById('imagePreviewContainer').style.display = 'none';
    }
}

function clearAllImages() {
    const previewGrid = document.getElementById('previewGrid');
    previewGrid.innerHTML = '';
    uploadedImages = [];
    document.getElementById('images').value = JSON.stringify(uploadedImages);
    document.getElementById('imagePreviewContainer').style.display = 'none';
}

// Export functions for use in other scripts
window.Ten12Admin = {
    loadDashboardStats,
    loadRecentProjects,
    editProject,
    showAdminMessage,
    showMessage,
    initImageUploads,
    handleThumbnailUpload,
    handleMultipleImageUpload,
    addImagePreview,
    removeImage,
    clearAllImages
};
