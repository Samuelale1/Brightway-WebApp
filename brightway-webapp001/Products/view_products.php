<?php
require '../includes/db.php';
require '../includes/auth.php';
requireLogin(['admin', 'salesperson']);

// Pagination Setup
$limit = 10; // Products per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search Filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchQuery = '';
$params = [];

// Build search query
if (!empty($search)) {
    $searchQuery = "WHERE (p.deleted_at IS NULL) AND (p.name LIKE ? OR p.category LIKE ?)";
    $searchWildcard = '%' . $search . '%';
    $params = [$searchWildcard, $searchWildcard];
}

// Count total products for pagination
$countSql = "SELECT COUNT(*) AS total FROM products p $searchQuery";
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param('ss', ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$totalProducts = $countResult['total'];
$totalPages = ceil($totalProducts / $limit);

// Final SQL with limit
$sql = "SELECT p.*, u.name AS creator_name 
        FROM products p 
        LEFT JOIN users u ON p.created_by = u.id 
        $searchQuery
        ORDER BY p.created_at DESC
        LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param('ss', ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
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
        .actions{
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
    </style>
</head>
<body>
    
    <h2>All Products</h2>

<form method="GET" style="margin-bottom: 20px;">
    <input type="text" name="search" placeholder="Search by name or category..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; width: 300px;">
    <button type="submit" style="padding: 8px 12px; background: #EC6408; color: white; border: none;">Search</button>
</form>


    <table>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price (₦)</th>
            <th>Category</th>
            <th>Added By</th>
            <th>Date</th>
            <th>Actions</th>
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
                    <td class="actions">
                   <?php if (isAdmin() || $_SESSION['user_id'] == $row['created_by']): ?>
     <?php if (is_null($row['deleted_at'])): ?>
         <a href="../Products/edit_product.php?id=<?php echo $row['id']; ?>" style="margin-right: 10px;">✏️ Edit</a>
         <a href="../Products/delete_product.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">❌ Delete</a>
    <?php else: ?>
            <a href="restore_product.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Restore this product?')">♻️ Undo Delete</a>
        <?php endif; ?>
    <?php else: ?>
    -
    <?php endif; ?>

                    </td>

                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No products found.</td>
            </tr>
        <?php endif; ?>
    </table>


    <div style="margin-top: 20px;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>" style="padding: 6px 12px; margin: 2px; border: 1px solid #ccc; <?php if ($i == $page) echo 'background: #EC6408; color: white;'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>

</body>
</html>
