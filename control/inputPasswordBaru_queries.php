<?php
$success = '';
$errorType = '';
$judul = '';
$token = $_GET['token'] ?? '';
$role = ''; // Akan diisi dari tabel password_resets jika token valid

// Validasi token di database
$reset = null;
if ($token) {
    // Gunakan prepared statement yang benar untuk sqlsrv
    $sql = "SELECT * FROM password_resets WHERE token = ? AND used = 0";
    $params = [$token];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        $reset = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

// Logika untuk menentukan tanggal kadaluarsa token 
if ($reset) {
    $role = $reset['role']; // role dari database, BUKAN dari GET/POST

    // Mapping table dan kolom berdasarkan role
    switch ($role) {
        case 'mahasiswa':
            $tableNama = 'Mahasiswa';
            $emailKolom = 'email';
            break;
        case 'dosen':
            $tableNama = 'Dosen';
            $emailKolom = 'email';
            break;
        case 'admin':
            $tableNama = 'Admin';
            $emailKolom = 'email';
            break;
        default:
            // Jika role tidak valid, anggap token tidak valid
            $reset = null;
    }

    if ($reset) { // Cek lagi setelah switch-case
        date_default_timezone_set('Asia/Jakarta');
        $now = new DateTime();
        $expires_at = $reset['expires_at'];

        // Pastikan expires_at adalah objek DateTime untuk perbandingan
        if (!$expires_at instanceof DateTime) {
            $expires_at = new DateTime($expires_at);
        }

        if ($expires_at > $now) {
            $role = $reset['role'];
        } else {
            $reset = null;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $reset) {
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validasi
    if (empty($newPassword) || empty($confirmPassword)) {
        header("Location: inputPasswordBaru.php?token=$token&error=empty");
        exit;
    } elseif (strlen($newPassword) < 8) {
        header("Location: inputPasswordBaru.php?token=$token&error=short");
        exit;
    } elseif ($newPassword !== $confirmPassword) {
        header("Location: inputPasswordBaru.php?token=$token&error=mismatch");
        exit;
    } else {
        // Hash password baru dan update ke database
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateUserSql = "UPDATE dbo.[$tableNama] SET password_hash = ? WHERE $emailKolom = ?";
        sqlsrv_query($conn, $updateUserSql, [$hash, $reset['email']]);

        // Tandai token sebagai sudah digunakan
        $updateTokenSql = "UPDATE password_resets SET used = 1 WHERE token = ?";
        sqlsrv_query($conn, $updateTokenSql, [$token]);

        // Hapus semua token yang sudah tidak berlaku untuk email ini
        $deleteOldTokensSql = "DELETE FROM password_resets WHERE (used = 1 OR expires_at < GETDATE()) AND email = ?";
        sqlsrv_query($conn, $deleteOldTokensSql, [$reset['email']]);

        $success = "Kata sandi berhasil diubah!";
    }
}

// Cek pesan sukses/error
if (isset($_GET['success'])) {
    $success = "Kata sandi berhasil diubah!";
}
$errorType = $_GET['error'] ?? '';

// Judul berdasarkan role
switch ($role) {
    case 'mahasiswa':
        $judul = 'Ubah Kata Sandi Mahasiswa';
        break;
    case 'dosen':
        $judul = 'Ubah Kata Sandi Dosen';
        break;
    case 'admin':
        $judul = 'Ubah Kata Sandi Admin';
        break;
    default:
        $judul = 'Ubah Kata Sandi';
        break;
}
?>