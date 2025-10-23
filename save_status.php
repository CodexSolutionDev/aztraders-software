<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: /");
    exit;
}

$file = __DIR__ . '/users.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statuses = $_POST['status'] ?? [];
    $emails = $_POST['email'] ?? [];

    // Read current users
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $newLines = [];

    foreach ($lines as $line) {
        $parts = explode('|', $line);
        if (count($parts) < 4) continue;

        $email = $parts[1];
        $key = array_search($email, $emails);

        if ($key !== false && isset($statuses[$key])) {
            // Replace status with updated value
            $parts[3] = $statuses[$key];
        }

        $newLines[] = implode('|', $parts);
    }

    // Save updated data back to file
    file_put_contents($file, implode(PHP_EOL, $newLines));

    header("Location: /staff.php"); // Change to your actual page URL
    exit;
}
?>
