<?php
$host = '';
$dbname = '';
$username = '';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM customers LIMIT 1");
    $firstRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $columns = $firstRow ? array_keys($firstRow) : ['name', 'email', 'phone', 'company', 'street', 'city']; // fallback columns

    // Now get all rows
    $stmt = $pdo->query("SELECT * FROM customers");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $customers = [];
    $columns = ['name', 'email', 'phone', 'company', 'street', 'city'];
}
?>
