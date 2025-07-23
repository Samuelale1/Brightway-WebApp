<?php
require '../includes/db.php';
require '../includes/auth.php';
requireLogin(['admin', 'salesperson']);

if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = $_GET['id'];
$userId = $_SESSION['user_id'];
$isAdmin = isAdmin();

// Check if user is allowed to delete
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

// Soft delete
$deleted_at = date('Y-m-d H:i:s');
$update = $conn->prepare("UPDATE products SET deleted_at = ? WHERE id = ?");
$update->bind_param("si", $deleted_at, $id);
$update->execute();

header("Location: ../Products/view_products.php?msg=Product deleted successfully");

exit;
?>
