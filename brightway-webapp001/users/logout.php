<?php
session_start();
session_unset();
session_destroy();
header("Location: brightway-webapp001\users\login.php");
exit();
?>