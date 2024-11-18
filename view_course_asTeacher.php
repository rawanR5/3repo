<?php
require_once 'connectiondb.php'; // Include database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch courses from the database
$query = "SELECT * FROM courses WHERE teacher_id = ?";
$stmt = $conn->prepare($query);
$teacher_id = 1; // Replace with the logged-in teacher's ID
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Courses as Teacher</title>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="view course As teacher.css">
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
                    <li><a href="homePageTeacher.html">Home</a></li>
                    <li><a href="view_course_as_teacher.php">Courses</a></li>
                    <li><a href="incomingSession-teacher.html">Sessions</a></li>
                    <li><a href="reviewsTeacher.html">Reviews & Ratings</a></li>
                    <li><a href="requestPage.html">Requests</a></li>
                    <li><a href="#footer">Contact us</a></li>
                </ul>
            </nav>
            <div class="user-icon">
                <img src="manageIcon1.png" alt="User Icon">
                <div class="dropdown">
                    <a href="manageAccTeacher.html">Manage Profile</a>
                    <a href="homePage.html">Log out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Section -->
    <div class="section-all">
        <div class="buttons-container2">
            <button class="btn" onclick="window.location.href='create_new_course.html'">+ Create Course</button>
        </div>
        <div class="card-list">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <a href="#" class="card-item">
                        <!-- Dynamically load course image -->
                        <img src="<?= htmlspecialchars($course['image_path']) ?>" 
                             alt="<?= htmlspecialchars($course['title']) ?> Image"
                             loading="lazy"
                             onerror="this.src='uploads/default-image.jpg';">
                        <span class="grammar"><?= htmlspecialchars($course['title']) ?></span>
                        <h3><?= htmlspecialchars($course['overview']) ?></h3>
                        <button class="btn-manage" 
                                onclick="window.location.href='manage_course.php?course_id=<?= $course['course_id'] ?>'">
                            Manage
                        </button>
                        <div class="arrow">
                            <i class="fas fa-arrow-right card-icon"></i>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No courses available. Click "Create Course" to add one!</p>
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
                    <li><a href="homePageTeacher.html">Home</a></li>
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
