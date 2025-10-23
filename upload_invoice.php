<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['invoiceFile']) || !isset($_POST['invoiceNumber'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        exit;
    }

    $invoiceNumber = $_POST['invoiceNumber'];
    $uploadDir = __DIR__ . '/Img/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileTmpPath = $_FILES['invoiceFile']['tmp_name'];
    $fileName = basename($_FILES['invoiceFile']['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['success' => false, 'error' => 'Invalid file type']);
        exit;
    }

    // Replace slashes or other chars in invoiceNumber to safe filename
    $safeInvoiceNumber = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $invoiceNumber);
    $newFileName = $safeInvoiceNumber . '.' . $fileExtension;

    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
