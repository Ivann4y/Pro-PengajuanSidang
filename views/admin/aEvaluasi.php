<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$path_to_root = '../../';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
  $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
  header("Location: " . $path_to_root . "index.php");
  exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
  header("Location: " . $path_to_root . "index.php");
  exit();
}

require "../../koneksi/koneksiAndrew.php";

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

$statusList = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $statusList[] = $row['status_revisi'];
  $allRows[] = $row; // simpan baris-baris untuk ditampilkan nanti
}

// Reset ulang pointer untuk looping nanti
sqlsrv_free_stmt($stmt);

// Hitung status global
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


?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Sidang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../css/button-styles.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../../assets/css/aEvaluasi.css?v=<?= time() ?>">
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
          <a href="aDetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span></a>
        </li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
          <b></b><b></b>
          <a href="aEvaluasi.php"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="aNilaiAkhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
        </li>
      </ul>
    </div>
    <div id="main-sidebar" class="NavSide__sidebar">
      <div class="NavSide__sidebar-brand">
        <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
      </div>
      <ul class="NavSide__sidebar-nav">
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="aDetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span></a>
        </li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
          <b></b><b></b>
          <a href="aEvaluasi.php"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="aNilaiAkhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
        </li>
      </ul>
    </div>

    <div class="NavSide__topbar">
      <div class="NavSide__toggle">
        <i class="bi bi-list open"></i>
        <i class="bi bi-x-lg close"></i>
      </div>
      <div class="header-icons">
        <i class="bi bi-bell-fill"></i>
        <div class="profile-icon">
          <i class="bi bi-person-fill fs-5"></i>
        </div>
      </div>
    </div>
    <main class="NavSide__main-content">

      <div>
        <h2 class="judul text-heading text-black" style="font-weight: 700;">Detail Evaluasi - <?= $judulSidang ?></h2>
      </div>
      <div class="d-flex justify-content-between mt-2">
        <h5>Catatan Perbaikan</h5>
        <span class="badge-custom <?= $badgeClass ?>">
          Status Revisi : <?= $statusRevisiGlobal ?>
        </span>
      </div>

      <?php foreach ($allRows as $row): ?>
        <?php $peranDosen = unpack("C", $row['peran_dosen'])[1]; ?>
        <?php
        $catatanFull = $row['catatan_sidang'];
        $catatanPreview = strlen($catatanFull) > 50 ? substr($catatanFull, 0, 50) . '...' : $catatanFull;
        ?>
        <div class="card-comment mt-4" data-bs-toggle="modal"
          data-bs-target="#modalDetail"
          data-catatan="<?= htmlspecialchars($catatanFull, ENT_QUOTES) ?>">
          <h6 class="card-h">
            <?= htmlspecialchars($row['nama_dosen']) ?>
            - <?= ($peranDosen == 1 ? 'Pembimbing' : 'Penguji') ?>
          </h6>

          <p class="mt-2 mb-0 text-truncate-2">
            <?= htmlspecialchars($catatanPreview) ?>
          </p>

          <?php if ($row['status_revisi'] === 'Pending'): ?>
            <div class="badge-statusDosen badge-warning"><?= $row['status_revisi'] ?></div>
          <?php elseif ($row['status_revisi'] === 'Disetujui'): ?>
            <div class="badge-statusDosen badge-success"><?= $row['status_revisi'] ?></div>
          <?php elseif ($row['status_revisi'] === 'Ditolak'): ?>
            <div class="badge-statusDosen badge-danger"><?= $row['status_revisi'] ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>


      <?php if (!empty($allRows[0]['dok_revisi'])): ?>
        <div class="revision-card shadow-sm">
          <h5 class="fw-bold text-primary">Dokumen Revisi</h5>
          <div class="revision-cardUp">
            <div class="text-center mt-3">
              <a href="../../<?= htmlspecialchars($allRows[0]['dok_revisi']) ?>" download="<?= htmlspecialchars($allRows[0]['nama_file']) ?>" target="_blank" style="text-decoration:none">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#8d99ae" class="bi bi-file-earmark-text-fill" viewBox="0 0 16 16">
                  <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.707 0H9.293zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM4.5 9a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 10.5a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 12a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1h-4z" />
                </svg>
                <p class="text-center text-muted small mt-3"><b><?= htmlspecialchars($allRows[0]['nama_file']) ?></b></p>
              </a>
            </div>
          </div>
        </div>

        <div id="downloadContainer"></div>

        <script>
          const containerDiv = document.createElement("div");
          containerDiv.className = "d-flex justify-content-end mt-4";

          const downloadLink = document.createElement("a");
          downloadLink.href = "<?= htmlspecialchars($allRows[0]['dok_revisi']) ?>";
          downloadLink.className = "btn-custom-primaryUnd";
          downloadLink.id = "btnUnduh";
          downloadLink.setAttribute("download", "<?= htmlspecialchars($allRows[0]['nama_file']) ?>");
          downloadLink.textContent = "Unduh";

          containerDiv.appendChild(downloadLink);
          document.getElementById("downloadContainer").appendChild(containerDiv);
        </script>

      <?php else: ?>
        <div class="revision-card shadow-sm">
          <h5 class="fw-bold text-primary">Dokumen Revisi</h5>
          <div class="revision-cardUp text-center">
            <p class="text-muted mt-3">Belum ada dokumen revisi yang diunggah.</p>
          </div>
        </div>
      <?php endif; ?>


      <div class="button-group-bottom mt-4">
        <button id="btnKembali" class="btn-custom-primary" onclick="location.href= 'aDaftarSidang.php'">
          <span class="icon-circle">
            <i class="fa-solid fa-arrow-left"></i>
          </span>
          Kembali
        </button>
      </div>

      <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-start">
              <h4 id="modalDetailLabel" class="fw-bold text-primary">Detail Catatan Perbaikan</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-3 pb-2">
              <p id="modalCatatanText"></p>
            </div>
            <div class="modal-footer border-0 justify-content-end">
              <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>

  <script>
    const modalDetail = document.getElementById('modalDetail');
    modalDetail.addEventListener('show.bs.modal', function(event) {
      const trigger = event.relatedTarget;
      const fullCatatan = trigger.getAttribute('data-catatan');
      document.getElementById('modalCatatanText').textContent = fullCatatan;
    });
  </script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>
    // Sidebar Toggle Logic
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");

    menuToggle.onclick = function() {
      menuToggle.classList.toggle("NavSide__toggle--active");
      sidebar.classList.toggle("NavSide__sidebar--active-mobile");
    };

    // Sidebar Active Item Logic (no change needed here as it's already functional)
    let listItems = document.querySelectorAll(".NavSide__sidebar-item");
    for (let i = 0; i < listItems.length; i++) {
      listItems[i].onclick = function() {
        if (!this.classList.contains("NavSide__sidebar-item--active")) {
          for (let j = 0; j < listItems.length; j++) {
            listItems[j].classList.remove("NavSide__sidebar-item--active");
          }
          this.classList.add("NavSide__sidebar-item--active");
        }
      };
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


  <script>
    <?php
    if (!empty($pesan) && strpos(strtolower($pesan), 'sukses') !== false):
    ?>
        <
        script >
        Swal.fire({
          title: 'Berhasil!',
          text: 'Dokumen Anda telah berhasil diunggah.',
          icon: 'success',
          confirmButtonColor: '#007bff'
        });
  </script>
<?php endif; ?>
</body>

</html>