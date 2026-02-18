<?php
// Prevent browser from caching the dashboard
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

session_start();
include '../includes/database.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$subjects = $conn->query("SELECT * FROM Subject WHERE TeacherID = $teacher_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard - Attendance System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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

        .container {
            flex: 1;
            padding-top: 30px;
            padding-bottom: 30px;
        }

        h2, h3, h4 {
            color: #1E88E5;
            font-weight: 600;
        }

        .welcome-box {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .subject-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .subject-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }

        .mark-btn {
            background-color: #1E88E5;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            margin-top: 10px;
        }

        .mark-btn:hover {
            background-color: #1565C0;
        }

        .logout-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }

        footer {
            background-color: #1E88E5;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="container">
            <h2 class="mb-4"><i class="fas fa-chalkboard-teacher"></i> Teacher Dashboard</h2>

            <div class="welcome-box">
                <h4>Welcome, <?php echo $_SESSION['teacher_name']; ?>! 👋</h4>
                <p>📁 Class: <?php echo $_SESSION['teacher_class']; ?> | Semester: <?php echo $_SESSION['teacher_semester']; ?></p>
            </div>

            <h3 class="mb-3">📚 Your Subjects</h3>
            <?php if ($subjects->num_rows > 0): ?>
                <div class="row g-4">
                    <?php while ($subject = $subjects->fetch_assoc()): ?>
                        <div class="col-md-6">
                            <div class="subject-card">
                                <h4><?php echo $subject['SubjectName']; ?></h4>
                                <p>Class: <?php echo $subject['Class']; ?> | Semester: <?php echo $subject['Semester']; ?></p>
                                <a href="mark_attendance.php?subject_id=<?php echo $subject['SubjectID']; ?>" class="mark-btn">
                                    ✅ Mark Attendance
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No subjects assigned to you.</p>
            <?php endif; ?>

            <div class="text-center mt-5">
                <a href="teacher_logout.php" class="logout-btn">🚪 Logout</a>
            </div>
        </div>

        <footer>
            &copy; <?= date("Y") ?> Narmada College Attendance System
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>