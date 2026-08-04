<?php
// =====================================================
// index.php - Welcome / Landing Page
// =====================================================
session_start();

// Redirect if already logged in
if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS Helpdesk System | Ahmadu Bello University</title>
    <link rel="icon" href="images/abu-logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="brand-badge"><img src="images/abu-logo-badge.png" alt="ABU Logo"></span>
            <span class="brand-text">
                Ahmadu Bello University
                <small>Dept. of Computer Science Helpdesk</small>
            </span>
        </a>
        <div class="ms-auto">
            <a href="login.php" class="btn btn-outline-light btn-sm me-2">Student Login</a>
            <a href="register.php" class="btn btn-light btn-sm">Register</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-content">
        <div class="hero-badge">
            <img src="images/abu-logo.png" alt="Ahmadu Bello University Logo">
        </div>
        <h1>Department of Computer Science</h1>
        <h2>Student Helpdesk System</h2>
        <p>
            Submit and track your complaints easily. Get timely responses
            from the department administration all in one place.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="register.php" class="btn btn-light btn-lg px-4">
                <i class="bi bi-person-plus-fill me-1"></i> Register Now
            </a>
            <a href="login.php" class="btn btn-outline-light btn-lg px-4">
                <i class="bi bi-box-arrow-in-right me-1"></i> Student Login
            </a>
        </div>
        <div class="mt-4">
            <span class="admin-link" style="opacity:0.6;">Admin?</span>
            <a href="admin_login.php" class="admin-link" style="margin-left: 6px;">
                <i class="bi bi-shield-lock"></i> Admin Login &rarr;
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
