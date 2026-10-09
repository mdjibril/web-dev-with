<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Delete the session cookie from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session data on the server
session_destroy();

header("Location: login.php?status=logged_out");
exit();