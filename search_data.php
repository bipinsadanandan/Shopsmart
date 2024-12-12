<?php
$conn = new mysqli('localhost', 'root', '', 'shopsmart',3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = $_GET['query'];
$result = $conn->query("SELECT * FROM orders WHERE product_name LIKE '%$query%'");

// Start the HTML table
echo "<table border='1'>
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

// Loop through each row and display the order details
while ($row = $result->fetch_assoc()) {
    $total_price = $row['product_price'] * $row['quantity']; // Calculate total price
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['username']}</td>
        <td>{$row['product_name']}</td>
        <td>{$row['product_price']}</td>
        <td>{$row['quantity']}</td>
        <td>{$total_price}</td>
        <td>{$row['payment_mode']}</td>
        <td>{$row['card_holder_name']}</td>
        <td>{$row['card_number']}</td>
        <td>{$row['cvv']}</td>
        <td>{$row['expiry_date']}</td>
        <td>{$row['upi_id']}</td>
        <td>{$row['order_date']}</td>
    </tr>";
}

// End the table
echo "</table>";


$conn->close();
?>
