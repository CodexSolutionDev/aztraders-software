<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: /");
    exit;
}

$db = new mysqli("", "", "", "");
if ($db->connect_error) {
    die("DB Connection failed: " . $db->connect_error);
}

// Allowed tables
$allowedTables = [];
$result = $db->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $allowedTables[] = $row[0];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = $_POST['table_name'] ?? '';
    $data = $_POST['data'] ?? [];
    $username = $db->real_escape_string($_POST['username'] ?? '');

    // Security check
    if (!in_array($table, $allowedTables)) {
        die("Invalid table selected.");
    }

    if (!is_array($data)) {
        die("Invalid data format.");
    }

    foreach ($data as $row) {
        $date = $db->real_escape_string($row['date'] ?? '');
        $detail = $db->real_escape_string($row['detail'] ?? '');
        $qty = (float) ($row['qty'] ?? 0);
        $rate = (float) ($row['rate'] ?? 0);
        $amount = (float) ($row['amount'] ?? 0);

        $query = "INSERT INTO `$table` (`username`,`date`, `detail`, `qty`, `rate`, `amount`) 
                  VALUES ('$username','$date', '$detail', $qty, $rate, $amount)";
        
        if (!$db->query($query)) {
            die("MySQL Error: " . $db->error . " in query: " . $query);
        }
    }

    header("Location: /expense");
    exit;
}
?>
