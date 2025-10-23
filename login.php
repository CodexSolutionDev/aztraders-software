<?php
// Start the session to maintain user state
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get sanitized user input
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Validate the inputs
    if (!$email || !$password) {
        $_SESSION['error'] = "Please fill in both fields.";
        header("Location: login.php");
        exit;
    }

    // Check if the users file exists
    $usersFile = 'users.txt';
    if (!file_exists($usersFile)) {
        $_SESSION['error'] = "User file not found.";
        header("Location: login.php");
        exit;
    }

    // Read the users from the file
    $users = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $userFound = false;

    // Loop through users to find a match
foreach ($users as $user) {
    list($name, $storedEmail, $storedHash, $status) = explode('|', $user);

    // Case-insensitive email check
    if (strtolower($email) === strtolower($storedEmail)) {
        // Validate the password
        if (!password_verify($password, $storedHash)) {
            $_SESSION['error'] = "Wrong password.";
            header("Location: login.php");
            exit;
        }

        // Check if the user account is approved
        if ($status !== 'approved') {
            $_SESSION['error'] = "Account not yet approved by admin.";
            header("Location: login.php");
            exit;
        }

        // Login success, set session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $name;
        header("Location: /home"); // Redirect to the home page
        exit;
    }
}

    // If no matching user was found
    $_SESSION['error'] = "User not found.";
    header("Location: login.php");
    exit;
}
?>

