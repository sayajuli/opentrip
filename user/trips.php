<?php
session_start();
<<<<<<< HEAD
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php"); exit;
=======
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'user') {
    // Kalau belum, tendang balik ke halaman login
    header("Location: ../login.php?pesan=belum_login");
    exit; // PENTING: Stop eksekusi script di bawahnya
>>>>>>> 418c4562026a96cd4ca033bf5fee065c81a2cd23
}
require '../config/koneksi.php';
include 'include/header.php';
include 'include/navbar.php';

$keyword = isset($_GET['cari']) ? $_GET['cari'] : "";
$lokasi  = isset($_GET['lokasi']) ? $_GET['lokasi'] : "";

// 1. QUERY TRIP AKTIF (Urutkan: Tersedia > Penuh)
$sql = "SELECT j.*, g.nama_gunung, g.gambar, g.lokasi, 
        (j.kuota_maks - (SELECT COUNT(*) FROM transaksi t WHERE t.id_jadwal = j.id_jadwal AND t.status_bayar='lunas')) as sisa_kuota
        FROM jadwal j 
        JOIN gunung g ON j.id_gunung = g.id_gunung 
        WHERE j.status_trip = 'buka' AND j.tanggal_berangkat > CURDATE()";

if ($keyword) $sql .= " AND g.nama_gunung LIKE '%$keyword%'";
if ($lokasi)  $sql .= " AND g.lokasi = '$lokasi'";

// Sortir: Sisa Kuota > 0 (diatas), Sisa Kuota <= 0 (dibawah), lalu tanggal terdekat
$sql .= " ORDER BY CASE WHEN (j.kuota_maks - (SELECT COUNT(*) FROM transaksi t WHERE t.id_jadwal = j.id_jadwal AND t.status_bayar='lunas')) > 0 THEN 0 ELSE 1 END, j.tanggal_berangkat ASC";

$stmt = $conn->query($sql);
$lokasi_opt = $conn->query("SELECT DISTINCT lokasi FROM gunung");

// 2. QUERY PORTOFOLIO (TRIP SELESAI)
$sql_hist = "SELECT j.*, g.nama_gunung, g.gambar, g.lokasi 
             FROM jadwal j JOIN gunung g ON j.id_gunung = g.id_gunung 
             WHERE j.status_trip = 'selesai' 
             ORDER BY j.tanggal_berangkat DESC LIMIT 4";
$stmt_hist = $conn->query($sql_hist);
?>

<div class="container pb-5">
    <div class="bg-white p-4 rounded-4 shadow-sm mb-4 border">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label class="form-label fw-bold">Cari Gunung</label>
                <input type="text" name="cari" class="form-control" placeholder="Contoh: Semeru..." value="<?= $keyword; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Lokasi</label>
                <select name="lokasi" class="form-select">
                    <option value="">Semua Lokasi</option>
                    <?php while($l = $lokasi_opt->fetch()) { ?>
                        <option value="<?= $l['lokasi']; ?>" <?= $lokasi==$l['lokasi']?'selected':'' ?>><?= $l['lokasi']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end"><button type="submit" class="btn btn-alam w-100 fw-bold">Temukan Trip</button></div>
        </form>
    </div>

    <div class="row g-4 mb-5">
        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
            $is_full = ($row['sisa_kuota'] <= 0);
        ?>
            <div class="col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden card-hover <?= $is_full ? 'opacity-75' : ''; ?>">
                    <div class="position-relative">
                        <img src="../uploads/<?= $row['gambar']; ?>" class="card-img-top object-fit-cover <?= $is_full ? 'grayscale' : ''; ?>" style="height: 200px;">
                        <?php if($is_full): ?>
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50">
                                <span class="badge bg-danger fs-5 px-3 py-2">KUOTA PENUH</span>
                            </div>
                        <?php else: ?>
                            <div class="position-absolute top-0 end-0 m-3 badge bg-white text-success shadow-sm"><?= date('d M', strtotime($row['tanggal_berangkat'])); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="fw-bold mb-1"><?= $row['nama_gunung']; ?></h5>
                        <small class="text-muted"><i class="fa-solid fa-map-pin text-danger"></i> <?= $row['lokasi']; ?></small>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold fs-5">Rp <?= number_format($row['harga']); ?></span>
                            <a href="../detail.php?id=<?= $row['id_jadwal']; ?>" class="btn <?= $is_full ? 'btn-secondary disabled' : 'btn-outline-success'; ?> rounded-pill px-4"><?= $is_full ? 'Penuh' : 'Detail'; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="pt-4 border-top">
        <h4 class="fw-bold mb-3"><i class="fa-solid fa-award text-warning"></i> Galeri Petualangan Kami</h4>
        <p class="text-muted mb-4">Trip yang telah sukses terlaksana. Klik untuk melihat ulasan & dokumentasi.</p>
        
        <div class="row g-4">
            <?php while($h = $stmt_hist->fetch(PDO::FETCH_ASSOC)) { ?>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-light card-hover position-relative">
                    <img src="../uploads/<?= $h['gambar']; ?>" class="card-img-top object-fit-cover grayscale" style="height: 150px;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-1"><?= $h['nama_gunung']; ?></h6>
                        <small class="text-muted d-block mb-2">
                            <i class="fa-solid fa-check-double text-success"></i> <?= date('d M Y', strtotime($h['tanggal_berangkat'])); ?>
                        </small>
                        <span class="badge bg-secondary">Sukses</span>

                        <a href="../detail.php?id=<?= $h['id_jadwal']; ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<style>
    .grayscale { filter: grayscale(100%); transition: .3s; }
    .card-hover:hover .grayscale { filter: grayscale(0%); }
    .opacity-75 { opacity: 0.75; }
</style>

<?php include 'include/footer.php'; ?>
