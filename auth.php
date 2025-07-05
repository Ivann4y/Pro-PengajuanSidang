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
        $usernameKolom = 'usn';
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
   
    $sql = "SELECT * FROM [dbo].[$tableNama] WHERE [$usernameKolom] = ?";
    $params = [$username];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
      
        throw new Exception("Query execution failed: " . print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($stmt)) {
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

       
        if (password_verify($password, $user[$passwordKolom])) {
            
            unset($user[$passwordKolom]);

            $_SESSION['user_data'] = $user;
            $_SESSION['role'] = $role;
            $_SESSION['is_logged_in'] = true;
            if ($role === 'mahasiswa' && isset($user['nim'])) {
                $_SESSION['nim'] = $user['nim'];
            }

      
            header("Location: " . $redirectPath);
            exit();
        }
    }


    $_SESSION['login_error'] = 'Username atau Password salah!';
   
    header("Location: views/$role/{$role[0]}Login.php?error=1&username=" . urlencode($username) . "&role=" . urlencode($role));

    exit();
} catch (Exception $e) {
  
    $_SESSION['login_error'] = 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
    error_log($e->getMessage()); 
    header("Location: views/$role/{$role[0]}Login.php?error=1&username=" . urlencode($username) . "&role=" . urlencode($role));
    exit();
}
