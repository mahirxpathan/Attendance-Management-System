<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all teachers
$result = $conn->query("SELECT TeacherID, Name, Username, Class, Semester FROM Teacher");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Teachers</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --academic-blue: #1E88E5;
      --academic-blue-dark: #1565C0;
      --card-bg: #fff;
      --card-shadow: rgba(0, 0, 0, 0.05);
      --text-muted: #555;
    }

    body {
    font-family: Arial, sans-serif;
    background-color: #f5f7fa;
    margin: 0;
    padding: 20px;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
 }

    .card {
      background-color: var(--card-bg);
      border-radius: 12px;
      box-shadow: 0 4px 10px var(--card-shadow);
      padding: 20px;
      margin-bottom: 30px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
    }

    h2 {
      color: var(--academic-blue);
      font-weight: 600;
      margin-bottom: 20px;
      text-align: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th {
      background-color: var(--academic-blue);
      color: white;
      padding: 12px;
      text-align: left;
    }

    td {
      padding: 12px;
      border-bottom: 1px solid #eee;
      }

    tr:hover {
      background-color: #f0f4fc;
    }

    footer {
       background-color: #1E88E5;
       color: white;
       text-align: center;
       padding: 15px 0;
       margin-top: auto;
       font-size: 0.9rem;
       border-top: 1px solid rgba(255,255,255,0.2);
    }
    .back-btn {
        display: inline-block;
        background-color: var(--academic-blue);
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: background-color 0.3s ease;
 }

.back-btn:hover {
  background-color: var(--academic-blue-dark);
 }
  </style>
</head>
<body>

    <div class="card">
    <h2>👨‍🏫 Registered Teachers</h2>
    <table>
        <tr>
        <th>No.</th> <!-- 👈 Serial number column -->
        <th>TeacherID</th>
        <th>Name</th>
        <th>Username</th>
        <th>Class</th>
        <th>Semester</th>
        </tr>
        <?php
        $count = 1; // 👈 Start counter
        while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $count++; ?></td> <!-- 👈 Increment and display -->
            <td style="text-align: center;"><?php echo $row['TeacherID']; ?></td>
            <td><?php echo htmlspecialchars($row['Name']); ?></td>
            <td><?php echo htmlspecialchars($row['Username']); ?></td>
            <td><?php echo htmlspecialchars($row['Class']); ?></td>
            <td><?php echo htmlspecialchars($row['Semester']); ?></td>
        </tr>
        <?php } ?>
    </table>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="admin_dashboard.php" class="back-btn">🔙 Back to Dashboard</a>
    </div>
  <footer>
    &copy; <?= date("Y") ?> Narmada College Attendance System
  </footer>

</body>
</html>