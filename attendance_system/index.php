<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Narmada College Attendance System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }
    .navbar-brand {
      font-weight: bold;
      font-size: 1.5rem;
    }
    .hero-section {
      background: linear-gradient(to right, #e3f2fd, #bbdefb);
      padding: 60px 20px;
      text-align: center;
    }
    .hero-section h1 {
      font-size: 2.5rem;
      color: #1E88E5;
      font-weight: 600;
    }
    .hero-section p {
      font-size: 1.1rem;
      color: #333;
      margin-top: 10px;
    }
    .login-buttons .btn {
      margin: 10px;
      min-width: 180px;
    }
    .section-title {
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 30px;
      text-align: center;
      color: #1E88E5;
    }
    .gallery-card {
      border: none;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    .gallery-card:hover {
      transform: scale(1.02);
    }
    .gallery-card img {
      height: 220px;
      object-fit: cover;
      width: 100%;
    }
    .info-section {
      padding: 60px 20px;
    }
    footer {
      background-color: #343a40;
      color: white;
      padding: 20px 0;
      text-align: center;
      font-size: 0.9rem;
    }
    .footer {
  background-color: #1E88E5;
  color: white;
  font-size: 0.9rem;
  margin-top: auto;
}

.footer hr {
  border-color: rgba(255,255,255,0.2);
}

.footer p {
  margin: 0;
}
  </style>
</head>
<body>

  <!-- Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color: #1E88E5;">
  <div class="container">
    <a class="navbar-brand text-white fw-semibold" href="#" style="font-size: 1.6rem;">Narmada College</a>
    <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Login
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="student/student_login.php">👨‍🎓 Student Login</a></li>
            <li><a class="dropdown-item" href="teacher/teacher_login.php">👨‍🏫 Teacher Login</a></li>
            <li><a class="dropdown-item" href="admin/admin_login.php">🛡️ Admin Login</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container">
      <h1>Welcome to the Attendance Management System</h1>
      <p>Designed for Narmada College of Science and Commerce to streamline attendance tracking for students, teachers, and administrators.</p>
      <div class="login-buttons d-flex justify-content-center flex-wrap mt-4">
        <a href="student/student_login.php" class="btn btn-outline-primary">👨‍🎓 Student Login</a>
        <a href="teacher/teacher_login.php" class="btn btn-outline-success">👨‍🏫 Teacher Login</a>
        <a href="admin/admin_login.php" class="btn btn-outline-dark">🛡️ Admin Login</a>
      </div>
    </div>
  </section>

  <!-- Gallery -->
  <section class="info-section bg-light">
    <div class="container">
      <h2 class="section-title">🏞️ Campus Gallery</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card gallery-card">
            <img src="assets/img/college1.webp" alt="College Image 1">
          </div>
        </div>
        <div class="col-md-4">
          <div class="card gallery-card">
            <img src="assets/img/college2.webp" alt="College Image 2">
          </div>
        </div>
        <div class="col-md-4">
          <div class="card gallery-card">
            <img src="assets/img/college3.webp" alt="College Image 3">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="info-section">
    <div class="container">
      <h2 class="section-title">🏫 About the College</h2>
      <p>Narmada College of Science and Commerce is a distinguished institution located in Bharuch, Gujarat. It offers a vibrant academic environment for students pursuing degrees in science, commerce, and technology. The college is known for its commitment to excellence, ethical values, and holistic development.</p>
      <p>With modern infrastructure, experienced faculty, and a focus on innovation, the college empowers students to become future-ready professionals. Facilities include advanced laboratories, digital classrooms, a rich library, and opportunities for extracurricular growth through cultural and sports activities.</p>
    </div>
  </section>

  <!-- Address Section -->
  <section class="info-section bg-white">
    <div class="container">
      <h2 class="section-title">📍 College Address</h2>
      <p><strong>Location:</strong> P2GV+M6F, Shuklatirth Rd, Zadeshwar, Near Shree Rang Township, Bharuch, Gujarat 392011, India</p>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer mt-auto">
  <div class="container text-center py-3">
    <hr class="mb-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
    <p class="mb-0 text-white">
      &copy; <?= date("Y") ?> Narmada College Attendance System | Designed by Mahir
    </p>
  </div>
</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>