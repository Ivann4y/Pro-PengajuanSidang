<?php
session_start();
require "../../koneksi/koneksiAndrew.php"; // Pastikan path koneksi ini benar

// Ambil ID Sidang dari URL
if (isset($_GET['id_sidang']) && !empty($_GET['id_sidang'])) {
    $id_sidang = (int)$_GET['id_sidang'];

<<<<<<< HEAD
    $sql = "SELECT dok_laporan, id_kelompok, dok_laporan FROM Sidang WHERE id_sidang = ?";
=======
    // 1. QUERY DIUBAH
    // Mengambil path file (dok_laporan) dari tabel Sidang dan
    // nama asli file (nama_file) dari tabel Detail_Sidang.
    $sql = "SELECT s.dok_laporan, ds.nama_file 
            FROM Sidang s
            LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
            WHERE s.id_sidang = ?";
    
>>>>>>> 66451757a8587745e4eacc5511515127d9c68f02
    $stmt = sqlsrv_prepare($conn, $sql, array(&$id_sidang));

    if ($stmt === false) {
        die("Terjadi kesalahan saat mempersiapkan query: " . print_r(sqlsrv_errors(), true));
    }
    if (!sqlsrv_execute($stmt)) {
        die("Terjadi kesalahan saat mengeksekusi query: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // 2. BLOK LOGIC DOWNLOAD DIUBAH TOTAL
    if ($row && !empty($row['dok_laporan'])) {
<<<<<<< HEAD
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
=======
        // Ambil path dan nama file dari hasil query
        $file_path_from_db = $row['dok_laporan'];
        $original_filename = $row['nama_file'] ?? basename($file_path_from_db);
        
        // Buat path lengkap menuju file di server
        $full_file_path = __DIR__ . '/../../' . $file_path_from_db;
        
        // Periksa apakah file benar-benar ada di folder server
        if (file_exists($full_file_path)) {
            if (ob_get_level()) {
                ob_end_clean(); // Hapus output buffer untuk mencegah file rusak
            }

            // Atur header untuk memaksa download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($original_filename) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($full_file_path));
            
            // Baca file dari disk dan kirim ke browser
            flush();
            readfile($full_file_path);
            exit; // Hentikan skrip
        } else {
            die("Error: File tidak ditemukan di server pada path: " . htmlspecialchars($full_file_path));
>>>>>>> 66451757a8587745e4eacc5511515127d9c68f02
        }
    } else {
        echo "Dokumen tidak ditemukan di database.";
    }
} else {
    echo "ID Sidang tidak valid.";
}
sqlsrv_close($conn); // Tutup koneksi database
?>