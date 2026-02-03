<?php
session_start();
require '../config/koneksi.php';
include 'include/header.php';
include 'include/navbar.php';

$id_user = $_SESSION['id_user'];

// Query Riwayat: Lunas/Batal/Selesai
// Subquery COUNT(*) reviews pakai id_transaksi biar akurat
$sql = "SELECT t.*, j.id_gunung, j.status_trip as status_jadwal, j.alasan_batal, j.tanggal_berangkat, g.nama_gunung,
        (SELECT COUNT(*) FROM reviews r WHERE r.id_transaksi = t.id_transaksi) as sudah_review
        FROM transaksi t
        JOIN jadwal j ON t.id_jadwal = j.id_jadwal
        JOIN gunung g ON j.id_gunung = g.id_gunung
        WHERE t.id_user = $id_user 
        AND ((t.status_bayar = 'lunas' AND j.tanggal_berangkat < CURDATE()) OR j.status_trip = 'batal' OR j.status_trip = 'selesai')
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                    $is_batal = ($row['status_jadwal'] == 'batal');
                ?>
                <tr>
                    <td class="fw-bold"><?= $row['nama_gunung']; ?></td>
                    <td><?= date('d M Y', strtotime($row['tanggal_berangkat'])); ?></td>
                    <td>
                        <?php if($is_batal): ?>
                            <span class="badge bg-danger">Dibatalkan Admin</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Selesai</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($is_batal): ?>
                            <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="Swal.fire('Info', 'Alasan: <?= htmlspecialchars($row['alasan_batal']); ?>', 'error')">Cek Alasan</button>
                        
                        <?php elseif($row['status_jadwal'] == 'selesai' && $row['sudah_review'] == 0): ?>
                            <button class="btn btn-sm btn-warning text-dark fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalRev<?= $row['id_transaksi']; ?>">
                                <i class="fa-regular fa-star"></i> Review
                            </button>

                            <div class="modal fade" id="modalRev<?= $row['id_transaksi']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Review <?= $row['nama_gunung']; ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="../api/user_action.php?act=review" method="POST" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi']; ?>">
                                                <input type="hidden" name="id_gunung" value="<?= $row['id_gunung']; ?>">
                                                
                                                <div class="mb-3 text-center">
                                                    <label class="form-label fw-bold">Rating</label>
                                                    <select name="rating" class="form-select text-center fs-5 text-warning fw-bold">
                                                        <option value="5">⭐⭐⭐⭐⭐</option>
                                                        <option value="4">⭐⭐⭐⭐</option>
                                                        <option value="3">⭐⭐⭐</option>
                                                        <option value="2">⭐⭐</option>
                                                        <option value="1">⭐</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Komentar</label>
                                                    <textarea name="komentar" class="form-control" rows="3" required placeholder="Ceritakan pengalamanmu..."></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small text-muted">Foto (Opsional)</label>
                                                    <input type="file" name="foto_review" class="form-control" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-alam w-100">Kirim Review</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php elseif($row['sudah_review'] > 0): ?>
                            <span class="badge bg-success"><i class="fa-solid fa-check"></i> Reviewed</span>
                        <?php else: ?>
                            <a href="../detail.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill">Detail</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>