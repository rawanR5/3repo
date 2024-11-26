<?php
session_start();


$teacher_id = $_SESSION['teacher_id'] ?? null;

if ($teacher_id === null) {
    echo "<script>alert('You need to log in as a teacher first.'); window.location.href='loginTeacher.php';</script>";
    exit;
}

require_once 'connectiondb.php';

$query = "SELECT first_name, last_name, email, bio, age, gender, city, phone FROM teachers WHERE teacher_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($firstName, $lastName, $email, $bio, $age, $gender, $city, $phone);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $deleteQuery = "DELETE FROM teachers WHERE teacher_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $teacher_id);
        if ($deleteStmt->execute()) {
            session_destroy(); 
            echo "<script>alert('Account deleted successfully.'); window.location.href='homePage.php';</script>";
        } else {
            echo "<script>alert('Error deleting account.');</script>";
        }
        $deleteStmt->close();
    } else {
        $newFirstName = $_POST['firstName'];
        $newLastName = $_POST['lastName'];
        $newEmail = $_POST['email'];
        $newBio = $_POST['bio'];
        $newAge = $_POST['age'];
        $newGender = $_POST['gender'];
        $newCity = $_POST['city'];
        $newPhone = $_POST['phone'];

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Please enter a valid email address.');</script>";
        } elseif ($newAge <= 0) {
            echo "<script>alert('Age must be a positive number.');</script>";
        } elseif (!preg_match('/^\d{8,15}$/', $newPhone)) { 
            echo "<script>alert('Phone number must consist of 8 to 15 digits.');</script>";
        } else {

            $updateQuery = "UPDATE teachers SET first_name = ?, last_name = ?, email = ?, bio = ?, age = ?, gender = ?, city = ?, phone = ? WHERE teacher_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ssssisssi", $newFirstName, $newLastName, $newEmail, $newBio, $newAge, $newGender, $newCity, $newPhone, $teacher_id);
            if ($updateStmt->execute()) {
                echo "<script>alert('Account updated successfully');</script>";
            } else {
                echo "<script>alert('Error updating account');</script>";
            }
            $updateStmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Langbloom - Manage Account</title>
    <link rel="stylesheet" href="manageAcc.css">
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
                    <li><a href="homePageTeacher.php">Home</a></li>
                    <li><a href="view_course_asTeacher.php">Courses</a></li>
                    <li><a href="incomingSession-teacher.php">Sessions</a></li>
                    <li><a href="reviewsTeacher.php">Reviews & Ratings</a></li>
                    <li><a href="requestPage.php">Requests</a></li>
                    <li><a href="#footer">Contact us</a></li>
                </ul>
            </nav>
            <div class="user-icon">
                <img src="manageIcon1.png" alt="User Icon">
                <div class="dropdown">
                    <a href="#">Manage Profile</a>
                    <a href="logout.php">Log out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Profile Management Section -->
    <section class="profile-management">
        <h1>Profile Management</h1>
        <form method="POST" onsubmit="return validateForm()">
            <!-- First Name and Last Name -->
            <div class="input-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" placeholder="First Name" value="<?php echo htmlspecialchars($firstName); ?>" required>
            </div>
            <div class="input-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" placeholder="Last Name" value="<?php echo htmlspecialchars($lastName); ?>" required>
            </div>
            
            <!-- Age and Gender -->
            <div class="input-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" placeholder="Age" value="<?php echo htmlspecialchars($age); ?>" required>
            </div>
            <div class="input-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="male" <?php echo ($gender == 'male' ? 'selected' : ''); ?>>Male</option>
                    <option value="female" <?php echo ($gender == 'female' ? 'selected' : ''); ?>>Female</option>
                </select>
            </div>
        
            <!-- City -->
            <div class="input-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" placeholder="City" value="<?php echo htmlspecialchars($city); ?>" required>
            </div>
        
            <!-- Email -->
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
        
            <!-- Bio -->
            <div class="input-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" placeholder="Write your bio here..."><?php echo htmlspecialchars($bio); ?></textarea>
            </div>

            <!-- Phone Number -->
            <div class="input-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <button type="submit" class="save-btn">Save Changes</button>
                <button type="submit" name="delete" class="delete-btn">Delete Account</button>
            </div>
        </form>
    </section>

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
                    <li><a href="homePageTeacher.php">Home</a></li>
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

    <script>
        function validateForm() {
            const email = document.getElementById("email").value;
            const age = document.getElementById("age").value;
            const phone = document.getElementById("phone").value;

            if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            if (age <= 0) {
                alert("Age must be a positive number.");
                return false;
            }

            if (!/^\d{8,15}$/.test(phone)) {
                alert("Phone number must consist of 8 to 15 digits.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
