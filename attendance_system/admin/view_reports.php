<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Today's date
$today = date('Y-m-d');

// Count attendance by status for today
$summary = $conn->query("
  SELECT Status, COUNT(*) as count 
  FROM attendance 
  WHERE Date = '$today' 
  GROUP BY Status
");

// Class-wise student count
$classwise = $conn->query("
  SELECT Class, Semester, COUNT(*) as total 
  FROM student 
  GROUP BY Class, Semester
");

// Total attendance records
$total_attendance = $conn->query("
  SELECT COUNT(*) as total 
  FROM attendance
")->fetch_assoc()['total'];

// Teachers who marked attendance today
$active_teachers = $conn->query("
  SELECT DISTINCT t.Name 
  FROM attendance a 
  JOIN Teacher t ON a.TeacherID = t.TeacherID 
  WHERE a.Date = '$today'
");

// Students with low attendance (<75%)
$low_attendance = $conn->query("
  SELECT s.RollNo, s.Name, 
    COUNT(a.AttendanceID) AS total_days,
    SUM(a.Status = 'Present') AS present_days,
    ROUND((SUM(a.Status = 'Present') / COUNT(a.AttendanceID)) * 100, 2) AS percentage
  FROM attendance a
  JOIN student s ON a.StudentID = s.StudentID
  GROUP BY s.StudentID
  HAVING percentage < 75
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Attendance Reports</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    background-color: #f5f5f5;
  }

  .main-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  .container-fluid {
    flex: 1;
    padding: 30px;
  }

  h2, h4, h5 {
    color: #1E88E5;
    font-weight: 600;
  }

  /* ✅ Stat Grid Layout */
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
    justify-content: center;
  }

  .stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
  }

  .stat-number {
    font-size: 2.2em;
    font-weight: bold;
    color: #1E88E5;
  }

  /* ✅ Report Grid Layout */
  .report-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    margin-top: 40px;
    margin-bottom: 40px;
  }

  @media (max-width: 768px) {
    .report-grid {
      grid-template-columns: 1fr;
    }
  }

  .card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    align-items: stretch;
    min-height: 250px;
  }

  .card h5 {
    margin-bottom: 15px;
    font-size: 1.1rem;
    color: #1E88E5;
  }

  /* ✅ Metric Display (e.g. Total Attendance) */
  .attendance-summary {
    text-align: center;
    padding: 20px 0;
  }

  .attendance-summary i {
    margin-bottom: 10px;
  }

  .attendance-count {
    font-size: 2.5rem;
    font-weight: bold;
    color: #1E88E5;
    margin-bottom: 5px;
  }

  .attendance-label {
    font-size: 1rem;
    color: #555;
  }

  /* ✅ Table Styling */
  table {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
    margin-top: 10px;
    font-size: 0.95rem;
    margin-bottom: 10px;
  }

  th, td {
    padding: 10px;
    border-bottom: 1px solid #eee;
  }

  th {
    background-color: #1E88E5;
    color: white;
  }

  /* ✅ Teacher List Styling */
  .card ul {
    padding-left: 20px;
    margin-top: 10px;
  }

  .card ul li {
    margin-bottom: 6px;
  }

  /* ✅ Empty State Styling */
  .empty-message {
    text-align: center;
    color: #999;
    font-style: italic;
    padding: 20px 0;
  }

 .back-btn {
  display: inline-block;
  background-color: #1E88E5;
  color: white;
  padding: 10px 20px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  transition: background-color 0.3s ease;
  text-align: center;
}
.back-btn-wrapper {
  display: flex;
  justify-content: center;
  margin-bottom: 30px;
}
  .back-btn:hover {
  background-color: #1565C0;
  transform: scale(1.05);
}

  /* ✅ Footer */
  footer {
    background-color: #1E88E5;
    color: white;
    text-align: center;
    padding: 15px 0;
    font-size: 0.9rem;
    border-top: 1px solid rgba(255,255,255,0.2);
  }

  /* ✅ Responsive tweaks for stat cards */
  @media (max-width: 480px) {
    .stat-number {
      font-size: 1.6em;
    }

    .stat-card {
      padding: 15px;
    }
  }
