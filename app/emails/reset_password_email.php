<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

function sendOTP($email, $username, $otp)
{
    $mail = new PHPMailer(true);

    // SMTP settings (Gmail)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'techjohnrel@gmail.com'; 
    $mail->Password   = 'dvdcwdkresywonit'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Sender & recipient
    $mail->setFrom('techjohnrel@gmail.com', 'UrSafe');
    $mail->addAddress($email);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Reset Password';

    $mail->Body = "
        <h4 style='margin:0; padding:0;'>Hello {$username},</h4>
        <p style='margin:0; padding:0;'>This is your OTP code to reset your password.</p>
        <br>

        <h1 style='letter-spacing:5px; margin:0;'>$otp</h1>
        <p style='margin:0;'>Please do not share this OTP code.</p>
        <br>

        <p style='margin:0; padding:0;'>This is a system generated e-mail. Please do not reply.</p>
    ";

    $mail->send();
}
?>