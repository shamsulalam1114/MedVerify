function validateChangePassword() {
    clearErrors();
    let isValid = true;

    // Validate Current Password
    const currentPassword = document.getElementById('current_password').value;
    if (currentPassword === '') {
        showError('currentPasswordError', 'Current password is required');
        isValid = false;
    }

    // Validate New Password
    const newPassword = document.getElementById('new_password').value;
    if (newPassword === '') {
        showError('newPasswordError', 'New password is required');
        isValid = false;
    } else if (newPassword.length < 6) {
        showError('newPasswordError', 'Password must be at least 6 characters');
        isValid = false;
    }

    // Validate Confirm Password
    const confirmPassword = document.getElementById('confirm_password').value;
    if (confirmPassword === '') {
        showError('confirmPasswordError', 'Please confirm your new password');
        isValid = false;
    } else if (newPassword !== confirmPassword) {
        showError('confirmPasswordError', 'Passwords do not match');
        isValid = false;
    }

    // Check if new password is different from current
    if (currentPassword !== '' && newPassword !== '' && currentPassword === newPassword) {
        showError('newPasswordError', 'New password must be different from current password');
        isValid = false;
    }

    return isValid;
}

function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

function clearErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.textContent = '';
        element.style.display = 'none';
    });
}
