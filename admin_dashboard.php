<?php
// =====================================================
// admin_dashboard.php - Admin Manages All Complaints
// =====================================================
session_start();
require 'db.php';

// Only logged-in admin can access this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];
$message    = '';

// ---- Handle Reply + Status Update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaint_id'])) {
    $complaint_id = (int) $_POST['complaint_id'];
    $admin_reply  = trim($_POST['admin_reply']);
    $status       = trim($_POST['status']);

    $allowed_statuses = ['Pending', 'In Progress', 'Resolved'];

    if (!in_array($status, $allowed_statuses)) {
        $message = '<div class="alert alert-danger">Invalid status selected.</div>';
    } else {
        $stmt = $pdo->prepare("UPDATE complaints SET admin_reply = ?, status = ? WHERE id = ?");
        $stmt->execute([$admin_reply, $status, $complaint_id]);
        $message = '<div class="alert alert-success">Complaint updated successfully!</div>';
    }
}

// ---- Fetch all complaints with student info ----
$stmt = $pdo->query("
    SELECT c.*, s.full_name, s.matric_number, s.email
    FROM complaints c
    JOIN students s ON c.student_id = s.id
    ORDER BY c.created_at DESC
");
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count by status
$total    = count($complaints);
$pending  = 0;
$progress = 0;
$resolved = 0;
foreach ($complaints as $c) {
    if ($c['status'] === 'Pending')     $pending++;
    if ($c['status'] === 'In Progress') $progress++;
    if ($c['status'] === 'Resolved')    $resolved++;
}

// Status badge helper
function statusBadge($status) {
    if ($status === 'Pending')     return '<span class="badge badge-pending">'     . $status . '</span>';
    if ($status === 'In Progress') return '<span class="badge badge-progress">'    . $status . '</span>';
    if ($status === 'Resolved')    return '<span class="badge badge-resolved">'    . $status . '</span>';
}

// Filter by status (optional)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';
$filtered = ($filter === 'All') ? $complaints : array_filter($complaints, fn($c) => $c['status'] === $filter);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CS Helpdesk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php">🔐 CS Helpdesk Admin</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white-50" style="font-size:0.85rem;">👤 <?= htmlspecialchars($admin_name) ?></span>
            <a href="logout.php?admin=1" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </div>
</nav>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2>Admin Dashboard</h2>
        <p>Manage and respond to student complaints</p>
    </div>
</div>

<div class="container mb-5">

    <?= $message ?>

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card total">
                <div class="stat-number"><?= $total ?></div>
                <div class="stat-label">Total Complaints</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card pending">
                <div class="stat-number"><?= $pending ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card progress">
                <div class="stat-number"><?= $progress ?></div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card resolved">
                <div class="stat-number"><?= $resolved ?></div>
                <div class="stat-label">Resolved</div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <span class="fw-semibold me-2 align-self-center" style="font-size:0.9rem;">Filter:</span>
        <?php foreach (['All', 'Pending', 'In Progress', 'Resolved'] as $f): ?>
            <a href="?filter=<?= urlencode($f) ?>"
               class="btn btn-sm <?= $filter === $f ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <?= $f ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Complaints List -->
    <?php if (empty($filtered)): ?>
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <div style="font-size:2.5rem;">📭</div>
                <p class="mt-2">No complaints found.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($filtered as $c): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-start">
                    <!-- Complaint Details -->
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <?= statusBadge($c['status']) ?>
                            <span class="badge bg-secondary" style="font-size:0.75rem;"><?= htmlspecialchars($c['category']) ?></span>
                        </div>
                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($c['title']) ?></h6>
                        <p class="mb-2" style="font-size:0.88rem; color:#555;"><?= nl2br(htmlspecialchars($c['description'])) ?></p>
                        <small class="text-muted">
                            👤 <?= htmlspecialchars($c['full_name']) ?>
                            (<?= htmlspecialchars($c['matric_number']) ?>)
                            · <?= htmlspecialchars($c['email']) ?>
                            · <?= date('d M Y, H:i', strtotime($c['created_at'])) ?>
                        </small>

                        <?php if (!empty($c['admin_reply'])): ?>
                        <div class="reply-box mt-3">
                            <small class="fw-semibold text-primary">Your Reply:</small><br>
                            <?= htmlspecialchars($c['admin_reply']) ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Reply Form -->
                    <div class="col-md-5 mt-3 mt-md-0">
                        <form method="POST" action="">
                            <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                            <div class="mb-2">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Admin Reply</label>
                                <textarea name="admin_reply" class="form-control form-control-sm" rows="3"
                                          placeholder="Type your reply..."><?= htmlspecialchars($c['admin_reply'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="Pending"     <?= $c['status'] === 'Pending'     ? 'selected' : '' ?>>Pending</option>
                                    <option value="In Progress" <?= $c['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Resolved"    <?= $c['status'] === 'Resolved'    ? 'selected' : '' ?>>Resolved</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Update Complaint</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
