<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = file_get_contents($_FILES['image']['tmp_name']);

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'shopsmart',3306);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO products (name, price, category, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $price, $category, $image);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    
    echo "Product added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page - Add Products</title>
</head>
<body>
    <h1>Add New Product</h1>
    <form action="admin.php" method="POST" enctype="multipart/form-data">
        <label for="name">Item Name:</label>
        <input type="text" name="name" required><br><br>

        <label for="price">Item Price:</label>
        <input type="number" step="0.01" name="price" required><br><br>

        <label for="category">Item Category:</label>
        <input type="text" name="category" required><br><br>

        <label for="image">Item Image:</label>
        <input type="file" name="image" accept="image/*" required><br><br>

        <button type="submit">Submit</button>
    </form>
    <a href="welcome.php">Go to Welcome Page</a>
</body>
</html>
