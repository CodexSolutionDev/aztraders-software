<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$filename = "users.txt";
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password     = $_POST['old_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "⚠️ Please fill all fields.";
        header("Location: change_password.php");
        exit;
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "❌ New passwords do not match.";
        header("Location: change_password.php");
        exit;
    }

    $users = file($filename, FILE_IGNORE_NEW_LINES);
    $updated_users = [];
    $password_changed = false;

    foreach ($users as $line) {
        $parts = explode("|", $line);
        if (count($parts) < 4) continue;

        list($uname, $email, $hashed_password, $status) = $parts;

        if (strcasecmp(trim($uname), trim($username)) === 0) {
            if (password_verify($old_password, $hashed_password)) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $updated_users[] = "$uname|$email|$new_hashed_password|$status";
                $password_changed = true;
            } else {
                $_SESSION['error'] = "❌ Old password is incorrect.";
                header("Location: change_password.php");
                exit;
            }
        } else {
            $updated_users[] = $line;
        }
    }

    if ($password_changed) {
        file_put_contents($filename, implode(PHP_EOL, $updated_users) . PHP_EOL);
        $_SESSION['success'] = "✅ Password updated successfully!";
        header("Location: home.php");
        exit;
    } else {
        $_SESSION['error'] = "❌ Could not update password (maybe user not found?)";
        header("Location: change_password.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-6 rounded-2xl shadow-lg w-96">
    <h2 class="text-xl font-bold mb-4">Change Password</h2>

    <?php if (!empty($message)) : ?>
      <div class="mb-4 p-2 text-sm text-center rounded-lg 
                  <?php echo strpos($message, '✅') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <label class="block mb-2 text-sm font-medium">Old Password</label>
      <input type="password" id="old_password" name="old_password" required class="w-full p-2 border rounded-lg mb-1">
      <label class="flex items-center text-xs mb-3">
        <input type="checkbox" onclick="togglePassword('old_password')" class="mr-2">
        Show Password
      </label>

      <label class="block mb-2 text-sm font-medium">New Password</label>
      <input type="password" id="new_password" name="new_password" required class="w-full p-2 border rounded-lg mb-1">
      <label class="flex items-center text-xs mb-3">
        <input type="checkbox" onclick="togglePassword('new_password')" class="mr-2">
        Show Password
      </label>

      <label class="block mb-2 text-sm font-medium">Confirm New Password</label>
      <input type="password" id="confirm_password" name="confirm_password" required class="w-full p-2 border rounded-lg mb-1">
      <label class="flex items-center text-xs mb-3">
        <input type="checkbox" onclick="togglePassword('confirm_password')" class="mr-2">
        Show Password
      </label>

      <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700">
        Update Password
      </button>
    </form>
  </div>

  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>
