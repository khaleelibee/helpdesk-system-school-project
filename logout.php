<?php
// =====================================================
// logout.php - Destroy session and redirect
// =====================================================
session_start();
session_destroy();

// Redirect admin to admin login, students to student login
if (isset($_GET['admin'])) {
    header("Location: admin_login.php");
} else {
    header("Location: login.php");
}
exit();
?>
