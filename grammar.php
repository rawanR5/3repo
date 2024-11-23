<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grammar English Course</title>
    <link rel="stylesheet" href="grammar.css">
    <script type="module" src="firebase.js"></script>
    <script type="module" src="app.js"></script>
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
                    <li><a href="browsCoursesStudent.php">Courses</a></li> 
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

    <!-- Course Details Section -->
    <div class="course-description">
        <?php
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'LangBloom');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check for course_id
        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
        if ($course_id == 0) {
            echo "<h2>Invalid course ID.</h2>";
            exit();
        }

        // Fetch course details
        $sql_course = "SELECT c.title, c.overview, c.num_sessions, c.image_path, t.first_name, t.last_name, t.teacher_id
                       FROM courses c
                       JOIN teachers t ON c.teacher_id = t.teacher_id
                       WHERE c.course_id = $course_id";
        $course_result = $conn->query($sql_course);

        if ($course_result && $course_result->num_rows > 0) {
            $course = $course_result->fetch_assoc();
            echo "<h2>" . htmlspecialchars($course['title']) . "</h2>";
            echo "<img src='" . htmlspecialchars($course['image_path']) . "' alt='Course Image' class='course-image'>";
            echo "<label>Description:</label>";
            echo "<p class='description'>" . htmlspecialchars($course['overview']) . "</p>";
            echo "<div class='details'>";
            echo "<div><label>Teacher:</label>";
            echo "<div class='details-text teacher-info'>";
            echo "<p>Professor " . htmlspecialchars($course['first_name']) . " " . htmlspecialchars($course['last_name']) . "</p>";
            echo "<a href='teacherinfobio.php?teacher_id=" . htmlspecialchars($course['teacher_id']) . "'><button class='view-info-button'>View Profile</button></a>";
            echo "</div></div>";
            echo "<div><label>Number of Sessions: </label><p class='details-text'>" . htmlspecialchars($course['num_sessions']) . "</p></div>";
            echo "</div>";
        } else {
            echo "<h2>Course not found.</h2>";
            exit();
        }

        // Fetch reviews and calculate average
        $sql_reviews = "SELECT r.rating, r.feedback, s.first_name, s.last_name
                        FROM reviews r
                        JOIN students s ON r.student_id = s.student_id
                        WHERE r.course_id = $course_id";
        $reviews_result = $conn->query($sql_reviews);

        $total_rating = 0; // To store total ratings
        $num_reviews = 0; // To store number of reviews

        if ($reviews_result && $reviews_result->num_rows > 0) {
            while ($review = $reviews_result->fetch_assoc()) {
                $total_rating += $review['rating'];
                $num_reviews++;
                echo "<div class='review'>";
                echo "<p><strong>" . htmlspecialchars($review['first_name']) . " " . htmlspecialchars($review['last_name']) . ":</strong></p>";
                echo "<p>Rating: " . str_repeat('⭐', $review['rating']) . " (" . $review['rating'] . "/5)</p>";
                echo "<p>\"".$review['feedback']."\"</p>";
                echo "</div>";
            }

            // Calculate the average
            $average_rating = $total_rating / $num_reviews;
            $average_rating = number_format($average_rating, 1);

            // Display the overall rating
            echo "<div class='rating'>";
            echo "<label>Overall Rating:</label>";
            echo "<p>$average_rating/5 (Based on $num_reviews reviews)</p>";
            echo "</div>";
        } else {
            echo "<p>No reviews yet.</p>";
        }

        // Process enroll button
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll'])) {
            $student_id = 1; // Change this to logged-in student's ID
            $course_status = 'In Progress';
            $sessions_completed = 0;

            // Insert enrollment into the database
            $sql_enroll = "INSERT INTO enrollments (course_id, student_id, course_status, sessions_completed) 
                           VALUES ($course_id, $student_id, '$course_status', $sessions_completed)";
            if ($conn->query($sql_enroll) === TRUE) {
                echo "<script>
                        alert('Successfully enrolled in the course.');
                        window.location.href = 'studentCourses.php';
                      </script>";
            } else {
                echo "Error: " . $conn->error;
            }
        }

        $conn->close();
        ?>

        <!-- Enroll button -->
        <form method="POST">
            <button class="enroll-button" type="submit" name="enroll">Enroll in Course</button>
        </form>
    </div>

    <!-- Footer -->
    <footer id="footer">
        <div class="container footer-container">
            <div class="footer-section footer-logo">
                <img src="whiteLogo.png" alt="Langbloom Logo" class="footer-logo-img">
                <h3>Langbloom.</h3>
                <p>Follow us on social media</p>
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
