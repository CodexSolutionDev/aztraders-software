<?php
include "db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id']);

$fields = [
  "bill_amount", "cheque_amount", "total_gst", "gst_1_5", "remaining_gst",
  "vendor_percent", "cashier_percent", "ag_percent", "office_percent", "balance"
];

$updates = [];
$params = [];
$types = "";

foreach ($fields as $field) {
  if (isset($data[$field])) {
    $updates[] = "$field = ?";
    $params[] = $data[$field];
    $types .= "s";
  }
}
$params[] = $id;
$types .= "i";

$sql = "UPDATE your_table_name SET " . implode(", ", $updates) . " WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
  echo "success";
} else {
  echo "error";
}
