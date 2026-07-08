// Navigation functionality
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');

    // Mobile navigation toggle
    navToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        navToggle.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Initialize theme toggle
    initThemeToggle();
    
    // Initialize hamburger menu
    initHamburgerMenu();
    
    // Set active navigation link
    const currentPath = window.location.pathname.split('/').pop();
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href').slice(1) === currentPath) {
            link.classList.add('active');
        }
    });

    // Active navigation link based on scroll position
    window.addEventListener('scroll', function() {
    });

    // Load featured projects
    loadFeaturedProjects();

    // Contact form submission
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactForm);
    }

    // Scroll animations
    initScrollAnimations();
});

// Theme Toggle Functionality
function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const radioInputs = document.querySelectorAll('.theme-radio-input');
    
    // Get saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    
    // Set the corresponding radio button as checked
    radioInputs.forEach(input => {
        if (input.value === savedTheme) {
            input.checked = true;
        }
    });
    
    if (themeToggle) {
        radioInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.checked) {
                    html.setAttribute('data-theme', this.value);
                    localStorage.setItem('theme', this.value);
                }
            });
        });
    }
}

// Hamburger Menu Functionality
function initHamburgerMenu() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');

    if (navToggle && navMenu) {
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navToggle.classList.remove('active');
                navMenu.classList.remove('active');
            }
        });

        // Close menu when clicking on a link
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navToggle.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }
}

// API Configuration
const API_BASE_URL = '/backend/api';

// Load featured projects
async function loadFeaturedProjects() {
    const container = document.getElementById('featuredProjects');
    if (!container) return;

    try {
        const response = await fetch(`${API_BASE_URL}/projects.php?limit=6`);
        const data = await response.json();

        if (data.records && data.records.length > 0) {
            container.innerHTML = data.records.map(project => createProjectCard(project)).join('');
        } else {
            container.innerHTML = '<p class="no-projects">No projects found.</p>';
        }
    } catch (error) {
        console.error('Error loading projects:', error);
        container.innerHTML = '<p class="error-message">Failed to load projects.</p>';
    }
}

// Create project card HTML
function createProjectCard(project) {
    const tags = project.tags ? project.tags.map(tag => `<span class="tag">${tag}</span>`).join('') : '';
    // Thumbnail path - if it starts with uploads/, prepend backend/, otherwise use assets/images/
    let thumbnail = project.thumbnail || '';
    if (thumbnail && !thumbnail.startsWith('http')) {
        if (thumbnail.startsWith('uploads/')) {
            thumbnail = '../backend/' + thumbnail;
        } else if (!thumbnail.includes('assets/')) {
            thumbnail = 'assets/images/placeholder.jpg';
        }
    }
    
    // Handle multiple images
    let imagesHtml = '';
    if (project.images && project.images.length > 0) {
        const images = project.images.map((img, index) => {
            let imgSrc = img.startsWith('http') ? img : (img.startsWith('uploads/') ? '../backend/' + img : img);
            return `<img src="${imgSrc}" alt="${project.title} - Image ${index + 1}" loading="lazy">`;
        }).join('');
        
        imagesHtml = `
            <div class="project-images">
                <div class="images-grid">
                    ${images}
                </div>
            </div>
        `;
    }
    
    return `
        <div class="project-card" onclick="window.location.href='project-detail.php?id=${project.id}'">
            <div class="project-thumbnail">
                <img src="${thumbnail}" alt="${project.title}" loading="lazy">
            </div>
            <div class="project-content">
                <h3 class="project-title">${project.title}</h3>
                <p class="project-description">${project.description}</p>
                <div class="project-tags">${tags}</div>
                ${imagesHtml}
                <div class="project-links">
                    ${project.live_url ? `<a href="${project.live_url}" target="_blank" class="project-link">Live Demo</a>` : ''}
                    ${project.github_url ? `<a href="${project.github_url}" target="_blank" class="project-link">GitHub</a>` : ''}
                </div>
            </div>
        </div>
    `;
}

