<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$students_count = $conn->query("SELECT COUNT(*) as count FROM Student")->fetch_assoc()['count'];

$subjects_count = $conn->query("SELECT COUNT(*) as count FROM Subject")->fetch_assoc()['count'];
$today = date('Y-m-d');
$today_attendance = $conn->query("SELECT COUNT(*) as count FROM Attendance WHERE Date='$today'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Attendance System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        .stat-card, .menu-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover, .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2.2em;
            font-weight: bold;
            color: #1E88E5;
        }

        .menu-card {
            text-decoration: none;
            color: #333;
            border: 2px solid #e0e0e0;
        }

        .menu-card:hover {
            border-color: #1E88E5;
        }

        .menu-card h4 {
            margin-bottom: 10px;
            color: #1E88E5;
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
        <div class="container-fluid px-4 py-4">
            <h2 class="mb-3">🔧 Admin Dashboard</h2>

            <div class="bg-light p-3 rounded mb-4">
                <h4>Welcome, <?php echo $_SESSION['admin_username']; ?>! 👋</h4>
                <p class="mb-0">Administrator Control Panel</p>
            </div>

              <h5>📊 Quick Stats</h5>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fas fa-user-graduate fa-2x mb-2 text-primary"></i>
                        <div class="stat-number"><?php echo $students_count; ?></div>
                        <div>Total Students</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fas fa-book fa-2x mb-2 text-success"></i>
                        <div class="stat-number"><?php echo $subjects_count; ?></div>
                        <div>Subjects</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fas fa-check-circle fa-2x mb-2 text-info"></i>
                        <div class="stat-number"><?php echo $today_attendance; ?></div>
                        <div>Today's Attendance</div>
                    </div>
                </div>
            </div>
                <div class="col-md-4">
            <div class="stat-card">
                <i class="fas fa-chalkboard-teacher fa-2x mb-2 text-secondary"></i>
                <div class="stat-number">
                    <?php
                    $teacher_count = $conn->query("SELECT COUNT(*) as count FROM Teacher")->fetch_assoc()['count'];
                    echo $teacher_count;
                    ?>
                </div>
                <div>Total Teachers</div>
            </div>
        </div>

          <h5>⚡ Quick Actions</h5>
            <div class="row g-4">
            <div class="col-md-4">
                <a href="manage_students.php" class="menu-card d-block">
                <h4>👥 Manage Students</h4>
                <p>Add/View students</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="manage_subjects.php" class="menu-card d-block">
                <h4>📚 Manage Subjects</h4>
                <p>Assign subjects to classes</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="view_reports.php" class="menu-card d-block">
                <h4>📈 View Reports</h4>
                <p>Attendance summaries</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="view_teachers.php" class="menu-card d-block">
                    <h4>👨‍🏫 View Teachers</h4>
                    <p>List of all registered teachers</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="admin_logout.php" class="menu-card d-block" style="background: #ffebee;">
                <h4>🚪 Logout</h4>
                <p>Sign out from system</p>
                </a>
            </div>
            </div>
        </div>

        <footer>
            &copy; <?= date("Y") ?> Narmada College Attendance System
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>