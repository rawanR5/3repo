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

// Handle POST requests for cancel and upload material
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? null;

    if ($action === 'cancel_session') {
        $session_id = intval($input['session_id']);

        // Fetch session details
        $session_query = "SELECT session_date, start_time FROM sessions WHERE session_id = ?";
        $stmt = $conn->prepare($session_query);
        $stmt->bind_param("i", $session_id);
        $stmt->execute();
        $session = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$session) {
            echo json_encode(['status' => 'error', 'message' => 'Session not found.']);
            exit();
        }

        $session_start = strtotime($session['session_date'] . ' ' . $session['start_time']);
        $current_time = time();

        if (($session_start - $current_time) > 86400) { // More than 24 hours before session start
            $cancel_query = "
                UPDATE sessions
                SET session_status = 'Canceled'
                WHERE session_id = ? 
                AND EXISTS (
                    SELECT 1 
                    FROM courses c 
                    WHERE c.course_id = sessions.course_id AND c.teacher_id = ?
                )
            ";
            $stmt = $conn->prepare($cancel_query);
            $stmt->bind_param("ii", $session_id, $teacher_id);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Session canceled successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to cancel session. Please try again.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Cancellation is only allowed 24 hours before the session start time.']);
        }
        exit();
    } elseif ($action === 'upload_material') {
        $session_id = intval($input['session_id']);
        $material = trim($input['material']);

        if (!empty($material)) {
            $upload_material_query = "
                UPDATE sessions
                SET material = ?
                WHERE session_id = ? 
                AND EXISTS (
                    SELECT 1 
                    FROM courses c 
                    WHERE c.course_id = sessions.course_id AND c.teacher_id = ?
                )
            ";
            $stmt = $conn->prepare($upload_material_query);
            $stmt->bind_param("sii", $material, $session_id, $teacher_id);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Material uploaded successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload material. Please try again.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Material link cannot be empty.']);
        }
        exit();
    }
}

// Update sessions where the current time > session end time and status is 'Accepted' to 'Previous'
$update_query = "
    UPDATE sessions
    SET session_status = 'Previous'
    WHERE session_status = 'Accepted' 
    AND (session_date < CURDATE() 
         OR (session_date = CURDATE() AND end_time < CURTIME()))
";
$conn->query($update_query);

// Fetch the sessions for the logged-in teacher, excluding "Previous" and "Pending" sessions
$query = "
    SELECT s.session_id, s.session_date, s.start_time, s.end_time, s.session_status, s.material,
           c.title AS course_title
    FROM sessions s
    JOIN courses c ON s.course_id = c.course_id
    WHERE c.teacher_id = ? AND s.session_status NOT IN ('Previous', 'Pending')
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
    <title>Incoming Sessions - Teacher</title>
    <link rel="stylesheet" href="style-seession.css">
    <script src="incomingSession-teacher.js" defer></script>
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
            <button class="button-s active" onclick="window.location.href='incomingSession-teacher.php'">Incoming sessions</button>
            <button class="button-s" onclick="window.location.href='previousSession-teacher.php'">Previous sessions</button>
        </div>

        <!-- Display sessions -->
        <?php foreach ($sessions as $session): 
            $session_start = new DateTime($session['session_date'] . ' ' . $session['start_time']);
            $current_time = new DateTime('now');

            $interval = $current_time->diff($session_start);
            $hours_difference = ($interval->invert ? -1 : 1) * ($interval->days * 24 + $interval->h);

            $is_cancellable = $hours_difference > 24;
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
                    <input 
    type="text" 
    placeholder="Paste the material link here (e.g., https://example.com)" 
    class="material-input" 
    style="
        width: 70%;
        padding: 10px 15px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 30px;
        outline: none;
        transition: border-color 0.3s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    "
    onfocus="this.style.borderColor='#355a71'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.15)';"
    onblur="this.style.borderColor='#ccc'; this.style.boxShadow='0 2px 4px rgba(0, 0, 0, 0.1)';"
/>
                    <button 
                        class="upload-material-button" 
                        data-session-id="<?= $session['session_id'] ?>"
                        style="
                            background-color: #355a71;
                            color: #fff;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 30px;
                            font-size: 14px;
                            font-weight: bold;
                            cursor: pointer;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                            transition: all 0.3s ease-in-out;
                        "
                        onmouseover="this.style.backgroundColor='#0056b3'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 8px rgba(0, 0, 0, 0.15)';"
                        onmouseout="this.style.backgroundColor='#355a71'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)';"
                        onmousedown="this.style.backgroundColor='#003d80'; this.style.transform='scale(0.98)'; this.style.boxShadow='0 3px 4px rgba(0, 0, 0, 0.1)';"
                        onmouseup="this.style.backgroundColor='#0056b3'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 8px rgba(0, 0, 0, 0.15)';"
                    >Upload material</button>
                <?php elseif ($session['session_status'] === 'Rejected'): ?>
                    <button class="disabled-button" disabled>You rejected this session</button>
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
