<?php
$conn = new mysqli('localhost', 'root', '', 'shopsmart',3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$type = $_GET['type'];

if ($type === 'user') {
    $result = $conn->query("SELECT * FROM users");
    echo "<table><tr><th>name</th><th>email</th><th>password</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['password']}</td></tr>";
    }
    echo "</table>";
}elseif ($type === 'orders') {
    $result = $conn->query("SELECT * FROM orders");
    
    // Display table headers matching the schema fields
    echo "<table>
            <tr>
                <th>Order ID</th>
                <th>Username</th>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Payment Mode</th>
                <th>Card Holder Name</th>
                <th>Card Number</th>
                <th>CVV</th>
                <th>Expiry Date</th>
                <th>UPI ID</th>
                <th>Order Date</th>
            </tr>";
    
    // Loop through the results and display each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['username']}</td>
                <td>{$row['product_name']}</td>
                <td>{$row['product_price']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['total_price']}</td>
                <td>{$row['payment_mode']}</td>
                <td>{$row['card_holder_name']}</td>
                <td>{$row['card_number']}</td>
                <td>{$row['cvv']}</td>
                <td>{$row['expiry_date']}</td>
                <td>{$row['upi_id']}</td>
                <td>{$row['order_date']}</td>
              </tr>";
    }
    
    echo "</table>";}

$conn->close();
?>
