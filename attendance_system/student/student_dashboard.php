<?php
// student_dashboard.php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Include database connection
require_once '../includes/database.php';

// Fetch student details from database
$student_id = $_SESSION['student_id'];
$query = "SELECT * FROM student WHERE StudentID = ?";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
    } else {
        // If student not found, redirect to login
        session_destroy();
        header("Location: student_login.php");
        exit();
    }
    $stmt->close();
} else {
    // Handle prepare error
    die("Database error: " . $conn->error);
}

// Fetch attendance data
$attendance_query = "SELECT 
    COUNT(*) as total_classes,
    SUM(Status = 'Present') as present_count 
    FROM attendance 
    WHERE StudentID = ?";
$attendance_stmt = $conn->prepare($attendance_query);

if ($attendance_stmt) {
    $attendance_stmt->bind_param("i", $student_id);
    $attendance_stmt->execute();
    $attendance_result = $attendance_stmt->get_result();
    
    if ($attendance_result) {
        $attendance_data = $attendance_result->fetch_assoc();
        
        // Calculate attendance percentage
        $total_classes = $attendance_data['total_classes'] ?: 1; // Avoid division by zero
        $present_count = $attendance_data['present_count'] ?: 0;
        $attendance_percentage = round(($present_count / $total_classes) * 100, 2);
    } else {
        // Handle case where there's an error
        $total_classes = 0;
        $present_count = 0;
        $attendance_percentage = 0;
    }
    $attendance_stmt->close();
} else {
    // Handle prepare error
    $total_classes = 0;
    $present_count = 0;
    $attendance_percentage = 0;
}

// Fetch subject-wise attendance
$subjects_query = "SELECT s.SubjectName, 
    COUNT(a.AttendanceID) as total_classes,
    SUM(a.Status = 'Present') as present_count,
    ROUND((SUM(a.Status = 'Present') / COUNT(a.AttendanceID)) * 100, 2) as percentage
    FROM subject s
    LEFT JOIN attendance a ON s.SubjectID = a.SubjectID AND a.StudentID = ?
    WHERE s.Class = ? AND s.Semester = ?
    GROUP BY s.SubjectID";

$subjects_stmt = $conn->prepare($subjects_query);
$subject_attendance = [];

if ($subjects_stmt) {
    $subjects_stmt->bind_param("isi", $student_id, $student['Class'], $student['Semester']);
    $subjects_stmt->execute();
    $subjects_result = $subjects_stmt->get_result();
    
    while ($row = $subjects_result->fetch_assoc()) {
        $subject_attendance[] = $row;
    }
    $subjects_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Attendance System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f7fa;
        color: #333;
        line-height: 1.6;
    }

    .main-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .container {
        flex: 1;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    header {
        background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4b6cb7;
        font-size: 20px;
    }

  <body>
  <div class="main-wrapper">
    <div class="container">
      <header>
        <div>
          <h1>Attendance System</h1>
          <p>Student Dashboard</p>
        </div>
        <div class="user-info">
          <div class="user-avatar">
            <i class="fas fa-user"></i>
          </div>
          <div>
            <p><?php echo htmlspecialchars($student['Name']); ?></p>
            <p>Roll No: <?php echo htmlspecialchars($student['RollNo']); ?></p>
            <p>Class: <?php echo htmlspecialchars($student['Class']); ?>, Semester: <?php echo htmlspecialchars($student['Semester']); ?></p>
          </div>
        </div>
      </header>

      <div class="dashboard-grid">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Attendance Summary</div>
            <i class="fas fa-chart-pie"></i>
          </div>
          <div class="attendance-summary">
            <div class="summary-item present">
              <div class="summary-value"><?php echo $attendance_percentage; ?>%</div>
              <div class="summary-label">Overall Attendance</div>
            </div>
            <div class="summary-item absent">
              <div class="summary-value"><?php echo (100 - $attendance_percentage); ?>%</div>
              <div class="summary-label">Absent Rate</div>
            </div>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo $attendance_percentage; ?>%"></div>
          </div>
          <p>
            <?php
            if ($attendance_percentage >= 80) {
              echo "Your attendance is excellent! Keep it up.";
            } elseif ($attendance_percentage >= 70) {
              echo "Your attendance is satisfactory but could be improved.";
            } else {
              echo "Your attendance is low. Please attend classes regularly.";
            }
            ?>
          </p>
        </div>

        <div class="card">
          <div class="card-header">
            <div class="card-title">Subject-wise Attendance</div>
            <i class="fas fa-book"></i>
          </div>
          <ul class="subject-list">
            <?php foreach ($subject_attendance as $subject):
              $percentage = $subject['percentage'] ?: 0;
              $status_class = 'good';
              if ($percentage < 70) $status_class = 'danger';
              elseif ($percentage < 80) $status_class = 'warning';
            ?>
              <li class="subject-item">
                <span class="subject-name"><?php echo htmlspecialchars($subject['SubjectName']); ?></span>
                <span class="attendance-percentage <?php echo $status_class; ?>">
                  <?php echo $percentage; ?>%
                </span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

       <div class="card">
  <div class="card-header">
    <div class="card-title">Quick Actions</div>
    <i class="fas fa-bolt"></i>
  </div>
  <div class="actions">
    <a href="student_logout.php" class="action-btn">
      <i class="fas fa-sign-out-alt action-icon"></i>
      <span>Logout</span>
    </a>
  </div>
</div>
      </div>
    </div>

    <footer>
      <p>&copy; <?= date("Y") ?> Narmada College Attendance System</p>
    </footer>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const subjects = document.querySelectorAll('.subject-item');
      subjects.forEach(subject => {
        subject.addEventListener('mouseenter', () => subject.style.backgroundColor = '#f5f5f5');
        subject.addEventListener('mouseleave', () => subject.style.backgroundColor = 'transparent');
      });
    });
  </script>
