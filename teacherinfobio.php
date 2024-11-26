<?php

$conn = new mysqli("localhost", "root", "", "LangBloom");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$teacher_id = isset($_GET['teacher_id']) ? intval($_GET['teacher_id']) : 0;


if ($teacher_id === 0) {
    die("Invalid teacher ID.");
}


$sql = "SELECT first_name, last_name, profile_photo, bio FROM teachers WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $teacher = $result->fetch_assoc();
    $first_name = $teacher['first_name'];
    $last_name = $teacher['last_name'];
    $profile_photo = $teacher['profile_photo'] ?: 'default-profile.png'; // صورة افتراضية إذا لم تكن موجودة
    $bio = $teacher['bio'] ?: "No bio available.";
} else {
    die("<h2>Instructor not found.</h2>");
}


$sql = "SELECT AVG(rating) AS average_rating, COUNT(review_id) AS num_reviews 
        FROM reviews 
        WHERE course_id IN (SELECT course_id FROM courses WHERE teacher_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $rating_data = $result->fetch_assoc();
    $average_rating = round($rating_data['average_rating'], 1) ?? 0;
    $num_reviews = $rating_data['num_reviews'] ?? 0;
} else {
    $average_rating = 0;
    $num_reviews = 0;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "$first_name $last_name - English Language Instructor"; ?></title>
    <link rel="stylesheet" href="teacherinfobio.css">
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
                    <li><a href="Brows_courses_student.php">Courses</a></li>
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
    
    <div class="container">
        <div class="instructor-image">
            <img src="<?php echo $profile_photo; ?>" alt="<?php echo "$first_name $last_name"; ?>">
            <div class="rating-and-reviews">
                <h3>Rating & Reviews</h3>
                <p>Rated <?php echo $average_rating; ?> based on <?php echo $num_reviews; ?> reviews.</p>
                <p>Rating: <?php echo str_repeat('⭐', round($average_rating)); ?></p>
            </div>
        </div>
        <div class="instructor-profile">
            <h2><?php echo "$first_name $last_name"; ?></h2>
            <div class="bio">
                <p><?php echo $bio; ?></p>
            </div>
            <div class="proficiency">
                <h3>Price Per Hour</h3>
                <ul>
                    <li>100$/session</li>
                </ul>
            </div>
        </div>
    </div>

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
        <p class="copyright">&copy; Copyright 2024 all rights reserved</p>
    </footer>
</body>
</html>
