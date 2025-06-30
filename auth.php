<?php

session_start();
require "koneksi/koneksiAndrew.php";


$role = $_POST['role'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header("Location: views/$role/{$role[0]}Login.php?error=empty&username=" . urlencode($username));
    exit();
}


$tableNama = '';
$usernameKolom = '';
$passwordKolom = 'password_hash';
$redirectPath = '';


switch ($role) {
    case 'mahasiswa':
        $tableNama = 'Mahasiswa';
        $usernameKolom = 'username';
        $redirectPath = 'views/mahasiswa/mBeranda.php';
        break;
    case 'dosen':
        $tableNama = 'Dosen';
        $usernameKolom = 'username';
        $redirectPath = 'views/dosen/dBeranda.php';
        break;
    case 'admin':
        $tableNama = 'Admin';
        $usernameKolom = 'username';
        $redirectPath = 'views/admin/aBeranda.php';
        break;
    default:
        $_SESSION['login_error'] = 'Role tidak valid!';
        header("Location: index.php");
        exit();
}

try {
    // 4. Buat query SQL yang aman menggunakan prepared statement
    // Menggunakan [ ] untuk nama tabel dan kolom adalah praktik aman di SQL Server
    $sql = "SELECT * FROM [dbo].[$tableNama] WHERE [$usernameKolom] = ?";
    $params = [$username];

    // Eksekusi query
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        // Gagal eksekusi query
        throw new Exception("Query execution failed: " . print_r(sqlsrv_errors(), true));
    }

    // 5. Ambil data user dan verifikasi password
    if (sqlsrv_has_rows($stmt)) {
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // **LANGKAH PALING PENTING: Verifikasi password**
        if (password_verify($password, $user[$passwordKolom])) {
            // Jika password cocok, login berhasil!

            // Hapus data sensitif sebelum disimpan ke session
            unset($user[$passwordKolom]);

            // Simpan data ke session
            $_SESSION['user_data'] = $user;
            $_SESSION['role'] = $role;
            $_SESSION['is_logged_in'] = true;
            if ($role === 'mahasiswa' && isset($user['nim'])) {
                $_SESSION['nim'] = $user['nim'];
            }

            // Redirect ke dashboard yang sesuai
            header("Location: " . $redirectPath);
            exit();
        }
    }

    // Jika user tidak ditemukan ATAU password salah, gagalkan login
    $_SESSION['login_error'] = 'Username atau Password salah!';
    // Redirect kembali ke halaman login yang sesuai
    header("Location: views/$role/{$role[0]}Login.php?error=1&username=" . urlencode($username) . "&role=" . urlencode($role));

    exit();
} catch (Exception $e) {
    // Tangani error server
    $_SESSION['login_error'] = 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
    error_log($e->getMessage()); // Catat error ke log server untuk di-debug
    header("Location: views/$role/{$role[0]}Login.php?error=1&username=" . urlencode($username) . "&role=" . urlencode($role));
    exit();
}
