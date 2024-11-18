<?php


$host = "localhost";        
$username = "root";        
$password = "";             
$dbname = "langbloom";      

// Database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set the character set to UTF-8 (recommended for compatibility with different languages)
$conn->set_charset("utf8");

// Function to sanitize input data
function sanitizeInput($input) {
    global $conn;
    return $conn->real_escape_string(trim($input));
}

// Close the connection at the end of the script

register_shutdown_function(function () use ($conn) {
    if ($conn->ping()) {
        $conn->close();
    }
});
?>

