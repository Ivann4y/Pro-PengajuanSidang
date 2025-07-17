<?php
session_start();
require "../../koneksi/koneksiAndrew.php"; // Adjust this path if your koneksi file is elsewhere

if (isset($_GET['id_sidang']) && !empty($_GET['id_sidang'])) {
    $id_sidang = $_GET['id_sidang'];

    $sql = "SELECT dok_laporan, id_kelompok, dok_laporan FROM Sidang WHERE id_sidang = ?";
    $stmt = sqlsrv_prepare($conn, $sql, array(&$id_sidang));

    if ($stmt === false) {
        die("Terjadi kesalahan saat mempersiapkan query: " . print_r(sqlsrv_errors(), true));
    }
    if (!sqlsrv_execute($stmt)) {
        die("Terjadi kesalahan saat mengeksekusi query: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($row && !empty($row['dok_laporan'])) {
        $file_path = $row['dok_laporan']; // path relatif/absolut ke file di server
        $id_kelompok = $row['id_kelompok'];

        if (file_exists($file_path)) {
            $filename = basename($file_path);
            $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $mime_types = [
                'pdf' => 'application/pdf',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'doc' => 'application/msword',
                'zip' => 'application/zip',
                // tambahkan sesuai kebutuhan
            ];
            $file_mime_type = $mime_types[$file_extension] ?? 'application/octet-stream';

            header('Content-Type: ' . $file_mime_type);
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        } else {
            echo "File tidak ditemukan di server.";
        }
    } else {
        echo "Dokumen tidak ditemukan atau kosong.";
    }
} else {
    echo "ID Sidang tidak valid.";
}
sqlsrv_close($conn); // Close the database connection
?>