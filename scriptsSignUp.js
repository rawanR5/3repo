// Form validation
/* document.querySelector('.signup-form-T').addEventListener('submit', function (e) {
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
 
}); */

import { addUser } from "./app.js";

document.querySelector('.signup-form-T').addEventListener('submit', async function (e) {
    e.preventDefault();

    const firstName = document.querySelector('input[placeholder="First name"]').value.trim();
    const lastName = document.querySelector('input[placeholder="Last name"]').value.trim();
    const email = document.querySelector('input[placeholder="Email"]').value.trim();
    const password = document.querySelector('input[placeholder="Password"]').value.trim();

    if (!firstName || !lastName || !email || !password) {
        alert("Please fill in all required fields!");
        return;
    }

    const userData = {
        first_name: firstName,
        last_name: lastName,
        email: email,
        password: btoa(password), // For demo; use bcrypt in production
        role: "s", // Default role for students
        age: 0,
        bio: "",
        city: "",
        gender: "",
        phone: "",
        photo: "",
    };

    try {
        await addUser(userData); // Pass the object
        alert("Sign-up successful!");
    } catch (error) {
        console.error("Sign-up failed:", error);
        alert("Failed to sign up. Please try again.");
    }

    console.log("User data being sent:", userData);
});
