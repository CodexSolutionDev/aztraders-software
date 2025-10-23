<?php
$usersFile = 'users.txt';
$adminEmail = ''; // Update this to actual admin email

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name && $email && $password) {
        $users = file_exists($usersFile) ? file($usersFile, FILE_IGNORE_NEW_LINES) : [];

        // Check if email already exists
        foreach ($users as $user) {
            [$existingName, $existingEmail] = explode('|', $user);
            if ($existingEmail === $email) {
                echo "⚠️ Email already registered. Please login or use another email.";
                exit;
            }
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        file_put_contents($usersFile, "$name|$email|$hashedPassword|pending\n", FILE_APPEND);

        // Email to admin with full approval link
        $domain = $_SERVER['HTTP_HOST'];
        $approveLink = "http://$domain/approve_user.php?email=" . urlencode($email);
        $subject = "🔐 New User Approval Needed";
        $message = "A new user signed up and needs your approval:\n\n"
                 . "Name: $name\nEmail: $email\n\n"
                 . "Click the link below to approve:\n$approveLink";
        $headers = "From: no-reply@$domain";

  if (mail($adminEmail, $subject, $message, $headers)) { 
            echo "✅ Signup successful! Please wait for admin approval.";
            echo "<script>
                setTimeout(function() {
                    window.location.href = '/';
                }, 2000);
            </script>";
        } else {
            echo "❌ Signup succeeded but failed to notify admin.";
        }

    } else {
        echo "⚠️ Please fill in all fields.";
    }
}
?>