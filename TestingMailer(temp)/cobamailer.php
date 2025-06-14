<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Get POST data
$recipientEmail = $_POST['email'] ?? '';
$recipientName = $_POST['name'] ?? '';

// Create reset password link
$resetLink = '../views/inputPasswordBaru.php?email=' . urlencode($recipientEmail);

// Validate inputs
if (empty($recipientEmail) || empty($recipientName)) {
    http_response_code(400);
    echo 'Email dan nama harus diisi';
    exit;
}

if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Format email tidak valid';
    exit;
}

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'vprasetya79@gmail.com';
    $mail->Password   = 'scph xypg ober syvw';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // Recipients
    $mail->setFrom('vprasetya79@gmail.com', 'Admin Pengajuan');
    $mail->addAddress($recipientEmail, $recipientName);

    // Embed image
    $mail->addEmbeddedImage('WhiteAstra.png', 'logo_whiteastra', 'WhiteAstra.png');

    // Create beautiful HTML email template
    $htmlMessage = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #0066cc;
            }
            .email-container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #f9f9f9;
            }
            .header {
                background: #0066cc;
                color: white;
                padding: 20px;
                text-align: center;
                border-radius: 5px 5px 0 0;
            }
            .logo {
                max-width: 200px;
                margin: 0 auto 20px auto;
                display: block;
            }
            .content {
                background: white;
                padding: 20px;
                border-radius: 0 0 5px 5px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .reset-button {
                display: inline-block;
                padding: 12px 25px;
                background-color: #0066cc;
                color: #ffffff !important;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
                font-weight: bold;
                font-family: Arial, sans-serif;
            }
            .reset-button:hover {
                background-color: #0052a3;
                color: #ffffff !important;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #dddddd;
                color: #666666;
                font-size: 12px;
            }
            .warning {
                color: #dc3545;
                font-size: 12px;
                margin-top: 15px;
            }
            .manual-link {
                word-break: break-all;
                color: #0066cc;
                margin-top: 10px;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">
                <img src="cid:logo_whiteastra" alt="White Astra Logo" class="logo">
                <h2>Reset Password</h2>
            </div>
            <div class="content">
                <p>Assalamu\'alaikum Wr. Wb.</p>
                <p>Yth. ' . htmlspecialchars($recipientName) . ',</p>
                <br>
                <p>Kami menerima permintaan untuk mereset password akun Anda. Untuk melanjutkan proses reset password, silakan klik tombol di bawah ini:</p>
                
                <center>
                    <a href="' . $resetLink . '" style="color: #ffffff; text-decoration: none;">
                        <span class="reset-button">Reset Password</span>
                    </a>
                </center>

                <p>Atau copy dan paste link berikut ke browser Anda:</p>
                <p class="manual-link">' . $resetLink . '</p>
                
                <p>Jika Anda tidak merasa melakukan permintaan reset password, Anda dapat mengabaikan email ini.</p>
                
                <div class="warning">
                    * Link reset password ini hanya berlaku selama 15 menit dan hanya dapat digunakan satu kali.
                </div>
                <br>
                <p>Wassalamu\'alaikum Wr. Wb.</p>
            </div>
            <div class="footer">
                Email ini dikirim kepada ' . htmlspecialchars($recipientName) . ' (' . htmlspecialchars($recipientEmail) . ')
                <br>
                Dikirim oleh Sistem Admin Pengajuan
            </div>
        </div>
    </body>
    </html>';

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Reset Password - Sistem Admin Pengajuan';
    $mail->Body    = $htmlMessage;
    $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], "\n", $htmlMessage));

    $mail->send();
    echo 'Email berhasil dikirim.';
} catch (Exception $e) {
    http_response_code(500);
    if (strpos($mail->ErrorInfo, 'SMTP connect() failed') !== false) {
        echo 'Gagal terhubung ke server email. Mohon periksa koneksi internet Anda.';
    } else {
        echo "Gagal mengirim email. Error: " . $mail->ErrorInfo;
    }
}
