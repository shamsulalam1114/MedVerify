function validateEditProfile() {
    clearErrors();
    let isValid = true;

    // Validate Full Name
    const fullName = document.getElementById('full_name').value.trim();
    if (fullName === '') {
        showError('fullNameError', 'Full name is required');
        isValid = false;
    } else if (fullName.length < 2) {
        showError('fullNameError', 'Full name must be at least 2 characters');
        isValid = false;
    }

    // Validate Email
    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        showError('emailError', 'Email is required');
        isValid = false;
    } else if (!emailPattern.test(email)) {
        showError('emailError', 'Please enter a valid email address');
        isValid = false;
    }

    // Validate Date of Birth (optional but must be in past if provided)
    const dob = document.getElementById('date_of_birth').value;
    if (dob !== '') {
        const dobDate = new Date(dob);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (dobDate > today) {
            showError('dobError', 'Date of birth cannot be in the future');
            isValid = false;
        }
    }

    // Validate Phone (optional but must be valid if provided)
    const phone = document.getElementById('phone').value.trim();
    if (phone !== '') {
        const phonePattern = /^[\d\s\+\-\(\)]+$/;
        if (!phonePattern.test(phone)) {
            showError('phoneError', 'Please enter a valid phone number');
            isValid = false;
        }
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
