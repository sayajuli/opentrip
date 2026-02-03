<?php 
$pageTitle = "Kelola Jadwal Trip";
include 'include/header.php'; 
include 'include/sidebar.php'; 
include '../api/auto_update.php';
require '../config/koneksi.php';

// --- 1. SETUP SEARCH, FILTER & PAGINATION ---

// Batas & Halaman
$batas = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// Tangkap Filter
$keyword = isset($_GET['cari']) ? $_GET['cari'] : "";
$filter_status = isset($_GET['status']) ? $_GET['status'] : "";
$filter_gunung = isset($_GET['gunung']) ? $_GET['gunung'] : "";

// --- 2. QUERY DINAMIS ---
$where_clause = "WHERE 1=1";
$params = [];

// A. Filter Keyword (Gunung atau Penjaga)
if (!empty($keyword)) {
    $where_clause .= " AND (gunung.nama_gunung LIKE :keyword OR users.nama_lengkap LIKE :keyword)";
    $params[':keyword'] = "%$keyword%";
}

// B. Filter Status
if (!empty($filter_status)) {
    $where_clause .= " AND jadwal.status_trip = :status";
    $params[':status'] = $filter_status;
}

// C. Filter Spesifik Gunung
if (!empty($filter_gunung)) {
    $where_clause .= " AND jadwal.id_gunung = :id_gunung";
    $params[':id_gunung'] = $filter_gunung;
}

// --- 3. EKSEKUSI QUERY ---

// A. Hitung Total Data (Buat Pagination)
$sql_count = "SELECT COUNT(*) FROM jadwal 
              JOIN gunung ON jadwal.id_gunung = gunung.id_gunung
              LEFT JOIN users ON jadwal.id_penjaga = users.id_user 
              $where_clause";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->execute($params);
$jumlah_data = $stmt_count->fetchColumn();
$total_halaman = ceil($jumlah_data / $batas);

// B. Ambil Data Halaman Ini
$sql_data = "SELECT jadwal.*, gunung.nama_gunung, users.nama_lengkap as guide 
             FROM jadwal 
             JOIN gunung ON jadwal.id_gunung = gunung.id_gunung
             LEFT JOIN users ON jadwal.id_penjaga = users.id_user 
             $where_clause 
             ORDER BY jadwal.tanggal_berangkat DESC 
             LIMIT $halaman_awal, $batas";
$stmt = $conn->prepare($sql_data);
$stmt->execute($params);

// C. Ambil Data Master untuk Dropdown Filter & Modal
$opt_gunung = $conn->query("SELECT * FROM gunung ORDER BY nama_gunung ASC");
$opt_penjaga = $conn->query("SELECT * FROM users WHERE role='penjaga' ORDER BY nama_lengkap ASC");

