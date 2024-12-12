<?php 
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'shopsmart', 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details for cart insertion
$user_name = $_SESSION['user'];

// Sample products array to simulate database items
$products = [
    ["img" => "item1.jpg", "name" => "SAMSUNG S20 ULTRA", "price" => 299, "category" => "Electronics"],
    ["img" => "item2.jpg", "name" => "Boat rokerz 450", "price" => 49, "category" => "Electronics"],
    ["img" => "item3.jpg", "name" => "Apple MAC BOOK PRO", "price" => 599, "category" => "Electronics"],
    ["img" => "item4.jpg", "name" => "Men's Jeans", "price" => 999, "category" => "Clothes"],
    ["img" => "item5.jpg", "name" => "Us polo Women's T-Shirt", "price" => 49, "category" => "Clothes"],
    ["img" => "item6.jpg", "name" => "Puma Sneakers", "price" => 599, "category" => "Shoes"],
    ["img" => "item7.jpg", "name" => "Puma Running Shoes", "price" => 299, "category" => "Shoes"],
    ["img" => "item8.jpg", "name" => "Long life Blender", "price" => 89, "category" => "Home Appliances"],
    ["img" => "item9.jpg", "name" => "LG Microwave Oven", "price" => 120, "category" => "Home Appliances"],
    ["img" => "item10.jpg", "name" => "LG Refrigerator", "price" => 799, "category" => "Home Appliances"],
    ["img" => "item11.jpg", "name" => "Vacuum Cleaner", "price" => 159, "category" => "Home Appliances"],
    ["img" => "item12.jpg", "name" => "LG Rice Cooker", "price" => 59, "category" => "Home Appliances"],
    ["img" => "item13.jpg", "name" => "LG Electric Kettle", "price" => 29, "category" => "Home Appliances"],
    ["img" => "item14.jpg", "name" => "Organic Apples", "price" => 4, "category" => "Groceries"],
    ["img" => "item15.jpg", "name" => "Milk - 1 Gallon", "price" => 3, "category" => "Groceries"],
    ["img" => "item16.jpg", "name" => "Whole Wheat Bread", "price" => 2, "category" => "Groceries"],
    ["img" => "item17.jpg", "name" => "Free-Range Eggs (12)", "price" => 5, "category" => "Groceries"],
    ["img" => "item18.jpg", "name" => "Fresh Salmon Fillet", "price" => 15, "category" => "Groceries"],
    ["img" => "item19.jpg", "name" => "Almonds - 1 lb", "price" => 10, "category" => "Groceries"]
];

// Filter products based on search query or category filter
$search_query = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';

$filtered_products = array_filter($products, function ($product) use ($search_query, $category_filter) {
    $matches_query = empty($search_query) || stripos($product['name'], $search_query) !== false;
    $matches_category = empty($category_filter) || $product['category'] === $category_filter;
    return $matches_query && $matches_category;
});

// Handle add to cart action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_category = $_POST['product_category'];
    $quantity = $_POST['quantity'];

    // Insert into cart table
    $stmt = $conn->prepare("INSERT INTO cart (username, product_name, product_price, product_category, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsi", $user_name, $product_name, $product_price, $product_category, $quantity);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to ShopSmart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
            background-image: url(bc.jpg);
        }
        .container {
            width: 85%;
            max-width: 1200px;
            margin: auto;
        }
        header {
            text-align: center;
            padding: 20px 0;
            background: #444;
            color: #fff;
        }
        .logo {
            width: 100px;
        }
        nav {
            background: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            padding: 5px 15px;
            border-radius: 4px;
        }
        nav a:hover {
            background: #ff9800;
        }
        .search-filter {
            margin: 20px 0;
            padding: 20px;
            background: #e0e0e0;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .products {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            margin-top: 20px;
        }
        .product {
            width: 220px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
            padding: 20px;
            transition: transform 0.3s;
        }
        .product:hover {
            transform: scale(1.05);
        }
        .product img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }
        .product h3 {
            font-size: 1.2em;
            color: #444;
            margin: 10px 0;
        }
        .product p {
            font-weight: bold;
            font-size: 1em;
            color: #ff9800;
        }
        .product button {
            padding: 10px 15px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background 0.3s;
        }
        .product button:hover {
            background: #218838;
        }
        footer {
            text-align: center;
            padding: 20px;
            background: #444;
            color: #fff;
            margin-top: 40px;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            z-index: 10;
        }
        .popup.show {
            display: block;
        }
        .popup h4 {
            color: #444;
        }
        .popup button {
            padding: 10px 15px;
            margin-top: 10px;
            background: #ff9800;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <img src="LOGO.jpg" alt="ShopSmart Logo" class="logo">
        <h1>Welcome, <?php echo $_SESSION['user']; ?>!</h1>
    </header>
    <nav>
        <a href="#">Home</a>
        <a href="about.html">About Us</a>
        <a href="contact.html">Contact Us</a>
        <a href="cart.php">Cart</a>
        <a href="seeorders.php">Order Status</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="search-filter">
        <form method="GET">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_query); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <option value="Electronics" <?php if ($category_filter === "Electronics") echo "selected"; ?>>Electronics</option>
                <option value="Clothes" <?php if ($category_filter === "Clothes") echo "selected"; ?>>Clothes</option>
                <option value="Shoes" <?php if ($category_filter === "Shoes") echo "selected"; ?>>Shoes</option>
                <option value="Home Appliances" <?php if ($category_filter === "Home Appliances") echo "selected"; ?>>Home Appliances</option>
            </select>
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="container">
        <div class="products">
            <?php 
            if (empty($filtered_products)) {
                echo "<p>No products found!</p>";
            } else {
                foreach ($filtered_products as $product) {
                    ?>
                    <div class="product">
                        <img src="pics/<?php echo $product['img']; ?>" alt="<?php echo $product['name']; ?>">
                        <h3><?php echo $product['name']; ?></h3>
                        <p>$<?php echo $product['price']; ?></p>
                        <form method="POST">
                            <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                            <input type="hidden" name="product_category" value="<?php echo $product['category']; ?>">
                            <input type="number" name="quantity" value="1" min="1" required>
                            <button type="submit" name="add_to_cart" onclick="showPopup('<?php echo $product['name']; ?>')">Add to Cart</button>
                        </form>
                    </div>
                    <?php 
                }
            }
            ?>
        </div>
    </div>

    <div class="popup" id="popup">
        <h4>Product added to cart!</h4>
        <button onclick="closePopup()">Close</button>
    </div>

    <footer>
        <p>&copy; 2024 ShopSmart. All rights reserved.</p>
    </footer>

    <script>
        function showPopup(productName) {
            const popup = document.getElementById('popup');
            popup.classList.add('show');
            popup.querySelector('h4').innerText = productName + ' has been added to the cart!';
            setTimeout(closePopup, 3000);
        }

        function closePopup() {
            document.getElementById('popup').classList.remove('show');
        }
    </script>
</body>
</html>
