<?php
session_start();
include_once '../includes/db.php';

// Get raw JSON POST data
$data = json_decode(file_get_contents("php://input"), true);
$productId = $data['productId'];

// Fetch product info
$sql = "SELECT id, product_name, price, image FROM products WHERE id = $productId";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if ($product) {
    // Now we create a cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // or if it exists we add or update item
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$productId] = [
            'id' => $product['id'],
            'name' => $product['product_name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => 1
        ];
    }

    echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
}
?>
