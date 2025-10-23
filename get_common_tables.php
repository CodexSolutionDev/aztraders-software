<?php
$search = isset($_GET['query']) ? $_GET['query'] : '';

$db1 = new mysqli("127.0.0.1", "u222423469_expense", "dVnB9tHuLrrGSt@", "u222423469_expense");
$db2 = new mysqli("127.0.0.1", "u222423469_az", "dVnB9tHuLrrGSt@", "u222423469_az");

function getTables($conn, $search) {
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        if (stripos($row[0], $search) !== false) {
            $tables[] = $row[0];
        }
    }
    return $tables;
}

$tables1 = getTables($db1, $search);
$tables2 = getTables($db2, $search);

$commonTables = array_intersect($tables1, $tables2);

echo json_encode(array_values($commonTables));
