// Add event listener for form submission
document.querySelector('.signup-form-T').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent the default form submission behavior

    // Collecting input values
    const firstName = document.querySelector('input[placeholder="First name"]').value.trim();
    const lastName = document.querySelector('input[placeholder="Last name"]').value.trim();
    const age = document.querySelector('input[placeholder="Age"]').value.trim();
    const gender = document.querySelector('select[name="gender"]').value.trim();
    const email = document.querySelector('input[placeholder="Email"]').value.trim();
    const password = document.querySelector('input[placeholder="Password"]').value.trim();
    const phone = document.querySelector('input[placeholder="Phone"]').value.trim();
    const city = document.querySelector('input[placeholder="City"]').value.trim();
    const bio = document.querySelector('textarea[placeholder="Short Bio"]').value.trim();
    const photoInput = document.querySelector('#photo-upload');
    const photo = photoInput.files.length > 0 ? photoInput.files[0] : null;

    // Validating inputs
    if (!firstName || !lastName || !age || !gender || !email || !password) {
        alert('Please fill in all required fields: First name, Last name, Age, Gender, Email, and Password.');
        return;
    }

    if (!isValidEmail(email)) {
        alert('Please enter a valid email address.');
        return;
    }

    if (password.length < 6) {
        alert('Password must be at least 6 characters long.');
        return;
    }

    if (isNaN(age) || age <= 0) {
        alert('Age must be a positive number.');
        return;
    }

    if (phone && !isValidPhone(phone)) {
        alert('Please enter a valid phone number.');
        return;
    }

    // Prepare data for submission
    const formData = new FormData();
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('age', age);
    formData.append('gender', gender);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('phone', phone);
    formData.append('city', city);
    formData.append('bio', bio);
    if (photo) {
        formData.append('photo', photo);
    }

    // Send form data to the server via AJAX
    fetch('signupTeacher.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.href = 'loginTeacher.html'; // Redirect on success
            } else {
                alert(data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });

    // Reset the form
    document.querySelector('.signup-form-T').reset();
});

// Helper function to validate email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Helper function to validate phone number
function isValidPhone(phone) {
    // Allow phone numbers with 9–15 digits, starting with a "+" or "0"
    const phoneRegex = /^(?:\+?[1-9][0-9]{0,2})?0?[5-9][0-9]{7,}$/;
    return phoneRegex.test(phone);
}
