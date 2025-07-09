<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin($role = null) {
    if (!isLoggedIn()) {
        header("Location: /brightway-webapp001/login.php");
        exit();
    }

    if ($role && $_SESSION['role'] !== $role) {
        echo "Access denied: You are not allowed to access this page.";
        exit();
    }
}
?>