</body>

    .card {
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 280px;
}

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: #4b6cb7;
    }

    .attendance-summary {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .summary-item {
        text-align: center;
        padding: 15px;
        border-radius: 8px;
    }

    .present {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .absent {
        background: #ffebee;
        color: #c62828;
    }

    .summary-value {
        font-size: 24px;
        font-weight: bold;
        margin: 5px 0;
    }

    .summary-label {
        font-size: 14px;
    }

    .progress-bar {
        height: 10px;
        background: #eee;
        border-radius: 5px;
        overflow: hidden;
        margin: 15px 0;
    }

    .progress-fill {
        height: 100%;
        background: #4b6cb7;
        border-radius: 5px;
    }

    .subject-list {
        list-style: none;
        padding-left: 0;
    }

    .subject-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .subject-name {
        font-weight: 500;
    }

    .attendance-percentage {
        font-weight: 600;
    }

    .good {
        color: #2e7d32;
    }

    .warning {
        color: #f57c00;
    }

    .danger {
        color: #c62828;
    }

    .actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-decoration: none;
        color: #333;
        transition: transform 0.3s;
    }

    .action-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .action-icon {
        font-size: 24px;
        margin-bottom: 10px;
        color: #4b6cb7;
    }

    footer {
        background-color: #1E88E5;
        color: white;
        text-align: center;
        padding: 15px 0;
        font-size: 0.9rem;
        border-top: 1px solid rgba(255,255,255,0.2);
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .attendance-summary {
            grid-template-columns: 1fr;
        }

        header {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }
    }
</style>
</head>
<body>
  <div class="main-wrapper">
    <div class="container">
      <header>
        <div>
          <h1>Attendance System</h1>
          <p>Student Dashboard</p>
        </div>
        <div class="user-info">
          <div class="user-avatar">
            <i class="fas fa-user"></i>
          </div>
          <div>
            <p><?php echo htmlspecialchars($student['Name']); ?></p>
            <p>Roll No: <?php echo htmlspecialchars($student['RollNo']); ?></p>
            <p>Class: <?php echo htmlspecialchars($student['Class']); ?>, Semester: <?php echo htmlspecialchars($student['Semester']); ?></p>
          </div>
        </div>
      </header>

      <div class="dashboard-grid">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Attendance Summary</div>
            <i class="fas fa-chart-pie"></i>
          </div>
          <div class="attendance-summary">
            <div class="summary-item present">
              <div class="summary-value"><?php echo $attendance_percentage; ?>%</div>
              <div class="summary-label">Overall Attendance</div>
            </div>
            <div class="summary-item absent">
              <div class="summary-value"><?php echo (100 - $attendance_percentage); ?>%</div>
              <div class="summary-label">Absent Rate</div>
            </div>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo $attendance_percentage; ?>%"></div>
          </div>
          <p>
            <?php
            if ($attendance_percentage >= 80) {
              echo "Your attendance is excellent! Keep it up.";
            } elseif ($attendance_percentage >= 70) {
              echo "Your attendance is satisfactory but could be improved.";
            } else {
              echo "Your attendance is low. Please attend classes regularly.";
            }
            ?>
          </p>
        </div>

        <div class="card">
          <div class="card-header">
            <div class="card-title">Subject-wise Attendance</div>
            <i class="fas fa-book"></i>
          </div>
          <ul class="subject-list">
            <?php foreach ($subject_attendance as $subject):
              $percentage = $subject['percentage'] ?: 0;
              $status_class = 'good';
              if ($percentage < 70) $status_class = 'danger';
              elseif ($percentage < 80) $status_class = 'warning';
            ?>
              <li class="subject-item">
                <span class="subject-name"><?php echo htmlspecialchars($subject['SubjectName']); ?></span>
                <span class="attendance-percentage <?php echo $status_class; ?>">
                  <?php echo $percentage; ?>%
                </span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

          <div class="card">
      <div class="card-header">
        <div class="card-title">Quick Actions</div>
        <i class="fas fa-bolt"></i>
      </div>
      <div class="actions">
        <a href="student_logout.php" class="action-btn">
          <i class="fas fa-sign-out-alt action-icon"></i>
          <span>Logout</span>
        </a>
      </div>
    </div>
          </div>
    </div>

    <footer>
      <p>&copy; <?= date("Y") ?> Narmada College Attendance System</p>
    </footer>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const subjects = document.querySelectorAll('.subject-item');
      subjects.forEach(subject => {
        subject.addEventListener('mouseenter', () => subject.style.backgroundColor = '#f5f5f5');
        subject.addEventListener('mouseleave', () => subject.style.backgroundColor = 'transparent');
      });
    });
  </script>
</body>
</html>