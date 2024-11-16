const firebaseConfig = {
  apiKey: "AIzaSyCS0xY0yZ3PNjFCtDCwBqL-fXYrHR0Vqng",
  authDomain: "langbloom-1d4f3.firebaseapp.com",
  projectId: "langbloom-1d4f3",
  storageBucket: "langbloom-1d4f3.firebasestorage.app",
  messagingSenderId: "595045032611",
  appId: "1:595045032611:web:0a68f8c04e1a40c666d516",
  measurementId: "G-CX1J9EZDWB"
};

const firebaseApp = firebase.initializeApp(firebaseConfig);
const db = firebaseApp.firestore();

