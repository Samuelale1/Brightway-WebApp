<?php
require '../includes/auth.php';

if (!isCustomer()) {
    echo "<script> alert('Acess denied') </script>";
    exit();
}
?>

<h2>Welcome Customer, <?php echo $_SESSION['name']; ?>!</h2>
<a href="../logout.php">Logout</a>
