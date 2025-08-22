<?php
require '../includes/auth.php';
requirelogin('admin');

if (!isAdmin()) {
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
    
    <title>Admin Dashboard</title>
     <link rel="icon" type="image/x-icon" href="..\assets\images\others\Brightway-logo.png">
    <link rel="stylesheet" href="../assets/Css/Style.css">

</head>
<body>
    <h2>Welcome Admin, <?php echo $_SESSION['name']. " To Brightway Webapp"  ; ?>!</h2>
    <a href="../users/logout.php">Logout</a>

</body>
</html>
