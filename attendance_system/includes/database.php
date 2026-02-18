<?php
$host = "localhost";
$user = "root";
$password = ""; // Default XAMPP password is empty
$dbname = "attendance_db";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // You can uncomment to test
?>