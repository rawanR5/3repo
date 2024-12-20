<?php
// Include the database connection file
include('connectiondb.php');

// Start session to get student ID and course ID
session_start();
$student_id = $_SESSION['student_id']; // Replace with actual student session ID
$course_id = $_GET['course_id']; // Get the course ID from the URL

// Handle form submission for rating
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating']; // Rating value
    $feedback = $_POST['feedback']; // Feedback text

    // Insert rating into the database
    $sql = "INSERT INTO reviews (student_id, course_id, rating, feedback) 
            VALUES ('$student_id', '$course_id', '$rating', '$feedback')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Thank you for your feedback!'); window.location.href='studentCourses.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Course</title>
    <link rel="stylesheet" href="rawan.css">
    <style>
        .stars {
            display: flex;
            justify-content: center;
            cursor: pointer;
            font-size: 2rem;
            color: gray;
        }

        .star {
            margin: 0 5px;
            transition: color 0.3s ease;
        }

        .star.selected {
            color: gold;
        }

        .star:hover ~ .star {
            color: gray;
        }

        .star:hover {
            color: gold;
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
                    <a href="#">Manage Profile</a>
                    <a href="#">Log out</a>
                </div>
            </div>
        </div>
    </header>

    <div class="form-container">
        <form id="rateForm" method="POST">
            <fieldset>
                <legend>Rate Course</legend>
                <label for="rating">Rate</label>
                <div class="stars">
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                </div>
                <input type="hidden" id="rating" name="rating" value="0">
                <label for="feedback">Give Your Feedback</label>
                <textarea id="feedback" name="feedback" rows="4" required></textarea>
                <div class="buttons">
                    <button type="submit" class="sub-btn">Submit</button>
                    <button type="button" class="cncl-btn" onclick="formReset()">Cancel</button>
                </div>
            </fieldset>
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

    <script>
        // JavaScript to handle star rating selection
        const stars = document.querySelectorAll('.star');
        stars.forEach((star, index) => {
            star.addEventListener('click', () => {
                // Update rating value
                document.getElementById('rating').value = index + 1;

                // Update star appearance
                stars.forEach((s, i) => {
                    if (i <= index) {
                        s.classList.add('selected');
                    } else {
                        s.classList.remove('selected');
                    }
                });
            });
        });

        // Redirect to another page when cancel is clicked
        function formReset() {
            window.location.href = 'studentCourses.php';
        }
    </script>
</body>
</html>
