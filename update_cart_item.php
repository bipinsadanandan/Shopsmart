<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    die("Unauthorized or missing data");
}

$user_name = $_SESSION['user'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'shopsmart', 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update the quantity in the cart table
$sql = "UPDATE cart SET quantity = ? WHERE id = ? AND username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $quantity, $product_id, $user_name);
$stmt->execute();

$stmt->close();
$conn->close();
?>
