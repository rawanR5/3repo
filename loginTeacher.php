<?php
require_once 'connectiondb.php'; // Include the database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and sanitize inputs
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Check if email and password are provided
    if (!$email || !$password) {
        echo "<script>alert('Please enter both email and password.');</script>";
    } else {
        // Check if the email and password match in the database
        $query = "SELECT teacher_id, password FROM teachers WHERE email = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            echo "<script>alert('Database prepare error: " . $conn->error . "');</script>";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($teacher_id, $hashed_password);
                $stmt->fetch();

                // Verify password
                if (password_verify($password, $hashed_password)) {
                    // Store teacher ID in session
                    $_SESSION['teacher_id'] = $teacher_id;
                    $_SESSION['logged_in'] = true;

                    // Redirect to the teacher home page
                    echo "<script>alert('Login successful! Redirecting...');</script>";
                    echo "<script>window.location.href = 'homePageTeacher.php';</script>";
                    exit(); // Ensure no further code is executed
                } else {
                    echo "<script>alert('Incorrect password. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('No account found with that email.');</script>";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo-text-container">
                <img src="logo (4).png" alt="Langbloom Logo" class="logo">
                <span class="website-name">Langbloom</span> <!-- Website name -->
            </div>
            <nav>
                <ul>
                    <li><a href="homePage.php">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#footer">Contact us</a></li>
                </ul>
            </nav>
            <a href="#" class="btn">Get Started</a>
        </div>
    </header>

    <div class="login-container">
        <div class="login-box">
            <img src="img/app-icon-person.png" alt="Person Icon" class="person-icon">
            <h2>Teacher Log In</h2>
            <p class="switch"><a href="loginStudent.php">Not a teacher? Log in as a student</a></p>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required class="input-s">
                <input type="password" name="password" placeholder="Password" required class="input-s">
                <button type="submit">Continue</button>
                <p class="switch"><a href="signupTeacher.php">Create an account?</a></p>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer id="footer">
        <div class="container footer-container">
            <div class="footer-section footer-logo">
                <img src="whiteLogo.png" alt="Langbloom Logo" class="footer-logo-img">
                <h3>Langbloom.</h3>
                <p>Follow on social service</p>
                <div class="social-icons">
                    <a href="#"><img src="facbook.png" alt="Facebook"></a>
                    <a href="#"><img src="instagram.png" alt="Instagram"></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Links</h3>
                <ul>
                    <li><a href="homePage.php">Home</a></li>
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
        <p class="copyright">© Copyright 2024 all rights reserved</p>
    </footer>
</body>
</html>
