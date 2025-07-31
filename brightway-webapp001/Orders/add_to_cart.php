<?php
include '../includes/db.php';
include '../includes/auth.php';

if (!isCustomer()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$customerId = $_SESSION['user_id'];
$productId = intval($data['productId']);

// Check if item already in cart
$checkQuery = "SELECT * FROM order_items 
               WHERE customer_id = $customerId AND product_id = $productId AND deleted_at IS NULL";
$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    // Item exists, update quantity
    mysqli_query($conn, "UPDATE order_items 
                         SET quantity = quantity + 1 
                         WHERE customer_id = $customerId AND product_id = $productId AND deleted_at IS NULL");
} else {
    // Item doesn't exist, insert new
    mysqli_query($conn, "INSERT INTO order_items (customer_id, product_id, quantity) 
                         VALUES ($customerId, $productId, 1)");
}

echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
?>
