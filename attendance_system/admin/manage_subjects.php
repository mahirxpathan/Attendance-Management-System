<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Add new teacher
if (isset($_POST['add_teacher'])) {
    $name = $_POST['teacher_name'];
    $username = $_POST['teacher_username'];
    $class = $_POST['teacher_class'];
    $semester = $_POST['teacher_semester'];
   $default_password = "teacher123"; // stored directly

    $stmt = $conn->prepare("INSERT INTO Teacher (Name, Username, Password, Class, Semester) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $username, $default_password, $class, $semester);
    $stmt->execute();
    $stmt->close();
}

// Detect edit mode
$edit_mode = false;
$edit_subject = [];

if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit_id'];

    $stmt = $conn->prepare("SELECT * FROM Subject WHERE SubjectID = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_subject = $result->fetch_assoc();
    $stmt->close();
}

// Add new subject
if (isset($_POST['add_subject'])) {
    $subject_name = $_POST['subject_name'];
    $class = $_POST['class'];
    $semester = $_POST['semester'];
    $teacher_id = $_POST['teacher_id'];

    $stmt = $conn->prepare("INSERT INTO Subject (SubjectName, Class, Semester, TeacherID) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $subject_name, $class, $semester, $teacher_id);
    $stmt->execute();
    $stmt->close();
}

// Update existing subject
if (isset($_POST['update_subject'])) {
    $subject_id = $_POST['subject_id'];
    $subject_name = $_POST['subject_name'];
    $class = $_POST['class'];
    $semester = $_POST['semester'];
    $teacher_id = $_POST['teacher_id'];

    $stmt = $conn->prepare("UPDATE Subject SET SubjectName=?, Class=?, Semester=?, TeacherID=? WHERE SubjectID=?");
    $stmt->bind_param("ssssi", $subject_name, $class, $semester, $teacher_id, $subject_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_subjects.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Subjects & Teachers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #f5f5f5;
      font-family: 'Poppins', sans-serif;
    }

    .container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 20px;
    }

    .card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      padding: 20px;
      margin-bottom: 30px;
    }

    h2 {
      color: #1E88E5;
      font-weight: 600;
      margin-bottom: 20px;
    }

    label {
      font-weight: 500;
      margin-top: 10px;
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    button {
      background-color: #1E88E5;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      margin-top: 15px;
    }

    button:hover {
      background-color: #1565C0;
    }

    table {
      width: 100%;
      background: white;
      border-collapse: collapse;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #1E88E5;
      color: white;
    }

    tr:hover {
      background-color: #f0f8ff;
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
  <div class="container">
    <!-- Add New Teacher -->
    <div class="card">
      <h2>Add New Teacher</h2>
      <form method="POST">
        <label>Name:</label>
        <input type="text" name="teacher_name" required>

        <label>Username:</label>
        <input type="text" name="teacher_username" required>

        <label>Class:</label>
        <select name="teacher_class" required>
          <option value="">-- Select Class --</option>
          <option value="BCA">BCA</option>
          <option value="MCA">MCA</option>
          <option value="BSc">BSc</option>
          <option value="B.Com">B.Com</option>
          <option value="BBA">BBA</option>
          <option value="BA">BA</option>
        </select>

        <label>Semester:</label>
        <input type="number" name="teacher_semester" min="1" max="8" required>

        <button type="submit" name="add_teacher">Add Teacher</button>
      </form>
    </div>

    <!-- Add or Edit Subject -->
    <div class="card">
      <h2><?php echo $edit_mode ? "Edit Subject" : "Add New Subject"; ?></h2>
      <form method="POST">
        <?php if ($edit_mode): ?>
          <input type="hidden" name="subject_id" value="<?php echo $edit_subject['SubjectID']; ?>">
        <?php endif; ?>

        <label>Subject Name:</label>
        <input type="text" name="subject_name" required value="<?php echo $edit_mode ? $edit_subject['SubjectName'] : ''; ?>">

        <label>Class:</label>
        <select name="class" required>
          <option value="">-- Select Class --</option>
          <?php
          $courses = ['BCA', 'MCA', 'BSc', 'B.Com', 'BBA', 'BA'];
          foreach ($courses as $course) {
              $selected = ($edit_mode && $edit_subject['Class'] == $course) ? 'selected' : '';
              echo "<option value='$course' $selected>$course</option>";
          }
          ?>
        </select>

        <label>Semester:</label>
        <input type="number" name="semester" min="1" max="8" required value="<?php echo $edit_mode ? $edit_subject['Semester'] : ''; ?>">

        <label>Assign Teacher:</label>
        <select name="teacher_id" required>
          <option value="">-- Select Teacher --</option>
          <?php
          $teachers = $conn->query("SELECT TeacherID, Name FROM Teacher ORDER BY Name");
          while ($row = $teachers->fetch_assoc()) {
              $selected = ($edit_mode && $edit_subject['TeacherID'] == $row['TeacherID']) ? 'selected' : '';
              echo "<option value='{$row['TeacherID']}' $selected>{$row['Name']}</option>";
          }
          ?>
        </select>

        <button type="submit" name="<?php echo $edit_mode ? 'update_subject' : 'add_subject'; ?>">
          <?php echo $edit_mode ? 'Update Subject' : 'Add Subject'; ?>
        </button>
      </form>
    </div>

    <!-- Existing Subjects Table -->
    <div class="card">
      <h2>Existing Subjects</h2>
      <table>
        <tr>
          <th>Subject Name</th>
          <th>Class</th>
          <th>Semester</th>
          <th>Assigned Teacher</th>
          <th>Action</th>
        </tr>
        <?php
        $result = $conn->query("
          SELECT s.SubjectID, s.SubjectName, s.Class, s.Semester, t.Name AS TeacherName
          FROM Subject s
          LEFT JOIN Teacher t ON s.TeacherID = t.TeacherID
          ORDER BY s.Class, s.Semester
        ");
        while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['SubjectName']}</td>
                  <td>{$row['Class']}</td>
                  <td>{$row['Semester']}</td>
                  <td>{$row['TeacherName']}</td>
                  <td><a href='manage_subjects.php?edit_id={$row['SubjectID']}' style='color: #1E88E5;'>Edit</a></td>
                </tr>";
        }
        ?>
      </table>
    </div>
  </div>
  <div class="back-btn-wrapper">
  <a href="admin_dashboard.php" class="back-btn">🔙 Back to Dashboard</a>
</div><br><br>

  <footer>
        &copy; <?= date("Y") ?> Narmada College Attendance System
    </footer>
</body>
</html>