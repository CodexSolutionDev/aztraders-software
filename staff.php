<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: /");
    exit;
}

$file = __DIR__ . '/users.txt';

$users = [];
if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode('|', $line);
        if (count($parts) >= 4) {
            $users[] = [
                'name' => $parts[0],
                'email' => $parts[1],
                'password' => $parts[2], // Don't show this in table
                'status' => $parts[3],
            ];
        }
    }
}
if (basename($_SERVER['PHP_SELF']) !== 'view.php') {
    unset($_SESSION['access_granted_view']);
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Staff</title>
<link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>

    <style>
  
  body {
    margin-top: 10px;
  }
</style><!-- Menu Bar -->
<div class="menu-bar fixed top-0 left-0 w-full bg-[#6b3de6] shadow z-50">
  <div class="flex justify-between items-center px-6 py-3 text-white font-medium" style="
    padding-bottom: unset;
    padding-top: unset;
">
    
    <!-- Centered Menu Links -->
    <div class="flex-1 flex justify-center space-x-6">
      <a href="/home" class="hover:bg-purple-800 px-4 py-2 rounded" >Dashboard</a>
      <a href="/sales" class="hover:bg-purple-800 px-4 py-2 rounded">Sales</a>
      <a href="/purchase" class="hover:bg-purple-800 px-4 py-2 rounded">Purchases</a>
      <a href="/department" class="hover:bg-purple-800 px-4 py-2 rounded">Department</a>
      <a href="/staff" class="hover:bg-purple-800 px-4 py-2 rounded"style="background-color: #5830be;">Staff</a>
      <a href="/Form" class="hover:bg-purple-800 px-4 py-2 rounded">Form</a>
      <a href="/ledger" class="hover:bg-purple-800 px-4 py-2 rounded">Ledger</a>           
      <a href="/expense" class="hover:bg-purple-800 px-4 py-2 rounded">Expense</a>
      <a href="/view" class="hover:bg-purple-800 px-4 py-2 rounded">View</a>
    </div>

    <!-- Right Side: Username -->
    <div id="username-display" class="text-sm font-semibold" >
      <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>
    </div>

  </div>
</div>


  </head>
  
<title>Staff Management</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50" style="padding-left: unset; padding-top: 50px;">

<div class="max-w-6xl mx-auto bg-white shadow rounded-lg p-6" style="padding-top:50px;">

  <h1 class="text-2xl font-bold mb-6 text-center text-purple-700">Staff List & Status Management</h1>

  <form id="staffForm" method="POST" action="save_status.php">
    <table class="min-w-full table-auto border-collapse border border-gray-300">
      <thead>
        <tr class="bg-purple-600 text-white">
          <th class="border border-gray-300 px-4 bg-[#6b3de6] shadow z-50 py-2" >Name</th>
          <th class="border border-gray-300 px-4 bg-[#6b3de6] shadow z-50 py-2">Email</th>
          <th class="border border-gray-300 px-4 bg-[#6b3de6] shadow z-50 py-2">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($users) === 0): ?>
          <tr>
            <td colspan="3" class="text-center p-4">No users found.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($users as $index => $user): ?>
            <tr class="hover:bg-purple-50">
              <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($user['name']) ?></td>
              <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
              <td class="border border-gray-300 px-4 py-2 text-center">
                <select name="status[<?= $index ?>]" class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-purple-500">
                  <option value="approved" <?= $user['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                  <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
                <input type="hidden" name="email[<?= $index ?>]" value="<?= htmlspecialchars($user['email']) ?>">
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="mt-6 flex justify-center">
      <button type="submit" class="bg-[#6b3de6] shadow z-50 text-white px-6 py-2 rounded hover:bg-purple-700 transition">Save Changes</button>
    </div>
  </form>

</div>

</body>
</html>

