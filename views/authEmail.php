<?php
session_start();
require_once '../koneksi/koneksiJoin.php'; 
require_once '../function/cobamailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['emailAstra']);
    $role = $_POST['role'] ?? 'guest';
    $tableNama = $_POST['tableNama'] ?? '';
    $emailKolom = $_POST['emailKolom'] ?? 'email';

    // Validasi jika input kosong
    if (empty($email)) {
        header("Location: lupaPassword.php?error=empty&role=$role");
        exit();
    }

    // Validasi jika format email tidak valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: lupaPassword.php?error=invalid&role=$role");
        exit();
    }

    // Validasi domain email harus @polytechnic.astra.ac.id
    if (!preg_match('/@polytechnic\.astra\.ac\.id$/', $email)) {
        header("Location: lupaPassword.php?error=domain&role=$role");
        exit();
    }

    // Validasi email & role di database
    $stmt = sqlsrv_query($conn, "SELECT * FROM [dbo].[$tableNama] WHERE [$emailKolom]=? AND role=?", [$email, $role]);
    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        date_default_timezone_set('Asia/Jakarta');
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        sqlsrv_query($conn, "INSERT INTO password_resets (email, role, token, expires_at) VALUES (?, ?, ?, ?)", [$email, $role, $token, $expires]);

        // Tentukan kolom nama sesuai role
        $recipientName = '';
        if ($role === 'mahasiswa' && isset($user['nama_mhs'])) {
            $recipientName = $user['nama_mhs'];
        } elseif ($role === 'dosen' && isset($user['nama_dosen'])) {
            $recipientName = $user['nama_dosen'];
        } elseif ($role === 'admin' && isset($user['nama'])) {
            $recipientName = $user['nama'];
        } elseif (isset($user['username'])) {
            $recipientName = $user['username']; // fallback lama
        } else {
            $recipientName = $email; // fallback terakhir
        }

        $result = sendResetPasswordEmail($email, $recipientName, $token);


        if ($result['success']) {
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_role'] = $role;
            $_SESSION['reset_table'] = $tableNama;
            header("Location: lupaPassword.php?success=1&role=$role");
            exit();
        } else {
            header("Location: lupaPassword.php?error=mail&role=$role");
            exit();
        }
    } else {
        header("Location: lupaPassword.php?error=notfound&role=$role");
        exit();
    }
}                                                                                                                                                                                                                                                                         