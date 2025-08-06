<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!isCustomer()) {
    echo "<script>alert('Access denied'); window.location.href = '../users/login.php'</script>";
    exit();
}

$customerId = $_SESSION['user_id'];
$sql = "SELECT oi.id AS item_id, p.name, p.price, p.image, oi.quantity
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.customer_id = $customerId AND oi.deleted_at IS NULL";
$result = mysqli_query($conn, $sql);

$cartItems = [];
$total = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cartItems[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="../assets/Css/Style.css">
</head>
<body>
    <h2>Your Cart</h2>
    <?php if (count($cartItems) === 0): ?>
        <p>Your cart is empty.</p>
        <a href="../dashboard/customer.php">Back to Shop</a>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product name</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><img src="../assets/images/products/<?= $item['image'] ?>" width="60" height="60"></td>
                        <td><?= $item['name'] ?></td>
                        <td>₦<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>₦<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3 class="h3">Total: ₦<?= number_format($total, 2) ?></h3>
    <div class="pay-div dflex">
         <form action="order_submit.php" method="post">
            <input type="hidden" name="payment_method" value="pay_on_delivery">
            <button type="submit">Pay on Delivery</button>
        </form>

        <form action="order_submit.php" method="post">
            <input type="hidden" name="payment_method" value="pay_now">
            <button type="submit">Pay Now</button>
        </form>
    </div>

        
    <?php endif; ?>
</body>
</html>
