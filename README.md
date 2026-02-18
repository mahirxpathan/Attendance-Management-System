# 📊 Attendance Management System

A professional, robust, and user-friendly web-based **Attendance Management System** designed to streamline tracking and reporting for educational institutions. This project is built using **PHP (procedural/OO)** and **MySQL**, focusing on ease of use and efficient data management.

---

## 🚀 Overview

Managing manual attendance can be tedious and error-prone. This system provides a digital solution with distinct portals for Admins, Teachers, and Students, ensuring transparency and real-time access to attendance data.

## ✨ Key Features

### 🔐 Multi-Role Access Control
- **Admin Dashboard**: Full control over the system. Manage student/teacher profiles, departments, subjects, and view comprehensive attendance reports.
- **Teacher Portal**: Interface to mark daily attendance for assigned subjects and classes. Generate subject-wise reports.
- **Student Portal**: Students can securely log in to monitor their attendance percentage and history.

### 📈 Functional Highlights
- **Automated Calculations**: Real-time calculation of attendance percentages.
- **Subject-Wise Tracking**: Record attendance specifically for different modules/subjects.
- **Data Export/Reporting**: View and analyze attendance trends across different semesters and years.
- **Responsive Design**: Clean and functional UI for both desktop and mobile views.

---

## 🛠️ Tech Stack

- **Backend**: PHP (7.x/8.x)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: Apache (XAMPP/WAMP/LAMP compliant)

---

## 🏁 Getting Started

### Prerequisites
- **Local Server**: [XAMPP](https://www.apachefriends.org/index.html) or [WAMP](https://www.wampserver.com/en/)
- **Browser**: Chrome, Firefox, or Safari

### Installation & Setup
1.  **Clone the Repo**:
    ```bash
    git clone https://github.com/yourusername/attendance-management-system.git
    ```
2.  **Database Configuration**:
    - Launch **phpMyAdmin**.
    - Create a database named `attendance_db`.
    - Import the `attendance_db.sql` file provided in the project root.
3.  **App Configuration**:
    - Navigate to `attendance_system/includes/database.php`.
    - Update `$user` and `$password` if your local MySQL setup differs from the default `root`/`password_empty`.
4.  **Launch**:
    - Move folder to `htdocs` (for XAMPP).
    - Visit `http://localhost/attendance_system`.

---

## 🔑 Access Credentials

> [!IMPORTANT]
> The following credentials are provided as **defaults** for demo purposes. For security, it is highly recommended to change these via the database or within the system settings after the initial login.

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `mahir` | `maru143` |
| **Teacher** | `ramnik` | `teacher123` |
| **Student** | `bca1_001` | `student` |

---

## 📝 License

Distributed under the MIT License. See `LICENSE` for more information.

---

## 👨‍💻 Contact

**Mahir** - [Your LinkedIn](https://linkedin.com/in/yourprofile) - [Your Email](mailto:youremail@example.com)

*Project Link: [https://github.com/yourusername/attendance-management-system](https://github.com/yourusername/attendance-management-system)*