// Handle contact form submission
async function handleContactForm(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const messageDiv = document.getElementById('formMessage');
    
    // Disable submit button
    submitButton.disabled = true;
    submitButton.textContent = 'Sending...';
    
    try {
        const response = await fetch(`${API_BASE_URL}/contact.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: formData.get('name'),
                email: formData.get('email'),
                message: formData.get('message')
            })
        });

        const result = await response.json();
        
        if (response.ok) {
            showMessage('Message sent successfully! We\'ll get back to you soon.', 'success');
            form.reset();
        } else {
            showMessage(result.message || 'Failed to send message. Please try again.', 'error');
        }
    } catch (error) {
        console.error('Contact form error:', error);
        showMessage('Failed to send message. Please try again.', 'error');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Send Message';
    }
}

// Show form message
function showMessage(message, type) {
    const messageDiv = document.getElementById('formMessage');
    messageDiv.textContent = message;
    messageDiv.className = `form-message ${type}`;
    messageDiv.style.display = 'block';
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// Initialize scroll animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const animatedElements = document.querySelectorAll('.section-header, .project-card, .stat, .contact-form');
    animatedElements.forEach(el => observer.observe(el));
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Search functionality (for projects page)
function initProjectSearch() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', debounce(performSearch, 300));
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', performSearch);
    }
}

async function performSearch() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const container = document.getElementById('projectsContainer');
    
    if (!container) return;
    
    const searchTerm = searchInput ? searchInput.value : '';
    const category = categoryFilter ? categoryFilter.value : '';
    
    try {
        let url = `${API_BASE_URL}/projects.php`;
        const params = new URLSearchParams();
        
        if (searchTerm) params.append('search', searchTerm);
        if (category) params.append('category', category);
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.records && data.records.length > 0) {
            container.innerHTML = data.records.map(project => createProjectCard(project)).join('');
        } else {
            container.innerHTML = '<p class="no-projects">No projects found matching your criteria.</p>';
        }
    } catch (error) {
        console.error('Search error:', error);
        container.innerHTML = '<p class="error-message">Failed to search projects.</p>';
    }
}

// Image gallery functionality (for project detail page)
function initImageGallery() {
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.gallery-thumbnail');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const imageUrl = this.getAttribute('data-image');
                mainImage.src = imageUrl;
                
                // Update active thumbnail
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
}

// Admin dashboard functionality
function initAdminDashboard() {
    // Check if user is authenticated
    checkAdminAuth();
    
    // Initialize admin-specific functionality
    initProjectManagement();
    initImageUpload();
}

function checkAdminAuth() {
    const token = localStorage.getItem('adminToken');
    const currentPage = window.location.pathname;
    
    if (!token && !currentPage.includes('admin/login.html')) {
        window.location.href = 'admin/login.html';
        return;
    }
    
    if (token) {
        // Verify token with server
        fetch(`${API_BASE_URL}/admin/auth.php`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => {
            if (!response.ok) {
                localStorage.removeItem('adminToken');
                window.location.href = 'admin/login.html';
            }
        })
        .catch(error => {
            console.error('Auth check failed:', error);
            localStorage.removeItem('adminToken');
            window.location.href = 'admin/login.html';
        });
    }
}

function initProjectManagement() {
    const projectForm = document.getElementById('projectForm');
    if (projectForm) {
        projectForm.addEventListener('submit', handleProjectSubmit);
    }
    
    // Initialize delete buttons
    const deleteButtons = document.querySelectorAll('.delete-project');
    deleteButtons.forEach(button => {
        button.addEventListener('click', handleProjectDelete);
    });
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
        
        const response = await fetch(`${API_BASE_URL}/projects.php${isEdit ? `?id=${projectId}` : ''}`, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            },
            body: JSON.stringify(projectData)
        });

        const result = await response.json();
        
        if (response.ok) {
            showAdminMessage(result.message, 'success');
            if (!isEdit) {
                form.reset();
            }
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
    
    if (!confirm('Are you sure you want to delete this project?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/projects.php?id=${projectId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            }
        });

        const result = await response.json();
        
        if (response.ok) {
            showAdminMessage(result.message, 'success');
            // Remove project from DOM
            e.target.closest('.project-item').remove();
        } else {
            showAdminMessage(result.message || 'Failed to delete project.', 'error');
        }
    } catch (error) {
        console.error('Delete error:', error);
        showAdminMessage('Failed to delete project. Please try again.', 'error');
    }
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

function initImageUpload() {
    const imageUpload = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');
    
    if (imageUpload && imagePreview) {
        imageUpload.addEventListener('change', handleImageUpload);
    }
}

async function handleImageUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    const formData = new FormData();
    formData.append('image', file);
    
    try {
        const response = await fetch(`${API_BASE_URL}/upload.php`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            },
            body: formData
        });

        const result = await response.json();
        
        if (response.ok) {
            imagePreview.src = result.url;
            // Update hidden input with image URL
            const thumbnailInput = document.getElementById('thumbnail');
            if (thumbnailInput) {
                thumbnailInput.value = result.url;
            }
        } else {
            showAdminMessage(result.message || 'Failed to upload image.', 'error');
        }
    } catch (error) {
        console.error('Upload error:', error);
        showAdminMessage('Failed to upload image. Please try again.', 'error');
    }
}

// Export functions for use in other scripts
window.Ten12Portfolio = {
    loadFeaturedProjects,
    performSearch,
    initImageGallery,
    initAdminDashboard,
    showMessage
};
