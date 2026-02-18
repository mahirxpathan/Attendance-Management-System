<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['roll'])) {
    $roll = filter_input(INPUT_GET, 'roll', FILTER_SANITIZE_STRING);

    // Step 1: Get StudentID from RollNo
    $stmt = $conn->prepare("SELECT StudentID FROM student WHERE RollNo = ?");
    $stmt->bind_param("s", $roll);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if ($student) {
        $student_id = $student['StudentID'];

        // Step 2: Delete attendance records
        $stmt = $conn->prepare("DELETE FROM attendance WHERE StudentID = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();

        // Step 3: Delete student record
        $stmt = $conn->prepare("DELETE FROM student WHERE RollNo = ?");
        $stmt->bind_param("s", $roll);
        $stmt->execute();
        $stmt->close();

        // Step 4: Redirect with success message
        header("Location: manage_students.php?deleted=1");
        exit();
    } else {
        // If RollNo not found
        header("Location: manage_students.php?error=notfound");
        exit();
    }
}
?>