function validateEditManufacturer() {
    // Clear previous errors
    clearErrors();

    let isValid = true;

    // Validate Name
    const name = document.getElementById('name').value.trim();
    if (name === '') {
        showError('nameError', 'Manufacturer name is required');
        isValid = false;
    } else if (name.length < 2) {
        showError('nameError', 'Name must be at least 2 characters');
        isValid = false;
    } else if (name.length > 255) {
        showError('nameError', 'Name must not exceed 255 characters');
        isValid = false;
    }

    // Validate Country
    const country = document.getElementById('country').value;
    if (country === '') {
        showError('countryError', 'Please select a country');
        isValid = false;
    }

    // Validate License Number
    const licenseNumber = document.getElementById('license_number').value.trim();
    if (licenseNumber === '') {
        showError('licenseNumberError', 'License number is required');
        isValid = false;
    } else if (licenseNumber.length < 5) {
        showError('licenseNumberError', 'License number must be at least 5 characters');
        isValid = false;
    }

    // Validate Contact Email (optional but must be valid if provided)
    const contactEmail = document.getElementById('contact_email').value.trim();
    if (contactEmail !== '') {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(contactEmail)) {
            showError('contactEmailError', 'Please enter a valid email address');
            isValid = false;
        }
    }

    // Validate Contact Phone (optional but must be valid if provided)
    const contactPhone = document.getElementById('contact_phone').value.trim();
    if (contactPhone !== '') {
        const phonePattern = /^[\d\s\+\-\(\)]+$/;
        if (!phonePattern.test(contactPhone)) {
            showError('contactPhoneError', 'Please enter a valid phone number');
            isValid = false;
        }
    }

    // Validate Website (optional but must be valid URL if provided)
    const website = document.getElementById('website').value.trim();
    if (website !== '') {
        try {
            new URL(website);
        } catch (e) {
            showError('websiteError', 'Please enter a valid website URL (e.g., https://example.com)');
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
