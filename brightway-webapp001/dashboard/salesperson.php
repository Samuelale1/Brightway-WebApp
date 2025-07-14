<?php
require '../includes/auth.php';

if (!isSalesperson()) {
    echo "<script> alert('Acess denied') </script>";
    exit();
}
?>

<h2>Welcome Salesperson, <?php echo $_SESSION['name']; ?>!</h2>
<a href="../logout.php">Logout</a>
