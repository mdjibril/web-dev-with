<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Validates that an active, authenticated user session exists.
 */
function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?error=unauthorized");
        exit();
    }
}

/**
 * Enforces Role-Based Access Control (RBAC).
 *
 * @param array $allowed_roles Array of strings allowed to access the page (e.g., ['admin'])
 */
function require_role(array $allowed_roles): void {
    require_login();

    if (!in_array($_SESSION['role'], $allowed_roles, true)) {
        http_response_code(403);
        echo "<!DOCTYPE html><html><head><title>403 Forbidden</title>";
        echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head>";
        echo "<body class='container mt-5 text-center'>";
        echo "<h1 class='text-danger display-4'>403 Forbidden</h1>";
        echo "<p class='lead'>Access Denied: Your account role (<strong>" . htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8') . "</strong>) lacks administrative privileges for this resource.</p>";
        echo "<a href='logout.php' class='btn btn-outline-secondary'>Log Out</a> ";
        echo "<a href='" . ($_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'staff_dashboard.php') . "' class='btn btn-primary'>Return to Dashboard</a>";
        echo "</body></html>";
        exit();
    }
}