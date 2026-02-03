<?php 
$pageTitle = "History Trip Selesai";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';
?>

<div class="content">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-clock-rotate-left text-success"></i> History Trip Selesai</h3>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tgl Berangkat</th>
                            <th>Gunung</th>
                            <th>Penjaga (Guide)</th>
                            <th>Peserta</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Logic: Ambil jadwal yang tanggalnya SUDAH LEWAT hari ini
                        $sql = "SELECT jadwal.*, gunung.nama_gunung, users.nama_lengkap as nama_penjaga 
                                FROM jadwal 
                                JOIN gunung ON jadwal.id_gunung = gunung.id_gunung
                                LEFT JOIN users ON jadwal.id_penjaga = users.id_user
                                WHERE jadwal.tanggal_berangkat < CURDATE() 
                                ORDER BY jadwal.tanggal_berangkat DESC";
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();

                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                            // Hitung jumlah peserta lunas di trip ini
                            $stmt_count = $conn->prepare("SELECT SUM(jumlah_peserta) FROM transaksi WHERE id_jadwal = ? AND status_bayar = 'lunas'");
                            $stmt_count->execute([$row['id_jadwal']]);
                            $total_peserta = $stmt_count->fetchColumn() ?: 0;
                        ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['tanggal_berangkat'])); ?></td>
                            <td class="fw-bold"><?= $row['nama_gunung']; ?></td>
                            <td>
                                <?php if($row['nama_penjaga']): ?>
                                    <span class="badge bg-info text-dark"><i class="fa-solid fa-user-shield"></i> <?= $row['nama_penjaga']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Belum ada</span>
                                <?php endif; ?>
                            </td>
                            <td><i class="fa-solid fa-users"></i> <?= $total_peserta; ?> Orang</td>
                            <td><span class="badge bg-secondary">Selesai</span></td>
                            <td>
                                <a href="laporan.php?id_jadwal=<?= $row['id_jadwal']; ?>" class="btn btn-sm btn-outline-success">
                                    <i class="fa-solid fa-print"></i> Rekap
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($stmt->rowCount() == 0): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-box-open fa-3x mb-3"></i><br>
                    Belum ada trip yang selesai (History kosong).
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>