<?php
require '../includes/auth.php';
include_once '../includes/db.php';

if (!isCustomer()) {
    echo "<script>alert('Access denied'); window.location.href = '../users/login.php'</script>";
    exit();
}

$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $stmt = $conn->prepare("UPDATE users SET phone_no = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssi", $phone, $address, $uid);
    $stmt->execute();
    echo "<script>alert('Profile updated!');</script>";
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $uid"));
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE customer_id = $uid ORDER BY created_at DESC");
?>

<h2>My Profile</h2>
<form method="post">
    Phone: <input type="text" name="phone" value="<?= htmlspecialchars($user['phone_no']) ?>"><br>
    Address: <textarea name="address"><?= htmlspecialchars($user['address']) ?></textarea><br>
    <button type="submit">Update</button>
</form>

<h2>My Orders</h2>
<table border="1">
<tr>
    <th>Order ID</th>
    <th>Status</th>
    <th>Total</th>
    <th>Date</th>
</tr>
<?php while ($o = mysqli_fetch_assoc($orders)): ?>
<tr>
    <td><?= $o['id'] ?></td>
    <td><?= $o['status'] ?></td>
    <td>₦<?= number_format($o['total_price'], 2) ?></td>
    <td><?= $o['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>

