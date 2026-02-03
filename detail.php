<?php
session_start();
require 'config/koneksi.php';

// Validasi ID
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }

$id = $_GET['id'];
$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

// 1. Ambil Data Trip
$sql = "SELECT j.*, g.nama_gunung, g.lokasi, g.gambar, g.deskripsi as desc_gunung, u.nama_lengkap as guide, g.id_gunung 
        FROM jadwal j
        JOIN gunung g ON j.id_gunung = g.id_gunung
        LEFT JOIN users u ON j.id_penjaga = u.id_user
        WHERE j.id_jadwal = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$d = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$d) { echo "<script>alert('Trip tidak ditemukan'); window.location='index.php';</script>"; exit; }

// 2. Hitung Kuota & Waktu
$cek = $conn->prepare("SELECT SUM(jumlah_peserta) FROM transaksi WHERE id_jadwal = ? AND status_bayar = 'lunas'");
$cek->execute([$id]);
$terisi = $cek->fetchColumn() ?: 0;
$sisa_kuota = $d['kuota_maks'] - $terisi;

$start = new DateTime($d['tanggal_berangkat']);
$end   = new DateTime($d['tanggal_selesai']);
$now   = new DateTime();
$durasi = $start->diff($end)->days + 1;
$selisih_hari = $now->diff($start)->days;

$is_h10 = ($start > $now && $selisih_hari <= 10); 
$is_lewat = ($start < $now);