$list_gunung = $opt_gunung->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success"><i class="fa-solid fa-calendar-days"></i> Jadwal Open Trip</h3>
        <button type="button" class="btn btn-alam" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fa-solid fa-plus"></i> Buat Jadwal Baru
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            
            <form method="GET" class="row g-2 mb-4 align-items-center">
                <div class="col-auto">
                    <select name="limit" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="10" <?= $batas == 10 ? 'selected' : '' ?>>10 Data</option>
                        <option value="20" <?= $batas == 20 ? 'selected' : '' ?>>20 Data</option>
                        <option value="50" <?= $batas == 50 ? 'selected' : '' ?>>50 Data</option>
                    </select>
                </div>

                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Status Trip --</option>
                        <option value="buka" <?= $filter_status == 'buka' ? 'selected' : '' ?>>Buka</option>
                        <option value="tutup" <?= $filter_status == 'tutup' ? 'selected' : '' ?>>Tutup (Full)</option>
                        <option value="selesai" <?= $filter_status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="batal" <?= $filter_status == 'batal' ? 'selected' : '' ?>>Batal</option>
                    </select>
                </div>

                <div class="col-auto">
                    <select name="gunung" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Pilih Gunung --</option>
                        <?php foreach($list_gunung as $g) { ?>
                            <option value="<?= $g['id_gunung']; ?>" <?= $filter_gunung == $g['id_gunung'] ? 'selected' : '' ?>>
                                <?= $g['nama_gunung']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col">
                    <div class="input-group input-group-sm">
                        <input type="text" name="cari" class="form-control" placeholder="Cari gunung / guide..." value="<?= $keyword; ?>">
                        <button class="btn btn-outline-success" type="submit"><i class="fa-solid fa-search"></i></button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tujuan</th>
                            <th>Tanggal & Durasi</th>
                            <th>Harga & Kuota</th>
                            <th>Penjaga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($stmt->rowCount() > 0) {
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $start = new DateTime($row['tanggal_berangkat']);
                                $end = new DateTime($row['tanggal_selesai']);
                                $durasi = $start->diff($end)->days + 1;
                        ?>
                        <tr>
                            <td class="fw-bold"><?= $row['nama_gunung']; ?></td>
                            <td>
                                <small class="text-muted"><i class="fa-solid fa-clock"></i> <?= $durasi; ?> Hari</small><br>
                                <span><?= date('d M', strtotime($row['tanggal_berangkat'])); ?> - <?= date('d M Y', strtotime($row['tanggal_selesai'])); ?></span>
                            </td>
                            <td>
                                <div class="text-success fw-bold">Rp <?= number_format($row['harga'],0,',','.'); ?></div>
                                <small class="text-muted">Max: <?= $row['kuota_maks']; ?> Org</small>
                            </td>
                            <td>
                                <?php if($row['guide']): ?>
                                    <span class="badge bg-info text-dark"><i class="fa-solid fa-user"></i> <?= $row['guide']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Belum ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    if($row['status_trip'] == 'buka') echo '<span class="badge bg-success">Buka</span>';
                                    elseif($row['status_trip'] == 'tutup') echo '<span class="badge bg-secondary">Tutup</span>';
                                    elseif($row['status_trip'] == 'selesai') echo '<span class="badge bg-primary">Selesai</span>';
                                    elseif($row['status_trip'] == 'batal') echo '<span class="badge bg-danger" data-bs-toggle="tooltip" title="'.$row['alasan_batal'].'">Batal <i class="fa-solid fa-circle-info"></i></span>';
                                ?>
                            </td>
                            <td>
                                  <a href="jadwal_detail.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-sm btn-info text-white rounded-pill px-3 mb-1">
                                      <i class="fa-solid fa-users-viewfinder"></i> Peserta
                                  </a>
                              
                                <a href="jadwal_edit.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-sm btn-warning text-white rounded-pill px-3">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <?php if($row['status_trip'] == 'buka' || $row['status_trip'] == 'tutup'): ?>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 btn-batal" 
                                        data-id="<?= $row['id_jadwal']; ?>" 
                                        data-gunung="<?= $row['nama_gunung']; ?>">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else { 
                        ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-calendar-xmark fa-2x mb-3"></i><br>
                                Jadwal tidak ditemukan.
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Total <?= $jumlah_data; ?> data</small>
                
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= $halaman == 1 ? 'disabled' : '' ?>">
                            <a class="page-link text-success" href="?halaman=<?= $halaman - 1; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&status=<?= $filter_status; ?>&gunung=<?= $filter_gunung; ?>"><</a>
                        </li>
                        
                        <?php for($i = 1; $i <= $total_halaman; $i++) : ?>
                            <li class="page-item <?= $halaman == $i ? 'active' : '' ?>">
                                <a class="page-link <?= $halaman == $i ? 'bg-success border-success' : 'text-success' ?>" 
                                   href="?halaman=<?= $i; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&status=<?= $filter_status; ?>&gunung=<?= $filter_gunung; ?>">
                                    <?= $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $halaman == $total_halaman ? 'disabled' : '' ?>">
                            <a class="page-link text-success" href="?halaman=<?= $halaman + 1; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&status=<?= $filter_status; ?>&gunung=<?= $filter_gunung; ?>">></a>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Buat Jadwal Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../api/admin_jadwal.php" method="POST">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Pilih Gunung</label>
                            <select name="id_gunung" class="form-select" required>
                                <option value="">-- Pilih Destinasi --</option>
                                <?php foreach($list_gunung as $g) { ?>
                                    <option value="<?= $g['id_gunung']; ?>"><?= $g['nama_gunung']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Pilih Penjaga (Guide)</label>
                            <select name="id_penjaga" class="form-select">
                                <option value="">-- Belum Ditentukan --</option>
                                <?php while($p = $opt_penjaga->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?= $p['id_user']; ?>"><?= $p['nama_lengkap']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Berangkat</label>
                            <input type="date" name="tgl_berangkat" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Selesai (Pulang)</label>
                            <input type="date" name="tgl_selesai" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Harga per Pax (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="harga" class="form-control input-rupiah" placeholder="Contoh: 1.500.000" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Kuota Maksimal</label>
                            <input type="number" name="kuota" class="form-control" value="20" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Deskripsi / Fasilitas Trip Ini</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Contoh: Include makan 3x, tenda, tiket masuk..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="btn_simpan" class="btn btn-alam">Terbitkan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        const btnBatal = document.querySelectorAll('.btn-batal');
        btnBatal.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const gunung = this.getAttribute('data-gunung');
                
                Swal.fire({
                    title: 'Batalkan Trip ke ' + gunung + '?',
                    text: "Masukkan alasan pembatalan untuk memberitahu user.",
                    input: 'textarea',
                    inputLabel: 'Alasan Pembatalan',
                    inputPlaceholder: 'Contoh: Cuaca ekstrem, badai...',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Batalkan Trip',
                    cancelButtonText: 'Jangan',
                    inputValidator: (value) => {
                        if (!value) { return 'Kamu harus menulis alasannya bang!' }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `../api/admin_jadwal.php?batal=${id}&alasan=${encodeURIComponent(result.value)}`;
                    }
                })
            });
        });
    });
</script>

<?php 
if(isset($_GET['pesan'])) { 
    if($_GET['pesan'] == 'sukses') echo "<script>Swal.fire('Berhasil', 'Jadwal trip diterbitkan!', 'success');</script>";
    elseif($_GET['pesan'] == 'update') echo "<script>Swal.fire('Berhasil', 'Jadwal diupdate', 'success');</script>";
    elseif($_GET['pesan'] == 'dibatal') echo "<script>Swal.fire('Trip Dibatalkan', 'Status berhasil diubah jadi Batal.', 'success');</script>";
}
include 'include/footer.php'; 
?>