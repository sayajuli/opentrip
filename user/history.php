<?php
session_start();

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php"); exit;
}
require '../config/koneksi.php';
include 'include/header.php';
include 'include/navbar.php';

$id_user = $_SESSION['id_user'];

$sql = "SELECT t.*, j.id_gunung, j.status_trip as status_jadwal, j.alasan_batal, j.tanggal_berangkat, g.nama_gunung,
        (SELECT COUNT(*) FROM reviews r WHERE r.id_transaksi = t.id_transaksi) as sudah_review
        FROM transaksi t
        JOIN jadwal j ON t.id_jadwal = j.id_jadwal
        JOIN gunung g ON j.id_gunung = g.id_gunung
        WHERE t.id_user = $id_user 
        AND (
            (t.status_bayar = 'lunas' AND (j.tanggal_berangkat < CURDATE() OR j.status_trip = 'selesai'))
            
            OR 
            
            (t.status_bayar = 'batal' OR j.status_trip = 'batal')
        )
        AND t.status_bayar NOT IN ('pending', 'menunggu_verifikasi', 'tolak')
        
        GROUP BY t.id_transaksi
        ORDER BY t.tanggal_booking DESC";

$stmt = $conn->query($sql);
?>

<div class="container pb-5">
    <h4 class="fw-bold mb-4 ps-2 border-start border-4 border-secondary">Riwayat Perjalanan</h4>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Trip</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                    // Cek Status Visual
                    $is_batal = ($row['status_jadwal'] == 'batal' || $row['status_bayar'] == 'batal');
                    $is_selesai = ($row['status_jadwal'] == 'selesai' || $row['tanggal_berangkat'] < date('Y-m-d'));
                ?>
                <tr>
                    <td class="fw-bold">
                        <?= $row['nama_gunung']; ?>
                        <br>
                        <small class="text-muted fw-normal" style="font-size: 11px;">#<?= $row['kode_booking']; ?></small>
                    </td>
                    
                    <td><?= date('d M Y', strtotime($row['tanggal_berangkat'])); ?></td>
                    
                    <td>
                        <?php if($is_batal): ?>
                            <span class="badge bg-danger">Dibatalkan</span>
                        <?php elseif($is_selesai): ?>
                            <span class="badge bg-secondary">Selesai</span>
                            <?php if($row['sudah_review'] > 0): ?>
                                <i class="fa-solid fa-star text-warning ms-1" style="font-size:10px;"></i>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-primary">Berjalan</span>
                        <?php endif; ?>
                    </td>

                    <td class="text-end">
                        
                        <?php if($is_batal): ?>
                            <button class="btn btn-sm btn-outline-danger rounded-pill me-1" 
                                    onclick="Swal.fire('Info Pembatalan', 'Alasan: <?= htmlspecialchars($row['alasan_batal'] ?: 'Dibatalkan oleh User/Admin'); ?>', 'info')">
                                <i class="fa-solid fa-circle-question"></i> Alasan
                            </button>
                        <?php endif; ?>

                        <?php if(!$is_batal && $is_selesai && $row['sudah_review'] == 0): ?>
                            <button class="btn btn-sm btn-warning text-dark fw-bold rounded-pill me-1" 
                                    data-bs-toggle="modal" data-bs-target="#modalRev<?= $row['id_transaksi']; ?>">
                                <i class="fa-regular fa-star"></i> Review
                            </button>

                            <div class="modal fade text-start" id="modalRev<?= $row['id_transaksi']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Review Trip <?= $row['nama_gunung']; ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="../api/user_action.php?act=review" method="POST" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi']; ?>">
                                                <input type="hidden" name="id_gunung" value="<?= $row['id_gunung']; ?>">
                                                
                                                <div class="mb-3 text-center">
                                                    <label class="form-label fw-bold">Beri Bintang</label>
                                                    <select name="rating" class="form-select text-center fs-5 text-warning fw-bold">
                                                        <option value="5">⭐⭐⭐⭐⭐ (Puas Banget)</option>
                                                        <option value="4">⭐⭐⭐⭐ (Bagus)</option>
                                                        <option value="3">⭐⭐⭐ (Biasa Aja)</option>
                                                        <option value="2">⭐⭐ (Kurang)</option>
                                                        <option value="1">⭐ (Kecewa)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Cerita Pengalamanmu</label>
                                                    <textarea name="komentar" class="form-control" rows="3" required placeholder="Gimana kesan pesannya?"></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small text-muted">Foto (Opsional)</label>
                                                    <input type="file" name="foto_review" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-alam w-100">Kirim Review</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if(!$is_batal): ?>
                            <a href="../detail.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
                                Detail <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        <?php endif; ?>

                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php if($stmt->rowCount() == 0): ?>
            <div class="text-center py-5 text-muted">
                <p>Belum ada riwayat perjalanan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'include/footer.php'; ?>