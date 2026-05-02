<?php
session_start();

$inputOtp = $_POST['otp'];

if ($inputOtp == $_SESSION['otp']) {
    header("Location: ../reset_update_password.php");
    exit();
} else {
    echo "Invalid OTP";
}
?>