<?php
session_start();
require 'db.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch admin by username
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $stored = $admin['password'];

        // Check if stored password is a bcrypt hash or plain text
        $isHashed = (strlen($stored) === 60 && substr($stored, 0, 4) === '$2y$');

        if ($isHashed) {
            $match = password_verify($password, $stored);
        } else {
            // Plain text comparison
            $match = ($password === $stored);

            // If matched, upgrade to hashed for next time
            if ($match) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE admin SET `password` = ? WHERE id = ?");
                $upd->execute([$hash, $admin['id']]);
            }
        }

        if ($match) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit();
        }
    }

    $error = "Invalid username or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CS Helpdesk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo-area">
            <div class="icon-circle">🔐</div>
            <h4>Admin Login</h4>
            <p>CS Helpdesk Administration</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                       placeholder="Enter admin username" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login as Admin</button>
        </form>

        <p class="text-center mt-3 mb-0" style="font-size: 0.82rem;">
            <a href="index.php" style="color: #999;">← Back to Home</a>
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
