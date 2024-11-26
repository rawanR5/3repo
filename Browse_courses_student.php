<?php
require_once 'connectiondb.php'; // Include database connection


// Fetch courses from the database
$query = "SELECT * FROM courses"; // Adjusted to fetch all courses
$stmt = $conn->prepare($query);
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
    <title>Browse Courses</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500&display=swap">
    <link rel="stylesheet" href="view course As teacher.css"> <!-- Ensure this CSS is correctly linked -->
    <style>
        /*shared Buttons */
        .buttons-container {
            text-align: center;
            margin: 2px 0;
            margin-top: 150px;
        }

        .btn {
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin: 0 10px;
            background-color: #ff4081;
            color: white;
            border-radius: 5px;
        }
        .grey-btn {width: 170px; background-color: #355a71;}
        .grey-btn1 {width: 170px; background-color: #ff4081;}
        .grey-btn2 {width: 170px; background-color: #ff4081;}

        .btn:hover {
            background-color: #d52c2c;
        }
    </style>
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
                <a href="homePage.php">Log out</a>
            </div>
        </div>
    </div>
</header>

 <!-- Button Section -->
<div class="buttons-container">
    <button class="btn grey-btn1" onclick="window.location.href='Browse_courses_student.php'">Browse Courses</button>
    <button class="btn grey-btn" onclick="window.location.href='studentCourses.html'">My Courses</button>
</div>

    <!-- Main Section -->
    <div class="section-all">
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
                                onclick="window.location.href='grammar.php?course_id=<?= $course['course_id'] ?>'">
                            More
                        </button>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No courses available.</p>
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
    <p copyright>&copy; Copyright 2024 all rights reserved</p>
</footer>

</body>
</html>
