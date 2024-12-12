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

// Fetch user's cart items
$user_name = $_SESSION['user'];
$stmt = $conn->prepare("SELECT id, product_name, product_price, product_category, quantity FROM cart WHERE username = ?");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();

// Handle update, delete, and payment requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $new_quantity = $_POST['quantity'];
        $product_id = $_POST['product_id'];
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $new_quantity, $product_id);
        $update_stmt->execute();
        $update_stmt->close();
        header("Location: cart.php");
    }

    if (isset($_POST['delete_item'])) {
        $product_id = $_POST['product_id'];
        $delete_stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
        $delete_stmt->bind_param("i", $product_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        header("Location: cart.php");
    }

    if (isset($_POST['move_for_payment'])) {
        // Insert each item into orders table for payment
        $result->data_seek(0); // Reset result pointer
        while ($row = $result->fetch_assoc()) {
            $product_name = $row['product_name'];
            $product_price = $row['product_price'];
            $quantity = $row['quantity'];
            $total_price = $product_price * $quantity;

            $insert_stmt = $conn->prepare("INSERT INTO orders (username, product_name, product_price, quantity, total_price) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("ssdii", $user_name, $product_name, $product_price, $quantity, $total_price);
            $insert_stmt->execute();
        }
        header("Location: payment.php"); // Redirect to payment page
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        /* Basic styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            text-align: center;
            padding: 20px;
            background-color: #3f51b5;
            color: #fff;
            font-size: 1.5em;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .total {
            font-weight: bold;
        }
        .btn-update, .btn-delete, .btn-payment {
            padding: 8px 16px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .btn-update {
            background-color: #4CAF50;
        }
        .btn-delete {
            background-color: #f44336;
        }
        .btn-payment {
            background-color: #ff9800;
            display: block;
            width: 200px;
            margin: 20px auto;
        }
        .btn-update:hover {
            background-color: #45a049;
        }
        .btn-delete:hover {
            background-color: #e53935;
        }
        .btn-payment:hover {
            background-color: #fb8c00;
        }
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            width: 300px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .popup .close-btn {
            cursor: pointer;
            color: #ff5a5a;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
    <script>
        function showPopup() {
            document.getElementById("popup").style.display = "flex";
        }
        function hidePopup() {
            document.getElementById("popup").style.display = "none";
        }
    </script>
</head>
<body>
    <header>
        <h1>Your Cart, <?php echo $_SESSION['user']; ?>!</h1>
    </header>

    <table>
        <tr>
            <th>Item Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Actions</th>
        </tr>
        <?php
        $grand_total = 0;
        while ($row = $result->fetch_assoc()) {
            $product_price = (float)$row['product_price'];
            $total_price = $product_price * $row['quantity'];
            $grand_total += $total_price;
            echo "<tr>
                    <td>{$row['product_name']}</td>
                    <td>{$row['product_category']}</td>
                    <td>\${$product_price}</td>
                    <td>
                        <form method='POST' style='display:inline-block;'>
                            <input type='number' name='quantity' value='{$row['quantity']}' min='1' style='width: 60px; text-align: center;'>
                            <input type='hidden' name='product_id' value='{$row['id']}'>
                            <button type='submit' name='update_quantity' class='btn-update'>Update</button>
                        </form>
                    </td>
                    <td>\${$total_price}</td>
                    <td>
                        <form method='POST' style='display:inline-block;'>
                            <input type='hidden' name='product_id' value='{$row['id']}'>
                            <button type='submit' name='delete_item' class='btn-delete'>Delete</button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
        <tr>
            <td colspan="4" class="total">Grand Total</td>
            <td class="total">$<?php echo number_format($grand_total, 2); ?></td>
            <td></td>
        </tr>
    </table>

    <form method="POST" onsubmit="showPopup(); return false;">
        <button type="submit" name="move_for_payment" class="btn-payment">Move for Payment</button>
    </form>

    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="hidePopup()">&times;</span>
            <h2>Proceed to Payment</h2>
            <p>All items have been moved to the payment section. You’ll be redirected shortly.</p>
            <form method="POST">
                <button type="submit" name="move_for_payment" class="btn-payment">Confirm</button>
            </form>
        </div>
    </div>
</body>
</html>
