<?php
require '../includes/db.php';      
require '../includes/auth.php';   
requireLogin(['admin', 'salesperson']); 

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $price = $_POST['price'];
    $category = htmlspecialchars($_POST['category']);
    $userId = $_SESSION['user_id']; // Get who is adding the product

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target_dir = "../assets/images/products/";
    $target_file = $target_dir . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Prepare to insert the product with user ID
        $created_by = $_SESSION['user_id'];
        $now = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO products (name, price, category, image, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssis", $name, $price, $category, $image, $created_by, $now);



        if ($stmt->execute()) {
            $message = "✅ Product added successfully!";
        } else {
            $message = "❌ Database error: " . $stmt->error;
        }
    } else {
        $message = "❌ Error uploading image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="assets/Css/Style.css">
</head>
<body style="font-family: sans-serif;">
    <h2>Add New Product</h2>

    <?php if ($message): ?>
        <p style="color: <?php echo (strpos($message, 'success') !== false) ? 'green' : 'red'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Product Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Price (₦):</label><br>
        <input type="number" step="0.01" name="price" required><br><br>

        <label>Category:</label><br>
        <input type="text" name="category"><br><br>

        <label>Product Image:</label><br>
        <input type="file" name="image" accept="image/*" required><br><br>

        <button type="submit">Add Product</button>
    </form>
</body>
</html>
