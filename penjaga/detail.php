<?php
session_start();
require '../config/koneksi.php';
include 'include/header.php';

$id_jadwal = $_GET['id'];
$id_guide  = $_SESSION['id_user'];

// 1. Ambil Info Jadwal (Validasi Guide)
$sql = "SELECT j.*, g.nama_gunung, g.lokasi 
        FROM jadwal j JOIN gunung g ON j.id_gunung = g.id_gunung 
        WHERE j.id_jadwal = ? AND j.id_penjaga = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_jadwal, $id_guide]);
$d = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$d) { echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>"; exit; }

// 2. Ambil Daftar Peserta LUNAS
$sql_peserta = "SELECT t.*, u.nama_lengkap, u.no_hp, u.email 
                FROM transaksi t 
                JOIN users u ON t.id_user = u.id_user 
                WHERE t.id_jadwal = ? AND t.status_bayar = 'lunas' 
                ORDER BY u.nama_lengkap ASC";
$stmt_p = $conn->prepare($sql_peserta);
$stmt_p->execute([$id_jadwal]);

// Hitung Total Pax
$total_pax = 0;
?>

<div class="container pb-5">
    <a href="index.php" class="text-decoration-none text-muted mb-3 d-inline-block"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-success text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="fw-bold mb-1"><?= $d['nama_gunung']; ?></h2>
                    <p class="mb-0 text-white-50"><i class="fa-solid fa-map-pin me-2"></i> <?= $d['lokasi']; ?></p>
                </div>
                <div class="text-end">
                    <h5 class="fw-bold mb-0"><?= date('d M Y', strtotime($d['tanggal_berangkat'])); ?></h5>
                    <small class="text-white-50">Tanggal Naik</small>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0">Manifest Peserta</h5>
        <a href="cetak_manifest.php?id=<?= $id_jadwal; ?>" target="_blank" class="btn btn-dark btn-sm rounded-pill px-3">
            <i class="fa-solid fa-print me-2"></i> Cetak PDF
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nama Peserta</th>
                        <th>Kontak</th>
                        <th class="text-center">Pax</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($stmt_p->rowCount() > 0): ?>
                        <?php while($p = $stmt_p->fetch(PDO::FETCH_ASSOC)) { 
                            $total_pax += $p['jumlah_peserta'];
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold d-block"><?= $p['nama_lengkap']; ?></span>
                                <small class="text-muted text-uppercase">#<?= $p['kode_booking']; ?></small>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= $p['no_hp']; ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                                    <i class="fa-brands fa-whatsapp"></i> Chat
                                </a>
                            </td>
                            <td class="text-center fw-bold fs-5"><?= $p['jumlah_peserta']; ?></td>
                        </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada peserta lunas.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="2" class="text-end fw-bold pe-3">TOTAL PESERTA</td>
                        <td class="text-center fw-bold fs-4 text-success"><?= $total_pax; ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>