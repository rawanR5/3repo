<?php

session_start();


if (!isset($_SESSION['student_id'])) {
    header("Location: loginStudent.php");
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Langbloom - e-Learning Platform</title>
    <link rel="stylesheet" href="fuvi.css">
    <script type="module" src="firebase.js"></script>
    <script type="module" src="app.js"></script>
</head>
<body>
   
    <header>
        <div class="container">
            <div class="logo-text-container">
                <img src="logo (4).png" alt="Langbloom Logo" class="logo">
                <span class="website-name">Langbloom</span>
            </div>
            <nav>
                <ul>
                    <li><a href="homePageStudent.php">Home</a></li>
                    <li><a href="Browse_courses_student.php">Courses</a></li> 
                    <li><a href="incomingSession-student.php">Sessions</a></li>
                    <li><a href="#footer">Contact us</a></li>
                </ul>
            </nav>
            <div class="user-icon">
                <img src="manageIcon1.png" alt="User Icon">
                <div class="dropdown">
                    <a href="manageAccStudent.php">Manage Profile</a>
                    <a href="?logout=true">Log out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-container">
            <div class="hero-text">
                <h1>Achieve Your Learning Goals, Effortlessly!</h1>
                <p>Join our platform to enjoy personalized learning experiences with expert guidance anytime, anywhere.</p>
                <a href="Browse_courses_student.php" class="btn">Browse Courses</a>
            </div>
            <div class="hero-image">
                <img src="studentPhoto.png" alt="Student Image">
            </div>
        </div>
    </section>

    <!-- Centered About Us Section -->
    <section class="about" id="about">
        <div class="about-container">
            <h2>About us</h2>
            <p>Our platform is designed to provide personalized, accessible, and high-quality education. We connect learners with expert mentors and offer innovative tools that support continuous learning.</p>
        </div>
        <div class="features">
            <div class="feature">
                <img src="personIcon1.png" alt="One on One Monitor">
                <h3>One on One Monitor</h3>
                <p>Get personalized, one-on-one learning sessions with expert mentors.</p>
            </div>
            <div class="feature">
                <img src="clockIcon1.png" alt="24/7 Mentor">
                <h3>24/7 Mentor</h3>
                <p>Access mentor support anytime, 24/7.</p>
            </div>
            <div class="feature">
                <img src="boardIcon1.png" alt="Whiteboard">
                <h3>Whiteboard</h3>
                <p>Collaborate in real-time with our interactive whiteboard.</p>
            </div>
            <div class="feature">
                <img src="moneyIcon1.png" alt="Affordable Price">
                <h3>Affordable Price</h3>
                <p>Enjoy premium education at an affordable price.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer">
        <div class="container footer-container">
            <div class="footer-section footer-logo">
                <img src="whiteLogo.png" alt="Langbloom Logo" class="footer-logo-img">
                <h3>Langbloom.</h3>
                <p>Follow on social media</p>
                <div class="social-icons">
                    <a href="#"><img src="facbook.png" alt="Facebook"></a>
                    <a href="#"><img src="instagram.png" alt="Instagram"></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Links</h3>
                <ul>
                    <li><a href="homePageStudent.php">Home</a></li>
                    <li><a href="#about">About us</a></li>
                    <li><a href="#footer">Contact us</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Support</h3>
                <p>Riyadh - Saudi Arabia</p>
                <p>+966555555555</p>
            </div>
        </div>
        <p class="copyright">&copy; Copyright 2024 all rights reserved</p>
    </footer>
</body>
</html>
