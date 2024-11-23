<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'connectiondb.php';
session_start();

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: loginTeacher.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Handle POST requests for updating material
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? null;

    if ($action === 'update_material') {
        $session_id = intval($input['session_id']);
        $material = trim($input['material']);

        if (!empty($material)) {
            $update_material_query = "
                UPDATE sessions
                SET material = ?
                WHERE session_id = ? 
                AND EXISTS (
                    SELECT 1 
                    FROM courses c 
                    WHERE c.course_id = sessions.course_id AND c.teacher_id = ?
                )
            ";
            $stmt = $conn->prepare($update_material_query);
            $stmt->bind_param("sii", $material, $session_id, $teacher_id);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Material updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update material. Please try again.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Material link cannot be empty.']);
        }
        exit();
    }
}


// Fetch the previous sessions for the logged-in teacher
$query = "
    SELECT s.session_id, s.session_date, s.start_time, s.end_time, s.material,
           c.title AS course_title
    FROM sessions s
    JOIN courses c ON s.course_id = c.course_id
    WHERE c.teacher_id = ? AND s.session_status = 'Previous'
    ORDER BY s.session_date ASC, s.start_time ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
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
    <title>Previous Sessions - Teacher</title>
    <link rel="stylesheet" href="style-seession.css">
    <script src="previousSession-teacher.js" defer></script>
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
                    <li><a href="homePageTeacher.php">Home</a></li>
                    <li><a href="viewCourseAsTeacher.php">Courses</a></li>
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
                    <a href="logout.php">Log out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Sessions -->
    <div class="container-s">
        <div class="buttons">
            <button class="button-s" onclick="window.location.href='teacherSchedule.php'">My Schedule</button>
            <button class="button-s" onclick="window.location.href='incomingSession-teacher.php'">Incoming sessions</button>
            <button class="button-s active" onclick="window.location.href='previousSession-teacher.php'">Previous sessions</button>
        </div>

        <!-- Display sessions -->
        <?php foreach ($sessions as $session): ?>
        <div class="session-card">
            <div class="session-details">
                <p><strong>Course Name :</strong> <?= htmlspecialchars($session['course_title']) ?></p>
                <p><strong><?= date("l - j F", strtotime($session['session_date'])) ?></strong></p>
                <p><?= date("g:i A", strtotime($session['start_time'])) ?> - <?= date("g:i A", strtotime($session['end_time'])) ?></p>
            </div>
            <div class="buttons-right">
                <?php if (!empty($session['material'])): ?>
                    <input 
                        type="text" 
                        value="<?= htmlspecialchars($session['material']) ?>" 
                        class="material-input" 
                        style="width: 70%; padding: 10px; border-radius: 30px; border: 1px solid #ccc;" 
                    />
                <?php else: ?>
                    <input 
                        type="text" 
                        placeholder="Paste the material link here (e.g., https://example.com)" 
                        class="material-input" 
                        style="width: 70%; padding: 10px; border-radius: 30px; border: 1px solid #ccc;" 
                    />
                <?php endif; ?>
                <button 
                    class="upload-material-button" 
                    data-session-id="<?= $session['session_id'] ?>" 
                    style="background-color: #355a71; color: white; border-radius: 30px; padding: 10px; border: none; cursor: pointer;"
                >
                    <?= empty($session['material']) ? "Upload Material" : "Update Material" ?>
                </button>
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
                    <li><a href="homePageTeacher.php">Home</a></li>
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
