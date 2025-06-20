<?php

session_start();
require "koneksi.php";


$role = $_POST['role'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if  (empty($username) || empty($password)) {
    header("Location: views/$role/{$role[0]}Login.php?error=empty&username=" . urlencode($username));
    exit();
}


$tableNama= '';
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
        header("Location: views/$role/{$role[0]}Login.php?error=invalid_role");
        exit();
}

try {
    // 4. Buat query SQL yang aman menggunakan prepared statement
    // Menggunakan [ ] untuk nama tabel dan kolom adalah praktik aman di SQL Server
    $sql = "SELECT * FROM [dbo].[$tableName] WHERE [$usernameColumn] = ?";
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
        if (password_verify($password, $user[$passwordColumn])) {
            // Jika password cocok, login berhasil!
            
            // Hapus data sensitif sebelum disimpan ke session
            unset($user[$passwordColumn]);

            // Simpan data ke session
            $_SESSION['user_data'] = $user;
            $_SESSION['role'] = $role;
            $_SESSION['is_logged_in'] = true;

            // Redirect ke dashboard yang sesuai
            header("Location: " . $redirectPath);
            exit();
        }
    }

    // Jika user tidak ditemukan ATAU password salah, gagalkan login
    $_SESSION['login_error'] = 'Username atau Password salah!';
    // Redirect kembali ke halaman login yang sesuai
    header("Location: views/{$role}/login.php?username=" . urlencode($username));
    exit();

} catch (Exception $e) {
    // Tangani error server
    $_SESSION['login_error'] = 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
    error_log($e->getMessage()); // Catat error ke log server untuk di-debug
    header("Location: views/{$role}/login.php?username=" . urlencode($username));
    exit();
}
?>










<!-- <?php
// if ($_SERVER['REQUEST_METHOD'] === 'POST') { -->
//     $role = $_POST['role'];
//     $username = trim($_POST['username']);
//     $password = trim($_POST['password']);

//     // Validasi jika username atau password kosong
//     if (empty($username) || empty($password)) {
//         header("Location: views/$role/{$role[0]}Login.php?error=empty&username=" . urlencode($username));
//         exit();
//     }

//     $sql = "SELECT username, role, password_hash FROM users WHERE username = ? AND role = ?";
//     $params = [$username, $role];

//     $stmt = sqlsrv_query($conn, $sql, $params);
//     if ($stmt === false) {
//         // Error handling jika query gagal
//         die(print_r(sqlsrv_errors(), true));
//     }

//     $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

//     // Cek apakah user ditemukan DAN apakah password yang diinput cocok dengan hash di database
//     if ($user && password_verify($password, $user['password_hash'])) {
//         // Jika berhasil:
//         // Set session
//         $_SESSION['username'] = $user['username'];
//         $_SESSION['role'] = $user['role'];
//         $_SESSION['login'] = true;

//         // Arahkan ke halaman beranda sesuai role
//         header("Location: views/{$user['role']}/{$user['role'][0]}Beranda.php");
//         exit();

//     } else {
//         // Jika gagal (username/password/role salah):
//         // Kembalikan ke halaman login dengan pesan error
//         header("Location: views/$role/{$role[0]}Login.php?error=invalid&username=" . urlencode($username));
//         exit();
//     }
// }
// // Jika data mengambil dari database

// // 
// // include 'koneksi.php';
// // $stmt = sqlsrv_query($conn, "SELECT * FROM users WHERE username=? AND role=?", [$username, $role]);
// // $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
// // if ($user && password_verify($password, $user['password_hash'])) {
// //     // Login sukses
// // } else {
// //     // Login gagal
// // }

// 
// ?>