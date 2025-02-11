<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Required if installed via Composer

function sendMail($recipientEmail, $voter_id)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP Server
        $mail->SMTPAuth = true;
        $mail->Username = 'rkkaung@gmail.com'; // Your Gmail ID
        $mail->Password = 'fpkducsbyegsnvvr'; // Your Gmail Password or App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Content
        $mail->setFrom('rkkaung@gmail.com', 'LwanNuuWatt');
        $mail->addAddress($recipientEmail); // Recipient Email
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your voter_id is: <b>$voter_id</b>.Use it to login to your account.";

        $mail->isHTML(true);
        $mail->send();

        echo "OTP sent successfully!";
        return true;
    } catch (Exception $e) {
        echo "Error sending OTP: {$mail->ErrorInfo}";
        return false;
    }
}