// 3. Cek User Sudah Booking?
$sudah_booking = false;
if($id_user > 0) {
    $cek_book = $conn->prepare("SELECT id_transaksi FROM transaksi 
                                WHERE id_user = ? 
                                AND id_jadwal = ? 
                                AND status_bayar NOT IN ('tolak', 'batal')");
    
    $cek_book->execute([$id_user, $id]);
    
    if($cek_book->rowCount() > 0) {
        $sudah_booking = true;
    }
}

// 4. Ambil Review Gunung
$sql_rev = "SELECT r.*, u.nama_lengkap 
            FROM reviews r JOIN users u ON r.id_user = u.id_user 
            WHERE r.id_gunung = ? 
            ORDER BY r.tanggal_review DESC LIMIT 5";
$stmt_rev = $conn->prepare($sql_rev);
$stmt_rev->execute([$d['id_gunung']]);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip <?= $d['nama_gunung']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="btn btn-outline-secondary rounded-pill" href="javascript:history.back()">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <span class="navbar-brand fw-bold text-success mx-auto"><?= $d['nama_gunung']; ?></span>
            <div style="width: 100px;"></div> 
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <img src="uploads/<?= $d['gambar']; ?>" class="w-100 object-fit-cover" style="height: 400px;">
                    <div class="card-body p-4">
                        <h2 class="fw-bold text-dark mb-3"><?= $d['nama_gunung']; ?></h2>
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 text-center h-100"><small class="d-block text-muted">Lokasi</small><span class="fw-bold"><?= $d['lokasi']; ?></span></div></div>
                            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 text-center h-100"><small class="d-block text-muted">Tanggal</small><span class="fw-bold"><?= date('d M', strtotime($d['tanggal_berangkat'])); ?></span></div></div>
                            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 text-center h-100"><small class="d-block text-muted">Durasi</small><span class="fw-bold"><?= $durasi; ?> Hari</span></div></div>
                            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 text-center h-100"><small class="d-block text-muted">Guide</small><span class="fw-bold"><?= $d['guide'] ?: '-'; ?></span></div></div>
                        </div>
                        <hr>
                        <h5 class="fw-bold">Deskripsi</h5>
                        <p class="text-secondary"><?= nl2br($d['desc_gunung']); ?></p>
                        <div class="alert alert-success mt-4">
                            <h6 class="fw-bold">Fasilitas:</h6>
                            <p class="mb-0 small"><?= nl2br($d['deskripsi']); ?></p>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3">Ulasan Pendaki (<?= $stmt_rev->rowCount(); ?>)</h5>
                <?php if($stmt_rev->rowCount() > 0): ?>
                    <div class="card border-0 bg-white shadow-sm rounded-4">
                        <div class="list-group list-group-flush rounded-4">
                            <?php while($rev = $stmt_rev->fetch(PDO::FETCH_ASSOC)) { ?>
                            <div class="list-group-item p-3 border-0 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-bold mb-1"><?= $rev['nama_lengkap']; ?></h6>
                                    <small class="text-muted"><?= date('d M Y', strtotime($rev['tanggal_review'])); ?></small>
                                </div>
                                <div class="text-warning small mb-2">
                                    <?= str_repeat('<i class="fa-solid fa-star"></i>', $rev['rating']); ?>
                                </div>
                                <p class="mb-2 text-secondary small">"<?= $rev['komentar']; ?>"</p>
                                
                                <?php if(!empty($rev['foto'])): ?>
                                    <img src="uploads/review/<?= $rev['foto']; ?>" class="rounded shadow-sm" style="height: 100px; cursor: pointer;" onclick="window.open(this.src)">
                                <?php endif; ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border border-dashed text-center text-muted">Belum ada review. Jadilah yang pertama!</div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow rounded-4 sticky-top" style="top: 90px; z-index: 10;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="fw-bold text-success mb-0">Rp <?= number_format($d['harga'],0,',','.'); ?></h3>
                            <span class="text-muted small">/ pax</span>
                        </div>

                        <?php if($sudah_booking): ?>
                            <div class="alert alert-info text-center py-4"><strong>Kamu sudah terdaftar.</strong></div>
                            <a href="user/upcoming.php" class="btn btn-outline-primary w-100 rounded-pill">Lihat Tiket</a>
                        
                        <?php elseif($is_lewat): ?>
                            <div class="alert alert-secondary text-center py-4"><strong>Trip Selesai</strong></div>
                        
                        <?php elseif($d['status_trip'] != 'buka' || $sisa_kuota <= 0): ?>
                            <div class="alert alert-secondary text-center py-4"><strong>Kuota Penuh</strong></div>
                        
                        <?php elseif($is_h10): ?>
                            <div class="alert alert-warning text-center py-4"><strong>Booking Ditutup (H-10)</strong></div>
                        
                        <?php else: ?>
                            <form action="api/user_booking.php" method="POST">
                                <input type="hidden" name="id_jadwal" value="<?= $id; ?>">
                                <input type="hidden" name="harga_satuan" id="hargaSatuan" value="<?= $d['harga']; ?>">
                                <div class="mb-3">
                                    <label class="small text-muted">Jumlah (Sisa: <?= $sisa_kuota; ?>)</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary" onclick="ubahQty(-1)">-</button>
                                        <input type="number" name="jumlah_peserta" id="qty" class="form-control text-center fw-bold" value="1" min="1" max="<?= $sisa_kuota; ?>" readonly>
                                        <button type="button" class="btn btn-outline-secondary" onclick="ubahQty(1)">+</button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mb-3 fw-bold">
                                    <span>Total</span><span class="text-success" id="totalBayar">Rp <?= number_format($d['harga'],0,',','.'); ?></span>
                                </div>
                                <?php if(isset($_SESSION['status']) && $_SESSION['status'] == 'login'): ?>
                                    <button type="submit" name="btn_booking" class="btn btn-alam w-100 rounded-pill py-2">Booking Sekarang</button>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-secondary w-100 rounded-pill py-2">Login Dulu</a>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const harga = <?= $d['harga']; ?>;
        const max = <?= $sisa_kuota; ?>;
        function ubahQty(val) {
            let qty = parseInt(document.getElementById('qty').value);
            let newQty = qty + val;
            if(newQty >= 1 && newQty <= max) {
                document.getElementById('qty').value = newQty;
                document.getElementById('totalBayar').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(newQty * harga);
            }
        }
    </script>
</body>
</html>