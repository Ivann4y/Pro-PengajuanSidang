<?php
session_start();
require "../../koneksi/koneksiAndrew.php"; // Adjust this path if your koneksi file is elsewhere

if (isset($_GET['id_sidang']) && !empty($_GET['id_sidang'])) {
    $id_sidang = $_GET['id_sidang'];

    $sql = "SELECT dok_laporan, id_kelompok FROM Sidang WHERE id_sidang = ?";
    $stmt = sqlsrv_prepare($conn, $sql, array(&$id_sidang));

    if ($stmt === false) {
        die("Terjadi kesalahan saat mempersiapkan query: " . print_r(sqlsrv_errors(), true));
    }
    if (!sqlsrv_execute($stmt)) {
        die("Terjadi kesalahan saat mengeksekusi query: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($row && !empty($row['dok_laporan'])) {
        $file_content = $row['dok_laporan'];
        $id_kelompok = $row['id_kelompok'];

        // IMPORTANT: Set the correct MIME type based on your stored file.
        // Assuming it's a ZIP file as per your original code.
        // If it can be PDF, DOCX, etc., you'll need to store the actual MIME type in your DB.
        $filename = "Dokumen_Laporan_Kelompok_" . htmlspecialchars($id_kelompok) . ".zip";
        $file_mime_type = 'application/zip'; // Example: 'application/pdf', 'application/msword'

        // Set HTTP headers for file download
        header('Content-Type: ' . $file_mime_type);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($file_content)); // Tell the browser the file size
        echo $file_content; // Output the binary content
        exit; // Stop further execution
    } else {
        echo "Dokumen tidak ditemukan atau kosong.";
    }
} else {
    echo "ID Sidang tidak valid.";
}
sqlsrv_close($conn); // Close the database connection
?>