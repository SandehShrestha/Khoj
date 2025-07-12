// Global flag to prevent multiple initializations
let accountPageInitialized = false;

function initializeAccountPage() {
    // Prevent multiple initializations
    if (accountPageInitialized) {
        return;
    }
    
    console.log('Initializing account page...');
    
    // Navigation handling
    const navItems = document.querySelectorAll('.settings-nav li');
    const sections = document.querySelectorAll('.settings-section');

    // Remove existing event listeners by cloning and replacing elements
    navItems.forEach(item => {
        const newItem = item.cloneNode(true);
        item.parentNode.replaceChild(newItem, item);
    });

    // Re-select elements after cloning
    const newNavItems = document.querySelectorAll('.settings-nav li');
    
    newNavItems.forEach(item => {
        item.addEventListener('click', function() {
            console.log('Nav item clicked:', this.getAttribute('data-section'));
            
            // Remove active class from all items
            newNavItems.forEach(nav => nav.classList.remove('active'));
            sections.forEach(section => section.classList.remove('active'));

            // Add active class to clicked item
            this.classList.add('active');
            
            // Show corresponding section
            const sectionId = this.getAttribute('data-section');
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.add('active');
                // If 'my-posts' section, fetch posts
                if (sectionId === 'my-posts') {
                    fetchAndRenderMyPosts();
                }
                // If 'profile-info' section, fetch profile info and re-attach submit handler
                if (sectionId === 'profile-info') {
                    fetchAndFillProfileInfo();
                    attachProfileFormHandler();
                }
            }
        });
    });

    // Form handling - remove existing listeners first
    const forms = document.querySelectorAll('.settings-form');
    forms.forEach(form => {
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
    });

    // Re-select forms and add listeners
    const newForms = document.querySelectorAll('.settings-form');
    newForms.forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });

    // Profile picture preview
    const profilePictureInput = document.getElementById('profilePicture');
    const profilePreview = document.getElementById('profile-preview');

    if (profilePictureInput && profilePreview) {
        // Remove existing listener
        const newInput = profilePictureInput.cloneNode(true);
        profilePictureInput.parentNode.replaceChild(newInput, profilePictureInput);
        
        // Add new listener
        newInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    alert('File size must be less than 5MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profile-preview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Password validation
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        // Remove existing listener
        const newPasswordForm = passwordForm.cloneNode(true);
        passwordForm.parentNode.replaceChild(newPasswordForm, passwordForm);
        
        // Add new listener
        newPasswordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const submitButton = newPasswordForm.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Updating...';
            if (newPassword.length < 8) {
                showNotification('Password must be at least 8 characters long', 'error');
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                return;
            }
            if (newPassword !== confirmPassword) {
                showNotification('Passwords do not match', 'error');
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                return;
            }
            try {
                const formData = new FormData();
                formData.append('current_password', currentPassword);
                formData.append('new_password', newPassword);
                const resp = await fetch('./api/change_password.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await resp.json();
                if (result.success) {
                    showNotification('Password updated successfully!', 'success');
                    newPasswordForm.reset();
                } else {
                    showNotification(result.message || 'Failed to update password.', 'error');
                    console.error('Password update error:', result);
                }
            } catch (error) {
                showNotification('Error updating password.', 'error');
                console.error('Password update exception:', error);
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    }
    
    accountPageInitialized = true;
    console.log('Account page initialized successfully');
}

// Function to reset initialization flag when leaving the page
function resetAccountPage() {
    accountPageInitialized = false;
    console.log('Account page reset');
}

// Call the function if the script is loaded directly
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAccountPage);
} else {
    initializeAccountPage();
}

// Form submission handler
async function handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const formId = form.id;

    try {
        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Saving...';

        // Here you would typically make an API call to update the user's data
        // For demonstration, we'll simulate an API call
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Show success message
        showNotification('Changes saved successfully!', 'success');
        
        // Reset form if it's the password form
        if (formId === 'password-form') {
            form.reset();
        }
    } catch (error) {
        showNotification('Error saving changes. Please try again.', 'error');
    } finally {
        // Reset button state
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
}

// Notification system
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    // Add styles for the notification
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.padding = '15px 25px';
    notification.style.borderRadius = '4px';
    notification.style.color = 'white';
    notification.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
    notification.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
    notification.style.zIndex = '1000';
    notification.style.animation = 'slideIn 0.3s ease-out';

    document.body.appendChild(notification);

    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add animation keyframes for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Form validation
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
        } else {
            input.classList.remove('error');
        }
    });

    return isValid;
}

// Add error styles
const errorStyle = document.createElement('style');
errorStyle.textContent = `
    .error {
        border-color: #f44336 !important;
    }
    .error:focus {
        box-shadow: 0 0 0 2px rgba(244, 67, 54, 0.2);
    }
`;
document.head.appendChild(errorStyle);

