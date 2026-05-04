<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

function sendUserEmail($email, $username, $password)
{
    $mail = new PHPMailer(true);

    try {
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
        $mail->Subject = 'Welcome to UrSafe';

        $mail->Body = "
            <div>
                <h4 style='margin:0; padding:0;'>Hello {$username},</h4>
                <p style='margin:0; padding:0;'>Welcome to <b>UrSafe</b>!</p>
                
                <br>

                <p style='margin:0; padding:0;'>
                    You may click the <b>UrSafe Login</b> button below to login to your account.
                </p>
                <br>

                <a href='https://ursafe.heliohost.us'
                    style='
                        background-color:#48c441;
                        color:#f2f2f2;
                        padding:8px 12px;
                        border-radius:12px;
                        display:inline-block;
                        font-size:16px;
                        text-decoration:none;
                        margin:0;
                    '>
                    UrSafe Login
                </a>
                <br>
                <br>

                <p style='margin:0; padding:0;'>This is a system generated e-mail. Please do not reply.</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
