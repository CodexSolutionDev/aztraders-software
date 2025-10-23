<?php
$host = "";      // Database server
$dbname = ""; // Aapka database name
$username = "";   // Database username
$password = "";   // Database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Error mode set karna taake problems easily trace ho
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
