<?php
// =====================================================
// db.php - Database Connection
// =====================================================

$host     = '127.0.0.1';   // Use IP instead of 'localhost' to avoid socket issues
$port     = '3307';         // Your MySQL is running on port 3307 (not the default 3306)
$dbname   = 'helpdesk';     // Your actual database name as seen in phpMyAdmin
$username = 'root';
$password = '';             // Leave empty if you have no MySQL password set

// Connect using PDO
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    // Show errors during development
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
