<?php
// get_new_invoice.php
require 'db_connection.php';

function generateUniqueInvoiceNumber($conn) {
    for ($i = 0; $i < 1000; $i++) {
        $number = rand(1000, 9999);
        $invoice = 'INV/' . $number;

        $stmt = $conn->prepare("SELECT COUNT(*) FROM invoices WHERE invoice_number = ?");
        $stmt->bind_param("s", $invoice);
        $stmt->execute();
        $stmt->bind_result($exists);
        $stmt->fetch();
        $stmt->close();

        if ($exists == 0) {
            $stmt = $conn->prepare("INSERT INTO invoices (invoice_number) VALUES (?)");
            $stmt->bind_param("s", $invoice);
            $stmt->execute();
            $stmt->close();

            return $invoice;
        }
    }

    return "INV/XXXX"; // fallback
}

header('Content-Type: application/json');
$newInvoiceNumber = generateUniqueInvoiceNumber($conn);
echo json_encode(['invoice_number' => $newInvoiceNumber]);
?>
