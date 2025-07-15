<?php
session_start();
require "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar


// Ambil ID sidang dari GET (sekali) lalu simpan ke session
if (isset($_GET['id']) && is_numeric($_GET['id'])) { //  Jika URL mengandung id dan isinya angka (?id=123)
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id']; // maka simpan id itu ke dalam $_SESSION['id_sidang_aktif']
    unset($_SESSION['nim_aktif']); // Hapus $_SESSION['nim_aktif'] agar nim sebelumnya tidak ikut terbawa.
    header("Location: dEvaluasiSidang.php");//Redirect ulang ke halaman dEvaluasiSidang.php agar URL jadi bersih (tanpa ?id=...).
    exit; // Hentikan program dengan exit setelah redirect.

}



// Ambil ID sidang dari GET (sekali) lalu simpan ke session
if (isset($_GET['id']) && is_numeric($_GET['id'])) { //Jika URL mengandung id dan nilainya adalah angka
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id']; //Simpan nilai id tadi ke dalam session bernama id_sidang_aktif.
    unset($_SESSION['nim_aktif']); //Hapus session nim_aktif agar nanti diatur ulang sesuai sidang yang baru dipilih.
    header("Location: dEvaluasiSidang.php"); //Arahkan (pindahkan) pengguna kembali ke halaman dEvaluasiSidang.php, tapi tanpa membawa ?id=... di URL.
    exit; // Hentikan program sampai disini
}

// Ambil NIM dari GET (sekali) lalu simpan ke session, lalu redirect untuk membersihkan URL
if (isset($_GET['nim']) && (!isset($_SESSION['nim_aktif']) || $_SESSION['nim_aktif'] !== $_GET['nim'])) { // jika URL mengandung nim dan nim_aktif nya beda dengan nim yang ada di url 
    $_SESSION['nim_aktif'] = $_GET['nim']; // maka simpan nilai nim dari url ke dalam session agar di gunakan terus menerus selama user beluam keluar
    header("Location: dEvaluasiSidang.php"); // redirect /  kembali ke halaman dEvaluasiSidang.php dengan url yang bersih tanpa nilai nim
    exit; // Hentikan program sampai di sini
}
//=================================================================================
// FIX: AMBIL ID SIDANG DARI SESSION SETELAH REDIRECT
// ===================================================================================
// Pastikan ID sidang ada di session sebelum melanjutkan
if (!isset($_SESSION['id_sidang_aktif'])) { // jika session id_sidang_aktif nya tidak ada atau null
    die("Sesi sidang tidak ditemukan. Silakan kembali ke daftar sidang dan pilih kembali."); // maka akan menampilkan pesan eror kaya gini
}

$id_sidang = $_SESSION['id_sidang_aktif'];// membuat variabel baru namanya id_sidang dengan menyimpan session id_sidang_aktif
// ===================================================================================


// ===================================================================================
// SIMULASI DOSEN LOGIN (GANTI DENGAN SESSION ASLI NANTI)
// ===================================================================================

if (!isset($_SESSION['user_data']['nomor_dosen'])) {  // jika session user data dan nomer dosen nya tidak ada 
    die("Akses ditolak."); } // maka akan menampilkan pesan eror seperti ini
$nomor_dosen_login = $_SESSION['user_data']['nomor_dosen']; // membuat variabel baru nomor_dosen_login dan menyimpan nya ke session


