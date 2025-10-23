<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';

// Database connection
$conn = new mysqli("", "", "", "");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Handle file upload (optional)
$uploadDir = __DIR__ . '/Img/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$tempFilePath = null;
$fileExtension = null;
$finalFilePath = null;

// === File Upload (datacreated format) ===
// === File Upload (datacreated format with readable date-time) ===
$filePath = null;
if (isset($_FILES['invoiceFile']) && $_FILES['invoiceFile']['error'] == 0) {
    $uploadDir = __DIR__ . '/Img/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // File name format: 2025-10-12 20:53:29.pdf
    $fileExt = pathinfo($_FILES['invoiceFile']['name'], PATHINFO_EXTENSION);
    $newFileName = date('Y-m-d H:i:s') . '.' . $fileExt;
    $newFileName = str_replace(':', '-', $newFileName); // ":" replace so OS allow kare
    $uploadPath = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES['invoiceFile']['tmp_name'], $uploadPath)) {
        $filePath = $uploadPath;
    } else {
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
        exit;
    }
}



// Decode JSON data from FormData
if (!isset($_POST['data'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$input = json_decode($_POST['data'], true);
if (!isset($input['rows']) || !isset($input['selected_table'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$rows = $input['rows'];
$selected_table = preg_replace('/[^a-zA-Z0-9_]/', '', $input['selected_table']);

// Auto-generate image filename using firm name (first row) + incremental suffix
// Auto-generate image filename using current date and time
if ($tempFilePath && $fileExtension && file_exists($tempFilePath)) {
    // Format: YYYY-MM-DD_HH-MM-SS
    $dateCreated = date('Y-m-d_H-i-s');
    $newName = 'invoice_' . $dateCreated . '.' . $fileExtension;
    $finalFilePath = $uploadDir . $newName;

    // Avoid overwrite agar same second me 2 files aayein
    $i = 1;
    while (file_exists($finalFilePath)) {
        $finalFilePath = $uploadDir . 'invoice_' . $dateCreated . "_$i." . $fileExtension;
        $i++;
    }

    if (!rename($tempFilePath, $finalFilePath)) {
        echo json_encode(['success' => false, 'message' => 'File rename failed']);
        exit;
    }
}


// Prepare insert for sales table
$stmt_sales = $conn->prepare("
    INSERT INTO sales (
        username, bill_amount, cheque_amount, total_gst, gst_1_5, remaining_gst,
        vendor_percent, cashier_percent, ag_percent, office_percent, balance, firm
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
if (!$stmt_sales) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed for sales: ' . $conn->error]);
    exit;
}

// Prepare insert for selected table
$stmt_selected = $conn->prepare("
    INSERT INTO `$selected_table` (
        username, bill_amount, cheque_amount, total_gst, gst_1_5, remaining_gst,
        vendor_percent, cashier_percent, ag_percent, office_percent, balance, firm
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
if (!$stmt_selected) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed for selected table: ' . $conn->error]);
    exit;
}

// Insert each row
foreach ($rows as $row) {
    if (count($row) < 12) continue;

    list($username, $bill, $cheque, $totalGst, $gst1_5, $remGst, $vendorpercent, $cashierpercent, $agpercent, $officepercent, $balance, $firm) =
        array_pad($row, 12, null);

    // Sanitize
        $username = $conn->real_escape_string($username);
    $bill = floatval($bill);
    $cheque = floatval($cheque);
    $totalGst = floatval($totalGst);
    $gst1_5 = floatval($gst1_5);
    $remGst = floatval($remGst);
    $vendorpercent = $conn->real_escape_string($vendorpercent);
    $cashierpercent = floatval($cashierpercent);
    $agpercent = floatval($agpercent);
    $officepercent = floatval($officepercent);
    $balance = floatval($balance);
    $firm = $conn->real_escape_string($firm);

    // Insert into sales
    $stmt_sales->bind_param(
        "sdddddsdddds",
        $username, $bill, $cheque, $totalGst, $gst1_5, $remGst,
        $vendorpercent, $cashierpercent, $agpercent, $officepercent, $balance, $firm
    );
    if (!$stmt_sales->execute()) {
        echo json_encode(['success' => false, 'message' => 'Insert failed in sales: ' . $stmt_sales->error]);
        exit;
    }

    // Insert into selected table
    $stmt_selected->bind_param(
        "sdddddsdddds",
        $username, $bill, $cheque, $totalGst, $gst1_5, $remGst,
        $vendorpercent, $cashierpercent, $agpercent, $officepercent, $balance, $firm
    );
    if (!$stmt_selected->execute()) {
        echo json_encode(['success' => false, 'message' => 'Insert failed in selected table: ' . $stmt_selected->error]);
        exit;
    }
}

// Cleanup
$stmt_sales->close();
$stmt_selected->close();
$conn->close();

echo json_encode(['success' => true]);
