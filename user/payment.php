<?php
session_start();
<<<<<<< HEAD
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php"); exit;
}
=======
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'user') {
    // Kalau belum, tendang balik ke halaman login
    header("Location: ../login.php?pesan=belum_login");
    exit; // PENTING: Stop eksekusi script di bawahnya
}

>>>>>>> 418c4562026a96cd4ca033bf5fee065c81a2cd23
require '../config/koneksi.php';
include 'include/header.php';
include 'include/navbar.php';

$id_user = $_SESSION['id_user'];
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'pending';

// Query Dinamis
if($tab == 'pending') {
    $sql = "SELECT t.*, j.tanggal_berangkat, g.nama_gunung 
            FROM transaksi t JOIN jadwal j ON t.id_jadwal = j.id_jadwal JOIN gunung g ON j.id_gunung = g.id_gunung
            WHERE t.id_user = $id_user AND t.status_bayar IN ('pending', 'menunggu_verifikasi', 'tolak')
            ORDER BY t.tanggal_booking DESC";
} else {
    // History Pembayaran (Lunas)
    $sql = "SELECT t.*, j.tanggal_berangkat, g.nama_gunung 
            FROM transaksi t JOIN jadwal j ON t.id_jadwal = j.id_jadwal JOIN gunung g ON j.id_gunung = g.id_gunung
            WHERE t.id_user = $id_user AND t.status_bayar = 'lunas'
            ORDER BY t.tanggal_booking DESC";
}
$stmt = $conn->query($sql);
?>

<div class="container pb-5">
    <h4 class="fw-bold mb-4 ps-2 border-start border-4 border-success">Keuangan & Tagihan</h4>

    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a href="?tab=pending" class="nav-link rounded-pill <?= $tab=='pending' ? 'bg-success text-white' : 'bg-white text-secondary border' ?>">
                Tagihan Aktif
            </a>
        </li>
        <li class="nav-item">
            <a href="?tab=lunas" class="nav-link rounded-pill <?= $tab=='lunas' ? 'bg-success text-white' : 'bg-white text-secondary border' ?>">
                Riwayat Pembayaran
            </a>
        </li>
    </ul>

    <div class="row">
        <?php if($stmt->rowCount() > 0) { 
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-light text-dark border">#<?= $row['kode_booking']; ?></span>
                            <?php 
                                if($row['status_bayar']=='pending') echo '<span class="badge bg-danger">Belum Bayar</span>';
                                elseif($row['status_bayar']=='menunggu_verifikasi') echo '<span class="badge bg-warning text-dark">Sedang Dicek</span>';
                                elseif($row['status_bayar']=='tolak') echo '<span class="badge bg-danger">Ditolak</span>';
                                elseif($row['status_bayar']=='lunas') echo '<span class="badge bg-success">Lunas / Terbayar</span>';
                            ?>
                        </div>
                        
                        <h5 class="fw-bold"><?= $row['nama_gunung']; ?></h5>
                        <p class="text-muted small mb-3">
                            Tgl Trip: <?= date('d M Y', strtotime($row['tanggal_berangkat'])); ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                            <div>
                                <small class="d-block text-muted">Nominal</small>
                                <span class="fw-bold text-success">Rp <?= number_format($row['total_bayar']); ?></span>
                            </div>
                            
                            <?php if($row['status_bayar'] == 'pending' || $row['status_bayar'] == 'tolak'): ?>
                                <a href="bayar.php?id=<?= $row['id_transaksi']; ?>" class="btn btn-sm btn-alam rounded-pill px-3">Bayar</a>
                            <?php elseif($row['status_bayar'] == 'lunas'): ?>
                                <button class="btn btn-sm btn-outline-success rounded-pill" disabled><i class="fa-solid fa-check"></i> Berhasil</button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-secondary rounded-pill" disabled>Proses</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } } else { ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Tidak ada data transaksi di kategori ini.</p>
            </div>
        <?php } ?>
    </div>
</div>

<?php include 'include/footer.php'; ?>
