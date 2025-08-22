<?php
require '../includes/db.php';      
require '../includes/auth.php';   
requireLogin(['admin', 'salesperson']); 

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $price = $_POST['price'];
    $category = htmlspecialchars($_POST['category']);
    $quantity =  $_POST['quantity'];
    $userId = $_SESSION['user_id']; // Get who is adding the product

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target_dir = "../assets/images/products/";
    $target_file = $target_dir . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Add the current date to sql 
        $now = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO products (name, price, category, image, created_by, created_at, quantity) VALUES (?, ?, ?, ?, ?, ?, ? )");
        $stmt->bind_param("sdssiss", $name, $price, $category, $image, $userId, $now, $quantity);


        if ($stmt->execute()) {
            /* 
            * This is where i have to change to a modal that says successfully added to database.
            */
            $message = "✅ Product added successfully!";
        } else {
            /* 
            * This is also where i handle the error ui
            */
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
    <link rel="icon" type="image/x-icon" href="images/Brightway-logo.png">
    <title>Add Product</title>
    <link rel="stylesheet" href="brightway-webapp001\assets\Css\Style.css">
    <link rel="icon" type="image/x-icon" href="..\assets\images\others\Brightway-logo.png">
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

        <label>Quantity Available:</label><br>
        <input type="text" name="quantity"><br><br>

        <button type="submit">Add Product</button>
    </form>
</body>
</html>
