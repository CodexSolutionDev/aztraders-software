<?php
// register.php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $raw_password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email.";
        exit;
    }

    // check existing email
    $chk = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $chk->bind_param("s", $email);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
        echo "Email already registered.";
        exit;
    }

    // create token and expiry (optional expiry: 48 hours)
    $token = bin2hex(random_bytes(16));
    $token_expire = date('Y-m-d H:i:s', time() + 48*3600);

    $password_hashed = password_hash($raw_password, PASSWORD_DEFAULT);

    $ins = $conn->prepare("INSERT INTO users (name, email, password, token, token_expire) VALUES (?, ?, ?, ?, ?)");
    $ins->bind_param("sssss", $name, $email, $password_hashed, $token, $token_expire);

    if ($ins->execute()) {
        $userId = $ins->insert_id;
        // build approve link — CHANGE domain to your real domain
        $approveLink = "https://department.aztraderss.com/approve_user.php?id={$userId}&token={$token}";

        // send mail to admin (simple mail(), for reliability use PHPMailer)
        $admin_email = "zufyan12345@gmail.com"; // replace
        $subject = "New Client Registration - Approval Required";
        $message = "
        <html><body>
          <h3>New user registration</h3>
          <p><strong>Name:</strong> {$name}<br>
          <strong>Email:</strong> {$email}</p>
          <p>Click to approve:<br><a href='{$approveLink}'>Approve user</a></p>
        </body></html>
        ";
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: no-reply@aztraderss.com\r\n";

        mail($admin_email, $subject, $message, $headers);

        echo "Registration successful. Wait for admin approval.";
    } else {
        echo "Error: " . $ins->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
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
    .register-box {
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
    p {
        margin-top: 12px;
    }
    a {
        color: #007bff;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(-20px);}
        to {opacity: 1; transform: translateY(0);}
    }
</style>
</head>
<body>

<div class="register-box">
    <h2>Create an Department Account</h2>
    <form method="post">
        <div>
        <input type="text" name="name" placeholder="Full name" required>
        <input type="email" name="email" placeholder="Email" required>
        </div>
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



<button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="/">Login here</a></p>
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

