<?php
// connect.php
// Database connection settings

$host = "";      // Database host
$user = "";  // Database username
$pass = "";  // Database password
$db   = "";  // Database name

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset to utf8
$conn->set_charset("utf8");

// Now $conn can be used in other PHP files
?>
