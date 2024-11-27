<?php
function checklogin($required_role)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user is logged in and has the required role
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $required_role) {
        // Redirect to the login page if the session or role is invalid
        $_SESSION = []; // Clear session for safety
        header("Location: ../login.php");
        exit;
    }
}
?>
