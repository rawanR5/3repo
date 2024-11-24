<?php
include_once("connectiondb.php");
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect if the teacher is not logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: loginTeacher.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Handle POST requests to accept or reject a request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['request_id']) && isset($_POST['status'])) {
        $request_id = intval($_POST['request_id']);
        $status = $_POST['status'];

        // جلب availability_id و session_id للطلب
        $stmt = $conn->prepare("
            SELECT availability_id, session_id 
            FROM requests 
            WHERE request_id = ?
        ");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $availability_id = $row['availability_id'];
            $session_id = $row['session_id'];

            // التحقق من الجلسات المقبولة لنفس availability_id
            if ($status == 'Accepted') {
                $check_stmt = $conn->prepare("
                    SELECT request_id 
                    FROM requests 
                    WHERE availability_id = ? 
                      AND request_status = 'Accepted'
                      AND session_id IS NOT NULL
                ");
                $check_stmt->bind_param("i", $availability_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    exit("Error: A session linked to this availability has already been accepted.");
                }
            }

            // تحديث حالة الطلب
            $update_request_stmt = $conn->prepare("
                UPDATE requests 
                SET request_status = ? 
                WHERE request_id = ?
            ");
            $update_request_stmt->bind_param("si", $status, $request_id);
            $update_request_stmt->execute();

            // إذا كان هناك session_id مرتبط، يتم تحديث حالة الجلسة
            if ($session_id) {
                $update_session_stmt = $conn->prepare("
                    UPDATE sessions 
                    SET session_status = ? 
                    WHERE session_id = ?
                ");
                $update_session_stmt->bind_param("si", $status, $session_id);
                $update_session_stmt->execute();
                $update_session_stmt->close();
            }

            $update_request_stmt->close();
            $stmt->close();

            exit("Success");
        } else {
            exit("Error: Request not found.");
        }
    }
}


$conn->query("
    UPDATE requests r
    JOIN sessions s ON r.session_id = s.session_id
    SET r.request_status = 'Canceled', s.session_status = 'Canceled'
    WHERE r.request_status = 'Pending' 
    AND r.request_date <= DATE_SUB(NOW(), INTERVAL 1 DAY)
");


$stmt = $conn->prepare("
    SELECT 
        r.request_id, 
        s.first_name, 
        s.last_name, 
        r.request_status, 
        c.title AS course_title, 
        ta.start_time, 
        ta.end_time,
        r.request_date
    FROM 
        requests r
    JOIN 
        students s ON r.student_id = s.student_id
    JOIN 
        courses c ON r.course_id = c.course_id
    JOIN 
        teachers t ON c.teacher_id = t.teacher_id
    JOIN 
        teacher_availability ta ON r.availability_id = ta.availability_id
    WHERE 
        t.teacher_id = ? AND r.request_status = 'Pending'
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests</title>
    <link rel="stylesheet" href="requestPage.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    <li><a href="incomingSessionTeacher.php">Sessions</a></li>
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
    <div class="container2">
        <h1>Requests</h1>

        <?php if ($result->num_rows > 0) { ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="request-box">
                    <div class="request-content">
                        <div class="request-details">
                            <strong>Student Name:</strong> <?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?>
                        </div>
                        <div class="request-details">
                            <strong>Course Title:</strong> <?php echo htmlspecialchars($row['course_title']); ?>
                        </div>
                        <div class="request-details">
                            <strong>Start Time:</strong> <?php echo htmlspecialchars($row['start_time']); ?>
                        </div>
                        <div class="request-details">
                            <strong>End Time:</strong> <?php echo htmlspecialchars($row['end_time']); ?>
                        </div>
                        <div class="request-details">
                            <strong>Request Date:</strong> <?php echo htmlspecialchars($row['request_date']); ?>
                        </div>
                    </div>
                    <div class="status" id="status-<?php echo $row['request_id']; ?>">
                        Status: <?php echo htmlspecialchars($row['request_status']); ?>
                    </div>
                    <div class="buttons" id="buttons-<?php echo $row['request_id']; ?>">
                        <?php if ($row['request_status'] == 'Pending') { ?>
                            <button class="accept" data-request-id="<?php echo $row['request_id']; ?>" data-status="Accepted">Accept</button>
                            <button class="reject" data-request-id="<?php echo $row['request_id']; ?>" data-status="Rejected">Reject</button>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No requests found.</p>
        <?php } ?>
    </div>
<script>
$(document).ready(function () {
    $(".accept, .reject").click(function () {
        const requestId = $(this).data("request-id");
        const status = $(this).data("status");

        // Send AJAX request
        $.ajax({
            url: "requestPage.php",
            type: "POST",
            data: { 
                request_id: requestId, 
                status: status 
            },
            success: function (response) {
                if (response.trim() === "Success") {
                    // Update the status in the UI
                    $("#status-" + requestId).text("Status: " + status);
                    
                    // Hide the buttons after action
                    $("#buttons-" + requestId).hide();
                } else {
                    // Show the error message
                    alert(response);
                }
            },
            error: function () {
                alert("An error occurred while updating the request.");
            }
        });
    });
});
</script>
    <!-- Footer -->
    <footer id="footer">
        <div class="container footer-container">
            <div class="footer-section footer-logo">
                <img src="whiteLogo.png" alt="Langbloom Logo" class="footer-logo-img">
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
                    <li><a href="homePageTeacher.php">Home</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#footer">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Support</h3>
                <p>Riyadh - Saudi Arabia</p>
                <p>+966555555555</p>
            </div>
        </div>
        <p class="copyright">&copy; Copyright 2024 All Rights Reserved</p>
    </footer>
</body>
</html>
