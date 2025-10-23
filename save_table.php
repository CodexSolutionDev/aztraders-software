<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: /");
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

// Check POST fields
if (
    !isset($_POST['sales_description'], $_POST['sales_qty'], $_POST['sales_rate'], $_POST['sales_gst'], $_POST['sales_amount']) ||
    !isset($_POST['labour_description'], $_POST['labour_qty'], $_POST['labour_rate'], $_POST['labour_pst'], $_POST['labour_amount'])
) {
    die("Missing or invalid POST data");
}

// Extract sales arrays
$sales_desc   = $_POST['sales_description'];
$sales_qty    = $_POST['sales_qty'];
$sales_rate   = $_POST['sales_rate'];
$sales_gst    = $_POST['sales_gst'];
$sales_amount = $_POST['sales_amount'];

// Extract labour arrays
$labour_desc   = $_POST['labour_description'];
$labour_qty    = $_POST['labour_qty'];
$labour_rate   = $_POST['labour_rate'];
$labour_pst    = $_POST['labour_pst'];
$labour_amount = $_POST['labour_amount'];

// Generate invoice ID
$sequence_key = 'global';
$seq_stmt = $conn->prepare("SELECT last_seq FROM invoice_sequence WHERE date = ?");
$seq_stmt->bind_param("s", $sequence_key);
$seq_stmt->execute();
$seq_stmt->bind_result($last_seq);
$found = $seq_stmt->fetch();
$seq_stmt->close();

if ($found) {
    $new_seq = $last_seq + 1;
    $update_stmt = $conn->prepare("UPDATE invoice_sequence SET last_seq = ? WHERE date = ?");
    $update_stmt->bind_param("is", $new_seq, $sequence_key);
    $update_stmt->execute();
    $update_stmt->close();
} else {
    $new_seq = 1;
    $insert_stmt = $conn->prepare("INSERT INTO invoice_sequence (date, last_seq) VALUES (?, ?)");
    $insert_stmt->bind_param("si", $sequence_key, $new_seq);
    $insert_stmt->execute();
    $insert_stmt->close();
}

$seq_str = str_pad($new_seq, 4, '0', STR_PAD_LEFT);
$invoice_id = 'inv_' . $seq_str;

// Create invoice table
$create_table_sql = "CREATE TABLE `$invoice_id` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('sales', 'labour') NOT NULL,
    description VARCHAR(255),
    qty INT,
    rate DECIMAL(10,2),
    gst DECIMAL(10,2),
    pst DECIMAL(10,2),
    amount DECIMAL(10,2)
)";
if (!$conn->query($create_table_sql)) {
    die("Error creating invoice table: " . $conn->error);
}

// Prepare insert statement
$stmt = $conn->prepare("INSERT INTO `$invoice_id` (type, description, qty, rate, gst, pst, amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ssidddd", $type, $description, $qty, $rate, $gst, $pst, $amount);

// Insert sales rows
for ($i = 0; $i < count($sales_desc); $i++) {
    if (
        trim($sales_desc[$i]) !== '' &&
        is_numeric($sales_qty[$i]) &&
        is_numeric($sales_rate[$i]) &&
        is_numeric($sales_gst[$i]) &&
        is_numeric($sales_amount[$i])
    ) {
        $type = 'sales';
        $description = trim($sales_desc[$i]);
        $qty = (int)$sales_qty[$i];
        $rate = (float)$sales_rate[$i];
        $gst = (float)$sales_gst[$i];
        $pst = 0.0;
        $amount = (float)$sales_amount[$i];
        $stmt->execute();
    }
}

// Insert labour rows
for ($i = 0; $i < count($labour_desc); $i++) {
    if (
        trim($labour_desc[$i]) !== '' &&
        is_numeric($labour_qty[$i]) &&
        is_numeric($labour_rate[$i]) &&
        is_numeric($labour_pst[$i]) &&
        is_numeric($labour_amount[$i])
    ) {
        $type = 'labour';
        $description = trim($labour_desc[$i]);
        $qty = (int)$labour_qty[$i];
        $rate = (float)$labour_rate[$i];
        $gst = 0.0;
        $pst = (float)$labour_pst[$i];
        $amount = (float)$labour_amount[$i];
        $stmt->execute();
    }
}
$stmt->close();

// Log invoice
$log_stmt = $conn->prepare("INSERT INTO invoices_log (invoice_id, created_at) VALUES (?, NOW())");
if ($log_stmt) {
    $log_stmt->bind_param("s", $invoice_id);
    $log_stmt->execute();
    $log_stmt->close();
} else {
    die("Error logging invoice: " . $conn->error);
}

echo "Invoice saved in table: " . $invoice_id;
exit;

?>
