<?php
$conn = new mysqli("127.0.0.1", "u222423469_az", "dVnB9tHuLrrGSt@", "u222423469_az");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("UPDATE users SET approved = 1 WHERE id = $id");

    // Notify user
    $res = $conn->query("SELECT email FROM users WHERE id = $id");
    $row = $res->fetch_assoc();
    $email = $row['email'];

    mail($email, "Account Approved", "Your account has been approved. You can now login.");
    echo "User approved.";
}
