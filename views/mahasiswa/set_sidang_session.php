<?php

session_start();
require "../../koneksi/koneksiAndrew.php";

if (!isset($_GET['id_sidang']) || !is_numeric($_GET['id_sidang'])) {
    // Invalid or missing id_sidang
    header("Location: mSidang.php");
    exit();
}

$id_sidang = (int)$_GET['id_sidang'];

// Query untuk memastikan id_sidang valid
$query = "SELECT jenis_sidang FROM Sidang WHERE id_sidang = ?";
$stmt = sqlsrv_prepare($conn, $query, array($id_sidang));

if ($stmt === false) {
    // Log error, redirect, or show friendly message
    header("Location: mSidang.php?error=query");
    exit();
}

if (!sqlsrv_execute($stmt)) {
    // Log error, redirect, or show friendly message
    header("Location: mSidang.php?error=execute");
    exit();
}

$sidang_data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($sidang_data) {
    $_SESSION['selected_sidang_id'] = $id_sidang;
    sqlsrv_close($conn);
    header("Location: mdetailSidang.php");
    exit();
} else {
    sqlsrv_close($conn);
    header("Location: mSidang.php?error=notfound");
    exit();
}