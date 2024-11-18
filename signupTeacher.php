<?php
require_once 'connectiondb.php'; // Include database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and sanitize inputs
    $firstName = htmlspecialchars($_POST['first_name'] ?? null);
    $lastName = htmlspecialchars($_POST['last_name'] ?? null);
    $age = intval($_POST['age'] ?? null);
    $gender = $_POST['gender'] ?? null;
    $email = filter_var($_POST['email'] ?? null, FILTER_VALIDATE_EMAIL);
    $password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT); // Encrypt password
    $phone = htmlspecialchars($_POST['phone'] ?? null); // Optional
    $city = htmlspecialchars($_POST['city'] ?? null); // Optional
    $bio = htmlspecialchars($_POST['bio'] ?? null); // Optional

    // Check required fields
    if (!$firstName || !$lastName || !$age || !$gender || !$email || !$password) {
        echo "<script>alert('Required fields are missing.');</script>";
        return;
    }

    // Handle the uploaded photo
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoTmpPath = $_FILES['photo']['tmp_name'];
        $photoName = $_FILES['photo']['name'];
        $uploadDir = 'uploads/';
        $photoPath = $uploadDir . uniqid() . '-' . basename($photoName);

        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the file to the uploads directory
        if (!move_uploaded_file($photoTmpPath, $photoPath)) {
            echo "<script>alert('Photo upload failed.');</script>";
            return;
        }
    }

    // Insert teacher data into the database
    $query = "INSERT INTO teachers (first_name, last_name, age, gender, email, password, phone, city, bio, profile_photo) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Database prepare error: " . $conn->error . "');</script>";
        return;
    }

    $stmt->bind_param("ssisssssss", $firstName, $lastName, $age, $gender, $email, $password, $phone, $city, $bio, $photoPath);

    if ($stmt->execute()) {
        echo "<script>alert('Signup successful!');</script>";
        echo "<script>window.location.href = 'loginTeacher.html';</script>";
    } else {
        echo "<script>alert('Database execute error: " . $stmt->error . "');</script>";
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
    <title>Sign Up Form</title>
    <link rel="stylesheet" href="stylesSignUpT.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo-text-container">
                <img src="logo (4).png" alt="Langbloom Logo" class="logo">
                <span class="website-name">Langbloom</span>
            </div>
            <nav>
                <ul>
                    <li><a href="homePage.html">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#footer">Contact us</a></li>
                </ul>
            </nav>
            <a href="#" class="btn">Get Started</a>
        </div>
    </header>

    <!-- Signup Section -->
    <div class="section-singup">
        <div class="signUp-container">
            <img src="img/app-icon-person.png" alt="Person Icon" class="person-icon2">
            <h2>Sign Up</h2>
            <p class="switch-signup">Not a teacher? <a href="signupStudent.html">Sign up as a student</a></p>

            <form action="" method="post" enctype="multipart/form-data" class="signup-form-T">
                <div class="form-row">
                    <input type="text" name="first_name" placeholder="First name" required>
                    <input type="text" name="last_name" placeholder="Last name" required>
                </div>
                <div class="form-row">
                    <input type="number" name="age" placeholder="Age" required>
                    <select name="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="form-row">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-row">
                    <input type="text" name="phone" placeholder="Phone">
                    <input type="text" name="city" placeholder="City">
                </div>
                <div class="form-row">
                    <textarea name="bio" placeholder="Short Bio"></textarea>
                </div>
                <div class="form-row">
                    <label for="photo-upload" class="photo-upload">
                        <input type="file" name="photo" id="photo-upload" accept="image/*">
                        <span>Photo (Optional)</span>
                        <div class="upload-icon">&#x2193;</div>
                    </label>
                </div>

                <button type="submit">Continue</button>
                <p class="login-link">Already have an account? <a href="loginTeacher.html">Login</a></p>
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
                    <li><a href="homePage.html">Home</a></li>
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
