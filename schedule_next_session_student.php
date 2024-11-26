<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'connectiondb.php';
session_start();

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: loginStudent.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Retrieve enrolled courses for the logged-in student
$query = "SELECT c.course_id, c.title, c.teacher_id 
          FROM enrollments e 
          JOIN courses c ON e.course_id = c.course_id 
          WHERE e.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();

// Handle AJAX request for fetching availability
if (isset($_GET['teacher_id'])) {
    $teacher_id = intval($_GET['teacher_id']);
    $date = isset($_GET['date']) ? $_GET['date'] : null;

    if (!$date) {
        // Fetch available dates
        $query = "SELECT DISTINCT available_date AS date 
                  FROM teacher_availability 
                  WHERE teacher_id = ? AND is_available = 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
        exit();
    } else {
        // Fetch available time slots for the specific date
        $query = "SELECT availability_id, start_time, end_time 
                  FROM teacher_availability 
                  WHERE teacher_id = ? AND available_date = ? AND is_available = 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $teacher_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
        exit();
    }
}

// Handle AJAX request for saving session and request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Log the received data for debugging
    error_log("Received data: " . print_r($input, true));

    $course_id = intval($input['course_id']);
    $session_date = $input['session_date'];
    $start_time = $input['start_time'];
    $end_time = $input['end_time'];
    $availability_id = intval($input['availability_id']);

    // Check for missing or invalid data
    if (!$course_id || !$session_date || !$start_time || !$end_time || !$availability_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data. Please check and try again.']);
        exit();
    }

    try {
        // Insert into sessions table
        $session_query = "INSERT INTO sessions (course_id, session_date, start_time, end_time, session_status) 
                          VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($session_query);
        if (!$stmt) {
            throw new Exception("Failed to prepare session query: " . $conn->error);
        }
        $stmt->bind_param("isss", $course_id, $session_date, $start_time, $end_time);
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute session query: " . $stmt->error);
        }
        $session_id = $conn->insert_id; // Get the inserted session ID
    
        // Insert into requests table
        $request_query = "INSERT INTO requests (student_id, course_id, availability_id, session_id, request_status) 
                          VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($request_query);
        if (!$stmt) {
            throw new Exception("Failed to prepare request query: " . $conn->error);
        }
        $stmt->bind_param("iiii", $student_id, $course_id, $availability_id, $session_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute request query: " . $stmt->error);
        }
    
        // Update teacher_availability table
        $update_availability_query = "UPDATE teacher_availability SET is_available = 0 WHERE availability_id = ?";
        $stmt = $conn->prepare($update_availability_query);
        if (!$stmt) {
            throw new Exception("Failed to prepare availability update query: " . $conn->error);
        }
        $stmt->bind_param("i", $availability_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute availability update query: " . $stmt->error);
        }
    
        $conn->commit();
    
        echo json_encode(['status' => 'success', 'message' => 'Your request has been sent successfully to the teacher.']);
    } catch (Exception $e) {
        error_log("Transaction failed: " . $e->getMessage());
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again.']);
    }
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Next Session</title>
    <link rel="stylesheet" href="schedule_next_session.css">
    <script src="schedule_next_session_student.js" defer></script>
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

    <div class="container-s">
    <div class="buttons">
        <button class="button-s active" onclick="window.location.href='schedule_next_session_student.php'">Schedule next session</button>
        <button class="button-s" onclick="window.location.href='incomingSession-student.php'">Incoming sessions</button>
        <button class="button-s" onclick="window.location.href='previousSession-student.php'">Previous sessions</button>
    </div>

    <?php foreach ($courses as $course): ?>
    <div class="course-section">
        <div class="course-name">
            <label><strong>Course Name:</strong></label>
            <span><?= htmlspecialchars($course['title']) ?></span>
        </div>
        <div class="schedule">
            <label for="date-<?= $course['course_id'] ?>"><strong>Date:</strong></label>
            <select id="date-<?= $course['course_id'] ?>" data-teacher-id="<?= $course['teacher_id'] ?>" class="date-select">
                <option value="" disabled selected>Select Date</option>
            </select>

            <label for="time-<?= $course['course_id'] ?>"><strong>Time:</strong></label>
            <select id="time-<?= $course['course_id'] ?>" class="time-select" disabled>
                <option value="" disabled selected>Select Time</option>
            </select>

            <button class="send-request-button" data-course-id="<?= $course['course_id'] ?>">Send Request</button>
        </div>
    </div>
    <?php endforeach; ?>
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
</body>
</html>
