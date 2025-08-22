<?php
require '../includes/auth.php';
include_once '../includes/db.php';

// Only admins allowed
if (!isAdmin()) {
    echo "<script>alert('Access denied'); window.location.href='../users/login.php'</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    $sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order deleted successfully'); window.location.href='treated_orders.php';</script>";
    } else {
        echo "<script>alert('Failed to delete order'); window.location.href='treated_orders.php';</script>";
    }
}
?>