// ===================================================================================
// BAGIAN 2: PROSES PENYIMPANAN DATA (SAAT FORM DI-SUBMIT)
// ===================================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // jika halaman ini di panggil karena form dikirim(sumbmit)

    // Ambil data dari form
    $nim_post = $_POST['nim'] ?? null;//Ambil nilai nim 
    $catatan_post = $_POST['catatanEvaluasi'] ?? ''; // ambil nilai catatan evaluasi jika tidak ada nilainya di set null atau kosong

    // semuanya dicek nih di ambil juga nilai penialaian nya dari form dan di konversi ke integer
    $n_dokumen    = (isset($_POST['n_dokumen'])    && $_POST['n_dokumen']    !== '') ? (int)$_POST['n_dokumen']    : null;
    $n_presentasi = (isset($_POST['n_presentasi']) && $_POST['n_presentasi'] !== '') ? (int)$_POST['n_presentasi'] : null;
    $n_tanyajawab = (isset($_POST['n_tanyajawab']) && $_POST['n_tanyajawab'] !== '') ? (int)$_POST['n_tanyajawab'] : null;
    $n_proyek     = (isset($_POST['n_proyek'])     && $_POST['n_proyek']     !== '') ? (int)$_POST['n_proyek']     : null;

    if (empty($nim_post)) { // jika nilai nim nya null
        $_SESSION['error'] = "Terjadi kesalahan: NIM mahasiswa tidak terkirim saat menyimpan data."; // maka dia akan menyimpan pesan eror berikut ke dalam session
        header("Location: dEvaluasiSidang.php"); // dan akan mengembalikan ke halaman sebelum nya yaitu dEvaluasiSidang.php
        exit; // program di hentikan di sini
    }
    
    // ====================================================================================
    // ===== [PERBAIKAN UTAMA] LOGIKA UPSERT UNTUK CATATAN EVALUASI SIDANG =====
    // ====================================================================================
    
    // dia ngecek nih apakah data pada tabel detail sidang udah ada apa belum
    $sql_cek_catatan = "SELECT COUNT(*) as 'count' FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
    $stmt_cek_catatan = sqlsrv_query($conn, $sql_cek_catatan, [$id_sidang, $nomor_dosen_login]);
    $catatan_exists = false;

    // jika hasil count nya atau jumlah nya >0 maka artinyna sudah ada catatannya
    if ($stmt_cek_catatan) { 
        $catatan_exists = sqlsrv_fetch_array($stmt_cek_catatan, SQLSRV_FETCH_ASSOC)['count'] > 0;
    }

    if ($catatan_exists) { //Jika catatan sudah ada 
        $sql_catatan = "UPDATE Detail_Sidang SET catatan_sidang = ? WHERE id_sidang = ? AND nomor_dosen = ?"; // maka buat query update dengan catatan sidang sesuai dengan id sidang dan nomer dosen
        $params_catatan = [$catatan_post, $id_sidang, $nomor_dosen_login]; // ini nilai baru yang akan di simpan
    } else {// Jika catatan belum ada
        //karena insert butuh id_matkul, maka diambil terlebih dahulu melalui join dari Sidang -> Kelompok -> Id_matkul
        $id_matkul_untuk_insert = null;
        $sql_get_matkul = "SELECT k.id_matkul FROM Sidang s JOIN Kelompok k ON s.id_kelompok = k.id_kelompok WHERE s.id_sidang = ?";
        $stmt_get_matkul = sqlsrv_query($conn, $sql_get_matkul, [$id_sidang]);
        if($data_matkul = sqlsrv_fetch_array($stmt_get_matkul, SQLSRV_FETCH_ASSOC)){
            $id_matkul_untuk_insert = $data_matkul['id_matkul'];
        }

        //Setelah dia dapet id_matkul nya disini adalah proses insert ke detail_sidang dengan 4 kolom atau atribut
        // yaitu id_sidang, nomer_dosen,id_matkul,catatan_Sidang
        $sql_catatan = "INSERT INTO Detail_Sidang (id_sidang, nomor_dosen, id_matkul, catatan_sidang) VALUES (?, ?, ?, ?)";
        $params_catatan = [$id_sidang, $nomor_dosen_login, $id_matkul_untuk_insert, $catatan_post];
    }

    // 3. Eksekusi query catatan (UPDATE atau INSERT)
    $stmt_proses_catatan = sqlsrv_query($conn, $sql_catatan, $params_catatan);
    if ($stmt_proses_catatan === false) { //jika gagal / false maka :
        $_SESSION['error'] = "Gagal menyimpan catatan evaluasi: " . print_r(sqlsrv_errors(), true); // simpan eror ke session
        header("Location: dEvaluasiSidang.php"); //mengembalikan ke halaman sesuai url ini
        exit; // Hentikan program / hentikan script
    }    

    // Ngecek apakah sudah ada data nilai untuk mahasiswa (nim_post) dari dosen
    $sql_cek_nilai = "SELECT COUNT(*) as 'count' FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?";
    $stmt_cek_nilai = sqlsrv_query($conn, $sql_cek_nilai, [$id_sidang, $nomor_dosen_login, $nim_post]); // Count(*) menghitung beberapa baris yang cocok
    $nilai_exists = sqlsrv_fetch_array($stmt_cek_nilai, SQLSRV_FETCH_ASSOC)['count'] > 0; // Jika hasil >0 maka nilainya_exists

    if ($nilai_exists) { // jika sudah ada atau nilai > 0 maka : 
        //Update nilai dengan atribut n_dokumen,n presentasi,n_tanyajawab, n_proyek
        $sql_nilai = "UPDATE Penilaian SET n_dokumen = ?, n_presentasi = ?, n_tanyajawab = ?, n_proyek = ? WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?";
        $params_nilai = [$n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek, $id_sidang, $nomor_dosen_login, $nim_post];
    } else { // Jika belum ada maka
        //Menginsert data baru dan nilai nya di simpan ke pada tabel penilaian
        $sql_nilai = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, n_dokumen, n_presentasi, n_tanyajawab, n_proyek) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params_nilai = [$id_sidang, $nim_post, $nomor_dosen_login, $n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek];
    }

    //Jalankan query simpan ($sql_nilai) dengan parameyer ($params_nilai)
    $stmt_nilai = sqlsrv_query($conn, $sql_nilai, $params_nilai);
    if ($stmt_nilai === false) { // Jika eror atau false maka
        //Akan menampilkan pesan eror dan di simpan ke dalam session
        $_SESSION['error'] = "Gagal menyimpan nilai: " . print_r(sqlsrv_errors(), true);
        header("Location: dEvaluasiSidang.php"); // redirect ke halaman sesuai url nya
        exit;// scrip di berhentikan sementara
    }

    $_SESSION['sukses'] = "Data evaluasi berhasil disimpan."; // Gunakan session untuk pesan sukses
    header("Location: dEvaluasiSidang.php");    // Redirect dengan status sukses jika semua berhasil

    exit(); // hentikan script nya
}


