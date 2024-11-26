<?php
require_once 'connectiondb.php'; // Include database connection

// Start session to access the logged-in user 
session_start();

// Assuming the student is logged in and we store their id in session
$student_id = $_SESSION['student_id'] ?? null; // Make sure student_id is available

if ($student_id === null) {
    // If the user is not logged in, redirect to login page
    echo "<script>alert('You need to log in first.'); window.location.href='loginStudent.php';</script>";
    exit;
}

// Initialize variables for form data
$firstName = $lastName = $email = '';

// Fetch the current user data from the database
$query = "SELECT first_name, last_name, email FROM students WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($firstName, $lastName, $email);
    $stmt->fetch();
}
$stmt->close();

// Handle form submission for updating user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Handle account deletion
        $deleteQuery = "DELETE FROM students WHERE student_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $student_id);
        if ($deleteStmt->execute()) {
            session_destroy(); // Destroy the session
            echo "<script>alert('Account deleted successfully.'); window.location.href='homePage.php';</script>";
        } else {
            echo "<script>alert('Error deleting account. Please try again later.');</script>";
        }
        $deleteStmt->close();
    } else {
        // Handle account update
        if (isset($_POST['firstName'], $_POST['lastName'], $_POST['email'])) {
            $newFirstName = $_POST['firstName'];
            $newLastName = $_POST['lastName'];
            $newEmail = $_POST['email'];

            // Update student information in the database
            $updateQuery = "UPDATE students SET first_name = ?, last_name = ?, email = ? WHERE student_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("sssi", $newFirstName, $newLastName, $newEmail, $student_id);
            if ($updateStmt->execute()) {
                // Reload the page to reflect the changes
                echo "<script>alert('Account updated successfully'); window.location.href='manageAccStudent.php';</script>";
            } else {
                echo "<script>alert('Error updating account');</script>";
            }
            $updateStmt->close();
        } else {
            echo "<script>alert('Please fill in all fields');</script>";
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
                    <a href="logout.php">Log out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Profile Management Section -->
    <section class="profile-management">
        <h1>Profile Management</h1>

        <form method="POST">
            <div class="input-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" placeholder="First Name" value="<?php echo htmlspecialchars($firstName); ?>" required>
            </div>
            <div class="input-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" placeholder="Last Name" value="<?php echo htmlspecialchars($lastName); ?>" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
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
