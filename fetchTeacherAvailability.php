<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
include 'connectiondb.php';

// Validate request
if (isset($_GET['teacher_id'])) {
    $teacher_id = intval($_GET['teacher_id']);
    $date = isset($_GET['date']) ? $_GET['date'] : null;

    // Fetch available dates if no specific date is provided
    if (!$date) {
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
    } else {
        // Fetch available time slots for the specific date
        $query = "SELECT start_time, end_time 
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
    }
} else {
    echo json_encode(["error" => "Invalid parameters"]);
}
?>

