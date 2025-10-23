<?php
$usersFile = 'users.txt';
$email = $_GET['email'] ?? '';

// Step 1: Validate the email format
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Invalid or missing email.";
    exit;
}

// Step 2: Check if the users file exists
if (!file_exists($usersFile)) {
    echo "❌ Users file not found.";
    exit;
}

// Step 3: Read the users file
$lines = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$updated = false;

// Step 4: Loop through the users and find the user to approve
foreach ($lines as &$line) {
    list($name, $userEmail, $password, $status) = explode('|', $line);
    
    if ($userEmail === $email && $status === 'pending') {
        // Step 5: Change the status to approved
        $line = "$name|$userEmail|$password|approved";
        $updated = true;
        break;
    }
}

// Step 6: Update the users file if we found and updated the user
if ($updated) {
    if (file_put_contents($usersFile, implode("\n", $lines) . "\n")) {
        echo "<h2>✅ User Approved Successfully</h2>";
        // Optionally, redirect to a list of users or another page
        // header("Location: users_list.php"); // Example redirection
    } else {
        echo "<h2>❌ Failed to update the users file. Please try again.</h2>";
    }
} else {
    echo "<h2>⚠️ User Not Found or Already Approved</h2>";
}
?>
