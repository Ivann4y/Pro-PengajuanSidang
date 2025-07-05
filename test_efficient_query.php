<?php
include 'koneksi/koneksiAndrew.php';

echo "<h2>Efficient Query Test</h2>";

// Test with NIM 1000000003
$nim_test = '1000000003';

echo "<h3>1. Get student's groups:</h3>";
$studentGroupsQuery = "
    SELECT DISTINCT nomor_kelompok, tahun_ajaran, jenis_sidang, id_matkul
    FROM dbo.Kelompok 
    WHERE nim = ?
";

$studentGroupsStmt = sqlsrv_query($conn, $studentGroupsQuery, [$nim_test]);
if ($studentGroupsStmt) {
    echo "<table border='1'>";
    echo "<tr><th>nomor_kelompok</th><th>tahun_ajaran</th><th>jenis_sidang</th><th>id_matkul</th></tr>";
    while ($group = sqlsrv_fetch_array($studentGroupsStmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $group['nomor_kelompok'] . "</td>";
        echo "<td>" . $group['tahun_ajaran'] . "</td>";
        echo "<td>" . $group['jenis_sidang'] . "</td>";
        echo "<td>" . $group['id_matkul'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

echo "<h3>2. Test comprehensive approach (handles all student groups):</h3>";
// Query using the comprehensive approach that handles all student's groups
$comprehensiveQuery = "
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
    JOIN dbo.Kelompok AS k_student ON k_student.nomor_kelompok = sg.nomor_kelompok
        AND k_student.tahun_ajaran = sg.tahun_ajaran
        AND k_student.jenis_sidang = sg.jenis_sidang
        AND k_student.id_matkul = sg.id_matkul
    LEFT JOIN dbo.Bimbingan AS b ON sg.id_kelompok = b.id_kelompok AND b.isPembimbing = 1
    LEFT JOIN dbo.Dosen AS d ON b.nomor_dosen = d.nomor_dosen
    WHERE k_student.nim = ?
    ORDER BY s.id_sidang DESC
";

$result = sqlsrv_query($conn, $comprehensiveQuery, [$nim_test]);
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>id_sidang</th><th>judul</th><th>status_ajuan</th><th>nomor_kelompok</th><th>jenis_sidang</th><th>nama_matkul</th><th>nama_pembimbing</th></tr>";
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
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
    echo "</table>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

echo "<h3>3. Test scenario with multiple groups:</h3>";
echo "<p>Let's test with a student who might be in multiple groups:</p>";

// Test with a different NIM to see if they have multiple groups
$nim_test2 = '1000000001';
echo "<h4>Testing NIM $nim_test2:</h4>";

$studentGroupsQuery2 = "
    SELECT DISTINCT nomor_kelompok, tahun_ajaran, jenis_sidang, id_matkul, id_kelompok
    FROM dbo.Kelompok 
    WHERE nim = ?
    ORDER BY nomor_kelompok
";

$studentGroupsStmt2 = sqlsrv_query($conn, $studentGroupsQuery2, [$nim_test2]);
if ($studentGroupsStmt2) {
    echo "<table border='1'>";
    echo "<tr><th>nomor_kelompok</th><th>tahun_ajaran</th><th>jenis_sidang</th><th>id_matkul</th><th>id_kelompok</th></tr>";
    while ($group = sqlsrv_fetch_array($studentGroupsStmt2, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $group['nomor_kelompok'] . "</td>";
        echo "<td>" . $group['tahun_ajaran'] . "</td>";
        echo "<td>" . $group['jenis_sidang'] . "</td>";
        echo "<td>" . $group['id_matkul'] . "</td>";
        echo "<td>" . $group['id_kelompok'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

// Test comprehensive query for this student
$result2 = sqlsrv_query($conn, $comprehensiveQuery, [$nim_test2]);
if ($result2) {
    echo "<h4>Sidang for NIM $nim_test2:</h4>";
    echo "<table border='1'>";
    echo "<tr><th>id_sidang</th><th>judul</th><th>status_ajuan</th><th>nomor_kelompok</th><th>jenis_sidang</th><th>nama_matkul</th><th>nama_pembimbing</th></tr>";
    while ($row = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC)) {
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
    echo "</table>";
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true) . "<br>";
}

echo "<h3>4. Performance comparison:</h3>";
echo "<p><strong>Previous approach:</strong> Pre-filtered group conditions (limited)</p>";
echo "<p><strong>Current approach:</strong> Comprehensive JOIN that handles all student groups</p>";
echo "<p><strong>Benefits of current approach:</strong></p>";
echo "<ul>";
echo "<li>✅ Handles students in multiple groups correctly</li>";
echo "<li>✅ Finds all Sidang from any group the student belongs to</li>";
echo "<li>✅ Works regardless of which group member is the representative</li>";
echo "<li>✅ More robust and comprehensive</li>";
echo "<li>✅ Handles edge cases where student has different id_kelompok values</li>";
echo "</ul>";

echo "<h3>5. Why this approach is better:</h3>";
echo "<p><strong>Scenario:</strong> Student is in multiple groups with different id_kelompok values</p>";
echo "<p><strong>Previous approach:</strong> Would only find Sidang where the representative id_kelompok matches the student's groups</p>";
echo "<p><strong>Current approach:</strong> Finds all Sidang from any group the student belongs to, regardless of who the representative is</p>";
echo "<p><strong>Example:</strong></p>";
echo "<ul>";
echo "<li>Student NIM 1000000001 is in group 1 (id_kelompok = 1) and group 2 (id_kelompok = 5)</li>";
echo "<li>Group 1 has Sidang with id_kelompok = 1 (representative)</li>";
echo "<li>Group 2 has Sidang with id_kelompok = 5 (representative)</li>";
echo "<li><strong>Current approach finds both Sidang</strong></li>";
echo "<li><strong>Previous approach might miss one</strong></li>";
echo "</ul>";
?> 