// ===================================================================================
// BAGIAN 3: PENGAMBILAN DATA UNTUK DITAMPILKAN DI HALAMAN
// ===================================================================================

// Membuat variabel default 
$id_kelompok = null;
$judul = 'Data tidak ditemukan';
$ruangan = '-';
$tanggal_formatted = '-';
$jam = '-';
$nama_matkul_sidang = 'Mata Kuliah Tidak Ditemukan';
$mahasiswa = [];
$current_nim = '';
$current_nama_mhs = 'Data mahasiswa tidak ditemukan';
$nomor_kelompok = null;
$jenis_sidang = null;
$id_matkul = null;
$nim_perwakilan = null;

//  Membuat Query untuk mengambil data sidang dengan menjoinkan tabel 
$sql_sidang = "
    SELECT 
        s.judul, 
        k.nomor_kelompok, 
        k.id_kelompok, 
        k.jenis_sidang, 
        k.id_matkul,
        k.nim AS nim_perwakilan, -- INI KRUSIAL UNTUK MENGAMBIL DOSEN PENGAMPU
        mk.nama_matkul
    FROM Sidang s 
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
    LEFT JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
    WHERE s.id_sidang = ?";
$result_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]); // Eksekusi query untuk mengambil data sidang

if ($data_sidang = sqlsrv_fetch_array($result_sidang, SQLSRV_FETCH_ASSOC)) { // Jika data sidang berhasil diambil
    $judul = $data_sidang['judul']; // Ambil judul sidang
    $nomor_kelompok = $data_sidang['nomor_kelompok']; // Ambil nomor kelompok
    $id_kelompok = $data_sidang['id_kelompok']; // Ambil ID kelompok
    $jenis_sidang = $data_sidang['jenis_sidang']; // Ambil jenis sidang (Tugas Akhir/Semester)
    $id_matkul = $data_sidang['id_matkul']; // Ambil ID matkul
    $nim_perwakilan = $data_sidang['nim_perwakilan']; // Ambil NIM perwakilan kelompok
    $nama_matkul_sidang = $data_sidang['nama_matkul'] ?? 'Tidak Terkait'; // Ambil nama matkul atau fallback

    if (isset($nomor_kelompok)) { // Jika ada nomor kelompok
        $sql_mhs = "SELECT DISTINCT k.nim, m.nama_mhs
                    FROM Kelompok k
                    JOIN Mahasiswa m ON k.nim = m.nim
                    WHERE k.nomor_kelompok = ?
                    ORDER BY m.nama_mhs ASC"; // Query ambil mahasiswa dari kelompok
        $stmt_mhs = sqlsrv_query($conn, $sql_mhs, [$nomor_kelompok]); // Eksekusi query mahasiswa
        if ($stmt_mhs) { // Jika berhasil
            while ($row_mhs = sqlsrv_fetch_array($stmt_mhs, SQLSRV_FETCH_ASSOC)) { // Loop hasil query
                $mahasiswa[] = $row_mhs; // Masukkan ke array mahasiswa
            }
        }
    }

    if (isset($_SESSION['nim_aktif']) && in_array($_SESSION['nim_aktif'], array_column($mahasiswa, 'nim'))) {
        $current_nim = $_SESSION['nim_aktif']; // Gunakan nim dari session jika valid
    } elseif (!empty($mahasiswa)) {
        $current_nim = $mahasiswa[0]['nim']; // Kalau tidak, pakai nim mahasiswa pertama
        $_SESSION['nim_aktif'] = $current_nim; // Simpan ke session
    }

    foreach ($mahasiswa as $mhs) { // Loop mahasiswa
        if ($mhs['nim'] == $current_nim) { // Cek jika nim cocok
            $current_nama_mhs = $mhs['nama_mhs']; // Ambil nama mahasiswa
            break; // Hentikan loop
        }
    }

    $dosen_pembimbing_list = []; // Inisialisasi list dosen pembimbing
    $dosen_penguji_list = []; // Inisialisasi list dosen penguji
    $dosen_pengampu_list = []; // Inisialisasi list dosen pengampu
    $labelPembimbing = "Dosen"; // Label default

    if ($jenis_sidang == 'Tugas Akhir') { // Jika jenis Tugas Akhir
        $labelPembimbing = "Dosen Pembimbing"; // Ganti label

        $sql_pembimbing = "
            SELECT d.nama_dosen FROM Bimbingan b
            JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
            WHERE b.id_kelompok = ? AND b.isPembimbing = 1"; // Query dosen pembimbing
        $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, [$id_kelompok]); // Eksekusi
        if ($stmt_pembimbing) {
            while ($row = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC)) {
                $dosen_pembimbing_list[] = $row['nama_dosen']; // Tambahkan ke list
            }
        }

        $sql_penguji = "
            SELECT d.nama_dosen FROM Penjadwalan pj
            JOIN Dosen d ON pj.nomor_dosen = d.nomor_dosen
            WHERE pj.id_sidang = ? AND pj.peran_dosen = 0"; // Query dosen penguji (peran 0)
        $stmt_penguji = sqlsrv_query($conn, $sql_penguji, [$id_sidang]); // Eksekusi
        if ($stmt_penguji) {
            while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
                $dosen_penguji_list[] = $row['nama_dosen']; // Tambahkan ke list
            }
        }

    } elseif ($jenis_sidang == 'Semester') { // Jika jenis Semester
        $labelPembimbing = "Dosen Pengampu"; // Ganti label

        $sql_pengampu = "
            SELECT DISTINCT d.nama_dosen FROM Pengampu_Kelas pk
            JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
            WHERE pk.id_matkul = ? AND pk.id_kelas = (
                SELECT TOP 1 km.id_kelas FROM Kelas_Mahasiswa km WHERE km.nim = ? )
            ORDER BY d.nama_dosen DESC"; // Query dosen pengampu
        $stmt_pengampu = sqlsrv_query($conn, $sql_pengampu, [$id_matkul, $nim_perwakilan]); // Eksekusi
        if ($stmt_pengampu) {
            while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
                $dosen_pengampu_list[] = $row['nama_dosen']; // Tambah ke list
            }
        }

        $dosen_pembimbing_list = $dosen_pengampu_list; // Gunakan pengampu sebagai pembimbing
        $dosen_penguji_list = []; // Tidak ada penguji
    }

    $sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai FROM Jadwal WHERE id_sidang = ?"; // Query jadwal
    $result_jadwal = sqlsrv_query($conn, $sql_jadwal, [$id_sidang]); // Eksekusi
    if ($result_jadwal && $data_jadwal = sqlsrv_fetch_array($result_jadwal, SQLSRV_FETCH_ASSOC)) {
        $ruangan = $data_jadwal['ruang_sidang'] ?? '-'; // Ambil ruang

        $jam_mulai = $data_jadwal['jam_sidang'] ? $data_jadwal['jam_sidang']->format('H:i') : null; // Format jam mulai
        $jam_selesai = $data_jadwal['jam_selesai'] ? $data_jadwal['jam_selesai']->format('H:i') : null; // Format jam selesai

        if ($jam_mulai && $jam_selesai) {
            $jam = $jam_mulai . ' - ' . $jam_selesai; // Gabungkan jam
        } elseif ($jam_mulai) {
            $jam = $jam_mulai; // Hanya jam mulai
        }

        if ($data_jadwal['tanggal_sidang'] instanceof DateTime) {
            setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian'); // Set bahasa Indonesia
            $tanggal_formatted = strftime('%A, %d %B %Y', $data_jadwal['tanggal_sidang']->getTimestamp()); // Format tanggal
        }
    }

    $catatan_revisi = ''; // Default kosong
    $sql_catatan = "SELECT catatan_sidang FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?"; // Query ambil catatan
    $result_catatan = sqlsrv_query($conn, $sql_catatan, [$id_sidang, $nomor_dosen_login]); // Eksekusi
    if ($result_catatan && $row_catatan = sqlsrv_fetch_array($result_catatan, SQLSRV_FETCH_ASSOC)) {
        $catatan_revisi = $row_catatan['catatan_sidang']; // Simpan catatan
    }

    $nilai_mahasiswa = ['n_dokumen' => '', 'n_presentasi' => '', 'n_tanyajawab' => '', 'n_proyek' => '']; // Default nilai kosong
    if (!empty($current_nim)) {
        $sql_get_nilai = "SELECT n_dokumen, n_presentasi, n_tanyajawab, n_proyek FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?"; // Query nilai
        $result_get_nilai = sqlsrv_query($conn, $sql_get_nilai, [$id_sidang, $nomor_dosen_login, $current_nim]); // Eksekusi
        if ($result_get_nilai && $row_nilai = sqlsrv_fetch_array($result_get_nilai, SQLSRV_FETCH_ASSOC)) {
            $nilai_mahasiswa = $row_nilai; // Simpan nilai
        }
    }
}

