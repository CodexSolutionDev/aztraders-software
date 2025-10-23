<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: /");
    exit;
}

// --- AZ Database connection ---
$host = '';
$dbname = '';
$username = '';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $job = $_POST['designation'] ?? '';
    $ntn = $_POST['ntn'] ?? '';

    // Insert into customers table
    $stmt = $pdo->prepare("INSERT INTO customers 
        (name, email, phone, designation, ntn)
        VALUES 
        (:name, :email, :phone, :designation, :ntn)");

    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':designation' => $job,
        ':ntn' => $ntn
    ]);

    // Create sanitized table name
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($name));

    // Create summary table in AZ database
    $createAzTableSQL = "CREATE TABLE IF NOT EXISTS `$tableName` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100),
        invoice_number VARCHAR(255),
        bill_amount DECIMAL(10,2),
        cheque_amount DECIMAL(10,2),
        total_gst DECIMAL(10,2),
        gst_1_5 DECIMAL(10,2),
        remaining_gst DECIMAL(10,2),
        vendor_percent VARCHAR(255),
        cashier_percent DECIMAL(10,2),
        ag_percent DECIMAL(10,2),
        office_percent DECIMAL(10,2),
        balance DECIMAL(10,2),
        firm VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;";
    $pdo->exec($createAzTableSQL);

    // --- EXPENSE database connection ---
    $expenseDb = new mysqli("127.0.0.1", "u222423469_expense", "dVnB9tHuLrrGSt@", "u222423469_expense");
    if ($expenseDb->connect_error) {
        throw new Exception("Expense DB connection failed: " . $expenseDb->connect_error);
    }

    // Create raw expense table in expense DB
    $createExpenseTableSQL = "CREATE TABLE IF NOT EXISTS `$tableName` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100),
        date DATE,
        detail VARCHAR(255),
        qty INT,
        rate DECIMAL(10,2),
        amount DECIMAL(10,2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;";

    if (!$expenseDb->query($createExpenseTableSQL)) {
        throw new Exception("Expense DB table creation failed: " . $expenseDb->error);
    }

    // ✅ Redirect on success
    header("Location: https://inventory.aztraderss.com/department");
    exit;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
