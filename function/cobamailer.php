<?php
require_once __DIR__ . '/src/PHPMailer.php';
require_once __DIR__ . '/src/SMTP.php';
require_once __DIR__ . '/src/Exception.php';
require_once __DIR__ . '/PesanGmail.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendResetPasswordEmail($recipientEmail, $recipientName, $token) {
    
     // Create reset password link
    $resetLink = 'http://localhost/PRG2/Pro-PengajuanSidang/views/inputPasswordBaru.php?token=' . urlencode($token);
        
    // Validasi input jika email kosong
    if (empty($recipientEmail)) {
        return [
            'success' => false,
            'message' => 'Email harus diisi'
        ];
    }

    // Validasi input jika email tidak valid
    if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'message' => 'Format email tidak valid'
        ];
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 3;                      // Enable verbose debug output
        $mail->isSMTP();                           // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';      // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                  // Enable SMTP authentication
        $mail->Username   = 'sidangastra@gmail.com'; // SMTP username
        $mail->Password   = 'slmu taja mkyp oisx'; // SMTP password (App Password)
        $mail->SMTPSecure = 'tls';                 // Enable TLS encryption
        $mail->Port       = 587;                   // TCP port to connect to
        $mail->CharSet    = 'UTF-8';               // Set charset to UTF-8

        // Disable SSL certificate verification
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Enable debug output
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer Debug: $str");
        };

        // Recipients
        $mail->setFrom('sidangastra@gmail.com', 'Admin Pengajuan');
        $mail->addAddress($recipientEmail, $recipientName);

        // Embed image
        $mail->addEmbeddedImage(__DIR__ . '/../assets/img/WhiteAstra.png', 'logo_whiteastra', 'WhiteAstra.png');

        // Get HTML message template from PesanGmail function
        $htmlMessage = getResetPasswordTemplate($recipientName, $resetLink);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password - Sistem Pengajuan Sidang';
        $mail->Body    = $htmlMessage;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], "\n", $htmlMessage));

        $mail->send();
        return [
            'success' => true,
            'message' => 'Email berhasil dikirim.'
        ];
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return [
            'success' => false,
            'message' => "Gagal mengirim email. Error: " . $mail->ErrorInfo
        ];
    }
}