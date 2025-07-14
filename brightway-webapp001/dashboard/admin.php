<?php
require '../includes/auth.php';

if (!isAdmin()) {
    echo "<script> alert('Acess denied') </script>";
    exit();
}
?>

<h2>Welcome Admin, <?php echo $_SESSION['name']; ?>!</h2>
<a href="../logout.php">Logout</a>
