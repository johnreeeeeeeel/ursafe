<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

function sendLockerSlotPaid($email, $username, $location, $slot_number, $size, $price) {
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
        $mail->Subject = 'Rejected Locker Application';

        $mail->Body = "
            <div>
                <h4>Hello {$username},</h4>

                <p>Your locker slot application has been <b>paid</b>.</p>

                <p><b>Slot Details:</b></p>
                <ul>
                    <li><b>Location:</b> {$location}</li>
                    <li><b>Slot Number:</b> {$slot_number}</li>
                    <li><b>Size:</b> {$size}</li>
                    <li><b>Price:</b> &#8369;{$price}</li>
                </ul>

                <br>

                <p>This is a system-generated email. Do not reply.</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>