<?php
require_once 'connectiondb.php'; //  database connection


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $title = $_POST['title'] ?? null;
    $overview = $_POST['overview'] ?? null;
    $sessions = $_POST['sessions'] ?? null;
    $teacher_id = 1; 

    
    if (!$title || !$overview || !$sessions) {
        echo "<script>alert('Required fields are missing.'); window.location.href='create_new_course.php';</script>";
        exit;
    }

    // Handle the uploaded image
    $imagePath = null;
    if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['course_image']['tmp_name'];
        $imageName = $_FILES['course_image']['name'];
        $uploadDir = 'uploads/';
        $imagePath = $uploadDir . uniqid() . '-' . basename($imageName);

        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the file to the uploads directory
        if (!move_uploaded_file($imageTmpPath, $imagePath)) {
            echo "<script>alert('Image upload failed.'); window.location.href='create_new_course.php';</script>";
            exit;
        }
    }

    // Insert course data into the database
    $query = "INSERT INTO courses (title, overview, num_sessions, image_path, teacher_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<script>alert('Database prepare error: " . $conn->error . "'); window.location.href='create_new_course.php';</script>";
        exit;
    }

    $stmt->bind_param("ssisi", $title, $overview, $sessions, $imagePath, $teacher_id);
    if ($stmt->execute()) {
        // Redirect to the view_course_asTeacher.php page on success
        header("Location: view_course_asTeacher.php");
        exit;
    } else {
        echo "<script>alert('Database execute error: " . $stmt->error . "'); window.location.href='create_new_course.php';</script>";
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a New Course</title>
    <link rel="stylesheet" href="create new course.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var fileInput = document.getElementById('course-image');
            var previewImg = document.getElementById('preview-img');

            fileInput.addEventListener('change', function() {
                var file = fileInput.files[0];
                var reader = new FileReader();

                reader.onloadend = function() {
                    previewImg.src = reader.result;
                    previewImg.style.display = 'block';
                }

                if (file) {
                    reader.readAsDataURL(file);
                } else {
                    previewImg.src = "";
                    previewImg.style.display = 'none';
                }
            });
        });
    </script>
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
                    <a href="manageAccTeacher.php">Manage Profile</a>
                    <a href="homePage.php">Log out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Course Creation Form -->
    <div class="container-course3">
        <form id="create-course-form" method="POST" enctype="multipart/form-data">
            <div class="form-header-course">
                <button type="reset" class="btn discard">Discard</button>
                <button type="submit" class="btn save2">Save Changes</button>
            </div>
            <label for="course-image"><span>Upload Course Image</span></label>
            <div class="image-upload">
                <input type="file" name="course_image" id="course-image" class="input-field image-field" accept="image/*">
                <div class="image-placeholder">
                    <img id="preview-img" src="" alt="Image Preview" style="display:none;">
                    <div class="upload-icon">
                        <img src="img/download507.png" alt="Upload photo">
                    </div>
                </div>
            </div>
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="input-field" placeholder="Enter title" required>
            <label for="overview">Overview</label>
            <textarea name="overview" id="overview" class="input-field textarea" placeholder="Enter overview" required></textarea>
            <label for="sessions">Number of Sessions</label>
            <input type="number" name="sessions" id="sessions" class="input-field" placeholder="Enter number of sessions" required>
        </form>
    </div>

    <!-- Footer -->
    <footer id="footer">
        <div class="container footer-container">
            <div class="footer-section footer-logo">
                <img src="whiteLogo.png" alt="Langbloom Logo" class="footer-logo-img">
                <h3>Langbloom.</h3>
                <p>Follow on social service</p>
                <div class="social-icons">
                    <a href="#"><img src="facebook.png" alt="Facebook"></a>
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
            <div the="footer-section">
                <h3>Support</h3>
                <p>Riyadh - Saudi Arabia</p>
                <p>+966555555555</p>
            </div>
        </div>
        <p class="copyright">&copy; Copyright 2024 all rights reserved</p>
    </footer>
</body>
</html>
