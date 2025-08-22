<?php
require '../includes/auth.php';
include_once '../includes/db.php';

if (!isCustomer()) {
    echo "<script>alert('Access denied'); window.location.href = '../users/login.php'</script>";
    exit();
}

$uid = $_SESSION['user_id'];

// Get customer info
$userRes = mysqli_query($conn, "SELECT phone_no, address FROM users WHERE id = $uid");
$user = mysqli_fetch_assoc($userRes);
$storedAddress = $user['address'] ?? '';
$phone = $user['phone_no'] ?? '';

// Fetch cart items from DB
$sql = "SELECT oi.id AS order_item_id, p.id AS product_id, p.name, p.price, p.image, oi.quantity
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.customer_id = $uid AND oi.deleted_at IS NULL";
$result = mysqli_query($conn, $sql);

// Calculate total
$total = 0;
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $items[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <link rel="stylesheet" href="../assets/Css/Style.css">
    <link rel="icon" type="image/x-icon" href="..\assets\images\others\Brightway-logo.png">
    <script>
    function toggleAddressEdit() {
        document.getElementById("address").readOnly = !document.getElementById("address").readOnly;
    }
    function validateAddress() {
        let addr = document.getElementById("address").value.trim();
        document.querySelectorAll(".pay-btn").forEach(btn => {
            btn.disabled = (addr === "");
        });
    }
    </script>
</head>
<body>
<h2>Your Cart</h2>

<?php if (count($items) === 0): ?>
    <p>Your cart is empty. <a href="../dashboard/customer.php">Back to shop</a></p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Product</th>
            <th>Image</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><img src="../assets/images/products/<?= $item['image'] ?>" width="60"></td>
            <td>₦<?= number_format($item['price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₦<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Total: ₦<?= number_format($total, 2) ?></h3>

    <h3>Delivery Address</h3>
    <?php if ($storedAddress): ?>
        <input type="text" id="address" name="address" value="<?= htmlspecialchars($storedAddress) ?>" readonly oninput="validateAddress()">
        <input type="checkbox" onchange="toggleAddressEdit()"> Change Address
    <?php else: ?>
        <input type="text" id="address" name="address" placeholder="Enter delivery address" required oninput="validateAddress()">
    <?php endif; ?>

    <form method="post" action="order_submit.php">
        <input type="hidden" name="method" value="delivery">
        <input type="hidden" name="total" value="<?= $total ?>">
        <input type="hidden" name="items" value='<?= json_encode($items) ?>'>
        <input type="hidden" name="phone" value="<?= $phone ?>">
        <input type="hidden" id="hidden_address" name="address">
        <button type="submit" class="pay-btn" disabled onclick="document.getElementById('hidden_address').value=document.getElementById('address').value;">Pay on Delivery</button>
    </form>

    <form method="post" action="order_submit.php">
        <input type="hidden" name="method" value="online">
        <input type="hidden" name="total" value="<?= $total ?>">
        <input type="hidden" name="items" value='<?= json_encode($items) ?>'>
        <input type="hidden" name="phone" value="<?= $phone ?>">
        <input type="hidden" id="hidden_address2" name="address">
        <button type="submit" class="pay-btn" disabled onclick="document.getElementById('hidden_address2').value=document.getElementById('address').value;">Pay Now</button>
    </form>
<?php endif; ?>

</body>
</html>
