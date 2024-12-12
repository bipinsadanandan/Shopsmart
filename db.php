<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "shopsmart";

$conn = new mysqli($host, $user, $pass, $db,3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
