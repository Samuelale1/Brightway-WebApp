<?php

session_start();

/**
 * Checks if a user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Force login before accessing a page
 * Optionally check for user role
 * the function is called as an array for 2 parameters
 * requireLogin(['admin', 'salesperson']);
 * OR
 * requireLogin('salesperson');
 */
function requireLogin($roles = null) {
    if (!isLoggedIn()) {
        echo "<script>alert('You haven\\'t logged in yet'); window.location.href = 'brightway-webapp001\users\login.php';</script>";
        exit();
    }

    // If roles are specified and the current user's role is not in the list
    if ($roles && !in_array($_SESSION['role'], (array)$roles)) {
        echo "<script>alert('Access denied: You are not allowed to access this page.'); window.history.back();</script>";
        exit();
    }
}



/**
 * Role helpers (optional if you prefer calling isLoggedIn() and checking role manually)
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isSalesperson() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'salesperson';
}

function isCustomer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'customer';
}
?>








