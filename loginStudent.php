<?php
require_once 'connectiondb.php'; // Include database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $email = filter_var($_POST['email'] ?? null, FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? null;

    // Check if required fields are filled
    if (!$email || !$password) {
        echo "<script>alert('Please enter both email and password.');</script>";
        return;
    }

    // Prepare SQL query to fetch user data from 'students' table
    $query = "SELECT student_id, password FROM students WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Database prepare error: " . $conn->error . "');</script>";
        return;
    }

    $stmt->bind_param("s", $email); // Bind the email parameter
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($student_id, $hashedPassword);  // Use student_id instead of id
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            // Login successful, store the student_id in the session
            $_SESSION['student_id'] = $student_id;

            // Redirect to the home page
            echo "<script>window.location.href = 'homePageStudent.php';</script>";
        } else {
            echo "<script>alert('Incorrect password.');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header 1 -->
    <header>
        <div class="container">
            <div class="logo-text-container">
                <img src="logo (4).png" alt="Langbloom Logo" class="logo">
                <span class="website-name">Langbloom</span>
            </div>
            <nav>
                <ul>
                    <li><a href="homePage.php">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#footer">Contact us</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <!--/ Header -->

    <div class="login-container">
        <div class="login-box">
            <img src="img/app-icon-person.png" alt="Person Icon" class="person-icon">
            <h2>Log In</h2>
            <p class="switch"> <a href="loginTeacher.php">Not a student? Log in as a teacher</a></p>
            <form action="" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Continue</button>
                <p class="switch"> <a href="signupStudent.php">Create an account?</a></p>
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
