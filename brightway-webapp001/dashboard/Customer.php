<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../includes/auth.php';
require '../includes/db.php';

if (!isCustomer()) {
    echo "<script>alert('Access denied'); window.location.href = '../users/login.php'</script>";
    exit();
}

// Fetch products
$sql = "SELECT * FROM products WHERE deleted_at IS NULL";
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
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../assets/Css/Style.css">
</head>
<body>
    <main>

    
    <h2>
        Welcome <?php echo htmlspecialchars($_SESSION['name']); ?> to Brightway WebApp!
    </h2>

    <a href="../Orders/cart.php">🛒 Cart <span id="cart-count"><?= $cart_count ?></span></a><br> 
    <a href="../users/customer_profile.php">Profile</a>   

    <div class="product-grid">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="product-card">
                <img src="../assets/images/products/<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['name']); ?>" class="product_img">
                <h3><?= htmlspecialchars($row['name']); ?></h3>
                <p>₦<?= number_format($row['price'], 2); ?></p>
                <button class="add-to-cart" data-id="<?= $row['id'] ?>">Add to Cart</button>

            </div>
        <?php endwhile; ?>
    </div>
    


    <script>
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.dataset.id;

                fetch('../Orders/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ productId: productId })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        document.getElementById('cart-count').textContent = data.cart_count;
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
    </main>
</body>
</html>
