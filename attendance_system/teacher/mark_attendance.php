<?php
session_start();
include '../includes/database.php';

// Check if teacher is logged in
if(!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$subject_id = $_GET['subject_id'] ?? null;
$success = "";
$existing_attendance = [];

// Verify the subject belongs to this teacher
if($subject_id) {
    $check_subject = $conn->query("SELECT * FROM Subject WHERE SubjectID = $subject_id AND TeacherID = $teacher_id");
    if($check_subject->num_rows == 0) {
        die("Access denied! This subject is not assigned to you.");
    }
    $subject = $check_subject->fetch_assoc();
}

// Get students for this class and semester
$class = $_SESSION['teacher_class'];
$semester = $_SESSION['teacher_semester'];
$students = $conn->query("SELECT * FROM Student WHERE Class = '$class' AND Semester = $semester ORDER BY RollNo");

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && $subject_id) {
    $date = $_POST['date'];
    
    // Check if attendance data was submitted
    if(isset($_POST['attendance']) && is_array($_POST['attendance'])) {
        foreach($_POST['attendance'] as $student_id => $status) {
            // Check if attendance already exists for this student, subject, and date
            $check_query = "SELECT * FROM Attendance WHERE StudentID = $student_id AND SubjectID = $subject_id AND Date = '$date'";
            $existing = $conn->query($check_query);
            
            if($existing->num_rows > 0) {
                // UPDATE existing record
                $stmt = $conn->prepare("UPDATE Attendance SET Status = ?, TeacherID = ? WHERE StudentID = ? AND SubjectID = ? AND Date = ?");
                $stmt->bind_param("siiss", $status, $teacher_id, $student_id, $subject_id, $date);
            } else {
                // INSERT new record
                $stmt = $conn->prepare("INSERT INTO Attendance (TeacherID, StudentID, SubjectID, Date, Status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iiiss", $teacher_id, $student_id, $subject_id, $date, $status);
            }
            $stmt->execute();
        }
        
        $success = "Attendance updated successfully for " . $subject['SubjectName'] . "!";
    }
    
    // Pre-load existing attendance for the selected date
    $result = $conn->query("SELECT StudentID, Status FROM Attendance WHERE SubjectID = $subject_id AND Date = '$date'");
    while($row = $result->fetch_assoc()) {
        $existing_attendance[$row['StudentID']] = $row['Status'];
    }
}

// Get today's date for default value
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance - Attendance System</title>
   
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #2e7d32;
        }
        .subject-info {
            background: #FFF3E0;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #FF9800;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="date"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }
        button {
            background: #2196F3;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover {
            background: #0b7dda;
        }
        .btn-success {
            background: #4CAF50;
        }
        .btn-success:hover {
            background: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }
        .back-link {
            display: inline-block;
            background: #2196F3;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .back-link:hover {
            background: #0b7dda;
        }
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #c62828;
        }
        .date-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
            .date-container {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Mark Attendance</h1>
            <p>Easily track and manage student attendance</p>
        </div>
        
        <?php if(!empty($success)): ?>
            <div class="success-message">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if($subject_id): ?>
            <div class="subject-info">
                <h3>Subject: <?php echo $subject['SubjectName']; ?></h3>
                <p>Class: <?php echo $class; ?> - Semester <?php echo $semester; ?></p>
            </div>
            
            <div class="card">
                <h3>Select Date</h3>
                <form method="POST">
                    <div class="date-container">
                        <div class="form-group">
                            <label for="date">📅 Date:</label>
                            <input type="date" id="date" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : $today; ?>" required>
                        </div>
                        <button type="submit" name="load">
                            Load Attendance
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if(isset($_POST['load']) || ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date']))): ?>
            <div class="card">
                <h3>Student Attendance</h3>
                <form method="POST">
                    <input type="hidden" name="date" value="<?php echo $_POST['date']; ?>">
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Attendance Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Reset pointer to beginning of result set
                            $students->data_seek(0);
                            while($student = $students->fetch_assoc()): 
                                $current_status = $existing_attendance[$student['StudentID']] ?? 'Present';
                            ?>
                            <tr>
                                <td><?php echo $student['RollNo']; ?></td>
                                <td><?php echo $student['Name']; ?></td>
                                <td>
                                    <select name="attendance[<?php echo $student['StudentID']; ?>]" required>
                                        <option value="Present" <?php echo $current_status == 'Present' ? 'selected' : ''; ?>>Present</option>
                                        <option value="Absent" <?php echo $current_status == 'Absent' ? 'selected' : ''; ?>>Absent</option>
                                        <option value="Late" <?php echo $current_status == 'Late' ? 'selected' : ''; ?>>Late</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <button type="submit" class="btn-success">
                        💾 Save Attendance
                    </button>
                </form>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="error-message">
                No subject selected. Please select a subject from your dashboard.
            </div>
        <?php endif; ?>
        
        <a href="teacher_dashboard.php" class="back-link">
            ← Back to Dashboard
        </a>
    </div>

    <script>
        // Set default date to today if not already set
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('date');
            if(dateInput && !dateInput.value) {
                dateInput.value = '<?php echo $today; ?>';
            }
        });
    </script>
</body>
</html>