<?php
session_start();
require '../config/koneksi.php';
include 'include/header.php';

$id_guide = $_SESSION['id_user'];

// Query Jadwal Khusus Guide Ini
$sql = "SELECT j.*, g.nama_gunung, g.gambar 
        FROM jadwal j 
        JOIN gunung g ON j.id_gunung = g.id_gunung 
        WHERE j.id_penjaga = ? 
        ORDER BY j.tanggal_berangkat DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_guide]);
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Halo, <?= $_SESSION['nama'] ?? 'Guide'; ?>! 👋</h4>
            <p class="text-muted small">Semangat memandu para pendaki.</p>
        </div>
    </div>

    <div class="row g-3">
        <?php if($stmt->rowCount() > 0): ?>
            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                $is_done = ($row['tanggal_berangkat'] < date('Y-m-d'));
                
                // Hitung Peserta Real
                $q_peserta = $conn->query("SELECT SUM(jumlah_peserta) FROM transaksi WHERE id_jadwal=".$row['id_jadwal']." AND status_bayar='lunas'")->fetchColumn() ?: 0;
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 <?= $is_done ? 'bg-light border' : ''; ?>">
                    <div class="d-flex">
                        <img src="../uploads/<?= $row['gambar']; ?>" class="object-fit-cover" style="width: 120px; height: auto; filter: <?= $is_done ? 'grayscale(100%)' : 'none'; ?>;">
                        <div class="card-body py-3">
                            <h6 class="fw-bold mb-1"><?= $row['nama_gunung']; ?></h6>
                            <small class="text-muted d-block mb-2">
                                <i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($row['tanggal_berangkat'])); ?>
                            </small>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-secondary rounded-pill">
                                    <i class="fa-solid fa-users"></i> <?= $q_peserta; ?> Pax
                                </span>
                                
                                <div>
                                    <?php if($is_done): ?>
                                        <span class="badge bg-dark me-1">Selesai</span>
                                        <a href="detail.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Lihat History">
                                            Info
                                        </a>
                                    <?php else: ?>
                                        <a href="detail.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-sm btn-success rounded-pill px-3">
                                            Lihat Data <i class="fa-solid fa-chevron-right ms-1"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa-solid fa-person-hiking fa-3x mb-3 opacity-50"></i><br>
                Belum ada jadwal tugas untukmu.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'include/footer.php'; ?>