$nilai_sudah_dikirim_dan_lengkap = false; // Inisialisasi

// Cek apakah semua nilai & catatan sudah diisi
if (
    isset($nilai_mahasiswa['n_dokumen'])    && $nilai_mahasiswa['n_dokumen']    !== '' && $nilai_mahasiswa['n_dokumen']    !== null &&
    isset($nilai_mahasiswa['n_presentasi']) && $nilai_mahasiswa['n_presentasi'] !== '' && $nilai_mahasiswa['n_presentasi'] !== null &&
    isset($nilai_mahasiswa['n_tanyajawab']) && $nilai_mahasiswa['n_tanyajawab'] !== '' && $nilai_mahasiswa['n_tanyajawab'] !== null &&
    isset($nilai_mahasiswa['n_proyek'])     && $nilai_mahasiswa['n_proyek']     !== '' && $nilai_mahasiswa['n_proyek']     !== null &&
    !empty(trim($catatan_revisi)) // Catatan tidak kosong
) {
    $nilai_sudah_dikirim_dan_lengkap = true; // Tandai sebagai lengkap
}

// Format daftar dosen pembimbing & penguji untuk ditampilkan ke HTML
$namaPembimbing_html = !empty($dosen_pembimbing_list) ? implode('<br>', array_map('htmlspecialchars', $dosen_pembimbing_list)) : 'Belum ditentukan'; // Gabungkan nama dosen
$namaPenguji_html = !empty($dosen_penguji_list) ? implode('<br>', array_map('htmlspecialchars', $dosen_penguji_list)) : 'Belum ditentukan'; // Gabungkan nama penguji

?>
