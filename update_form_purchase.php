<?php
include 'purchaseconnection.php';

// JSON input le raha hai
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  echo "no_data";
  exit;
}

// Table name GET se le lo (because JS se ?table=... ja raha hai)
$table = $_GET['table'] ?? '';
$id = $data['id'] ?? '';

if (!$table || !$id) {
  echo "missing_table_or_id";
  exit;
}

// id aur table remove nahi karni ki zarurat — just safety
unset($data['id']);

// Update SET clause build karte hain
$set = [];
foreach ($data as $key => $val) {
  $set[] = "`$key` = '" . $conn->real_escape_string($val) . "'";
}

$sql = "UPDATE `$table` SET " . implode(", ", $set) . " WHERE id = " . intval($id);

if ($conn->query($sql)) {
  echo "success";
} else {
  echo "error: " . $conn->error;
}
?>
