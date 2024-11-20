<?php
$message = ''; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['First_name'] ?? '';
    $lastName = $_POST['Last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $profilePhoto = $_FILES['photo-upload'] ?? null; 

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters long.';
    } else {
      
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  
        $profilePhotoPath = null;
        if ($profilePhoto && $profilePhoto['error'] == 0) {
            $targetDir = "uploads/"; 
            $targetFile = $targetDir . basename($profilePhoto['name']);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));


            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($profilePhoto['tmp_name'], $targetFile)) {
                    $profilePhotoPath = $targetFile; 
                } else {
                    $message = 'Error uploading the photo.';
                }
            } else {
                $message = 'Only JPG, JPEG, PNG & GIF files are allowed.';
            }
        }

        $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $dbname = "langbloom";

        $conn = new mysqli($servername, $username, $dbpassword, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, email, password, profile_photo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $profilePhotoPath);

        if ($stmt->execute()) {
            echo "<script>alert('Account created successfully! Redirecting to login...'); window.location.href = 'loginStudent.php';</script>";
            exit;
        } else {
            $message = 'Error: ' . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Form</title>
    <link rel="stylesheet" href="stylesSignUpT.css">
    <script type="module" src="firebase.js"></script>
    <script type="module" src="app.js"></script>
    <script type="module" src="scriptsSignUp.js"></script>
    <script src="scriptsSignUpS.js" defer></script>
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
                    <li><a href="homePage.php">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#footer">Contact us</a></li>
                </ul>
            </nav>
            <a href="#" class="btn">Get Started</a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="section-singup">
        <div class="signUp-container">
            <img src="img/app-icon-person.png" alt="Person Icon" class="person-icon2">

            <h2>Sign Up</h2>
            <p class="switch-signup">Not a Student? <a href="signupTeacher.php">Sign up as a teacher?</a></p>

            <!-- Form -->
            <form action="" method="POST" class="signup-form-T" enctype="multipart/form-data">
                <div class="form-row">
                    <input type="text" name="First_name" placeholder="First name" required>
                    <input type="text" name="Last_name" placeholder="Last name" required>
                </div>
                <div class="form-row">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-row">
                    <label for="photo-upload" class="photo-upload">
                        <input type="file" name="photo-upload" id="photo-upload" accept="image/*">
                        <span>Photo (Optional)</span>
                        <div class="upload-icon">&#x2193;</div>
                    </label>
                </div>
                <button type="submit">Continue</button>
                <p class="login-link">Already have an account? <a href="loginStudent.php">Login</a></p>
            </form>

            <!-- Display Message -->
            <?php if ($message): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
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
        <p class="copyright">&copy; Copyright 2024 all rights reserved</p>
    </footer>
</body>
</html>
