// Form validation
document.querySelector('.signup-form-T').addEventListener('submit', function (e) {
    e.preventDefault();

    const firstName = document.querySelector('input[placeholder="First name"]').value.trim();
    const lastName = document.querySelector('input[placeholder="Last name"]').value.trim();
    const email = document.querySelector('input[placeholder="Email"]').value.trim();
    const password = document.querySelector('input[placeholder="Password"]').value.trim();

    if (!firstName || !lastName || !email || !password) {
        alert('Please fill in all required fields!');
        return;
    }

    if (password.length < 8) {
        alert('Password must be at least 8 characters!');
        return;
    }

    alert('Form submitted successfully!');
 
});
