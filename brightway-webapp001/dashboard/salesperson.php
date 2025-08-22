<?php
require '../includes/auth.php';
include_once '../includes/db.php';

requireLogin(['admin', 'salesperson']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Salesperson Dashboard</title>
        <link rel="stylesheet" href="../assets/Css/Style.css">
        <link rel="icon" type="image/x-icon" href="..\assets\images\others\Brightway-logo.png">
        <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
            height: 100%;
            background: #222;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            padding: 15px;
            text-decoration: none;
            color: white;
        }
        .sidebar a:hover {
            background: #444;
        }
        .main {
            margin-left: 230px;
            padding: 20px;
        }
        </style>
    </head>
<body>

<div class="sidebar">
    <h3 style="text-align:center;">Sales Panel</h3>
    <a href="salesperson.php">🏠 Dashboard</a>
    <a href="../Orders/salesperson_orders.php">📦 Orders</a>
    <a href="../users/logout.php">🚪 Logout</a>
</div>

<div class="main">
    <h1>Welcome, <?= $_SESSION['name'] ?></h1>
    <p>Select an option from the sidebar.</p>
</div>

</body>
</html>
