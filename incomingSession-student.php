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

// Update sessions where the current time > session end time and status is 'Accepted' to 'Previous'
$update_query = "
    UPDATE sessions
    SET session_status = 'Previous', session_number = session_number + 1
    WHERE session_status = 'Accepted' 
    AND (session_date < CURDATE() 
         OR (session_date = CURDATE() AND end_time < CURTIME()))
";
$conn->query($update_query);

// Fetch the sessions for the logged-in student, excluding "Previous" sessions
$query = "
    SELECT s.session_id, s.session_date, s.start_time, s.end_time, s.session_status, 
           c.title AS course_title
    FROM sessions s
    JOIN enrollments e ON s.course_id = e.course_id
    JOIN courses c ON s.course_id = c.course_id
    WHERE e.student_id = ? AND s.session_status != 'Previous'
    ORDER BY s.session_date ASC, s.start_time ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$sessions = [];
while ($row = $result->fetch_assoc()) {
    $sessions[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incoming Session Student</title>
    <link rel="stylesheet" href="style-seession.css">
    <script src="incomingSession-student.js" defer></script>
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

    <!-- Sessions -->
    <div class="container-s">
        <div class="buttons">
            <button class="button-s" onclick="window.location.href='schedule_next_session_student.php'">Schedule next session</button>
            <button class="button-s active" onclick="window.location.href='incomingSession-student.php'">Incoming sessions</button>
            <button class="button-s" onclick="window.location.href='previousSession-student.php'">Previous sessions</button>
        </div>

        <!-- Display sessions -->
        <?php foreach ($sessions as $session): 
            $session_start = strtotime($session['session_date'] . ' ' . $session['start_time']);
            $time_difference = $session_start - time();
            $is_cancellable = $time_difference > 86400; // 24 hours
        ?>
        <div class="session-card">
            <div class="session-details">
                <p><strong>Course Name :</strong> <?= htmlspecialchars($session['course_title']) ?></p>
                <p><strong><?= date("l - j F", strtotime($session['session_date'])) ?></strong></p>
                <p><?= date("g:i A", strtotime($session['start_time'])) ?> - <?= date("g:i A", strtotime($session['end_time'])) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($session['session_status']) ?></p>
            </div>
            <div class="buttons-right">
                <?php if ($session['session_status'] === 'Accepted'): ?>
                    <button class="join-button">Join now</button>
                    <?php if ($is_cancellable): ?>
                        <button class="cancel-button" data-session-id="<?= $session['session_id'] ?>">Cancel session</button>
                    <?php else: ?>
                        <button class="disabled-button" disabled>Cancel session (Not allowed)</button>
                    <?php endif; ?>
                <?php elseif ($session['session_status'] === 'Pending'): ?>
                    <button class="disabled-button" disabled>Still waiting for the teacher's response</button>
                <?php elseif ($session['session_status'] === 'Rejected'): ?>
                    <button class="disabled-button" disabled>The teacher rejected the request</button>
                <?php elseif ($session['session_status'] === 'Canceled'): ?>
                    <button class="disabled-button" disabled>Join now</button>
                    <button class="disabled-button" disabled>Cancel session</button>
                <?php endif; ?>
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
                    <a href="#"><img src="facebook.png" alt="Facbook"></a>
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
