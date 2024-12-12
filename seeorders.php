<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'shopsmart', 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user orders
$user_name = $_SESSION['user'];
$stmt = $conn->prepare("SELECT id, product_name, product_price, quantity, total_price, payment_mode, order_date FROM orders WHERE username = ? ORDER BY order_date DESC");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #1D2671, #C33764);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }
        .container {
            width: 80%;
            max-width: 800px;
            background-color: #ffffff11;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(15px);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
            color: #FFEB3B;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: #fff;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .order-details {
            margin-bottom: 20px;
        }
        .order-id {
            font-weight: bold;
            color: #FFEB3B;
        }
        .date {
            font-size: 0.9em;
            color: #ddd;
        }
        .button-back {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 15px;
            background-color: #FF5722;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .button-back:hover {
            background-color: #FF7043;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Orders</h1>
        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-details">
                    <span class="order-id">Order ID: <?php echo $order['id']; ?></span><br>
                    <span class="date">Date: <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></span>
                    <table>
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Payment Mode</th>
                        </tr>
                        <tr>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td>$<?php echo number_format($order['product_price'], 2); ?></td>
                            <td><?php echo $order['quantity']; ?></td>
                            <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                            <td><?php echo ucfirst($order['payment_mode']); ?></td>
                        </tr>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #ddd; font-size: 1.2em;">No orders found.</p>
        <?php endif; ?>
        <a href="welcome.php" class="button-back">Back to Home</a>
    </div>
</body>
</html>
