<?php
include 'salesconnection.php';
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  echo "no_data";
  exit;
}

// ✅ ID aur Table name nikaalo
$id = intval($data['id'] ?? 0);
$table = isset($data['table']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $data['table']) : '';

if (!$id || !$table) {
  echo "missing_params";
  exit;
}

unset($data['id'], $data['table']);

// ✅ Update query prepare karo
$set = [];
foreach ($data as $key => $val) {
  $set[] = "`$key` = '" . $conn->real_escape_string($val) . "'";
}

$sql = "UPDATE `$table` SET " . implode(", ", $set) . " WHERE id = $id";

// ✅ Run query
if ($conn->query($sql)) {
  echo "success";
} else {
  echo "error: " . $conn->error;
}
?>
