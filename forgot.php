<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "user_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$email = $_POST['email'];

// Check if user exists
$result = $conn->query("SELECT * FROM users WHERE email='$email'");
if ($result->num_rows == 0) {
    die("Error: Email not found.");
}

$row = $result->fetch_assoc();

// Send password recovery email
$to = $row['email'];
$subject = "Password Recovery";
$message = "Hello " . $row['username'] . ",\n\nYour password is: " . $row['password'];
$headers = "From: noreply@yourwebsite.com";

if (mail($to, $subject, $message)) {
    echo "Password has been sent to your registered email.";
} else {
    echo "Error: Could not send the email.";
}

$conn->close();
?>
