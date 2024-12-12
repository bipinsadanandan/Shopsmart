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

// Get the item ID from the URL
$item_id = $_GET['id'];

// Prepare and execute deletion query
$sql = "DELETE FROM cart WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();

$stmt->close();
$conn->close();

// Redirect back to the cart page
header("Location: cart.php");
exit();
?>
