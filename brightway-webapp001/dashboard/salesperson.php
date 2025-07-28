<?php
require '../includes/auth.php';
requirelogin(['salesperson','admin']);

if (!isSalesperson()) {
    echo "<script> alert('Access denied') </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="brightway-webapp001\assets\images\others\Brightway-logo.png">
    
    <title>Salesperson</title>
    <link rel="stylesheet" href="assets/Css/Style.css">
</head>
<body>
    <h2>Welcome Salesperson, <?php echo $_SESSION['name']. "! to Brightway Webapp"; ?></h2>
    <a href="../logout.php">Logout</a>
</body>
</html>

