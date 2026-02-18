<?php
session_start();
include '../includes/database.php';

if (isset($_SESSION['student_id'])) {
    header("Location: student_dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rollno = trim($_POST['rollno']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM Student WHERE RollNo = '$rollno'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $student = $result->fetch_assoc();

        if ($password == 'student' || password_verify($password, $student['Password'])) {
            $_SESSION['student_id'] = $student['StudentID'];
            $_SESSION['student_name'] = $student['Name'];
            $_SESSION['student_rollno'] = $student['RollNo'];

            header("Location: student_dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Student not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login - Attendance System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #bbdefb);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-card h3 {
            color: #1E88E5;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-control:focus {
            border-color: #1E88E5;
            box-shadow: 0 0 0 0.2rem rgba(30,136,229,0.25);
        }
        .btn-primary {
            background-color: #1E88E5;
            border: none;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #1565C0;
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 38px;
            cursor: pointer;
            color: #888;
        }
        .info-box {
            background: #fff3cd;
            padding: 10px;
            border-radius: 6px;
            font-size: 0.9em;
            color: #333;
        }
        .error-box {
            background: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.95em;
        }
        footer {
            text-align: center;
            padding: 15px 0;
            color: #555;
            font-size: 0.85em;
        }
         .home-btn-wrapper {
            text-align: center;
            margin-top: 20px;
        }

        .home-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #1E88E5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease;
        }

        .home-btn:hover {
            background-color: #1565C0;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="login-card">
            <h3><i class="fas fa-user-graduate"></i> Student Login</h3>

            <?php if (!empty($error)): ?>
                <div class="error-box"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">🎫 Roll Number</label>
                    <input type="text" name="rollno" class="form-control" placeholder="Enter your roll number" required>
                </div>
                <div class="mb-3 position-relative">
                    <label class="form-label">🔑 Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" value="student" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
<!-- 
            <div class="info-box mt-4">
                <strong>ℹ️ Default Login:</strong><br>
                Roll Number: <strong>BCA001</strong><br>
                Password: <strong>student</strong>
            </div> -->

            <div class="text-center mt-3">
                <a href="../teacher/teacher_login.php">← Teacher Login</a> |
                <a href="../admin/admin_login.php">Admin Login →</a>
                <div class="home-btn-wrapper">
            <a href="../index.php" class="home-btn">🏠 Home</a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        &copy; <?= date("Y") ?> Narmada College Attendance System
    </footer>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.querySelector(".toggle-password");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }
    </script>
</body>
</html>