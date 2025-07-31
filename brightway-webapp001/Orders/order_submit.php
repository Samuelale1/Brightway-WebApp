<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!isCustomer()) {
    echo "<script>alert('Unauthorized'); window.location.href = '../users/login.php'</script>";
    exit();
}

$customerId = $_SESSION['user_id'];
$method = $_POST['payment_method'];

$itemsQuery = mysqli_query($conn, "SELECT oi.*, p.price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.customer_id = $customerId AND oi.deleted_at IS NULL");

$totalPrice = 0;
while ($item = mysqli_fetch_assoc($itemsQuery)) {
    $totalPrice += $item['price'] * $item['quantity'];
}

if ($totalPrice == 0) {
    echo "<script>alert('Cart is empty!'); window.location.href = 'cart.php';</script>";
    exit();
}

// Insert into orders table
mysqli_query($conn, "INSERT INTO orders (customer_id, total_price, payment_method) VALUES ($customerId, $totalPrice, '$method')");
$orderId = mysqli_insert_id($conn);

// Optionally: mark items as soft-deleted if they’ve been checked out
mysqli_query($conn, "UPDATE order_items SET deleted_at = NOW() WHERE customer_id = $customerId AND deleted_at IS NULL");

// Optionally: Add logic to assign salesperson and send notification

echo "<script>alert('Order placed successfully!'); window.location.href = '../dashboard/customer.php';</script>";
