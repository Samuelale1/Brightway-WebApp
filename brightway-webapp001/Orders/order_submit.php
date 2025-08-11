<?php
require '../includes/auth.php';
include_once '../includes/db.php';

if (!isCustomer()) {
    echo "<script>alert('Access denied'); window.location.href = '../users/login.php'</script>";
    exit();
}

$uid = $_SESSION['user_id'];
$method = $_POST['method'];
$total = $_POST['total'];
$items = json_decode($_POST['items'], true);
$phone = $_POST['phone'];
$address = trim($_POST['address']);

// Save address to users table if not set
$userRes = mysqli_query($conn, "SELECT address FROM users WHERE id = $uid");
$user = mysqli_fetch_assoc($userRes);
if (empty($user['address'])) {
    $stmt = $conn->prepare("UPDATE users SET address = ? WHERE id = ?");
    $stmt->bind_param("si", $address, $uid);
    $stmt->execute();
}

// Insert into orders table
$status = ($method === 'delivery') ? 'pending' : 'paid';
$stmt = $conn->prepare("INSERT INTO orders (customer_id, delivery_address, status, total_price) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issi", $uid, $address, $status, $total);
$stmt->execute();
$order_id = $stmt->insert_id;

// Link order_items to this order
foreach ($items as $it) {
    $stmt2 = $conn->prepare("UPDATE order_items SET order_id = ?, deleted_at = NOW() WHERE id = ?");
    $stmt2->bind_param("ii", $order_id, $it['order_item_id']);
    $stmt2->execute();
}

echo "<script>alert('Order placed successfully!'); window.location.href='../dashboard/customer.php'</script>";
