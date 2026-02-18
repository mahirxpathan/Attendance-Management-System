<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['roll'])) {
    $roll = $_GET['roll'];

    // Fetch current name
    $stmt = $conn->prepare("SELECT Name FROM student WHERE RollNo = ?");
    $stmt->bind_param("s", $roll);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}

if (isset($_POST['update_name'])) {
    $new_name = $_POST['new_name'];
    $roll = $_POST['roll'];

    $stmt = $conn->prepare("UPDATE student SET Name = ? WHERE RollNo = ?");
    $stmt->bind_param("ss", $new_name, $roll);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Student Name</title>
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
      padding: 40px;
    }

    .card {
      background-color: var(--card-bg);
      border-radius: 12px;
      box-shadow: 0 4px 10px var(--card-shadow);
      padding: 20px;
      max-width: 600px;
      margin: 0 auto;
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

    input[type="text"] {
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
  </style>
</head>
<body>

  <div class="card">
    <h2>Edit Name for Roll No: <?php echo htmlspecialchars($roll); ?></h2>
    <form method="POST">
      <input type="hidden" name="roll" value="<?php echo htmlspecialchars($roll); ?>">
      <label>New Name:</label>
      <input type="text" name="new_name" value="<?php echo htmlspecialchars($student['Name']); ?>" required>
      <button type="submit" name="update_name" onclick="return confirm('Are you sure you want to update the student\'s name?')">Update Name</button>
    </form>
  </div>

</body>
</html>