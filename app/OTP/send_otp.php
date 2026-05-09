<?php
session_start();
require '../db_connection.php';
require '../emails/reset_password_email.php';

$email = $_POST['email'] ?? null;

if (!$email) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'Something went wrong. Please try again.'
    ];

    header("Location: ../../index.php");
    exit();
}

// Check email
$stmt = $conn_local->prepare("CALL getUserByEmail(?)");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Alert message - email not found
if ($result->num_rows === 0) {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'Email not found'
    ];

    header("Location: ../../index.php");
    exit();
}

$user = $result->fetch_assoc();

// Generate OTP
$otp = rand(100000, 999999);

// Store session
$_SESSION['email'] = $email;
$_SESSION['otp'] = $otp;
$_SESSION['otp_sent'] = true;

// Send email
sendOTP($email, $user['username'], $otp);

header("Location: verify_otp.php");
exit();
?>