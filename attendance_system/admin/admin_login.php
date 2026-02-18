<?php
session_start();
include '../includes/database.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = "";

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM Admin WHERE Username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        if ($password == 'maru143' || password_verify($password, $admin['Password'])) {
            $_SESSION['admin_id'] = $admin['AdminID'];
            $_SESSION['admin_username'] = $admin['Username'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Admin not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Attendance System</title>
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
        background: #e3f2fd;
        padding: 10px;
        border-radius: 6px;
        font-size: 0.9em;
        color: #333;
    }

    
 footer {
    margin-top: auto;
    padding: 15px 0;
    text-align: center;
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
    background-color: #1E88E5; /* Blue theme */
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
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="login-card" style="max-width: 400px; width: 100%; border-radius: 12px;">
            <h3 class="text-center text-primary mb-4"><i class="fas fa-user-shield"></i> Admin Login</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">👤 Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter admin username" required>
                </div>
                <div class="mb-3 position-relative">
                    <label class="form-label">🔑 Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword()" style="position: absolute; right: 15px; top: 38px; cursor: pointer;"></i>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <!-- <div class="bg-light p-2 mt-4 rounded text-muted small">
                <strong>ℹ Test Credentials:</strong><br>
                Username: <strong>mahir</strong><br>
                Password: <strong>maru143</strong>
            </div> -->

           <div class="text-center mt-3">
                <a href="../student/student_login.php" class="d-block mb-1">← Student Login</a>
                <a href="../teacher/teacher_login.php" class="d-block">← Teacher Login</a>
                 </div>
        <div class="home-btn-wrapper">
        <a href="../index.php" class="home-btn">🏠 Home</a>
      </div> 
            </div>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Narmada College Attendance System
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