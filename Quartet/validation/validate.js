/*
validate.js
A script to validate form inputs.
Authors: Alexandra Stratton, Brinley Hull, Jose Leyba, Ben Renner, Kyle Moore
Creation date: 4/26/2025
Revisions:

*/
// Validate name fields (First and Last)
function validateName() {
    const nameInput = document.getElementById(this.id);
    const nameError = document.getElementById(this.id + '-error');
    const nameValue = nameInput.value.trim();

    if (!nameValue) {
        nameError.textContent = "This field is required";
        nameError.style.display = 'block';
        nameInput.classList.add('invalid');
        return false;
    } else if (!/^[A-Za-z\s'-]+$/.test(nameValue)) {
        nameError.textContent = "Only letters and basic punctuation allowed";
        nameError.style.display = 'block';
        nameInput.classList.add('invalid');
        return false;
    } else if (nameValue.length > 50) {
        nameError.textContent = "Maximum 50 characters allowed";
        nameError.style.display = 'block';
        nameInput.classList.add('invalid');
        return false;
    } else {
        nameError.style.display = 'none';
        nameInput.classList.remove('invalid');
        return true;
    }
}

// Validate price
function validatePrice() {
    const priceInput = document.getElementById(this.id);
    const priceError = document.getElementById(this.id + '-error');
    if (priceInput.value <= 0 || isNaN(priceInput.value)) {
        priceError.textContent = "Price must be a positive number";
        priceError.style.display = 'inline';
        return false;
    } else {
        priceError.style.display = 'none';
        return true;
    }
}

// Validate service duration
function validateDuration() {
    const durationInput = document.getElementById(this.id);
    const durationError = document.getElementById(this.id + '-error');
    if (durationInput.value <= 0 || isNaN(durationInput.value)) {
        durationError.textContent = "Duration must be a positive number";
        durationError.style.display = 'inline';
        return false;
    } else {
        durationError.style.display = 'none';
        return true;
    }
}

// Validate product image
function validateImage(id='image') {
    const imageInput = document.getElementById(id);
    const imageError = document.getElementById(id + '-error');
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    const maxSize = 10 * 1024 * 1024; // 10MB

    if (imageInput.files.length > 0) {
        const file = imageInput.files[0];
        if (!allowedTypes.includes(file.type)) {
            imageError.textContent = "Only JPEG, PNG, and GIF images are allowed.";
            imageError.style.display = 'inline';
            return false;
        } else if (file.size > maxSize) {
            imageError.textContent = "File size must be less than 10MB.";
            imageError.style.display = 'inline';
            return false;
        } else {
            imageError.style.display = 'none';
            displayFileName();
            return true;
        }
    } else if (imageError) {
        imageError.textContent = "Please upload an image.";
        imageError.style.display = 'inline';
        return false;
    } else {
        return true;
    }
}

/* Validates Email */
function validateEmail() {
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailInput.value.trim() === "") {
        emailError.textContent = "Email cannot be empty.";
        emailError.style.display = 'inline';
        return false;
    }
    if (!emailRegex.test(emailInput.value)) {
        emailError.textContent = "Please enter a valid email address (e.g., user@example.com).";
        emailError.style.display = 'inline';
        return false;
    }
    emailError.style.display = 'none';
    return true;
}

/* Validates Phone Number */
function validatePhone() {
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phone-error');
    let phoneNumber = phoneInput.value.trim();

    if (phoneNumber === "") {
        phoneError.textContent = "Phone number cannot be empty.";
        phoneError.style.display = 'inline';
        return false;
    }

    // Remove all non-numeric characters
    phoneNumber = phoneNumber.replace(/\D/g, '');

    // Remove country code if it starts with "1" (U.S. numbers)
    if (phoneNumber.length === 11 && phoneNumber.startsWith('1')) {
        phoneNumber = phoneNumber.substring(1);
    }

    // Ensure it's a valid 10-digit U.S. number
    if (phoneNumber.length !== 10) {
        phoneError.textContent = "Please enter a valid phone number";
        phoneError.style.display = 'inline';
        return false;
    }

    // Hide error message if valid
    phoneError.style.display = 'none';

    // Format phone number as ##########
    phoneInput.value = phoneNumber;

    return true;
}