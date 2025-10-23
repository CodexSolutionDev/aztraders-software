<?php
// config.php
// Place this file in your project root and include it where DB access is needed.
// Edit the DB_* constants below with your own database credentials.

declare(strict_types=1);

session_start();

// --- Configuration (change these) ---
define('DB_HOST', '');      // or 'localhost'
define('DB_USER', '');  // your DB username
define('DB_PASS', ''); // your DB password
define('DB_NAME', '');  // your DB name
define('DB_PORT', );             // optional: change if not default

// Optional: set default timezone
date_default_timezone_set('Asia/Karachi');

// Optional: show errors in development (set false in production)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// --- MySQLi connection (use $conn in your scripts) ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if ($conn->connect_errno) {
    // In production, avoid echoing DB errors. Log them instead.
    error_log("DB connect error: (" . $conn->connect_errno . ") " . $conn->connect_error);
    die("Database connection failed. Please check configuration.");
}

// Set charset to utf8mb4
if (! $conn->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $conn->error);
}

// --- PDO (optional) ---
try {
    $pdoDsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=utf8mb4";
    $pdo = new PDO($pdoDsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // If you prefer not to use PDO, this is safe to ignore; mysqli is available.
    error_log("PDO connect failed: " . $e->getMessage());
    $pdo = null;
}

// --- Helper: check if table exists (safe) ---
function tableExists(mysqli $conn, string $tableName): bool {
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName); // sanitize
    $res = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($tableName) . "'");
    if (! $res) return false;
    return $res->num_rows > 0;
}

// --- (Optional) Helper: safe redirect ---
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

?>
