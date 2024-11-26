<?php
// Include the database connection
include 'connectiondb.php';

// Start the session
session_start();

// Check if the user is logged in as a teacher
if (!isset($_SESSION['teacher_id'])) {
    header("Location: loginTeacher.php");
    exit();
}

// Function to get the starting Sunday date for the current week
function getCurrentWeekStart() {
    $currentDate = new DateTime();
    $currentDayOfWeek = $currentDate->format('w'); // 0 for Sunday
    $currentDate->modify("-$currentDayOfWeek days"); // Adjust to Sunday
    return $currentDate;
}

// Handle incoming POST requests for updating the schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $decodedInput = json_decode($rawInput, true);

    if (isset($decodedInput['data'])) {
        $submittedData = json_decode($decodedInput['data'], true);

        // Check if data is empty
        if (empty($submittedData)) {
            echo json_encode(["status" => "error", "message" => "No time slots selected."]);
            exit();
        }

        $teacher_id = $_SESSION['teacher_id'];
        $conn->begin_transaction();

        try {
            // Clear existing records for the teacher
            $stmtDelete = $conn->prepare("DELETE FROM teacher_availability WHERE teacher_id = ?");
            $stmtDelete->bind_param("i", $teacher_id);
            $stmtDelete->execute();

            // Insert new time slots
            foreach ($submittedData as $entry) {
                if (empty($entry['date']) || empty($entry['start_time']) || empty($entry['end_time'])) {
                    throw new Exception("Invalid time slot data.");
                }

                $date = $conn->real_escape_string($entry['date']);
                $start_time = $conn->real_escape_string($entry['start_time']);
                $end_time = $conn->real_escape_string($entry['end_time']);

                $stmtInsert = $conn->prepare(
                    "INSERT INTO teacher_availability (teacher_id, available_date, start_time, end_time, is_available) VALUES (?, ?, ?, ?, 1)"
                );
                $stmtInsert->bind_param("isss", $teacher_id, $date, $start_time, $end_time);
                $stmtInsert->execute();
            }

            $conn->commit();
            echo json_encode(["status" => "success", "message" => "Schedule updated successfully."]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request."]);
        exit();
    }
}

// Fetch existing availability for the teacher
$teacher_id = $_SESSION['teacher_id'];
$query = "SELECT available_date, start_time, end_time, is_available FROM teacher_availability WHERE teacher_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare availability data
$availability = [];
while ($row = $result->fetch_assoc()) {
    $availability[] = $row;
}

$stmt->close();

// Generate the dates for the current week
$sundayDate = getCurrentWeekStart();
$days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Schedule - My Schedule</title>
    <link rel="stylesheet" href="teacherSchedule.css">
    <script>
        const existingAvailability = <?php echo json_encode($availability); ?>;
    </script>
    <script src="teacherSchedule.js" defer></script>
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
                    <a href="logout.php">Log out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Time Slot Selection Section -->
    <form id="availability-form" method="POST">
        <input type="hidden" name="data" id="availability-data">
        <div class="container-s">
            <div class="buttons">
                <button type="button" class="button-s active">My Schedule</button>
                <button type="button" class="button-s" onclick="window.location.href='incomingSession-teacher.php'">Incoming sessions</button>
                <button type="button" class="button-s" onclick="window.location.href='previousSession-teacher.php'">Previous sessions</button>
            </div>

            <!-- Days of the Week -->
            <?php
foreach ($days as $index => $day) {
    $currentDate = clone $sundayDate;
    $currentDate->modify("+$index days");
    $formattedDate = $currentDate->format('Y-m-d');
    echo "
    <div class='day-container'>
        <div class='day-header'>
            <label for='date-$day'>$day:</label>
            <input type='date' id='date-$day' name='date-$day' class='date-picker' value='$formattedDate'>
        </div>
        <div class='time-slots'>";
    
    for ($hour = 8; $hour <= 15; $hour++) {
        $startTime = str_pad($hour, 2, "0", STR_PAD_LEFT) . ":00:00";
        $endTime = str_pad($hour + 1, 2, "0", STR_PAD_LEFT) . ":00:00";
        echo "<div class='time-slot' data-day='$day' data-start-time='$startTime' data-end-time='$endTime'>$hour:00 - " . ($hour + 1) . ":00</div>";
    }

    echo "
        </div>
    </div>";
}
?>
            <button class="update-button">Update</button>
        </div>
    </form>

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
                    <li><a href="#">Home</a></li>
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
