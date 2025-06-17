<?php

session_start();
require "../../koneksi.php";

if (isset($_GET['id_sidang'])) {
    $id_sidang = $_GET['id_sidang'];
    
    // Query untuk mendapatkan jenis sidang
    $query = "SELECT jenis_sidang FROM Sidang WHERE id_sidang = ?";
    $stmt = sqlsrv_prepare($conn, $query, array(&$id_sidang));
    
    if ($stmt === false) {
        echo "Terjadi kesalahan saat mempersiapkan query:<br>";
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                echo "SQLSTATE: " . $error['SQLSTATE'] . "<br>";
                echo "Code: " . $error['code'] . "<br>";
                echo "Message: " . $error['message'] . "<br>";
            }
        }
        exit();
    }
    
    if (!sqlsrv_execute($stmt)) {
        echo "Terjadi kesalahan saat mengeksekusi query:<br>";
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                echo "SQLSTATE: " . $error['SQLSTATE'] . "<br>";
                echo "Code: " . $error['code'] . "<br>";
                echo "Message: " . $error['message'] . "<br>";
            }
        }
        exit();
    }
    
    $sidang_data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
    if ($sidang_data) {
        $_SESSION['selected_sidang_id'] = $id_sidang;
        
        // Redirect berdasarkan jenis sidang
        if ($sidang_data['jenis_sidang'] == 0) { // Tugas Akhir
            header("Location: mdetailSidangTA.php");
        } else { // Sidang Semester
            header("Location: mdetailSidang.php");
        }
        exit();
    } else {
        echo "Sidang tidak ditemukan.";
        exit();
    }
} else {
    echo "ID Sidang tidak ditemukan.";
    exit();
}

sqlsrv_close($conn);
?>