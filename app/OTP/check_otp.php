<?php
session_start();

$inputOtp = $_POST['otp'];

if ($inputOtp == $_SESSION['otp']) {
    $_SESSION['alert_message'] = [
        'type' => 'success',
        'text' => 'OTP verified successfully'
    ];

    header("Location: ../auth/reset_update_password.php");
    exit();

} else {
    $_SESSION['alert_message'] = [
        'type' => 'danger',
        'text' => 'Invalid OTP'
    ];

    header("Location: verify_otp.php");
    exit();
}
?>