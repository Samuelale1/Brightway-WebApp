<?php
require '../includes/db.php';
require '../includes/auth.php';
requireLogin(); // any logged-in user

// Get product ID from URL
if (!isset($_GET['id'])) {
    echo "Product ID is missing.";
    exit;
}
$id = (int) $_GET['id'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND deleted_at IS NULL");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Product not found.";
    exit;
}

$product = $result->fetch_assoc();

// Only admin or creator can edit
if (!isAdmin() && $_SESSION['user_id'] != $product['added_by']) {
    echo "Access denied.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $imagePath = $product['image']; // default to existing image

    // Optional: handle new image upload
    if (!empty($_FILES['image']['name'])) {
    $uploadDir = '../assets/images/products/';
    $imageName = time() . '_' . basename($_FILES['image']['name']);
    $targetPath = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $imagePath = $imageName;
    }
}


    // Update product
    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, quantity=?, image=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param("sdssi", $name, $price, $quantity, $imagePath, $id);
    $stmt->execute();

    echo "<script>alert('✅ Product updated!'); window.location.href='view_products.php';</script>";
}
?>

<h2>🛠️ Edit Product</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>

    <label>Price:</label><br>
    <input type="number" name="price" value="<?= $product['price'] ?>" step="0.01" required><br><br>

    <label>Quantity:</label><br>
    <input type="number" name="quantity" value="<?= $product['quantity'] ?>" min="0" required><br><br>

    <label>Current Image:</label><br>
    <img src="../assets/images/products/<?= htmlspecialchars($product['image']) ?>" width="100"><br><br>

    <label>Change Image (optional):</label><br>
    <input type="file" name="image"><br><br>

    <button type="submit">✅ Update Product</button>
</form>
