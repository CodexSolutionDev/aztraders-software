<?php
session_start();
header('Content-Type: application/json');

// Security check
if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

include 'db_connect.php'; // ye PDO wali main DB connection hai

// Expense DB connection (mysqli)
$expenseDb = new mysqli("", "", "", "");
if ($expenseDb->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Expense DB connection failed: ' . $expenseDb->connect_error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$password = $data['password'] ?? null;

// Password check
if ($password !== "51268") {
    echo json_encode(['success' => false, 'error' => 'Invalid password']);
    exit;
}

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'No ID provided']);
    exit;
}

try {
    // Step 1: Get customer name
    $stmt = $pdo->prepare("SELECT name FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'Customer not found']);
        exit;
    }

    $customerName = trim($row['name']);

    // Step 2: Delete row from customers table
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$id]);

    // Step 3: Normalize customer name → table format
    $normalizedName = preg_replace('/[^a-zA-Z0-9]+/', '_', $customerName);
    $normalizedName = preg_replace('/_+/', '_', $normalizedName);
    $normalizedName = trim($normalizedName, '_');
    $normalizedName = strtolower($normalizedName);

    // Step 4: Check + drop table in BOTH databases
    $droppedTables = [];

    // --- Main DB (PDO) ---
    $stmt = $pdo->query("SHOW TABLES");
    $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($allTables as $tbl) {
        $simplifiedTbl = preg_replace('/_+/', '_', strtolower($tbl));
        if ($simplifiedTbl === $normalizedName ||
            strpos($simplifiedTbl, $normalizedName) !== false ||
            strpos($normalizedName, $simplifiedTbl) !== false) {
            $pdo->exec("DROP TABLE `" . str_replace("`", "``", $tbl) . "`");
            $droppedTables[] = "MainDB: $tbl";
        }
    }

    // --- Expense DB (MySQLi) ---
    $res = $expenseDb->query("SHOW TABLES");
    while ($row = $res->fetch_array()) {
        $tbl = $row[0];
        $simplifiedTbl = preg_replace('/_+/', '_', strtolower($tbl));
        if ($simplifiedTbl === $normalizedName ||
            strpos($simplifiedTbl, $normalizedName) !== false ||
            strpos($normalizedName, $simplifiedTbl) !== false) {
            $expenseDb->query("DROP TABLE `" . $expenseDb->real_escape_string($tbl) . "`");
            $droppedTables[] = "ExpenseDB: $tbl";
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Row deleted. Dropped tables: " . (count($droppedTables) ? implode(', ', $droppedTables) : 'none')
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
