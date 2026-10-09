<?php
require_once 'auth_check.php';
// Restrict strictly to administrators
require_role(['admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Control Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand mb-0 h1">Pharmacy Administration (RBAC Tier: Admin)</span>
    <div class="d-flex align-items-center text-white gap-3">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8'); ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-danger">Log Out</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="alert alert-success">
        <strong>Authorization Cleared:</strong> You are viewing privileged administrative control files.
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <h5 class="card-title">Medication Catalog</h5>
                    <p class="card-text text-muted">Create, edit, and delete drug listings.</p>
                    <a href="add_medication.php" class="btn btn-outline-primary btn-sm">Open Module</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning h-100">
                <div class="card-body">
                    <h5 class="card-title">Inventory Alerts</h5>
                    <p class="card-text text-muted">View low-stock and soon-expiring drugs.</p>
                    <a href="alerts_dashboard.php" class="btn btn-outline-warning btn-sm">Open Module</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger h-100">
                <div class="card-body">
                    <h5 class="card-title">User Management</h5>
                    <p class="card-text text-muted">Manage system users, roles, and security policies.</p>
                    <button class="btn btn-outline-danger btn-sm" disabled>Manage Staff (Restricted)</button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>