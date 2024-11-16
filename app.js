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
            console.log(`${doc.id} => ${JSON.stringify(doc.data())}`);
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