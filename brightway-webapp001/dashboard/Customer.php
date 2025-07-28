<?php
require '../includes/auth.php';

include_once '../includes/db.php';


if (!isCustomer()) {
    echo "<script> alert('Access denied'); window.location.href = 'brightway-webapp001\users\login.php'</script>";
    exit();
}
$sql = "SELECT * FROM products WHERE deleted_at IS NULL"; // Only available products
$result = mysqli_query($conn, $sql);
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
    <h2> <?php echo "Welcome ".$_SESSION['name'] ."! To Brightway webapp "  ; ?>!</h2>

    <div class="product-grid">
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <div class="product-card">
            <img src="../assets/images/products/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="product_img">
            <h3><?php echo $row['name']; ?></h3>
            <p>₦<?php echo $row['price']; ?></p>
            <button onclick="addToCart(<?php echo $row['id']; ?>)">Add to Cart</button>
        </div>
    <?php } ?>
</div>

    <a href="../logout.php">Logout</a>
</body>
</html>

