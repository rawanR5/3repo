// remove

// Select form and inputs
const signupForm = document.querySelector('.signup-form-T');
const firstNameInput = document.querySelector('input[name="First_name"]');
const lastNameInput = document.querySelector('input[name="Last_name"]');
const emailInput = document.querySelector('input[name="email"]');
const passwordInput = document.querySelector('input[name="password"]');
const fileInput = document.querySelector('#photo-upload');

// Helper function to validate email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Form submission handler
signupForm.addEventListener('submit', (event) => {
    let isValid = true;
    let message = "";

    // Prevent form submission
    event.preventDefault();

    // Validate first name
    if (firstNameInput.value.trim() === "") {
        isValid = false;
        message += "First name is required.\n";
    }

    // Validate last name
    if (lastNameInput.value.trim() === "") {
        isValid = false;
        message += "Last name is required.\n";
    }

    // Validate email
    if (emailInput.value.trim() === "" || !validateEmail(emailInput.value.trim())) {
        isValid = false;
        message += "Valid email is required.\n";
    }

    // Validate password
    if (passwordInput.value.trim().length < 6) {
        isValid = false;
        message += "Password must be at least 6 characters long.\n";
    }

    // Validate file (optional)
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
        if (!allowedTypes.includes(file.type)) {
            isValid = false;
            message += "Uploaded file must be a JPG or PNG image.\n";
        } else if (file.size > 2 * 1024 * 1024) { // 2 MB limit
            isValid = false;
            message += "Uploaded file must be smaller than 2MB.\n";
        }
    }

    // Show validation messages or submit form
    if (!isValid) {
        alert(message);
    } else {
        // Submit the form programmatically
        signupForm.submit();
    }
});
