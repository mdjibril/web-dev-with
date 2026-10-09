<?php
require_once 'auth_check.php';
// Both admin and staff can access this basic workspace
require_role(['admin', 'staff']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Operations Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-primary bg-primary px-4 text-white">
    <span class="navbar-brand mb-0 h1 text-white">Pharmacy Staff Operations Desk</span>
    <div class="d-flex align-items-center gap-3">
        <span>Worker: <?php echo htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8'); ?> (Role: <?php echo htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8'); ?>)</span>
        <a href="logout.php" class="btn btn-sm btn-light">Log Out</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="card p-4">
        <h4>Operational Tasks</h4>
        <p class="text-muted">Standard staff privileges permit prescription lookup and dispensing logs.</p>
        <div class="alert alert-secondary">
            Note: You do not have permissions to manage user roles or delete stock records.
        </div>
        <a href="admin_dashboard.php" class="btn btn-warning w-25">Attempt Accessing Admin Panel</a>
    </div>
</div>
</body>
</html>