<?php
include '../koneksi/koneksiAndrew.php';



// Mengambil data profil dari masing-masing role
 switch ($role) {
        case 'mahasiswa':
            $query = "SELECT * FROM View_ProfilMahasiswa ORDER BY username ASC";
            break;
        case 'dosen':
            $query = "SELECT * FROM View_ProfilDosen ORDER BY username ASC";
            break;
        case 'admin':
            // $tableNama = 'Admin';
            // $emailKolom = 'email';
             $query = "SELECT * FROM View_ProfilAdmin ORDER BY username ASC";
            break;
        default:
            // Jika role tidak valid, anggap token tidak valid
            $reset = null;
    }



$stmt = sqlsrv_query($conn, $query);

$profilDitampilkan = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $profilDitampilkan[] = $row;
}

$response = [
    "data" => $profilDitampilkan,
];

header('Content-Type: application/json');
echo json_encode($response);
?>