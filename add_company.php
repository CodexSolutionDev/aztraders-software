<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: /");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = $_POST['company_name'];

    // Sanitize table name
    $table_name = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($company_name));

    // Database connection
    $conn = new mysqli("127.0.0.1", "u222423469_expense", "dVnB9tHuLrrGSt@", "u222423469_expense"); // Update database name

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create table query
$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
    sr_no INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    detail VARCHAR(255) NOT NULL,
    qty FLOAT NOT NULL,
    rate FLOAT NOT NULL,
    amount FLOAT NOT NULL
)";


    if ($conn->query($sql) === TRUE) {
        // ✅ Redirect to purchase (expense) page after success
        header("Location: /expense");
        exit;
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $conn->close();
}
?>
