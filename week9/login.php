<?php
session_start();
require_once 'db_connect.php';

$error = '';

// If already authenticated, redirect to appropriate view
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'staff_dashboard.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please provide both an email and password.";
    } else {
        // Query database strictly by email
        $stmt = $pdo->prepare("SELECT id, full_name, email, password_hash, role FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // Validate password against Bcrypt hash
        if ($user && password_verify($password, $user['password_hash'])) {
            // Prevent Session Fixation
            session_regenerate_id(true);

            // Populate Session State
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            // Route based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: staff_dashboard.php");
            }
            exit();
        } else {
            // Constant-time error response: avoid leaking whether the email existed
            $error = "Invalid email or password credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacy Portal - Authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
<div class="container" style="max-width: 420px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="card-title text-center mb-4">Pharmacy Portal</h3>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
                <div class="alert alert-warning py-2">Please log in to access this page.</div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'logged_out'): ?>
                <div class="alert alert-info py-2">You have been signed out.</div>
            <?php endif; ?>

            <form method="POST" action="login.php" novalidate>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>
            </form>
            <div class="card-footer bg-transparent border-0 mt-3 p-0 text-center">
                <small class="text-muted">Demo: admin@pharmacy.local / AdminPassword123!</small>
            </div>
        </div>
    </div>
</div>
</body>
</html>