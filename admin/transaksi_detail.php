<?php
session_start();
require '../config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit;
}

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id_transaksi = $_GET['id'];

// --- PROSES UPDATE STATUS ---
if (isset($_POST['update_status'])) {
    $status_baru = $_POST['status_baru']; // lunas, tolak, batal
    
    try {
        $stmt = $conn->prepare("UPDATE transaksi SET status_bayar = ? WHERE id_transaksi = ?");
        $stmt->execute([$status_baru, $id_transaksi]);
        
        // Redirect balik dengan pesan sukses
        echo "<script>alert('Status berhasil diubah menjadi: ".strtoupper($status_baru)."'); window.location='transaksi_detail.php?id=$id_transaksi';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: ".$e->getMessage()."');</script>";
    }
}

// --- AMBIL DATA TRANSAKSI ---
$sql = "SELECT t.*, u.nama_lengkap, u.no_hp, u.email, g.nama_gunung, j.tanggal_berangkat 
        FROM transaksi t
        JOIN users u ON t.id_user = u.id_user
        JOIN jadwal j ON t.id_jadwal = j.id_jadwal
        JOIN gunung g ON j.id_gunung = g.id_gunung
        WHERE t.id_transaksi = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_transaksi]);
$d = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$d) { echo "Data tidak ditemukan!"; exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Transaksi - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fa-solid fa-arrow-left me-2"></i> Kembali
                </a>
                <h4 class="fw-bold mb-0">Detail Transaksi #<?= $d['kode_booking']; ?></h4>
            </div>

            <div class="row">
                <div class="col-md-7 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white py-3 fw-bold">
                            <i class="fa-solid fa-image me-2 text-primary"></i> Bukti Pembayaran
                        </div>
                        <div class="card-body text-center bg-light d-flex align-items-center justify-content-center" style="min-height: 300px;">
                            <?php if (!empty($d['bukti_bayar'])): ?>
                                <img src="../uploads/bukti/<?= $d['bukti_bayar']; ?>" class="img-fluid rounded shadow-sm" style="max-height: 500px;">
                            <?php else: ?>
                                <div class="text-muted opacity-50">
                                    <i class="fa-regular fa-image fa-3x mb-2"></i><br>
                                    User belum upload bukti bayar.
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white text-center py-3">
                            <?php if (!empty($d['bukti_bayar'])): ?>
                                <a href="../uploads/bukti/<?= $d['bukti_bayar']; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fa-solid fa-expand me-1"></i> Lihat Ukuran Penuh
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-secondary mb-3">INFORMASI PEMESAN</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" width="100">Nama</td>
                                    <td class="fw-bold"><?= $d['nama_lengkap']; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">WhatsApp</td>
                                    <td>
                                        <a href="https://wa.me/<?= $d['no_hp']; ?>" target="_blank" class="text-decoration-none text-success fw-bold">
                                            <i class="fa-brands fa-whatsapp"></i> <?= $d['no_hp']; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email</td>
                                    <td><?= $d['email']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-secondary mb-3">DETAIL PERJALANAN</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success text-white rounded-3 p-3 me-3 text-center" style="width: 60px;">
                                    <i class="fa-solid fa-mountain fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0"><?= $d['nama_gunung']; ?></h5>
                                    <small class="text-muted"><?= date('d M Y', strtotime($d['tanggal_berangkat'])); ?></small>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Jumlah Peserta</span>
                                <span class="fw-bold"><?= $d['jumlah_peserta']; ?> Orang</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Total Tagihan</span>
                                <span class="fw-bold text-success fs-4">Rp <?= number_format($d['total_bayar']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-secondary mb-3">VERIFIKASI STATUS</h6>
                            
                            <div class="alert alert-info text-center py-2 mb-3">
                                Status Saat Ini: <strong><?= strtoupper($d['status_bayar'] ?? 'pending'); ?></strong>
                            </div>

                            <form method="POST">
                                <div class="d-grid gap-2">
                                    <button type="submit" name="update_status" value="lunas" class="btn btn-success fw-bold py-2 rounded-pill" 
                                            onclick="return confirm('Yakin validasi pembayaran ini LUNAS?')">
                                        <i class="fa-solid fa-check-circle me-2"></i> Terima / Lunas
                                    </button>
                                    
                                    <button type="submit" name="update_status" value="tolak" class="btn btn-danger fw-bold py-2 rounded-pill"
                                            onclick="return confirm('Yakin ingin MENOLAK pembayaran ini?')">
                                        <i class="fa-solid fa-circle-xmark me-2"></i> Tolak Bukti
                                    </button>
                                    
                                    <button type="submit" name="update_status" value="pending" class="btn btn-outline-secondary fw-bold py-2 rounded-pill">
                                        <i class="fa-solid fa-rotate-left me-2"></i> Reset ke Pending
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>