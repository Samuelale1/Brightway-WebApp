<?php
require '../includes/auth.php';
include_once '../includes/db.php';

// Only admins can view
requireLogin(['admin','salesperson']);

// Search filter
$search = $_GET['search'] ?? '';
$where = "WHERE o.status = 'sent'";
$params = [];
$paramTypes = '';

if (!empty($search)) {
    $searchTerm = "%" . $search . "%";
    $where .= " AND (u.name LIKE ? OR sp.name LIKE ? OR o.delivery_person_name LIKE ?)";
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $paramTypes = 'sss';
}

// Build query
$sql = "SELECT o.id, u.name AS customer_name, u.address, u.phone_no,
               o.total_price, o.delivery_person_name, o.delivery_person_phone,
               sp.name AS salesperson_name, o.sent_out_at
        FROM orders o
        JOIN users u ON o.customer_id = u.id
        LEFT JOIN users sp ON o.salesperson_id = sp.id
        $where
        ORDER BY o.sent_out_at DESC";

// Prepare + execute safely
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Treated Orders</title>
    <link rel="stylesheet" href="../assets/Css/Style.css">
    <style>
        .main { margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: 2px #EC6408; color: #fff; }
        .search-box { margin-bottom: 15px; }
        .search-box input { padding: 8px; width: 250px; }
        .btn-export { margin-right: 10px; padding: 8px 12px; background: green; color: white; text-decoration: none; border-radius: 5px; }
        .btn-export:hover { opacity: 0.8; }
        .delete-btn { background: red; color: white; padding: 5px 8px; border: none; cursor: pointer; border-radius: 3px; }
    </style>
</head>
<body>
<div class="main">
    <h2>Treated Orders</h2>

    <!-- Search -->
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by customer, salesperson or delivery person" 
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Export Buttons -->
    <div>
        <a href="export_orders.php?type=csv" class="btn-export">📂 Export CSV</a>
        <a href="export_orders.php?type=excel" class="btn-export">📊 Export Excel</a>
        <a href="export_orders.php?type=pdf" class="btn-export">📄 Export PDF</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Total Price</th>
                <th>Delivery Person</th>
                <th>Delivery Phone</th>
                <th>Salesperson</th>
                <th>Sent Out At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($order = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $order['id'] ?></td>
                <td><?= $order['customer_name'] ?></td>
                <td><?= $order['address'] ?></td>
                <td><?= $order['phone_no'] ?></td>
                <td>₦<?= number_format($order['total_price'], 2) ?></td>
                <td><?= $order['delivery_person_name'] ?></td>
                <td><?= $order['delivery_person_phone'] ?></td>
                <td><?= $order['salesperson_name'] ?></td>
                <td><?= $order['sent_out_at'] ?></td>
                <td>
                    <form method="POST" action="delete_order.php" onsubmit="return confirm('Delete this order?');">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
