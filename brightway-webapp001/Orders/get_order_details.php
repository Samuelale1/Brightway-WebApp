<?php
require '../includes/auth.php';
include_once '../includes/db.php';

if (!isSalesperson()) {
    exit("Unauthorized");
}

$order_id = intval($_GET['id']);

$sql = "SELECT oi.*, p.name AS product_name, p.image 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No items found.";
    exit;
}

echo "<table border='1' cellpadding='5' style='width:100%'>";
echo "<tr><th>Product</th><th>Image</th><th>Quantity</th><th>Price</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['product_name']}</td>
            <td><img src='../assets/images/products/{$row['image']}' width='50'></td>
            <td>{$row['quantity']}</td>
            <td>₦" . number_format($row['price'], 2) . "</td>
          </tr>";
}
echo "</table>";
?>
