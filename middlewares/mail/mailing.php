<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    //Server settings for Gmail
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'oyegbilegbemiga@gmail.com'; // Your Gmail email address
    $mail->Password = 'sxjefyydehhotwfn'; // Your Gmail password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS encryption
    $mail->Port = 587; // TCP port for TLS

    //Recipient
    $mail->setFrom('shopafrica@noreply.com', 'Shop Africa'); // Your Gmail email address and name
    $mail->addAddress('oyegbilegbemiga@gmail.com', 'Marvellous Oyegbile'); // Recipient email address and name

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Subject of the email';
    $mail->Body = 'This is the HTML message body';
    $mail->AltBody = 'This is the plain text message body';

    // Send email
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
