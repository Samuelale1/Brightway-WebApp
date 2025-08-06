<?php
require '../includes/db.php';
require '../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['productId']) || !isCustomer()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or missing product']);
    exit;
}

$customerId = $_SESSION['user_id'];
$productId = (int)$data['productId'];

// Check if item already in cart
$check = $conn->prepare("SELECT id, quantity FROM order_items WHERE customer_id = ? AND product_id = ? AND deleted_at IS NULL");
$check->bind_param("ii", $customerId, $productId);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Update quantity
    $row = $result->fetch_assoc();
    $newQty = $row['quantity'] + 1;

    $update = $conn->prepare("UPDATE order_items SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $newQty, $row['id']);
    $update->execute();
} else {
    // Insert new item
    $insert = $conn->prepare("INSERT INTO order_items (customer_id, product_id, quantity) VALUES (?, ?, 1)");
    $insert->bind_param("ii", $customerId, $productId);
    $insert->execute();
}

echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
?>

