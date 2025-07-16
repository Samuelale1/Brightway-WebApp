<?php
require '../includes/auth.php';

if (!isCustomer()) {
    echo "<script> alert('Access denied') </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/Brightway-logo.png">
    <title>Customer</title>
</head>
<body>
    <h2> <?php echo "Welcome ".$_SESSION['name'] ."! To Brightway webapp "  ; ?>!</h2>
    <a href="../logout.php">Logout</a>
</body>
</html>

