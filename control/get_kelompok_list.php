<?php
// path: public_html/dosen/pages/get_kelompok_list.php
include '../koneksi/koneksiAndrew.php'; // Sesuaikan path jika perlu

header('Content-Type: application/json');

$kelompok_list = [];

// Query to get all kelompok and their anggota
$sql = "
    SELECT
        K.id_kelompok,
        M.nim,
        M.nama_mhs,
        M.prodi
    FROM
        Kelompok K
    LEFT JOIN
        Kelompok_Mahasiswa KM ON K.id_kelompok = KM.id_kelompok
    LEFT JOIN
        Mahasiswa M ON KM.nim = M.nim
    ORDER BY
        K.id_kelompok DESC, M.nim ASC
";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    echo json_encode(['error' => sqlsrv_errors()]);
    exit();
}

$temp_kelompok = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $id_kelompok = $row['id_kelompok'];

    // Inisialisasi kelompok jika belum ada
    if (!isset($temp_kelompok[$id_kelompok])) {
        $temp_kelompok[$id_kelompok] = [
            'id_kelompok' => $id_kelompok,
            'prodi' => '', // Prodi akan diisi dari anggota pertama atau jika ada kolom prodi di tabel Kelompok
            'anggota' => []
        ];
    }

    // Tambahkan prodi dari anggota jika belum terisi
    if (empty($temp_kelompok[$id_kelompok]['prodi']) && !empty($row['prodi'])) {
        $temp_kelompok[$id_kelompok]['prodi'] = $row['prodi'];
    }

    // Tambahkan anggota jika ada
    if (!empty($row['nim'])) {
        $temp_kelompok[$id_kelompok]['anggota'][] = [
            'nim' => $row['nim'],
            'nama_mhs' => $row['nama_mhs']
        ];
    }
}

// Ubah associative array menjadi indexed array
$kelompok_list = array_values($temp_kelompok);

echo json_encode($kelompok_list);
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>