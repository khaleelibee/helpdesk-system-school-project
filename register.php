<?php
// =====================================================
// register.php - Student Registration
// =====================================================
session_start();
require 'db.php';

// Redirect if already logged in
if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error   = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $full_name     = trim($_POST['full_name']);
    $email         = trim($_POST['email']);
    $matric_number = trim($_POST['matric_number']);
    $password      = $_POST['password'];
    $confirm_pass  = $_POST['confirm_password'];

    // Basic validation
    if (empty($full_name) || empty($email) || empty($matric_number) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($password !== $confirm_pass) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email or matric number already exists
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ? OR matric_number = ?");
        $stmt->execute([$email, $matric_number]);

        if ($stmt->rowCount() > 0) {
            $error = "Email or matric number already registered.";
        } else {
            // Hash password and insert student
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO students (full_name, email, matric_number, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $matric_number, $hashed]);
            $success = "Registration successful! You can now login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration | CS Helpdesk - ABU</title>
    <link rel="icon" href="images/abu-logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo-area">
            <div class="icon-circle">
                <img src="images/abu-logo.png" alt="ABU Logo">
            </div>
            <h4>CS Helpdesk System</h4>
            <p>Create your student account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle-fill me-1"></i> <?= htmlspecialchars($success) ?>
                <a href="login.php" class="alert-link">Login here</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control"
                       placeholder="e.g. Khaleel Ibrahim"
                       value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>"
                       required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                       placeholder="e.g. khaleel@example.com"
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                       required>
            </div>
            <div class="mb-3">
                <label class="form-label">Matric Number</label>
                <input type="text" name="matric_number" class="form-control"
                       placeholder="e.g. U21CS9999"
                       value="<?= isset($_POST['matric_number']) ? htmlspecialchars($_POST['matric_number']) : '' ?>"
                       required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min. 6 characters" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control"
                       placeholder="Repeat your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-person-plus-fill me-1"></i> Create Account
            </button>
        </form>

        <p class="text-center mt-3 mb-0" style="font-size: 0.88rem; color: var(--gray-500);">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
