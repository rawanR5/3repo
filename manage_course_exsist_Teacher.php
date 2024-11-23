<?php
require_once 'connectiondb.php'; //  database connection

// Assume course_id is passed via GET request
$course_id = $_GET['course_id'] ?? null;

// Placeholder for course data
$course_data = null;

// Fetch course data from the database
if ($course_id) {
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $course_data = $result->fetch_assoc();
    } else {
        echo "No course found with ID: " . $course_id;
        exit;
    }
    $stmt->close();
} else {
    echo "No course ID provided.";
    exit;
}

// Handle form submission to update or delete the course details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Handle course deletion
        $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        if ($stmt->execute()) {
            echo "<script>alert('Course deleted successfully!'); window.location.href='view_course_asTeacher.php';</script>";
        } else {
            echo "<script>alert('Error deleting course: " . $stmt->error . "');</script>";
        }
        $stmt->close();
        $conn->close();
        exit;
    } else {
        // Update the course in the database
        $title = $_POST['title'] ?? $course_data['title'];
        $overview = $_POST['overview'] ?? $course_data['overview'];
        $sessions = $_POST['sessions'] ?? $course_data['num_sessions'];

        $stmt = $conn->prepare("UPDATE courses SET title = ?, overview = ?, num_sessions = ? WHERE course_id = ?");
        $stmt->bind_param("ssii", $title, $overview, $sessions, $course_id);
        if ($stmt->execute()) {
            echo "<script>alert('Course updated successfully!'); window.location.href='view_course_asTeacher.php?course_id={$course_id}';</script>";
        } else {
            echo "<script>alert('Error updating course: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Existing Course</title>
    <link rel="stylesheet" href="create new course.css">
</head>
<body class="body-create-course">
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
                    <li><a href="view_course_asTeacher.php">Courses</a></li>
                    <li><a href="incomingSession-teacher.html">Sessions</a></li>
                    <li><a href="reviewsTeacher.html">Reviews & Ratings</a></li>
                    <li><a href="requestPage.html">Requests</a></li>
                    <li><a href="#footer">Contact us</a></li>
                </ul>
            </nav>
            <div class="user-icon">
                <img src="manageIcon.png" alt="User Icon">
                <div class="dropdown">
                    <a href="manageAccTeacher.html">Manage Profile</a>
                    <a href="homePage.html">Log out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Course Management Form -->
    <div class="container-course3">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-header-course">
                <button class="btn discard" type="submit" name="delete" value="1" onclick="return confirm('Are you sure you want to delete this course?');">Delete Course</button>
                <button type="submit" class="btn save2">Save Changes</button>
            </div>
            <label for="course-image">Upload Course Image</label>
            <div class="image-upload">
                <input type="file" id="course-image" name="course_image" class="input-field image-field" accept="image/*">
                <div class="image-placeholder">
                    <img id="preview-img" src="<?= $course_data['image_path']; ?>" alt="Image Preview">
                </div>
            </div>
            <label for="title">Title</label>
            <input type="text" id="title" name="title" class="input-field" value="<?= htmlspecialchars($course_data['title']); ?>">
            <label for="overview">Overview</label>
            <textarea id="overview" name="overview" class="input-field"><?= htmlspecialchars($course_data['overview']); ?></textarea>
            <label for="sessions">Number of Sessions</label>
            <input type="number" id="sessions" name="sessions" class="input-field" value="<?= $course_data['num_sessions']; ?>">
        </form>
    </div>

    <!-- Footer -->
    <footer id="footer">
        <div class="container footer-container">
            <div class="footer-section footer-logo">
                <img src="footerLogo.png" alt="Langbloom Logo" class="footer-logo-img">
                <h3>Langbloom</h3>
                <p>Follow us on social media</p>
                <div class="social-icons">
                    <a href="#"><img src="facebook.png" alt="Facebook"></a>
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
    </footer>
</body>
</html>
