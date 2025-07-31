
<?php

require '../includes/auth.php';

include_once '../includes/db.php';


if (!isCustomer()) {
    echo "<script> alert('Access denied'); window.location.href = 'brightway-webapp001\users\login.php'</script>";
    exit();
}
$sql = "SELECT * FROM products WHERE deleted_at IS NULL"; // Only available products
$result = mysqli_query($conn, $sql);
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $res = mysqli_query($conn, "SELECT SUM(quantity) AS total FROM order_items WHERE customer_id = $uid AND deleted_at IS NULL");
    $data = mysqli_fetch_assoc($res);
    $cart_count = $data['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/images/others/Brightway-logo.png">
    <title>Customer</title>
    <link rel="stylesheet" href="../assets/Css/Style.css">
    <script src="../assets/app/cart.js"></script>
</head>

<body>
   <h2 class="h2">
<?php echo "Welcome ".$_SESSION['name'] ."! To Brightway webapp"; ?>
</h2>

<div class="product-grid">
    <?php 
    while($row = mysqli_fetch_assoc($result)) { ?>
        <div class="product-card">
            <img src="../assets/images/products/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="product_img">
            <h3><?php echo $row['name']; ?></h3>
            <p>₦<?php echo $row['price']; ?></p>
           <button class="add-to-cart" data-id="<?= $product['id'] ?>">Add to Cart</button>
        </div>
    <?php } ?>
</div>
 <a href="../Orders/cart.php">🛒 Cart <span id="cart-count"><?= $cart_count ?></span></a><br>
<script>
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', () => {
        const productId = button.dataset.id;

        fetch('../Orders/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Added to cart!');
                location.reload(); // This refreshes cart counter from PHP
            } else {
                alert(data.message);
            }
        });
    });
});
</script>
    <a href="../users/logout.php">Logout</a>
</body>
</html>

