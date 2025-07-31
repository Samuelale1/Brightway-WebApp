<?php
require '../includes/auth.php';
session_start();
include_once '../includes/db.php';

if (!isCustomer()) {
    echo "<script>alert('Access denied'); window.location.href = '../users/login.php'</script>";
    exit();
}

$cart_json = $_POST['cart'] ?? '';
$phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
$address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
$customer_id = $_SESSION['user_id'];
$cart = json_decode($cart_json, true);

if (!$cart || empty($phone) || empty($address)) {
    echo "Invalid submission.";
    exit();
}

// Create the order (no salesperson yet)
$order_sql = "INSERT INTO orders (customer_id) VALUES ('$customer_id')";
if (!mysqli_query($conn, $order_sql)) {
    echo "Order creation failed: " . mysqli_error($conn);
    exit();
}

$order_id = mysqli_insert_id($conn);

foreach ($cart as $product_id => $item) {
    $qty = $item['qty'];

    $price_query = "SELECT price FROM products WHERE id = $product_id AND deleted_at IS NULL";
    $price_result = mysqli_query($conn, $price_query);
    
    if ($row = mysqli_fetch_assoc($price_result)) {
        $price = $row['price'];
        $subtotal = $qty * $price;

        // Insert into order_items
        $insert_item = "INSERT INTO order_items (order_id, product_id, quantity, price_at_order, subtotal) 
                        VALUES ('$order_id', '$product_id', '$qty', '$price', '$subtotal')";
        mysqli_query($conn, $insert_item);
    }
}

// Done: clear cart and redirect
echo "
<script>
    localStorage.removeItem('cart');
    alert('Order placed successfully!');
    window.location.href = '../dashboard/customer.php';
</script>";
?>
