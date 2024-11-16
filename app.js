import { collection, addDoc, getDocs } from "https://www.gstatic.com/firebasejs/10.5.0/firebase-firestore.js";
import { db } from "./firebase.js";

// إضافة مستخدم جديد إلى قاعدة البيانات
/* const addUser = async (firstName, lastName, email) => {
    try {
        const docRef = await addDoc(collection(db, "users"), {
            first_name: firstName,
            last_name: lastName,
            email: email
        });
        console.log("User added with ID: ", docRef.id);
    } catch (e) {
        console.error("Error adding user: ", e);
    }
}; */

export const addUser = async (userData) => {
    try {
        console.log("Adding user data:", userData); // Debug log
        const docRef = await addDoc(collection(db, "Users"), userData);
        console.log("User added with ID:", docRef.id);
    } catch (e) {
        console.error("Error adding user:", e.message);
    }
};

// قراءة بيانات المستخدمين
const getUsers = async () => {
    try {
        const querySnapshot = await getDocs(collection(db, "Users"));
        querySnapshot.forEach((doc) => {
            console.log(${doc.id} => ${JSON.stringify(doc.data())});
        });
    } catch (e) {
        console.error("Error fetching users: ", e);
    }
};

// استدعاء الوظائف عند تحميل الصفحة
document.addEventListener("DOMContentLoaded", () => {
    // مثال: إضافة مستخدم جديد
    addUser("John", "Doe", "john.doe@example.com");

    // مثال: قراءة بيانات المستخدمين
    getUsers();
});


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

    console.log("User data being sent:", userData);
});