<?php
session_start();
header('Content-Type: application/json');

// User login check
if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// Request data read karein
$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$id = intval($data['id']);
unset($data['id']); // ID ko update list se hatao

// Database connection directly yahin
$servername = "";
$username   = ""; // yaha apna username daalein
$password   = ""; // yaha apna password daalein
$dbname     = "";

$conn = new mysqli($servername, $username, $password, $dbname);

// Connection error check
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'DB connection failed: ' . $conn->connect_error]);
    exit;
}

// Update query prepare
$updates = [];
foreach ($data as $col => $value) {
    $updates[] = "`" . $conn->real_escape_string($col) . "` = '" . $conn->real_escape_string($value) . "'";
}

if (empty($updates)) {
    echo json_encode(['success' => false, 'error' => 'No data to update']);
    exit;
}

$sql = "UPDATE customers SET " . implode(', ', $updates) . " WHERE id = $id";

// Run query
if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>