// --- My Posts Feed Logic ---
async function fetchAndRenderMyPosts() {
    const feed = document.getElementById('my-posts-feed');
    if (!feed) return;
    feed.innerHTML = '<p style="padding:2rem;">Loading your posts...</p>';
    try {
        const response = await fetch('./api/get_items.php?mine=1');
        const result = await response.json();
        if (result.success && Array.isArray(result.items)) {
            if (result.items.length === 0) {
                feed.innerHTML = '<p style="padding:2rem;">You have not posted any items yet.</p>';
                return;
            }
            feed.innerHTML = '';
            result.items.forEach(item => {
                const itemCard = document.createElement('div');
                itemCard.className = 'item-card facebook-style';
                itemCard.innerHTML = `
                    <div class="item-header-row">
                        <div class="item-profile-block">
                            <div class="profile-pic-circle"><i class='fa fa-user'></i></div>
                            <div class="profile-details">
                                <span class="item-username">${item.found_by_name || 'Unknown'}</span>
                                <span class="item-phone">${item.uploader_phone || 'N/A'}</span>
                                <span class="item-date">${item.Created_at ? new Date(item.Created_at).toLocaleString() : ''}</span>
                            </div>
                        </div>
                        <div class="item-meta">
                            <span class="item-status ${item.Is_claimed ? 'claimed' : 'unclaimed'}">${item.Is_claimed ? 'Claimed' : 'Unclaimed'}</span>
                            ${item.Is_claimed == 0 ? `<button class='delete-post-btn' data-id='${item.Item_ID}'>Delete</button>` : ''}
                        </div>
                    </div>
                    <div class="item-title">${item.Item_Name}</div>
                    <div class="item-description full-width">${item.Description}</div>
                    <div class="item-image"><img src="${item.Image_url ? item.Image_url : './images/login-page-bg.jpg'}" alt="Item Image" onerror="this.src='./images/login-page-bg.jpg'"></div>
                `;
                feed.appendChild(itemCard);
            });
            // Add delete button listeners
            feed.querySelectorAll('.delete-post-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    if (!confirm('Are you sure you want to delete this post? This cannot be undone.')) return;
                    const itemId = this.getAttribute('data-id');
                    try {
                        const formData = new FormData();
                        formData.append('item_id', itemId);
                        const resp = await fetch('./api/delete_item.php', {
                            method: 'POST',
                            body: formData
                        });
                        const res = await resp.json();
                        if (res.success) {
                            showNotification('Post deleted successfully!', 'success');
                            fetchAndRenderMyPosts();
                        } else {
                            showNotification(res.message || 'Failed to delete post.', 'error');
                        }
                    } catch (error) {
                        showNotification('Error deleting post.', 'error');
                    }
                });
            });
        } else {
            feed.innerHTML = '<p style="padding:2rem;">Failed to load your posts.</p>';
        }
    } catch (error) {
        feed.innerHTML = '<p style="padding:2rem;">Error loading your posts.</p>';
    }
}

// --- Profile Info Fetch & Update ---
async function fetchAndFillProfileInfo() {
    try {
        const resp = await fetch('./api/get_items.php?mine=1');
        const result = await resp.json();
        if (result.success && Array.isArray(result.items) && result.items.length > 0) {
            // Use the first item to get user info (since all items have the same user info)
            const user = result.items[0];
            document.getElementById('fullName').value = user.found_by_name || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('email').readOnly = true;
            document.getElementById('phone').value = user.uploader_phone || '';
        }
    } catch (error) {
        // Optionally show error
    }
}

// Attach profile form submit handler on page load if Profile Information is active
function attachProfileFormHandler() {
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        const newForm = profileForm.cloneNode(true);
        profileForm.parentNode.replaceChild(newForm, profileForm);
        newForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const username = document.getElementById('fullName').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const submitButton = newForm.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
            try {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('phone', phone);
                const resp = await fetch('./api/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await resp.json();
                if (result.success) {
                    showNotification('Profile updated successfully!', 'success');
                    // Update welcome message if present
                    const welcome = document.querySelector('.welcome-message');
                    if (welcome && result.user && result.user.Username) {
                        welcome.textContent = `Welcome ${result.user.Username}!`;
                    }
                    // Re-fetch and fill profile info to ensure UI is in sync
                    await fetchAndFillProfileInfo();
                } else {
                    showNotification(result.message || 'Failed to update profile.', 'error');
                    console.error('Profile update error:', result);
                }
            } catch (error) {
                showNotification('Error updating profile.', 'error');
                console.error('Profile update exception:', error);
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    }
}

// On page load, if Profile Information is active, attach the handler
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('profile-info')?.classList.contains('active')) {
            attachProfileFormHandler();
        }
    });
} else {
    if (document.getElementById('profile-info')?.classList.contains('active')) {
        attachProfileFormHandler();
    }
}
