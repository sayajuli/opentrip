<?php 
$pageTitle = "Detail Peserta Trip";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';

$id_jadwal = $_GET['id'];
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'semua';

// 1. Ambil Data Jadwal & Gunung
$sql_jadwal = "SELECT jadwal.*, gunung.nama_gunung, users.nama_lengkap as guide 
                FROM jadwal 
                JOIN gunung ON jadwal.id_gunung = gunung.id_gunung
                LEFT JOIN users ON jadwal.id_penjaga = users.id_user 
                WHERE jadwal.id_jadwal = ?";
$stmt = $conn->prepare($sql_jadwal);
$stmt->execute([$id_jadwal]);
$d = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$d) { echo "<script>window.location='jadwal.php';</script>"; exit; }

// 2. Hitung Peserta Lunas (Untuk Progress Bar, Tetap Lunas only)
$stmt_count = $conn->prepare("SELECT SUM(jumlah_peserta) FROM transaksi WHERE id_jadwal = ? AND status_bayar = 'lunas'");
$stmt_count->execute([$id_jadwal]);
$terisi = $stmt_count->fetchColumn() ?: 0;
$sisa = $d['kuota_maks'] - $terisi;

// 3. LOGIC FILTER QUERY PESERTA
$sql_peserta = "SELECT transaksi.*, users.nama_lengkap, users.no_hp, users.email 
                FROM transaksi 
                JOIN users ON transaksi.id_user = users.id_user 
                WHERE transaksi.id_jadwal = :id ";

// Tambahan filter status kalo bukan 'semua'
if($status_filter != 'semua') {
    $sql_peserta .= " AND transaksi.status_bayar = :status ";
}

$sql_peserta .= " ORDER BY transaksi.status_bayar ASC, transaksi.tanggal_booking DESC";

$stmt_p = $conn->prepare($sql_peserta);

// Bind Param Dinamis
$params = [':id' => $id_jadwal];
if($status_filter != 'semua') {
    $params[':status'] = $status_filter;
}

$stmt_p->execute($params);
?>

<div class="content">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a href="jadwal.php" class="btn btn-outline-secondary btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            <h3 class="fw-bold text-success">Manifest Peserta Trip</h3>
        </div>
        <a href="cetak_jadwal.php?id=<?= $id_jadwal; ?>&status=<?= $status_filter; ?>" target="_blank" class="btn btn-outline-danger fw-bold">
            <i class="fa-solid fa-file-pdf"></i> Download PDF
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-success text-white">
                <div class="card-body p-4">
                    <h2 class="fw-bold"><i class="fa-solid fa-mountain"></i> <?= $d['nama_gunung']; ?></h2>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <small class="text-white-50">Tanggal Berangkat</small>
                            <h5 class="fw-bold"><?= date('d M Y', strtotime($d['tanggal_berangkat'])); ?></h5>
                        </div>
                        <div class="col-md-4">
                            <small class="text-white-50">Guide / Penjaga</small>
                            <h5 class="fw-bold"><?= $d['guide'] ?: '-'; ?></h5>
                        </div>
                        <div class="col-md-4">
                            <small class="text-white-50">Status Trip</small>
                            <h5 class="fw-bold text-uppercase"><?= $d['status_trip']; ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h6 class="text-muted text-uppercase">Kuota Tersedia</h6>
                    <h1 class="display-4 fw-bold <?= $sisa == 0 ? 'text-danger' : 'text-primary' ?>">
                        <?= $sisa; ?> <small class="fs-6 text-muted">/ <?= $d['kuota_maks']; ?></small>
                    </h1>
                    <div class="progress mt-2" style="height: 10px;">
                        <?php $persen = ($d['kuota_maks'] > 0) ? ($terisi / $d['kuota_maks']) * 100 : 0; ?>
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $persen; ?>%"></div>
                    </div>
                    <small class="mt-2 text-muted"><?= $terisi; ?> Peserta Lunas</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Daftar Transaksi / Peserta</h5>
            
            <form method="GET" class="d-flex align-items-center">
                <input type="hidden" name="id" value="<?= $id_jadwal; ?>">
                <label class="me-2 fw-bold text-muted small">Filter Status:</label>
                <select name="status" class="form-select form-select-sm w-auto border-success text-success fw-bold" onchange="this.form.submit()">
                    <option value="semua" <?= $status_filter=='semua'?'selected':''; ?>>Semua Data</option>
                    <option value="lunas" <?= $status_filter=='lunas'?'selected':''; ?>>✅ Lunas</option>
                    <option value="pending" <?= $status_filter=='pending'?'selected':''; ?>>⏳ Pending</option>
                    <option value="menunggu_verifikasi" <?= $status_filter=='menunggu_verifikasi'?'selected':''; ?>>🔍 Butuh Verifikasi</option>
                    <option value="batal" <?= $status_filter=='batal'?'selected':''; ?>>❌ Batal / Tolak</option>
                </select>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Kode Booking</th>
                            <th>Nama Pemesan</th>
                            <th>Kontak (WA)</th>
                            <th>Jml Pax</th>
                            <th>Status Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if($stmt_p->rowCount() > 0) {
                            while($p = $stmt_p->fetch(PDO::FETCH_ASSOC)) { 
                        ?>
                        <tr>
                            <td class="ps-4"><?= $no++; ?></td>
                            <td class="fw-bold font-monospace text-primary">#<?= $p['kode_booking']; ?></td>
                            <td>
                                <div class="fw-bold"><?= $p['nama_lengkap']; ?></div>
                                <small class="text-muted"><?= $p['email']; ?></small>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= $p['no_hp']; ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                                    <i class="fa-brands fa-whatsapp"></i> <?= $p['no_hp']; ?>
                                </a>
                            </td>
                            <td class="text-center fw-bold"><?= $p['jumlah_peserta']; ?> Org</td>
                            <td>
                                <?php if($p['status_bayar'] == 'lunas'): ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-check"></i> Lunas</span>
                                <?php elseif($p['status_bayar'] == 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php elseif($p['status_bayar'] == 'menunggu_verifikasi'): ?>
                                    <span class="badge bg-info text-dark">Cek Bukti</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Batal/Tolak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="transaksi_detail.php?id=<?= $p['id_transaksi']; ?>" class="btn btn-sm btn-alam">
                                    <i class="fa-solid fa-file-invoice"></i> Cek
                                </a>
                            </td>
                        </tr>
                        <?php } } else { ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Data tidak ditemukan untuk filter ini.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>