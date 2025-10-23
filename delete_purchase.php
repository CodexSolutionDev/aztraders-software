<?php
include 'purchaseconnection.php';

// JSON input le raha hai
$data = json_decode(file_get_contents("php://input"), true);

$ids = $data['ids'] ?? [];
$table = $_GET['table'] ?? ($data['table'] ?? ''); // ✅ URL ya JSON dono se accept kare

if (empty($ids) || !$table) {
  echo "missing_data";
  exit;
}

// IDs ko safe integer bana rahe hain
$idList = implode(",", array_map('intval', $ids));

// Delete query
$sql = "DELETE FROM `$table` WHERE id IN ($idList)";

if ($conn->query($sql)) {
  // ✅ Reorder IDs to keep continuous sequence
  $conn->query("SET @count = 0");
  $conn->query("UPDATE `$table` SET id = (@count := @count + 1) ORDER BY id");
  $conn->query("ALTER TABLE `$table` AUTO_INCREMENT = 1");

  echo "deleted_and_reordered";
} else {
  echo "error: " . $conn->error;
}
?>
