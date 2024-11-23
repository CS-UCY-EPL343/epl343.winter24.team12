<?php
function checklogin($required_role)
{
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user is logged in and has the required role
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != $required_role) {
        // Redirect to the login page if the session or role is invalid
        $host = $_SERVER['HTTP_HOST'];
        $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = "../login.php"; // Redirect to login page
        $_SESSION = []; // Clear session for safety
        header("Location: http://$host$uri/$extra");
        exit;
    }
}
?>
