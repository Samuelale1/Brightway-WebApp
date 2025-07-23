<?php
require '../includes/db.php';
require '../includes/auth.php';
requireLogin(['admin', 'salesperson']);

if (!isset($_GET['id'])) {
    die("Missing ID.");
}

$id = $_GET['id'];
$userId = $_SESSION['user_id'];
$isAdmin = isAdmin();

// Check ownership
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

if (!$isAdmin && $product['created_by'] != $userId) {
    die("Unauthorized.");
}

$update = $conn->prepare("UPDATE products SET deleted_at = NULL WHERE id = ?");
$update->bind_param("i", $id);
$update->execute();

header("Location: ../Products/view_products.php?msg=Product restored");
exit;
?>
