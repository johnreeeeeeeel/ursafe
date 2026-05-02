<?php
session_start();
require 'db_connection.php';
require 'emails/temporary_password_email.php';

// Get email from session
$email = $_SESSION['reset_email'] ?? null;

if (!$email) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'Session expired. Please try again.'
    ];

    header("Location: ../index.php");
    exit();
}

// Get user
$stmt = $conn_local->prepare("SELECT username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'User not found.'
    ];

    header("Location: ../index.php");
    exit();
}

$username = $user['username'];

// Generate new password
$plainPassword = strtoupper(bin2hex(random_bytes(4)));
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Update password
$update = $conn_local->prepare("UPDATE users SET password = ? WHERE email = ?");
$update->bind_param("ss", $hashedPassword, $email);

if ($update->execute()) {
    sendResetEmail($email, $username, $plainPassword);

    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'Password reset successful. Check your email.'
    ];
} else {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'Failed to update password.'
    ];
}

// Cleanup
unset($_SESSION['reset_email'], $_SESSION['otp'], $_SESSION['otp_sent']);

header("Location: ../index.php");
exit();
?>