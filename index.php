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
    <title>CS Helpdesk System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">🎓 CS Helpdesk</a>
        <div class="ms-auto">
            <a href="login.php" class="btn btn-outline-light btn-sm me-2">Student Login</a>
            <a href="register.php" class="btn btn-light btn-sm">Register</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="auth-wrapper" style="background: linear-gradient(135deg, #0d2b55 0%, #1a4a8a 100%); min-height: calc(100vh - 56px);">
    <div class="text-center text-white" style="max-width: 600px; padding: 20px;">
        <div style="font-size: 4rem; margin-bottom: 16px;">🖥️</div>
        <h1 style="font-weight: 700; font-size: 2rem; margin-bottom: 12px;">
            Department of Computer Science
        </h1>
        <h2 style="font-size: 1.3rem; font-weight: 400; opacity: 0.85; margin-bottom: 28px;">
            Student Helpdesk System
        </h2>
        <p style="font-size: 0.95rem; opacity: 0.75; margin-bottom: 36px; line-height: 1.7;">
            Submit and track your complaints easily. Get timely responses from the department administration.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="register.php" class="btn btn-light btn-lg px-4" style="font-weight: 600; color: #0d2b55;">
                Register Now
            </a>
            <a href="login.php" class="btn btn-outline-light btn-lg px-4">
                Student Login
            </a>
        </div>
        <div class="mt-4">
            <small style="opacity: 0.6;">Admin?</small>
            <a href="admin_login.php" style="color: rgba(255,255,255,0.7); font-size: 0.85rem; margin-left: 6px;">
                Admin Login →
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
