<?php
// get_suggested_kelompok_id.php
include '../../../koneksi/koneksiJoin.php';
header('Content-Type: application/json');

// Get the context from the GET parameters
$tahun_ajaran = $_GET['tahun_ajaran'] ?? null;
$jenis_sidang = $_GET['jenis_sidang'] ?? null;
$id_matkul = $_GET['id_matkul'] ?? null;

// If the necessary context is missing, we can't make a suggestion.
if (empty($tahun_ajaran) || empty($jenis_sidang) || empty($id_matkul)) {
    echo json_encode(['suggestion' => null, 'message' => 'Incomplete data']);
    exit();
}

// SQL to get all existing, unique group numbers for the given context, sorted numerically.
$sql = "SELECT DISTINCT nomor_kelompok 
        FROM Kelompok 
        WHERE tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?
        ORDER BY nomor_kelompok ASC";

$stmt = sqlsrv_query($conn, $sql, [$tahun_ajaran, $jenis_sidang, $id_matkul]);

if ($stmt === false) {
    // On database error, we can't suggest anything.
    echo json_encode(['suggestion' => null, 'message' => 'DB error']);
    exit();
}

$existing_numbers = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $existing_numbers[] = (int)$row['nomor_kelompok'];
}

// If no groups exist yet, the first suggestion is always 1.
if (empty($existing_numbers)) {
    echo json_encode(['suggestion' => 1]);
    exit();
}

// --- Logic to find the first gap ---
$suggestion = 1;
// Loop through the sorted list of existing numbers.
foreach ($existing_numbers as $number) {
    // If the current number matches our expected suggestion, it means this slot is taken.
    // So, we increment our suggestion to check for the next number.
    if ($number == $suggestion) {
        $suggestion++;
    } else {
        // If the current number does NOT match our suggestion, it means we've found a gap.
        // For example, if existing numbers are [1, 2, 4] and our suggestion is 3,
        // the loop will hit '4' while the suggestion is '3'. This is the gap.
        // Loop selesai pas ketemu kosongan
        break;
    }
}

// After the loop, $suggestion will either be the first gap or the next number after the sequence.
echo json_encode(['suggestion' => $suggestion]);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>