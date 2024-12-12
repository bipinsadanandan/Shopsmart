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
$stmt = $conn->prepare("SELECT id, product_name, product_price, quantity FROM cart WHERE username = ?");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();

$total_amount = 0;
$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $item_total = $row['product_price'] * $row['quantity'];
    $total_amount += $item_total;
    $cart_items[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
    // Capture payment details
    $payment_mode = $_POST['payment_mode'];
    $card_holder_name = $_POST['card_holder_name'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $upi_id = $_POST['upi_id'] ?? '';

    // Begin transaction
    $conn->begin_transaction();

    try {
        foreach ($cart_items as $item) {
            $product_name = $item['product_name'];
            $product_price = $item['product_price'];
            $quantity = $item['quantity'];
            $total_price = $product_price * $quantity;

            // Insert order details into orders table
            $order_stmt = $conn->prepare("INSERT INTO orders (username, product_name, product_price, quantity, total_price, payment_mode, card_holder_name, card_number, cvv, expiry_date, upi_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $order_stmt->bind_param("ssdidssssss", $user_name, $product_name, $product_price, $quantity, $total_price, $payment_mode, $card_holder_name, $card_number, $cvv, $expiry_date, $upi_id);
            $order_stmt->execute();
            $order_stmt->close();
        }

        // Clear user's cart after order is placed
        $clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE username = ?");
        $clear_cart_stmt->bind_param("s", $user_name);
        $clear_cart_stmt->execute();
        $clear_cart_stmt->close();

        // Commit transaction
        $conn->commit();
        echo "<script>alert('Payment successful! Your order has been placed.'); window.location.href='welcome.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Payment failed. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #4CAF50, #81C784);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .payment-container {
            background-color: #fff;
            padding: 30px;
            width: 60%;
            max-width: 600px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }
        h1 {
            color: #444;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .total-row {
            font-weight: bold;
            color: #333;
        }
        .btn-pay {
            display: block;
            width: 100%;
            padding: 14px;
            border: none;
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
            cursor: pointer;
            border-radius: 4px;
            text-align: center;
            margin-top: 15px;
        }
        .btn-pay:hover {
            background-color: #45a049;
        }
        .payment-modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            margin: 5% auto;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            position: relative;
        }
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            right: 15px;
            top: 10px;
            background: transparent;
            border: none;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .input-group {
            margin-bottom: 15px;
        }
        label, input {
            display: block;
            width: 100%;
        }
        select, input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>Order Summary</h1>
        <table>
            <tr>
                <th>Item Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </tr>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3">Grand Total</td>
                <td>$<?php echo number_format($total_amount, 2); ?></td>
            </tr>
        </table>

        <form method="POST">
            <label for="payment_mode">Select Payment Mode</label><br>
            <select name="payment_mode" id="payment_mode" required>
                <option value="">Select Payment Mode</option>
                <option value="upi">UPI</option>
                <option value="card">Debit/Credit Card</option>
            </select>
            <br><br>

            <!-- UPI Payment Modal -->
            <div id="upi-modal" class="payment-modal">
                <div class="modal-content">
                    <button class="close" id="close-upi-modal">&times;</button>
                    <label for="upi_id">Enter UPI ID</label>
                    <input type="text" name="upi_id" id="upi_id" placeholder="Enter your UPI ID" required><br><br>
                    <button type="submit" class="btn-pay" name="pay_now">Pay Now</button>
                </div>
            </div>

            <!-- Card Payment Fields -->
            <div id="card-details" style="display:none;">
                <div class="input-group">
                    <label for="card_holder_name">Card Holder Name</label>
                    <input type="text" name="card_holder_name" placeholder="Card Holder Name">
                </div>
                <div class="input-group">
                    <label for="card_number">Card Number</label>
                    <input type="text" name="card_number" placeholder="Card Number">
                </div>
                <div class="input-group">
                    <label for="cvv">CVV</label>
                    <input type="text" name="cvv" placeholder="CVV">
                </div>
                <div class="input-group">
                    <label for="expiry_date">Expiry Date</label>
                    <input type="text" name="expiry_date" placeholder="MM/YY">
                </div>
                <button type="submit" class="btn-pay" name="pay_now">Confirm Payment</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('payment_mode').addEventListener('change', function() {
            const cardDetails = document.getElementById('card-details');
            const upiModal = document.getElementById('upi-modal');
            if (this.value === 'card') {
                cardDetails.style.display = 'block';
                upiModal.style.display = 'none';
            } else if (this.value === 'upi') {
                cardDetails.style.display = 'none';
                upiModal.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
                upiModal.style.display = 'none';
            }
        });

        document.getElementById('close-upi-modal').addEventListener('click', function() {
            document.getElementById('upi-modal').style.display = 'none';
        });
    </script>
</body>
</html>
