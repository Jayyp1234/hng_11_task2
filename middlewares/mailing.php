<?php
require __DIR__ .'/../config/constants.php';
require 'mail/vendor/autoload.php';

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
    $mail->Username = MAILING_EMAIL_ADDRESS; // Your Gmail email address
    $mail->Password = MAILING_PASSWORD; // Your Gmail password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS encryption
    $mail->Port = 587; // TCP port for TLS

    // Send email
    $mail->send();
} catch (Exception $e) {
    return false;
}
