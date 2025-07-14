<?php
session_start();
if ($_SESSION['role'] !== 'dosen') {
    header("Location: ../../index.php");
    exit();
}

if (!isset($_SESSION['user_data']['nomor_dosen'])) {
    die("Error: Data dosen tidak ditemukan di session. Silakan login kembali.");
}
$nomorDosen = $_SESSION['user_data']['nomor_dosen'];

include '../../koneksi/koneksiAndrew.php';
require_once '../../control/get_unread_notif.php';

if ($conn === false) {
    die("Koneksi gagal: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id_sidang'])) {
    
    $id_sidang = (int)$_POST['id_sidang'];
    $action = $_POST['action'];
    $newStatus = $action === 'accept' ? 'Approved' : ($action === 'reject' ? 'Rejected' : null);

    if (!$newStatus) {
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
        exit();
    }

    // --- Ambil info Sidang ---
    $sql = "SELECT s.id_sidang, s.id_kelompok, k.jenis_sidang, k.id_matkul, k.tahun_ajaran
            FROM Sidang s
            JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
            WHERE s.id_sidang = ?";
    $stmt = sqlsrv_query($conn, $sql, [$id_sidang]);
    $sidang = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if (!$sidang) {
        echo json_encode(['success' => false, 'message' => 'Sidang tidak tersedia.']);
        exit();
    }

    $authorized = false;
    if ($sidang['jenis_sidang'] === 'Tugas Akhir') {
        $sqlAuth = "SELECT 1 FROM Bimbingan WHERE id_kelompok = ? AND nomor_dosen = ? AND isPembimbing = 1";
        $stmtAuth = sqlsrv_query($conn, $sqlAuth, [$sidang['id_kelompok'], $nomorDosen]);
        $authorized = sqlsrv_fetch($stmtAuth) !== false;
    } elseif ($sidang['jenis_sidang'] === 'Semester') {
        $sqlAuth = "SELECT 1 FROM Pengampu_Kelas WHERE id_matkul = ? AND tahun_ajaran = ? AND nomor_dosen = ?";
        $stmtAuth = sqlsrv_query($conn, $sqlAuth, [$sidang['id_matkul'], $sidang['tahun_ajaran'], $nomorDosen]);
        $authorized = sqlsrv_fetch($stmtAuth) !== false;
    }

    if (!$authorized) {
        echo json_encode(['success' => false, 'message' => 'Kamu nggak berhak ngubah status pengajuan ini.']);
        exit();
    }

    // --- Update status ajuannya ---
    $updateSql = "UPDATE Sidang SET status_ajuan = ? WHERE id_sidang = ?";
    $updateStmt = sqlsrv_query($conn, $updateSql, [$newStatus, $id_sidang]);
    if ($updateStmt === false) {
        echo json_encode(['success' => false, 'message' => 'Gagal ngubah status.']);
        exit();
    }
    echo json_encode(['success' => true, 'message' => 'Status pengajuan berhasil diubah.']);
    exit();
}


// Atur paginasinya (buat halaman-halaman)
$rowsPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Ini filter buat statusnya
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'Pending';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Semua';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$offset = max(0, ($currentPage - 1) * $rowsPerPage);

// Langkah 1: Bikin query dasarnya dulu, isinya semua join sama kolom yang dibutuhin.
$querySource = "
(
    SELECT
        s.id_sidang,
        s.status_ajuan,
        ku.id_kelompok,
        ku.nomor_kelompok,
        s.judul,
        mk.nama_matkul,
        ku.jenis_sidang AS tipe_sidang_text,
        CASE
            WHEN ku.jenis_sidang = 'Tugas Akhir' THEN
                (SELECT STRING_AGG(d.nama_dosen, ', ') FROM Bimbingan b JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = ku.id_kelompok AND b.isPembimbing = 1)
            ELSE
                (SELECT STRING_AGG(d.nama_dosen, ', ') FROM Pengampu_Kelas pk JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen WHERE pk.id_matkul = ku.id_matkul)
        END AS nama_dosen,
        CASE
            WHEN ku.jenis_sidang = 'Tugas Akhir' THEN
                (SELECT STRING_AGG(CAST(b.nomor_dosen AS VARCHAR), ',') FROM Bimbingan b WHERE b.id_kelompok = ku.id_kelompok AND b.isPembimbing = 1)
            ELSE
                (SELECT STRING_AGG(CAST(pk.nomor_dosen AS VARCHAR), ',') FROM Pengampu_Kelas pk WHERE pk.id_matkul = ku.id_matkul)
        END AS list_nomor_dosen
    FROM
        Sidang s
    JOIN
        (SELECT DISTINCT id_kelompok, nomor_kelompok, tahun_ajaran, jenis_sidang, id_matkul FROM dbo.Kelompok) AS ku ON s.id_kelompok = ku.id_kelompok
    JOIN
        MataKuliah mk ON ku.id_matkul = mk.id_matkul
) AS FullDataSet
";

// Langkah 2: Bikin klausa WHERE sama parameternya secara dinamis.
$whereConditions = [];
$params = [];

// Kondisi dasar: Dosen yang lagi login
$whereConditions[] = "list_nomor_dosen LIKE ?";
array_push($params, '%' . $nomorDosen . '%');

// [MODIFIKASI] Terapin kondisi filter statusnya secara dinamis
if ($statusFilter === 'History') {
    $whereConditions[] = "status_ajuan IN ('Approved', 'Rejected')";
} else if (in_array($statusFilter, ['Pending', 'Approved', 'Rejected'])) {
    $whereConditions[] = "status_ajuan = ?";
    array_push($params, $statusFilter);
}

// Terapin kondisi filter jenis sidang
if ($filter === 'TA') {
    $whereConditions[] = "tipe_sidang_text = 'Tugas Akhir'";
} elseif ($filter === 'Semester') {
    $whereConditions[] = "tipe_sidang_text = 'Semester'";
}

if (!empty($search)) {
    // Cari di beberapa kolom yang relevan
    $whereConditions[] = "(nomor_kelompok LIKE ? OR judul LIKE ? OR nama_matkul LIKE ?)";
    // Tambahkan parameter pencarian sebanyak jumlah kolom yang dicari
    array_push($params, '%' . $search . '%', '%' . $search . '%', '%' . $search . '%');
}


// Gabungin semua kondisi jadi satu string
$whereClause = "WHERE " . implode(" AND ", $whereConditions);

// ... (Sisa kode PHP untuk pagination dan eksekusi query tetap sama) ...
$countSql = "SELECT COUNT(*) as total FROM " . $querySource . " " . $whereClause;
$countStmt = sqlsrv_query($conn, $countSql, $params);
if ($countStmt === false) {
    die("Error saat menghitung data: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
$totalRecords = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)['total'] ?? 0;
$totalPages = ($rowsPerPage > 0) ? ceil($totalRecords / $rowsPerPage) : 1;

if ($totalPages > 0 && $currentPage > $totalPages) {
    $currentPage = $totalPages;
    $offset = max(0, ($currentPage - 1) * $rowsPerPage);
}

$mainSql = "SELECT * FROM " . $querySource . " " . $whereClause . "
            ORDER BY id_sidang DESC
            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

$mainParams = array_merge($params, [$offset, $rowsPerPage]);
$result = sqlsrv_query($conn, $mainSql, $mainParams);

if ($result === false) {
    die("Error saat mengambil data: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
$nomor = max(1, $offset + 1); 

// Ambil jumlah notifikasi belum dibaca untuk dosen
// Hapus seluruh query unread notif lama dan variabel terkait

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/dPengajuan.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Dosen - Pengajuan</title>
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>
        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons d-flex d-md-none">
                <a href="dNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill position-relative"><?php if ($unread_count > 0): ?><span class="notif-badge"> <?= $unread_count ?> </span><?php endif; ?></i>
                </a>
                <div class="profile-icon">
                    <a href="dProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
        <main class="NavSide__main-content" id="dBeranda">
            <div class="dashboard-header">
                <div class="header-title-group">
                    <h2 class="bodyHeading">Pengajuan Sidang</h2>
                    <h2 class="text-heading mt-3" style="font-size:1.5rem; font-weight:500;">
                        <?php echo isset($_SESSION['user_data']['nama_dosen']) ? htmlspecialchars($_SESSION['user_data']['nama_dosen']) : 'Dosen'; ?> (Dosen)
                    </h2>
                </div>
                <div class="header-icons d-none d-md-flex">
                    <a href="dNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill position-relative"><?php if ($unread_count > 0): ?><span class="notif-badge"> <?= $unread_count ?> </span><?php endif; ?></i></a>
                    <div class="profile-icon">
                        <a href="dProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row"></div><br><br>
                <div class="row">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <label for="ddStatus" class="fw-semibold mb-0">Status:</label>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddStatus">
                                <?php
                                if ($statusFilter === 'Pending') echo 'Pengajuan Aktif';
                                elseif ($statusFilter === 'History') echo 'Semua';
                                elseif ($statusFilter === 'Approved') echo 'Disetujui';
                                elseif ($statusFilter === 'Rejected') echo 'Ditolak';
                                ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?status=Pending&filter=<?= urlencode($filter) ?>">Pengajuan Aktif</a></li>
                                <li><a class="dropdown-item" href="?status=History&filter=<?= urlencode($filter) ?>">Semua</a></li>
                                <li><a class="dropdown-item" href="?status=Approved&filter=<?= urlencode($filter) ?>">Disetujui</a></li>
                                <li><a class="dropdown-item" href="?status=Rejected&filter=<?= urlencode($filter) ?>">Ditolak</a></li>
                            </ul>
                        </div>

                        <label for="ddMsidang" class="fw-semibold mb-0 ms-3">Filter:</label>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMSidang">
                                <?php
                                if ($filter === 'TA') echo 'Sidang TA';
                                elseif ($filter === 'Semester') echo 'Sidang Semester';
                                else echo 'Semua';
                                ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?status=<?= urlencode($statusFilter) ?>&filter=Semua">Semua</a></li>
                                <li><a class="dropdown-item" href="?status=<?= urlencode($statusFilter) ?>&filter=TA">Sidang TA</a></li>
                                <li><a class="dropdown-item" href="?status=<?= urlencode($statusFilter) ?>&filter=Semester">Sidang Semester</a></li>
                            </ul>
                        </div>
                        <div class="search-input-group ms-auto d-flex align-items-center">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari Kelompok, Judul, Matkul...">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn kelompok-btn" style="max-width:300px;" onclick="openKelompokModal()" id="kelompokBtn">
                            <i class="bi bi-people-fill me-2"></i>Kelompok
                        </button>
                    </div>
                </div>
                <div class="row">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nomor Kelompok</th>
                                <th scope="col">Judul</th>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Dosen Pembimbing / Pengampu</th>
                                <th scope="col">Jenis Sidang</th>
                                <?php if ($statusFilter !== 'Pending') : ?>
                                    <th scope="col">Status</th>
                                <?php endif; ?>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($totalRecords > 0 && sqlsrv_has_rows($result)) : ?>
                                <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) : ?>
                                    <tr class="isiTabel jadiBiru">
                                        <td><?= $nomor++; ?></td>
                                        <td><?= htmlspecialchars($row['nomor_kelompok']); ?></td>
                                        <td><?= htmlspecialchars($row['judul'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($row['nama_matkul'] ?? 'N/A'); ?></td>
                                       <td>
                                            <?php
                                            // Jika jenis sidang adalah 'Tugas Akhir', tampilkan normal dari database
                                            if ($row['tipe_sidang_text'] === 'Tugas Akhir') {
                                                echo htmlspecialchars($row['nama_dosen']);
                                            } 
                                            // Jika 'Semester', tampilkan hanya nama dosen yang sedang login dari session
                                            else { 
                                                echo htmlspecialchars($_SESSION['user_data']['nama_dosen']);
                                            }
                                            ?>
                                        </td>
                                        <td><?= ($row['tipe_sidang_text'] === 'Tugas Akhir') ? 'TA' : 'Semester'; ?></td>
                                        <?php if ($statusFilter !== 'Pending') : ?>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                if ($row['status_ajuan'] == 'Approved') {
                                                    $statusClass = 'text-success fw-bold';
                                                } elseif ($row['status_ajuan'] == 'Rejected') {
                                                    $statusClass = 'text-danger fw-bold';
                                                }
                                                echo '<span class="' . $statusClass . '">' . htmlspecialchars($row['status_ajuan']) . '</span>';
                                                ?>
                                            </td>
                                        <?php endif; ?>
                                        <td style="text-align: center;">
                                            <form action="dDetailPengajuan.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="id_sidang" value="<?= $row['id_sidang']; ?>">
                                                <input type="hidden" name="tipe" value="<?= ($row['tipe_sidang_text'] === 'Tugas Akhir') ? 'TA' : 'Semester'; ?>">
                                                
                                                <input type="hidden" name="from_status" value="<?= htmlspecialchars($statusFilter) ?>">
                                                <input type="hidden" name="from_filter" value="<?= htmlspecialchars($filter) ?>">
                                                <input type="hidden" name="from_page" value="<?= htmlspecialchars($currentPage) ?>">

                                                <button type="submit" class="detail-btn">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="<?= ($statusFilter !== 'Pending') ? '8' : '7' ?>" class="text-center" style="padding: 20px;">Tidak ada data ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="pagination-container" id="paginationContainer" style="display: none;">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center" id="paginationList">
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
             <?php if ($totalPages > 0): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php
                                    $queryParams = "status=" . urlencode($statusFilter) . "&filter=" . urlencode($filter) . "&search=" . urlencode($search);
                                    ?>
                                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>&<?= $queryParams ?>">«</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&<?= $queryParams ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>&<?= $queryParams ?>">»</a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>

            <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div style="background-color: rgb(67, 54, 240);">
                            <div class="modal-header">
                                <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                            </div>
                        </div>
                        <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
                        <div class="modal-footer justify-content-center border-0"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button><button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button></div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="kelompokModal" tabindex="-1" aria-labelledby="kelompokModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header modal-header-custom">
                            <h5 class="modal-title" id="kelompokModalLabel">Kelompok Mahasiswa</h5>
                            <div class="modal-header-actions">
                                <button type="button" class="btn btn-outline-light btn-sm me-2" id="newKelompokBtn" onclick="resetToCreateMode()" style="display: none;">
                                    <i class="bi bi-plus-circle me-1"></i>Kelompok Baru
                                </button>
                                <button type="button" class="btn-close btn-close-white" onclick="closeKelompokModal()" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="modal-tab-container">
                                <button class="modal-tab active" id="tambah-tab-btn" onclick="switchTab('tambah')">Tambah Kelompok</button>
                                <button class="modal-tab" onclick="switchTab('daftar')">Daftar Kelompok</button>
                            </div>
                            <div id="tambah-tab" class="modal-tab-content active">
                                <div class="kelompok-form-container">
                                    <form id="kelompokForm" autocomplete="off">
                                        <div class="kelompok-form-group">
                                            <label for="nomor_kelompok">Nomor Kelompok:</label>
                                            <input type="text" id="nomor_kelompok" name="nomor_kelompok" readonly />
                                        </div>

                                        <div class="kelompok-form-group">
                                            <label for="tahun_ajaran">Tahun Ajaran:</label>
                                            <input type="text" id="tahun_ajaran" name="tahun_ajaran" value="<?= date('Y') ?>" readonly />
                                        </div>

                                        <div class="kelompok-form-group">
                                            <label for="kelompok_prodi">Prodi:</label>
                                            <div class="custom-select-wrapper">
                                                <select id="kelompok_prodi" name="kelompok_prodi" onchange="filterMahasiswaByProdi()">
                                                    <option value="">Pilih Prodi</option>
                                                    <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                                                    <option value="Manajemen Informatika">Manajemen Informatika</option>
                                                </select>
                                                <i class="bi bi-caret-down-fill custom-select-arrow"></i>
                                            </div>
                                        </div>

                                        <div class="kelompok-form-group">
                                            <label for="jenis_sidang">Jenis Sidang:</label>
                                            <div class="custom-select-wrapper">
                                                <select id="jenis_sidang" name="jenis_sidang" required>
                                                    <option value="">Pilih Jenis Sidang</option>
                                                    <option value="Tugas Akhir">Tugas Akhir</option>
                                                    <option value="Semester">Semester</option>
                                                </select>
                                                <i class="bi bi-caret-down-fill custom-select-arrow"></i>
                                            </div>
                                        </div>

                                        <div class="kelompok-form-group" id="matkul-group" style="display:none;">
                                            <label for="id_matkul">Mata Kuliah:</label>
                                            <select id="id_matkul" name="id_matkul"></select>
                                        </div>

                                        <div class="form-section-card" id="dosen-wrapper-group" style="display:none;">
                                            <div class="form-section-title">Dosen Pembimbing <span class="text-muted">(Opsional - Anda otomatis menjadi pembimbing)</span></div>
                                            <div class="dosen-wrapper" id="dosen-wrapper"></div>
                                        </div>

                                        <div class="form-section-card">
                                            <div class="form-section-title">Anggota Mahasiswa</div>
                                            <div class="anggota-wrapper" id="anggota-wrapper">
                                                <div class="anggota-form-group" id="anggota-form-1">
                                                    <label for="anggota_1">Anggota 1:</label>
                                                    <div class="anggota-input-group">
                                                        <div class="input-container">
                                                            <input type="text" id="anggota_1" name="anggota[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, 1)" />
                                                            <div class="autocomplete-dropdown" id="autocomplete_1" style="display: none;"></div>
                                                        </div>
                                                        <div class="anggota-nama-display" id="anggota_nama_1">Nama akan muncul otomatis</div>
                                                        <div class="form-toggle-buttons">
                                                            <button type="button" onclick="addAnggota()">+</button>
                                                            <button type="button" onclick="removeAnggota()" style="display: none;">-</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="kelompok-form-actions modal-footer border-0">
                                            <button type="button" class="btn btn-danger" onclick="closeKelompokModal()">Batalkan</button>
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div id="daftar-tab" class="modal-tab-content">
                                <div class="kelompok-filter-container mb-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="filter-semester" value="Semester" checked>
                                            <label class="form-check-label" for="filter-semester">
                                                Semester
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="filter-tugas-akhir" value="Tugas Akhir" checked>
                                            <label class="form-check-label" for="filter-tugas-akhir">
                                                Tugas Akhir
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="kelompok-list-container" id="kelompok-list-container">
                                    <p class="text-center text-muted">Memuat daftar kelompok...</p>
                                </div>
                                <div class="kelompok-form-actions modal-footer justify-content-center border-0">
                                    <button type="button" class="btn btn-danger" onclick="closeKelompokModal()">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div> 
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/kelompokModal.js"></script>
    <script src="../../assets/js/dPengajuan.js"></script>
</body>
</html>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">