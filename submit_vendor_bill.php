<?php
// submit_vendor_bill.php

// Database connection (mysqli example)
$conn = new mysqli('', '', '', '');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form data lena
$invoice_number = $_POST['invoice_number']; // invoice number
$vendor = $_POST['vendor'];
$tax_id = $_POST['tax_id'];
$bill_reference = $_POST['bill_reference'];
$bill_date = $_POST['bill_date'];
$accounting_date = $_POST['accounting_date'];
$due_date = $_POST['due_date'];

// Insert query example
$sql = "INSERT INTO vendor_bills (invoice_number, vendor, tax_id, bill_reference, bill_date, accounting_date, due_date)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $invoice_number, $vendor, $tax_id, $bill_reference, $bill_date, $accounting_date, $due_date);

if ($stmt->execute()) {
    echo "Bill saved successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
