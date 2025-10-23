<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    $uploadDir = __DIR__ . '/pdf/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $tmpName = $_FILES['pdf']['tmp_name'];
    $fileName = basename($_FILES['pdf']['name']);
    $destination = $uploadDir . $fileName;

    if (move_uploaded_file($tmpName, $destination)) {
        echo "PDF saved to /pdf/ as $fileName";
    } else {
        echo "Error saving PDF.";
    }
} else {
    echo "No file received.";
}
?>
