<?php
session_start();
require '../config/koneksi.php';
// Cek Login User
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php"); exit;
}
include 'include/header.php'; // Header HTML biasa
include 'include/navbar.php'; // Navbar Responsive tadi

$id_user = $_SESSION['id_user'];

// 1. QUERY STATISTIK
// Total Trip diikuti (Lunas & Selesai)
$stat1 = $conn->query("SELECT COUNT(*) FROM transaksi WHERE id_user=$id_user AND status_bayar='lunas'")->fetchColumn();
// Trip Terdekat (Lunas & Belum Berangkat)
$stat2 = $conn->query("SELECT COUNT(*) FROM transaksi t JOIN jadwal j ON t.id_jadwal=j.id_jadwal WHERE t.id_user=$id_user AND t.status_bayar='lunas' AND j.tanggal_berangkat > CURDATE()")->fetchColumn();

// 2. QUERY TRIP TERDEKAT (YANG AKAN DATANG)
$sql_upcoming = "SELECT t.*, j.tanggal_berangkat, g.nama_gunung, g.gambar 
                 FROM transaksi t 
                 JOIN jadwal j ON t.id_jadwal = j.id_jadwal 
                 JOIN gunung g ON j.id_gunung = g.id_gunung
                 WHERE t.id_user = $id_user AND t.status_bayar = 'lunas' AND j.tanggal_berangkat >= CURDATE()
                 ORDER BY j.tanggal_berangkat ASC LIMIT 1";
$upcoming = $conn->query($sql_upcoming)->fetch(PDO::FETCH_ASSOC);

// 3. QUERY REKOMENDASI (TRIP BUKA)
$sql_rekomen = "SELECT j.*, g.nama_gunung, g.gambar, g.lokasi 
                FROM jadwal j JOIN gunung g ON j.id_gunung = g.id_gunung 
                WHERE j.status_trip = 'buka' AND j.tanggal_berangkat > CURDATE() 
                ORDER BY j.tanggal_berangkat ASC LIMIT 3";
$rekomen = $conn->query($sql_rekomen);
?>

<div class="container pb-5">
    
    <div class="card bg-success text-white rounded-4 border-0 mb-4 shadow-sm overflow-hidden" style="background: linear-gradient(45deg, #198754, #20c997);">
        <div class="card-body p-4 p-lg-5 position-relative">
            <div class="row align-items-center position-relative" style="z-index: 2;">
                <div class="col-md-7">
                    <h2 class="fw-bold mb-3">Halo, <?= $_SESSION['nama']; ?>! 👋</h2>
                    <p class="fs-5 mb-4">"Gunung tidak membuat kita melupakan masalah, tapi membuat kita merasa bahwa masalah tidak lagi penting."</p>
                    <a href="trips.php" class="btn btn-light text-success fw-bold rounded-pill px-4 shadow">Cari Petualangan Baru</a>
                </div>
                <div class="col-md-5 d-none d-md-block text-end">
                    <i class="fa-solid fa-person-hiking fa-6x text-white-50"></i>
                </div>
            </div>
            <i class="fa-solid fa-mountain fa-10x position-absolute text-white" style="opacity: 0.1; right: -20px; bottom: -50px;"></i>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="card card-stat bg-white shadow-sm p-3 text-center h-100">
                        <h3 class="fw-bold text-success mb-0"><?= $stat1; ?></h3>
                        <small class="text-muted">Trip Diikuti</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card card-stat bg-white shadow-sm p-3 text-center h-100">
                        <h3 class="fw-bold text-warning mb-0"><?= $stat2; ?></h3>
                        <small class="text-muted">Trip Next</small>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mb-3"><i class="fa-regular fa-calendar-check text-primary"></i> Jadwal Terdekat Kamu</h6>
            <?php if($upcoming): ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <img src="../uploads/<?= $upcoming['gambar']; ?>" class="card-img-top" style="height: 120px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="fw-bold"><?= $upcoming['nama_gunung']; ?></h6>
                        <small class="text-muted d-block mb-2"><i class="fa-solid fa-clock"></i> <?= date('d M Y', strtotime($upcoming['tanggal_berangkat'])); ?></small>
                        <a href="../detail.php?id=<?= $upcoming['id_jadwal']; ?>" class="btn btn-sm btn-outline-success w-100 rounded-pill">Lihat Detail</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center text-muted border border-dashed rounded-4">
                    Belum ada jadwal trip terdekat.
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">🔥 Open Trip Terbaru</h5>
                <a href="trips.php" class="text-decoration-none small text-success">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="row g-3">
                <?php while($row = $rekomen->fetch(PDO::FETCH_ASSOC)) { 
                    // Hitung Sisa Kuota
                    $q_cek = $conn->query("SELECT SUM(jumlah_peserta) FROM transaksi WHERE id_jadwal=".$row['id_jadwal']." AND status_bayar='lunas'")->fetchColumn() ?: 0;
                    $sisa = $row['kuota_maks'] - $q_cek;
                ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="row g-0 h-100">
                            <div class="col-4">
                                <img src="../uploads/<?= $row['gambar']; ?>" class="img-fluid h-100 object-fit-cover">
                            </div>
                            <div class="col-8">
                                <div class="card-body p-3 d-flex flex-column h-100 justify-content-center">
                                    <h6 class="fw-bold text-dark mb-1"><?= $row['nama_gunung']; ?></h6>
                                    <small class="text-muted mb-2"><i class="fa-solid fa-location-dot"></i> <?= $row['lokasi']; ?></small>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="text-success fw-bold small">Rp <?= number_format($row['harga']); ?></span>
                                        <a href="../detail.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-sm btn-alam rounded-pill px-3" style="font-size: 10px;">Cek</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

            <div class="mt-4 p-4 bg-light rounded-4 border border-dashed text-center">
                <i class="fa-solid fa-tags fa-2x text-warning mb-2"></i>
                <h6 class="fw-bold">Ajak 5 Teman, Gratis 1 Pax!</h6>
                <p class="small text-muted mb-0">Hubungi admin untuk klaim promo grup spesial ini.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>