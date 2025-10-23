<?php
// login.php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_input = trim($_POST['name']);    // e.g. "Khizar Mumtaz"
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // convert to table name: lowercase, spaces->underscore, keep a-z0-9_
    $table = strtolower($name_input);
    $table = preg_replace('/\s+/', '_', $table);
    $table = preg_replace('/[^a-z0-9_]/', '', $table);
$email_input = strtolower(trim($_POST['email'])); // convert login email to lowercase

    // validate user & approved
    $stmt = $conn->prepare("SELECT id, password, name FROM users WHERE email = ? AND approved = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        echo "Invalid credentials or account not approved.";
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        echo "Invalid password.";
        exit;
    }

    // check table existence in both DBs
    $safe_table = $conn->real_escape_string($table); // sanitized
    $q1 = $conn->query("SHOW TABLES LIKE '{$safe_table}'");
    $q2 = $db_expense->query("SHOW TABLES LIKE '{$safe_table}'");

    $exists1 = $q1 && $q1->num_rows > 0;
    $exists2 = $q2 && $q2->num_rows > 0;

    if ($exists1 && $exists2) {
        // OK, login success — set session and redirect to home (which will use $db_expense and this table)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['table_name'] = $table;
        header("Location: /home");
        exit;
    } else {
        echo "Department not found in both databases.";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="icon" type="image/png" sizes="32x32" href="/images/weblogo.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="/images/weblogo.png?v=2">
<link rel="shortcut icon" href="/images/weblogo.png?v=2">

<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #6b3de6, #8363f5);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .login-box {
    position: relative; /* span absolute ke liye relative parent */
}
#togglePassword {
    position: absolute;
    right: 30px;
    top: 175px; /* adjust according to your layout */
    font-size: 18px;
}

    .login-box {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0px 8px 20px rgba(0,0,0,0.15);
        width: 350px;
        text-align: center;
        animation: fadeIn 0.6s ease-in-out;
    }
    h2 {
        margin-bottom: 20px;
        color: #333;
    }
    input {
        width: 90%;
        padding: 12px;
        margin: 10px 0;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        outline: none;
        transition: 0.3s;
        text-align: center;
    }
    input:focus {
        border-color: #6b3de6;
        box-shadow: 0 0 8px rgba(107,61,230,0.3);
    }
    button {
        background: #6b3de6;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        transition: 0.3s;
    }
    button:hover {
        background: #5630b5;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(-20px);}
        to {opacity: 1; transform: translateY(0);}
    }
</style>
</head>
<body>

<div class="login-box">
    <h2>Department Login</h2>
    <form method="post">
        <input type="text" name="name" placeholder="Your full name" required>
        <input type="email" name="email" placeholder="Email" required>
<div style="position: relative; width: 100%;">
  <input
    type="password"
    id="password"
    placeholder="Password"
    style="
      width: 100%;
      padding: 12px 40px 12px 12px; /* extra right padding for eye */
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 16px;
      outline: none;
      box-sizing: border-box;
    "
  />
  <span
    id="togglePassword"
    style="
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 18px;
      color: #555;
    "
  >👁️‍🗨️</span>
</div>

        <button type="submit">Login</button>
    </form>
    <p style="text-align:center; margin-top:10px;">
    Don't have an account? 
    <a href="/register.php" style="color:#007bff; text-decoration:none;">
        Register here
    </a>
</p>

</div>
<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

togglePassword.addEventListener('click', function () {
    if(password.type === 'password') {
        password.type = 'text';
        togglePassword.textContent = '👁️'; // open eye
    } else {
        password.type = 'password';
        togglePassword.textContent = '👁️‍🗨️'; // closed eye
    }
});
</script>


</body>
</html>

