<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Add new student
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $class = $_POST['class'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $password = "student"; // hardcoded password

    // Generate roll number prefix: e.g., bca1_
    $prefix = strtolower(str_replace('.', '', $class)) . $semester; // e.g., bca1, bcom3
    $roll_prefix = $prefix . '_'; // e.g., bca1_

    // Get the highest existing roll number for this class-semester combo
    $stmt = $conn->prepare("SELECT RollNo FROM student WHERE RollNo LIKE ? ORDER BY RollNo DESC LIMIT 1");
    $like_pattern = $roll_prefix . '%';
    $stmt->bind_param("s", $like_pattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
    $last_roll_str = substr($row['RollNo'], strlen($roll_prefix));
    $last_roll = is_numeric($last_roll_str) ? intval($last_roll_str) : 0;
    $new_roll = $last_roll + 1;
} else {
    $new_roll = 1;
}


    // Final roll number: e.g., bca1_001
    $rollno = $roll_prefix . str_pad($new_roll, 3, '0', STR_PAD_LEFT);
    $stmt->close();

    // Insert new student
    $stmt = $conn->prepare("INSERT INTO student (RollNo, Name, Class, Year, Semester, Password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $rollno, $name, $class, $year, $semester, $password);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Student</title>
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
    }

    .card {
      background-color: var(--card-bg);
      border-radius: 12px;
      box-shadow: 0 4px 10px var(--card-shadow);
      padding: 20px;
      margin-bottom: 30px;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }

    h2 {
      color: var(--academic-blue);
      font-weight: 600;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-top: 10px;
      font-weight: 500;
      color: var(--text-muted);
    }

    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      margin-top: 20px;
      background-color: var(--academic-blue);
      color: white;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background-color: var(--academic-blue-dark);
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
      .btn-edit, .btn-delete {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    margin-right: 6px;
  }

  .btn-edit {
    background-color: #1E88E5;
    color: white;
  }

  .btn-edit:hover {
    background-color: #1565C0;
  }

  .btn-delete {
    background-color: #e53935;
    color: white;
  }

  .btn-delete:hover {
    background-color: #c62828;
  }
    .actions {
    display: flex;
    gap: 8px;
    justify-content: flex-start;
    align-items: center;
  }
    tr:hover {
    background-color: #f0f4fc;
  }
    .btn-edit, .btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    white-space: nowrap;
  }
    footer {
    background-color: #1E88E5;
    color: white;
    text-align: center;
    padding: 15px 0;
    font-size: 0.9rem;
    border-top: 1px solid rgba(255,255,255,0.2);
    }
    .back-btn-wrapper {
    text-align: center;
    margin-top: 30px;
  }

  .back-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #1E88E5;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
  }

  .back-btn:hover {
    background-color: #1565C0;
  }
  </style>
</head>
<body>

  <div class="card">
    <h2>Add New Student</h2>
    <form method="POST">
      <label>Name:</label>
      <input type="text" name="name" required>

      <label>Class:</label>
      <select name="class" required>
        <option value="">-- Select Class --</option>
        <option value="BCA">BCA</option>
        <option value="MCA">MCA</option>
        <option value="BSc">BSc</option>
        <option value="B.Com">B.Com</option>
        <option value="BBA">BBA</option>
        <option value="BA">BA</option>
      </select>

      <label>Academic Year:</label>
      <select name="year" required>
        <option value="">-- Select Year --</option>
        <?php
        for ($y = 2023; $y <= 2027; $y++) {
            echo "<option value='$y'>$y</option>";
        }
        ?>
      </select>

      <label>Semester:</label>
      <select name="semester" required>
        <option value="">-- Select Semester --</option>
        <?php
        for ($s = 1; $s <= 8; $s++) {
            echo "<option value='$s'>$s</option>";
        }
        ?>
      </select>

      <button type="submit" name="add_student">Add Student</button>
    </form>
  </div>
  <form method="GET" class="card" style="max-width: 700px; margin: 0 auto 20px;">
    <h2>Filter Students</h2>

    <label>Class:</label>
    <select name="filter_class">
      <option value="">-- All Classes --</option>
      <option value="BCA">BCA</option>
      <option value="MCA">MCA</option>
      <option value="BSc">BSc</option>
      <option value="B.Com">B.Com</option>
      <option value="BBA">BBA</option>
      <option value="BA">BA</option>
    </select>

    <label>Semester:</label>
    <select name="filter_semester">
      <option value="">-- All Semesters --</option>
      <?php for ($s = 1; $s <= 8; $s++) {
        echo "<option value='$s'>$s</option>";
      } ?>
    </select>

    <button type="submit">Apply Filter</button>
  </form>
    <div class="card">
    <h2>Existing Students</h2>
    <table>
      <tr>
        <th>Roll No</th>
        <th>Name</th>
        <th>Class</th>
        <th>Year</th>
        <th>Semester</th>
        <th>Actions</th> 
      </tr>
      <?php
      $filter_class = $_GET['filter_class'] ?? '';
      $filter_semester = $_GET['filter_semester'] ?? '';

      $query = "SELECT RollNo, Name, Class, Year, Semester FROM student WHERE 1";

      if (!empty($filter_class)) {
          $query .= " AND Class = '" . $conn->real_escape_string($filter_class) . "'";
      }
      if (!empty($filter_semester)) {
          $query .= " AND Semester = " . intval($filter_semester);
      }

      $query .= " ORDER BY Class, Semester";
      $result = $conn->query($query);
      while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['RollNo']}</td>
            <td>{$row['Name']}</td>
            <td>{$row['Class']}</td>
            <td>{$row['Year']}</td>
            <td>{$row['Semester']}</td>
            <td class='actions'>
              <a href='edit_student.php?roll={$row['RollNo']}' class='btn-edit'><i class='fas fa-edit'></i> Edit</a>
            <a href='delete_student.php?roll={$row['RollNo']}' class='btn-delete' onclick=\"return confirm('Are you sure you want to delete this student and all related attendance records?')\">
              <i class='fa-solid fa-trash'></i> Delete
            </a>
              </td>
          </tr>";
      }
      ?>
    </table>
  </div>
  <div class="back-btn-wrapper">
  <a href="admin_dashboard.php" class="back-btn">🔙 Back to Dashboard</a>
</div><br><br>

  <footer>
        &copy; <?= date("Y") ?> Narmada College Attendance System
    </footer>
</body>
</html>