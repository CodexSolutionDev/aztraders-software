<?php
include "db.php"; // apna DB connection include karo

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id']);

$sql = "UPDATE sales_table SET 
  date = ?, 
  detail = ?, 
  qty = ?, 
  rate = ?, 
  amount = ? 
WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
  "ssiddi", 
  $data['date'], 
  $data['detail'], 
  $data['qty'], 
  $data['rate'], 
  $data['amount'], 
  $id
);

if ($stmt->execute()) {
  echo "success";
} else {
  echo "error: " . $conn->error;
}
