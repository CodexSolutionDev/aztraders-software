<?php
include 'salesconnection.php';

$data = json_decode(file_get_contents("php://input"), true);

$ids = $data['ids'] ?? [];
$table = $data['table'] ?? '';

if (empty($ids)) {
  echo "no_ids";
  exit;
}

if (empty($table)) {
  echo "no_table";
  exit;
}

// IDs ko safe integer bana lo
$idList = implode(",", array_map('intval', $ids));

// 🔹 Step 1: Delete selected rows
$delete = "DELETE FROM `$table` WHERE id IN ($idList)";
if ($conn->query($delete)) {

  // 🔹 Step 2: Reorder IDs (so they are continuous again)
  $conn->query("SET @num := 0");
  $conn->query("UPDATE `$table` SET id = (@num := @num + 1) ORDER BY id");
  $conn->query("ALTER TABLE `$table` AUTO_INCREMENT = 1");

  echo "deleted_and_reordered";
} else {
  echo "error: " . $conn->error;
}
?>
