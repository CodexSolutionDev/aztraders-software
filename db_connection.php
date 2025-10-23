<?php
// db_connection.php

$servername = "";   // Usually localhost
$username = "";  // Replace with your DB username
$password = "";  // Replace with your DB password
$dbname = "";    // Replace with your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set to utf8mb4 for better unicode support
$conn->set_charset("utf8mb4");
?>
