<?php
// File: Coba_auth.php

session_start();

// Ganti dengan detail koneksi server Anda
$serverName = "LAPTOP-7POM2U9J\\SQLEXPRESS"; 
$connectionOptions = [
    "Database" => "ZIa",
    "TrustServerCertificate" => true
];

// Membuat koneksi ke database
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Cek jika koneksi gagal
if ($conn === false) {
    // Menampilkan error secara detail adalah baik untuk development,
    // tapi di production, sebaiknya catat ke log dan tampilkan pesan umum.
    die(print_r(sqlsrv_errors(), true));
}

// Mengambil data dari form dengan aman
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? ''; // Ambil role dari form

// Validasi: pastikan input tidak kosong
if (empty($username) || empty($password)) {
    // Arahkan kembali ke halaman login dengan pesan error 'empty'
    header("Location: views/admin/login.php?error=empty&username=" . urlencode($username));
    exit;
}

// Siapkan query yang aman menggunakan parameterized query untuk mencegah SQL Injection
$sql = "SELECT * FROM users WHERE username = ? AND role = ?";
$params = [$username, $role];

// Eksekusi query
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt) {
    // Ambil hasil query sebagai array asosiatif
    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Jika pengguna ditemukan, verifikasi password
        // password_verify membandingkan password input dengan hash di database
        if (password_verify($password, $row['password'])) {
            // Jika BERHASIL:
            // Simpan informasi pengguna ke dalam session
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            
            // Arahkan ke halaman dashboard admin
            header("Location: views/admin/aBeranda.php");
            exit;
        }
    }
}

// Jika GAGAL (user tidak ditemukan atau password salah):
// Arahkan kembali ke halaman login dengan pesan error '1'
header("Location: views/admin/aLogin.php?error=1&username=" . urlencode($username));
exit;
?>