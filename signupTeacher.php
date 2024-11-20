<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Sign Up</title>
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
                    <li><a href="homePage.php">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#footer">Contact us</a></li>
                </ul>
            </nav>
            <a href="#" class="btn">Get Started</a>
        </div>
    </header>

    <!-- Sign Up Section -->
    <div class="section-singup">
        <div class="signUp-container">
            <img src="img/app-icon-person.png" alt="Person Icon" class="person-icon2">
            <h2>Sign Up</h2>
            <p class="switch-signup">Not a teacher? <a href="signupStudent.php">Sign up as a student</a></p>

            <!-- Sign Up Form -->
            <form action="" method="POST" enctype="multipart/form-data" class="signup-form-T" id="signupForm">
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
                    <input type="text" name="phone" placeholder="Phone (8-15 digits)" required>
                    <input type="text" name="city" placeholder="City" required>
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
                <p class="login-link">Already have an account? <a href="loginTeacher.php">Login</a></p>
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
        <p class="copyright">&copy; Copyright 2024 all rights reserved</p>
    </footer>

    <!-- PHP Code -->
    <?php
    require_once 'connectiondb.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName = htmlspecialchars($_POST['first_name'] ?? null);
        $lastName = htmlspecialchars($_POST['last_name'] ?? null);
        $age = intval($_POST['age'] ?? null);
        $gender = $_POST['gender'] ?? null;
        $email = filter_var($_POST['email'] ?? null, FILTER_VALIDATE_EMAIL);
        $passwordInput = $_POST['password'] ?? '';
        $phone = htmlspecialchars($_POST['phone'] ?? null);
        $city = htmlspecialchars($_POST['city'] ?? null);
        $bio = htmlspecialchars($_POST['bio'] ?? null);

        $errors = [];
        if (!$firstName || !$lastName || !$email || !$passwordInput || !$phone || !$city) {
            $errors[] = 'All required fields must be filled.';
        }
        if (!preg_match('/^\d{8,15}$/', $phone)) {
            $errors[] = 'Phone number must be between 8 and 15 digits.';
        }
        if ($age <= 0) {
            $errors[] = 'Age must be a positive number.';
        }
        if (strlen($passwordInput) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }

        if (!empty($errors)) {
            echo "<script>alert('" . implode("\\n", $errors) . "');</script>";
        } else {
            $password = password_hash($passwordInput, PASSWORD_BCRYPT);

            $photoPath = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photoTmpPath = $_FILES['photo']['tmp_name'];
                $photoName = uniqid() . '-' . basename($_FILES['photo']['name']);
                $uploadDir = 'uploads/';
                $photoPath = $uploadDir . $photoName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                move_uploaded_file($photoTmpPath, $photoPath);
            }

            $query = "INSERT INTO teachers (first_name, last_name, age, gender, email, password, phone, city, bio, profile_photo) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssisssssss", $firstName, $lastName, $age, $gender, $email, $password, $phone, $city, $bio, $photoPath);

            if ($stmt->execute()) {
                echo "<script>alert('Signup successful!');</script>";
                echo "<script>window.location.href = 'loginTeacher.php';</script>";
            } else {
                echo "<script>alert('Database error: " . $stmt->error . "');</script>";
            }

            $stmt->close();
            $conn->close();
        }
    }
    ?>
</body>
</html>
