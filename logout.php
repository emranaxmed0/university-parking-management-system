<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
if (!empty($_SESSION)) {
    session_unset();
}

// Destroy the session
session_destroy();

// Optional: clear session cookie (recommended for full logout)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to home page
header("Location: index.php");
exit();
?>