</style>
</head>
<body>
  <div class="main-wrapper">
    <div class="container-fluid">

      <h2>📈 Attendance Reports</h2>

      <!-- 📅 Today's Summary -->
      <h5 class="mb-3">📅 Today's Summary (<?php echo $today; ?>)</h5>
      <div class="stat-grid">
        <?php if ($summary->num_rows > 0) {
          while ($row = $summary->fetch_assoc()) {
            $icon = '';
            $color = '';
            if ($row['Status'] == 'Present') {
              $icon = 'fa-check-circle';
              $color = 'text-success';
            } elseif ($row['Status'] == 'Absent') {
              $icon = 'fa-times-circle';
              $color = 'text-danger';
            } elseif ($row['Status'] == 'Late') {
              $icon = 'fa-clock';
              $color = 'text-warning';
            }
        ?>
          <div class="stat-card">
            <i class="fas <?php echo $icon; ?> fa-2x mb-2 <?php echo $color; ?>"></i>
            <div class="stat-number"><?php echo $row['count']; ?></div>
            <div><?php echo $row['Status']; ?></div>
          </div>
        <?php
          }
        } else {
        ?>
          <div class="stat-card">
            <i class="fas fa-info-circle fa-2x mb-2 text-muted"></i>
            <div class="stat-number">0</div>
            <div>No attendance data</div>
          </div>
        <?php } ?>
      </div>

      <!-- 📊 Report Sections -->
      <div class="report-grid">

        <!-- 📚 Class-Wise Student Count -->
        <div class="card">
          <h5>📚 Class-Wise Student Count</h5>
          <table>
            <tr>
              <th>No</th>
              <th>Class</th>
              <th>Semester</th>
              <th>Total Students</th>
            </tr>
            <?php
            $i = 1;
            if ($classwise->num_rows > 0) {
              while ($row = $classwise->fetch_assoc()) {
            ?>
              <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($row['Class']); ?></td>
                <td><?php echo htmlspecialchars($row['Semester']); ?></td>
                <td><?php echo $row['total']; ?></td>
              </tr>
            <?php
              }
            } else {
              echo "<tr><td colspan='4' class='empty-message'>No student data available</td></tr>";
            }
            ?>
          </table>
        </div>

        <div class="card">
          <h5>📋 Total Attendance Records</h5>
          <div class="attendance-summary">
            <i class="fas fa-database fa-2x text-info mb-2"></i>
            <div class="attendance-count"><?php echo number_format($total_attendance); ?></div>
            <div class="attendance-label">entries recorded</div>
          </div>
        </div>

        <!-- 👨‍🏫 Teachers Who Marked Attendance -->
        <div class="card">
          <h5>👨‍🏫 Teachers Who Marked Attendance Today</h5>
          <ul>
            <?php
            if ($active_teachers->num_rows > 0) {
              while ($row = $active_teachers->fetch_assoc()) {
                echo "<li>✔️ " . htmlspecialchars($row['Name']) . "</li>";
              }
            } else {
              echo "<li class='empty-message'>No attendance marked today</li>";
            }
            ?>
          </ul>
        </div>

        <!-- ⚠️ Students with Low Attendance -->
        <div class="card">
          <h5>⚠️ Students with Low Attendance (&lt;75%)</h5>
          <table>
            <tr>
              <th>No</th>
              <th>Roll No</th>
              <th>Name</th>
              <th>Attendance %</th>
            </tr>
            <?php
            $j = 1;
            if ($low_attendance->num_rows > 0) {
              while ($row = $low_attendance->fetch_assoc()) {
            ?>
              <tr>
                <td><?php echo $j++; ?></td>
                <td><?php echo htmlspecialchars($row['RollNo']); ?></td>
                <td><?php echo htmlspecialchars($row['Name']); ?></td>
                <td><?php echo $row['percentage']; ?>%</td>
              </tr>
            <?php
              }
            } else {
              echo "<tr><td colspan='4' class='empty-message'>No low attendance records found</td></tr>";
            }
            ?>
          </table>
        </div>

      </div>

      <!-- 🔙 Back Button -->
    <div class="back-btn-wrapper">
      <a href="admin_dashboard.php" class="back-btn">🔙 Back to Dashboard</a>
    </div>

    </div>

    <!-- 📎 Footer -->
    <footer>
      &copy; <?= date("Y") ?> Narmada College Attendance System
    </footer>
  </div>
</body>
</html>