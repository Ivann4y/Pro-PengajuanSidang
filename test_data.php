<?php
include 'koneksi/koneksiAndrew.php';

echo "<h2>Database Test</h2>";

// Test 1: Check if there are any students
echo "<h3>1. Students in database:</h3>";
$sql_students = "SELECT COUNT(*) as count FROM Mahasiswa";
$result_students = sqlsrv_query($conn, $sql_students);
if ($result_students) {
    $count = sqlsrv_fetch_array($result_students, SQLSRV_FETCH_ASSOC)['count'];
    echo "Total students: $count<br>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

// Test 2: Check if there are any groups
echo "<h3>2. Groups in database:</h3>";
$sql_groups = "SELECT COUNT(*) as count FROM Kelompok";
$result_groups = sqlsrv_query($conn, $sql_groups);
if ($result_groups) {
    $count = sqlsrv_fetch_array($result_groups, SQLSRV_FETCH_ASSOC)['count'];
    echo "Total groups: $count<br>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

// Test 3: Check if there are any sidang
echo "<h3>3. Sidang in database:</h3>";
$sql_sidang = "SELECT COUNT(*) as count FROM Sidang";
$result_sidang = sqlsrv_query($conn, $sql_sidang);
if ($result_sidang) {
    $count = sqlsrv_fetch_array($result_sidang, SQLSRV_FETCH_ASSOC)['count'];
    echo "Total sidang: $count<br>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

// Test 4: Show sample data
echo "<h3>4. Sample Kelompok data:</h3>";
$sql_sample = "SELECT TOP 5 * FROM Kelompok";
$result_sample = sqlsrv_query($conn, $sql_sample);
if ($result_sample) {
    echo "<table border='1'>";
    echo "<tr><th>id_kelompok</th><th>nomor_kelompok</th><th>nim</th><th>tahun_ajaran</th><th>jenis_sidang</th><th>id_matkul</th></tr>";
    while ($row = sqlsrv_fetch_array($result_sample, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id_kelompok'] . "</td>";
        echo "<td>" . $row['nomor_kelompok'] . "</td>";
        echo "<td>" . $row['nim'] . "</td>";
        echo "<td>" . $row['tahun_ajaran'] . "</td>";
        echo "<td>" . $row['jenis_sidang'] . "</td>";
        echo "<td>" . $row['id_matkul'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

// Test 5: Show sample Sidang data
echo "<h3>5. Sample Sidang data:</h3>";
$sql_sample_sidang = "SELECT TOP 5 * FROM Sidang";
$result_sample_sidang = sqlsrv_query($conn, $sql_sample_sidang);
if ($result_sample_sidang) {
    echo "<table border='1'>";
    echo "<tr><th>id_sidang</th><th>id_kelompok</th><th>judul</th><th>status_ajuan</th><th>waktu_pengumpulan</th></tr>";
    while ($row = sqlsrv_fetch_array($result_sample_sidang, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id_sidang'] . "</td>";
        echo "<td>" . $row['id_kelompok'] . "</td>";
        echo "<td>" . $row['judul'] . "</td>";
        echo "<td>" . $row['status_ajuan'] . "</td>";
        echo "<td>" . ($row['waktu_pengumpulan'] ? $row['waktu_pengumpulan']->format('Y-m-d H:i:s') : 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

// Test 6: Test the exact query from mPengajuan
echo "<h3>6. Test mPengajuan query (for NIM 1000000003):</h3>";
$nim_test = '1000000003';
$sql_test = "
    SELECT DISTINCT
        s.id_sidang, 
        s.judul, 
        s.status_ajuan,
        sg.nomor_kelompok,
        sg.jenis_sidang,
        sg.tahun_ajaran,
        mk.nama_matkul,
        d.nama_dosen AS nama_pembimbing
    FROM dbo.Sidang AS s
    JOIN dbo.Kelompok AS sg ON s.id_kelompok = sg.id_kelompok
    JOIN dbo.MataKuliah AS mk ON sg.id_matkul = mk.id_matkul
    JOIN dbo.Kelompok AS k_member ON k_member.nomor_kelompok = sg.nomor_kelompok
        AND k_member.tahun_ajaran = sg.tahun_ajaran
        AND k_member.jenis_sidang = sg.jenis_sidang
        AND k_member.id_matkul = sg.id_matkul
    LEFT JOIN dbo.Bimbingan AS b ON sg.id_kelompok = b.id_kelompok AND b.isPembimbing = 1
    LEFT JOIN dbo.Dosen AS d ON b.nomor_dosen = d.nomor_dosen
    WHERE k_member.nim = ?
    ORDER BY s.id_sidang DESC
";

$result_test = sqlsrv_query($conn, $sql_test, [$nim_test]);
if ($result_test) {
    $count = 0;
    while ($row = sqlsrv_fetch_array($result_test, SQLSRV_FETCH_ASSOC)) {
        $count++;
        if ($count == 1) {
            echo "<table border='1'>";
            echo "<tr><th>id_sidang</th><th>judul</th><th>status_ajuan</th><th>nomor_kelompok</th><th>jenis_sidang</th><th>nama_matkul</th><th>nama_pembimbing</th></tr>";
        }
        echo "<tr>";
        echo "<td>" . $row['id_sidang'] . "</td>";
        echo "<td>" . $row['judul'] . "</td>";
        echo "<td>" . $row['status_ajuan'] . "</td>";
        echo "<td>" . $row['nomor_kelompok'] . "</td>";
        echo "<td>" . $row['jenis_sidang'] . "</td>";
        echo "<td>" . $row['nama_matkul'] . "</td>";
        echo "<td>" . ($row['nama_pembimbing'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    if ($count > 0) {
        echo "</table>";
        echo "Found $count records for NIM $nim_test";
    } else {
        echo "No records found for NIM $nim_test";
    }
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

// Test 7: Show the relationship between Kelompok and Sidang
echo "<h3>7. Relationship between Kelompok and Sidang:</h3>";
$sql_relationship = "
    SELECT 
        k.nim,
        k.id_kelompok,
        k.nomor_kelompok,
        s.id_sidang,
        s.judul,
        s.status_ajuan
    FROM Kelompok k
    LEFT JOIN Sidang s ON k.id_kelompok = s.id_kelompok
    WHERE k.nim IN ('1000000001', '1000000002', '1000000003', '1000000004')
    ORDER BY k.nomor_kelompok, k.nim
";

$result_relationship = sqlsrv_query($conn, $sql_relationship);
if ($result_relationship) {
    echo "<table border='1'>";
    echo "<tr><th>NIM</th><th>id_kelompok</th><th>nomor_kelompok</th><th>id_sidang</th><th>judul</th><th>status_ajuan</th></tr>";
    while ($row = sqlsrv_fetch_array($result_relationship, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['nim'] . "</td>";
        echo "<td>" . $row['id_kelompok'] . "</td>";
        echo "<td>" . $row['nomor_kelompok'] . "</td>";
        echo "<td>" . ($row['id_sidang'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['judul'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['status_ajuan'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

// Test 8: Test the exact query structure you provided
echo "<h3>8. Test with your exact query structure (Sidang ID 4001):</h3>";
$sql_exact = "
SELECT
    s.id_sidang AS SidangID,
    s.judul AS SidangJudul,
    s.status_ajuan AS SidangStatusAjuan,
    sg.nomor_kelompok AS GroupNumber,
    sg.tahun_ajaran AS GroupTahunAjaran,
    sg.jenis_sidang AS GroupJenisSidang,
    mk_group.nama_matkul AS GroupMataKuliah,
    k_member.nim AS MemberNIM,
    m.nama_mhs AS MemberName,
    k_member.id_kelompok AS MemberEnrollmentID,
    d.nama_dosen AS DosenPembimbingName
FROM
    [dbo].[Sidang] AS s
JOIN
    [dbo].[Kelompok] AS sg ON s.id_kelompok = sg.id_kelompok
JOIN
    [dbo].[MataKuliah] AS mk_group ON sg.id_matkul = mk_group.id_matkul
JOIN
    [dbo].[Kelompok] AS k_member
    ON k_member.nomor_kelompok = sg.nomor_kelompok
    AND k_member.tahun_ajaran = sg.tahun_ajaran
    AND k_member.jenis_sidang = sg.jenis_sidang
    AND k_member.id_matkul = sg.id_matkul
JOIN
    [dbo].[Mahasiswa] AS m ON k_member.nim = m.nim
LEFT JOIN
    [dbo].[Bimbingan] AS b ON k_member.id_kelompok = b.id_kelompok AND b.isPembimbing = 1
LEFT JOIN
    [dbo].[Dosen] AS d ON b.nomor_dosen = d.nomor_dosen
WHERE
    s.id_sidang = 4001
ORDER BY
    k_member.nim ASC
";

$result_exact = sqlsrv_query($conn, $sql_exact);
if ($result_exact) {
    echo "<table border='1'>";
    echo "<tr><th>SidangID</th><th>SidangJudul</th><th>SidangStatusAjuan</th><th>GroupNumber</th><th>GroupTahunAjaran</th><th>GroupJenisSidang</th><th>GroupMataKuliah</th><th>MemberNIM</th><th>MemberName</th><th>MemberEnrollmentID</th><th>DosenPembimbingName</th></tr>";
    while ($row = sqlsrv_fetch_array($result_exact, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['SidangID'] . "</td>";
        echo "<td>" . $row['SidangJudul'] . "</td>";
        echo "<td>" . $row['SidangStatusAjuan'] . "</td>";
        echo "<td>" . $row['GroupNumber'] . "</td>";
        echo "<td>" . $row['GroupTahunAjaran'] . "</td>";
        echo "<td>" . $row['GroupJenisSidang'] . "</td>";
        echo "<td>" . $row['GroupMataKuliah'] . "</td>";
        echo "<td>" . $row['MemberNIM'] . "</td>";
        echo "<td>" . $row['MemberName'] . "</td>";
        echo "<td>" . $row['MemberEnrollmentID'] . "</td>";
        echo "<td>" . ($row['DosenPembimbingName'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}
?> 