// User Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize user management
    initUserManagement();
});

function initUserManagement() {
    // Load users list
    loadUsers();
    
    // Initialize user form
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', handleUserSubmit);
    }
}

async function loadUsers() {
    const usersList = document.getElementById('usersList');
    
    try {
        const response = await fetch('/backend/api/admin/users.php');
        const data = await response.json();
        
        if (response.ok && data.users) {
            usersList.innerHTML = data.users.map(user => createUserCard(user)).join('');
            
            // Add delete event listeners
            const deleteButtons = usersList.querySelectorAll('.delete-user');
            deleteButtons.forEach(button => {
                button.addEventListener('click', handleDeleteUser);
            });
        } else {
            usersList.innerHTML = '<p class="no-users">No admin users found.</p>';
        }
    } catch (error) {
        console.error('Load users error:', error);
        usersList.innerHTML = '<p class="error-message">Failed to load users.</p>';
    }
}

function createUserCard(user) {
    const isCurrentUser = user.id == <?php echo $_SESSION['admin_id']; ?>;
    
    return `
        <div class="user-card">
            <div class="user-info">
                <h3>${user.username}</h3>
                <p>${user.email}</p>
                <small>Created: ${new Date(user.created_at).toLocaleDateString()}</small>
            </div>
            <div class="user-actions">
                ${isCurrentUser ? '<span class="current-user">Current User</span>' : `<button class="btn btn-danger delete-user" data-user-id="${user.id}">Delete</button>`}
            </div>
        </div>
    `;
}

async function handleUserSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const password = formData.get('password');
    const confirmPassword = formData.get('confirmPassword');
    
    // Validate passwords match
    if (password !== confirmPassword) {
        showUserMessage('Passwords do not match', 'error');
        return;
    }
    
    // Validate password strength
    if (password.length < 6) {
        showUserMessage('Password must be at least 6 characters long', 'error');
        return;
    }
    
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    // Disable submit button
    submitButton.disabled = true;
    submitButton.textContent = 'Creating...';
    
    try {
        const response = await fetch('/backend/api/admin/users.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username: formData.get('username'),
                email: formData.get('email'),
                password: password
            })
        });

        const result = await response.json();
        
        if (response.ok) {
            showUserMessage('Admin user created successfully!', 'success');
            form.reset();
            loadUsers(); // Reload users list
        } else {
            showUserMessage(result.message || 'Failed to create user', 'error');
        }
    } catch (error) {
        console.error('Create user error:', error);
        showUserMessage('Failed to create user. Please try again.', 'error');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
}

async function handleDeleteUser(e) {
    const userId = e.target.getAttribute('data-user-id');
    
    if (!confirm('Are you sure you want to delete this admin user? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/backend/api/admin/users.php?id=${userId}`, {
            method: 'DELETE'
        });

        const result = await response.json();
        
        if (response.ok) {
            showUserMessage('Admin user deleted successfully!', 'success');
            loadUsers(); // Reload users list
        } else {
            showUserMessage(result.message || 'Failed to delete user', 'error');
        }
    } catch (error) {
        console.error('Delete user error:', error);
        showUserMessage('Failed to delete user. Please try again.', 'error');
    }
}

function showUserMessage(message, type) {
    const messageDiv = document.getElementById('userMessage');
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.className = `form-message ${type}`;
        
        // Clear message after 5 seconds
        setTimeout(() => {
            messageDiv.textContent = '';
            messageDiv.className = 'form-message';
        }, 5000);
    }
}