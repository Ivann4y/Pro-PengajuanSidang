

<?php
//generate JSON sidangMendatang untuk dashboard

include '../koneksi.php';

$query = "SELECT tanggal_sidang, judul FROM View_SidangMendatang ORDER BY tanggal_sidang ASC";
$stmt = sqlsrv_query($conn, $query);

$sidang = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
   // Jika sudah DateTime, gunakan format langsung
    if ($row['tanggal_sidang'] instanceof DateTime) {
        $row['tanggal_sidang'] = $row['tanggal_sidang']->format('Y-m-d');
    } else {
        $row['tanggal_sidang'] = date('Y-m-d', strtotime($row['tanggal_sidang']));
    }
    $sidang[] = $row;
}
header('Content-Type: application/json');
echo json_encode($sidang);
?>