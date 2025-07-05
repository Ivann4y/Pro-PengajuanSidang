<?php
// path: public_html/dosen/pages/get_kelompok_list.php
include '../koneksi/koneksiAndrew.php'; // Sesuaikan path jika perlu

header('Content-Type: application/json');

$kelompok_list = [];

// Query to get all kelompok and their anggota using the new structure
$sql = "
    SELECT
        k.nomor_kelompok,
        k.tahun_ajaran,
        k.jenis_sidang,
        k.id_matkul,
        k.nim,
        m.nama_mhs,
        m.prodi,
        mk.nama_matkul
    FROM
        Kelompok k
    JOIN
        Mahasiswa m ON k.nim = m.nim
    JOIN
        MataKuliah mk ON k.id_matkul = mk.id_matkul
    ORDER BY
        k.tahun_ajaran DESC, k.jenis_sidang ASC, k.nomor_kelompok ASC, k.nim ASC
";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    echo json_encode(['error' => sqlsrv_errors()]);
    exit();
}

$temp_kelompok = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $group_key = $row['tahun_ajaran'] . '_' . $row['jenis_sidang'] . '_' . $row['id_matkul'] . '_' . $row['nomor_kelompok'];

    // Initialize kelompok if not exists
    if (!isset($temp_kelompok[$group_key])) {
        $temp_kelompok[$group_key] = [
            'nomor_kelompok' => $row['nomor_kelompok'],
            'tahun_ajaran' => $row['tahun_ajaran'],
            'jenis_sidang' => $row['jenis_sidang'],
            'nama_matkul' => $row['nama_matkul'],
            'prodi' => $row['prodi'],
            'anggota' => []
        ];
    }

    // Add anggota
    $temp_kelompok[$group_key]['anggota'][] = [
        'nim' => $row['nim'],
        'nama_mhs' => $row['nama_mhs']
    ];
}

// Convert associative array to indexed array
$kelompok_list = array_values($temp_kelompok);

echo json_encode($kelompok_list);
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>