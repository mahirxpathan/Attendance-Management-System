# Attendance Management System

A simple and efficient web-based Attendance Management System built with PHP and MySQL.

## Features
- **Admin Dashboard**: Manage students, teachers, classes, and subjects.
- **Teacher Portal**: Mark attendance and view reports.
- **Student Portal**: View personal attendance records.
- **Subject-wise Attendance**: Track attendance for individual subjects.

## Technologies Used
- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP
- **Database**: MySQL

## Getting Started

### Prerequisites
- XAMPP / WAMP / LAMP or any local PHP environment.
- MySQL Database.

### Installation
1.  **Clone the repository**:
    ```bash
    git clone <repository-url>
    ```
2.  **Database Setup**:
    - Open phpMyAdmin.
    - Create a new database named `attendance_db`.
    - Import the `attendance_db.sql` file located in the root directory.
3.  **Configuration**:
    - Open `attendance_system/includes/database.php`.
    - Update the database credentials (`host`, `user`, `password`, `dbname`) if necessary.
4.  **Run the Application**:
    - Move the project folder to your local server's root (e.g., `htdocs` for XAMPP).
    - Access the application via `http://localhost/attendance_system`.

## Admin Credentials
- **Username**: `mahir`
- **Password**: `maru143`

## License
MIT License
