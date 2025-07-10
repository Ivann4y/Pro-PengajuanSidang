<?php
if (
    !isset($_SESSION['id_sidang_aktif']) || !is_numeric($_SESSION['id_sidang_aktif']) ||
    !isset($_SESSION['judul']) || empty($_SESSION['judul'])
) {
    $_SESSION['error_message'] = "Sidang tidak ditemukan atau belum dipilih.";
    header("Location: aDaftarSidang.php");
    exit();
}

$judulSidang = $_SESSION['judul'];
$id_sidang = (int) $_SESSION['id_sidang_aktif'];

$sql = "
    SELECT 
    ds.nomor_dosen,
    d.nama_dosen,
    ds.catatan_sidang,
    ds.status_revisi,
    ds.dok_revisi,
    ds.nama_file,
    p.peran_dosen
FROM Detail_Sidang ds
JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
JOIN Penjadwalan p ON ds.id_sidang = p.id_sidang AND ds.nomor_dosen = p.nomor_dosen
WHERE ds.id_sidang = ?
";

$params = [$id_sidang];
$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die("Query gagal: " . print_r(sqlsrv_errors(), true));
}

$allRows = [];
$statusList = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $statusList[] = $row['status_revisi'];
    $allRows[] = $row; // simpan baris-baris untuk ditampilkan nanti
}

// Reset ulang pointer untuk looping nanti
sqlsrv_free_stmt($stmt);

// Hitung status global
if (!empty($allRows)) {
    if (in_array('Ditolak', $statusList)) {
        $statusRevisiGlobal = "Ditolak";
        $badgeClass = "badge-danger";
    } elseif (in_array('Pending', $statusList)) {
        $statusRevisiGlobal = "Belum Disetujui";
        $badgeClass = "badge-warning";
    } else {
        $statusRevisiGlobal = "Disetujui";
        $badgeClass = "badge-success";
    }
} else {
    // Kalau data kosong
    $statusRevisiGlobal = "Tidak ada data";
    $badgeClass = "badge-secondary";
}
