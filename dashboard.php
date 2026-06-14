<?php
// =====================================================
// dashboard.php - Student Dashboard
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

// Fetch this student's complaints
$stmt = $pdo->prepare("SELECT * FROM complaints WHERE student_id = ? ORDER BY created_at DESC");
$stmt->execute([$student_id]);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count complaints by status
$total     = count($complaints);
$pending   = 0;
$progress  = 0;
$resolved  = 0;
foreach ($complaints as $c) {
    if ($c['status'] === 'Pending')     $pending++;
    if ($c['status'] === 'In Progress') $progress++;
    if ($c['status'] === 'Resolved')    $resolved++;
}

// Helper function for status badge
function statusBadge($status) {
    if ($status === 'Pending')     return '<span class="badge badge-pending"><i class="bi bi-hourglass-split me-1"></i>' . $status . '</span>';
    if ($status === 'In Progress') return '<span class="badge badge-progress"><i class="bi bi-arrow-repeat me-1"></i>' . $status . '</span>';
    if ($status === 'Resolved')    return '<span class="badge badge-resolved"><i class="bi bi-check-circle-fill me-1"></i>' . $status . '</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | CS Helpdesk - ABU</title>
    <link rel="icon" href="images/abu-logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <span class="brand-badge"><img src="images/abu-logo-badge.png" alt="ABU Logo"></span>
            <span class="brand-text">
                Ahmadu Bello University
                <small>Dept. of Computer Science &mdash; Helpdesk</small>
            </span>
        </a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="user-chip"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($student_name) ?></span>
            <a href="submit_complaint.php" class="btn btn-sm btn-outline-light"><i class="bi bi-plus-circle me-1"></i>New Complaint</a>
            <a href="logout.php" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
        </div>
    </div>
</nav>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2>My Dashboard</h2>
        <p>Track your complaints and responses</p>
    </div>
</div>

<div class="container mb-5">

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card total">
                <i class="bi bi-folder2-open"></i>
                <div class="stat-number"><?= $total ?></div>
                <div class="stat-label">Total Complaints</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card pending">
                <i class="bi bi-hourglass-split"></i>
                <div class="stat-number"><?= $pending ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-progress">
                <i class="bi bi-arrow-repeat"></i>
                <div class="stat-number"><?= $progress ?></div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card resolved">
                <i class="bi bi-check-circle"></i>
                <div class="stat-number"><?= $resolved ?></div>
                <div class="stat-label">Resolved</div>
            </div>
        </div>
    </div>

    <!-- Complaints Table -->
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-ul me-1"></i> My Complaints</span>
            <a href="submit_complaint.php" class="btn btn-sm btn-light"><i class="bi bi-plus-circle me-1"></i>Submit New</a>
        </div>
        <div class="card-body p-0">
            <?php if (empty($complaints)): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p class="mb-3">You have not submitted any complaints yet.</p>
                    <a href="submit_complaint.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Submit a Complaint</a>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Admin Reply</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complaints as $i => $c): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($c['title']) ?></strong>
                                <br>
                                <small class="text-muted"><?= htmlspecialchars(substr($c['description'], 0, 60)) ?>...</small>
                            </td>
                            <td><?= htmlspecialchars($c['category']) ?></td>
                            <td><?= statusBadge($c['status']) ?></td>
                            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                            <td>
                                <?php if (!empty($c['admin_reply'])): ?>
                                    <div class="reply-box">
                                        <?= htmlspecialchars($c['admin_reply']) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:0.82rem;"><i class="bi bi-clock-history me-1"></i>Awaiting reply...</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
