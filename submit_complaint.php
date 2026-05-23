<?php
// =====================================================
// submit_complaint.php - Submit a New Complaint
// =====================================================
session_start();
require 'db.php';

// Only logged-in students can access this page
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id   = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];
$error        = '';
$success      = '';

// Available complaint categories
$categories = [
    'Results Issues',
    'Course Registration',
    'ICT/Lab Problems',
    'Lecturer Complaints',
    'Project/SIWES Issues',
    'Others'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $category    = trim($_POST['category']);
    $description = trim($_POST['description']);

    // Validate inputs
    if (empty($title) || empty($category) || empty($description)) {
        $error = "All fields are required.";
    } elseif (!in_array($category, $categories)) {
        $error = "Invalid category selected.";
    } else {
        // Insert complaint into database
        $stmt = $pdo->prepare("INSERT INTO complaints (student_id, title, category, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$student_id, $title, $category, $description]);
        $success = "Your complaint has been submitted successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint - CS Helpdesk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">🎓 CS Helpdesk</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white-50" style="font-size:0.85rem;">👤 <?= htmlspecialchars($student_name) ?></span>
            <a href="dashboard.php" class="btn btn-sm btn-outline-light">My Dashboard</a>
            <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </div>
</nav>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2>Submit a Complaint</h2>
        <p>Describe your issue and we will respond as soon as possible</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">New Complaint Form</div>
                <div class="card-body p-4">

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($success) ?>
                            <a href="dashboard.php" class="alert-link">View Dashboard</a>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Complaint Title</label>
                            <input type="text" name="title" class="form-control"
                                   placeholder="Brief title of your complaint"
                                   value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">-- Select a category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat ?>"
                                        <?= (isset($_POST['category']) && $_POST['category'] === $cat) ? 'selected' : '' ?>>
                                        <?= $cat ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="6"
                                      placeholder="Describe your complaint in detail..."
                                      required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                            <small class="text-muted">Be as specific as possible to help us resolve your issue faster.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">Submit Complaint</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
