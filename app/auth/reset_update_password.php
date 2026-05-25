<?php
session_start();
require '../db_connection.php';
require '../emails/temporary_password_email.php';

// Get email from session
$email = $_SESSION['email'] ?? null;

if (!$email) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'Something went wrong. Please try again.'
    ];

    header("Location: ../../index.php");
    exit();
}

// Get user by email
$stmt = $conn_local->prepare("CALL getUserByEmail(?)");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$result->free();
$stmt->close();

while ($conn_local->more_results()) {
    $conn_local->next_result();
}

if (!$user) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'User not found.'
    ];

    header("Location: ../../index.php");
    exit();
}

$username = $user['username'];

// Generate new password
$plainPassword = strtoupper(bin2hex(random_bytes(4)));
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Update password
$update = $conn_local->prepare("CALL updateUserPassword(?, ?)");
$update->bind_param("ss", $email, $hashedPassword);

$success = $update->execute();

while ($conn_local->more_results()) {
    $conn_local->next_result();
}

if ($success) {
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

$update->close();

// Cleanup
unset(
    $_SESSION['email'], 
    $_SESSION['otp'], 
    $_SESSION['otp_sent']
);

$_SESSION['show_success_password_reset_modal'] = true;

header("Location: ../../index.php");
exit();
?>