<?php
require '../includes/db.php';
require '../includes/auth.php';

// Only admin and salesperson should access this page
requireLogin(['admin', 'salesperson']);

// Fetch products and join with user table to get creator name
$sql = "SELECT p.*, u.name AS creator_name 
        FROM products p 
        LEFT JOIN users u ON p.created_by = u.id 
        ORDER BY p.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Products</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f8f8f8;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background: #EC6408;
            color: #fff;
        }
        img {
            width: 60px;
            height: 60px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <h2>All Products</h2>

    <table>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price (₦)</th>
            <th>Category</th>
            <th>Added By</th>
            <th>Date</th>
        </tr>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php $counter = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><img src="../assets/images/products/<?php echo $row['image']; ?>" alt="Product Image"></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>₦<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['creator_name']); ?></td>
                    <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No products found.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
