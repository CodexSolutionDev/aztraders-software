<?php
// create_pass_hash.php  — run this ONCE, then delete this file
// Usage: open in browser once or run `php create_pass_hash.php` from command line

$plain = ''; // your password (only used here, this file should be deleted after use)

// Recommended path: outside web root. Adjust if needed.
$hashDir = __DIR__ . '/../private';
$hashFile = $hashDir . '/pass_hash.txt';

if (!is_dir($hashDir)) {
    if (!mkdir($hashDir, 0750, true)) {
        die("Failed to create directory: $hashDir");
    }
}

// create hash
$hash = password_hash($plain, PASSWORD_BCRYPT);

if (file_put_contents($hashFile, $hash) === false) {
    die("Failed to write hash file: $hashFile");
}

chmod($hashFile, 0600);

echo "Hash file created at: $hashFile\n";
echo "NOW: Delete this create_pass_hash.php file for security.\n";
