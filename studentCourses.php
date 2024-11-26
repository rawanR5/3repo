<?php
// Include the database connection file
include('connectiondb.php');

// Start the session to get student ID
session_start();

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: loginStudent.php");  // Redirect to login page if not logged in
    exit();
}

$student_id = $_SESSION['student_id']; // Fetch student session ID

// Using prepared statement to prevent SQL Injection
$stmt = $conn->prepare("SELECT c.course_id, c.title, c.image_path, c.num_sessions, e.sessions_completed 
                        FROM courses c
                        JOIN enrollments e ON c.course_id = e.course_id
                        WHERE e.student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query was successful
if ($result === false) {
    // Output error message if query fails
    die("Error executing query: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student's Courses</title>
    <link rel="stylesheet" href="rawan.css">
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

    <!-- Button Section -->
    <div class="buttons-container">
        <button class="btn grey-btn" onclick="window.location.href='browsCoursesStudent.php'">Browse Courses</button>
        <button class="btn grey-btn1" onclick="window.location.href='studentCourses.php'">My Courses</button>
    </div>

    <!-- Course Cards Section -->
    <div class="course-session-container">
        <?php
        // Check if the result has rows
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <div class="mycourse-card">
                    <div class="course-session-image">
                        <!-- Display course image with the correct path -->
                        <img src="images/<?php echo $row['image_path']; ?>" class="courseImage" alt="Course Image">
                    </div>
                    <div class="session-text">
                        <p class="courseTitle"><?php echo htmlspecialchars($row['title']); ?></p>
                        <p>
                            <br>completed sessions: <br>
                            <?php echo $row['sessions_completed']; ?> / <?php echo $row['num_sessions']; ?><br>
                            course status: <br>
                            <?php 
                            if ($row['sessions_completed'] == $row['num_sessions']) {
                                echo "completed";
                            } else {
                                echo "in progress";
                            }
                            ?>
                        </p>
                    </div>
                    <button class="rate-course-btn" 
                            onclick="window.location.href='<?php echo ($row['sessions_completed'] == $row['num_sessions']) ? 'rateCourse.php?course_id=' . $row['course_id'] : 'schedule_next_session_student.php?course_id=' . $row['course_id']; ?>'">
                        <?php echo ($row['sessions_completed'] == $row['num_sessions']) ? 'Rate Course' : 'Continue'; ?>
                    </button>
                </div>
            <?php }
        } else {
            // If no courses are found
            echo "<p>No courses found for this student.</p>";
        }
        ?>
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
                    <li><a href="homePageStudent.html">Home</a></li>
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

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>
