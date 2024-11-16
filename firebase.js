import { initializeApp } from "https://www.gstatic.com/firebasejs/10.5.0/firebase-app.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/10.5.0/firebase-firestore.js";


const firebaseConfig = {
    apiKey: "AIzaSyCS0xY0yZ3PNjFCtDCwBqL-fXYrHR0Vqng",
    authDomain: "langbloom-1d4f3.firebaseapp.com",
    projectId: "langbloom-1d4f3",
    storageBucket: "langbloom-1d4f3.firebasestorage.app",
    messagingSenderId: "595045032611",
    appId: "1:595045032611:web:0a68f8c04e1a40c666d516",
    measurementId: "G-CX1J9EZDWB"
  };
  

const app = initializeApp(firebaseConfig);
export const db = getFirestore(app);
