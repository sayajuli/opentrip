<?php 
$pageTitle = "Pusat Laporan";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';

// 1. SETUP FILTER DEFAULT
$tipe   = isset($_GET['tipe']) ? $_GET['tipe'] : 'transaksi'; // Default Transaksi
$tgl_a  = isset($_GET['tgl_a']) ? $_GET['tgl_a'] : date('Y-m-01'); // Awal Bulan
$tgl_b  = isset($_GET['tgl_b']) ? $_GET['tgl_b'] : date('Y-m-d');  // Hari Ini

// 2. LOGIC QUERY BERDASARKAN TIPE
$data = [];
$judul_laporan = "";

switch ($tipe) {
    case 'transaksi':
        $judul_laporan = "Laporan Keuangan & Transaksi";
        $sql = "SELECT t.*, u.nama_lengkap, j.harga as harga_trip 
                FROM transaksi t 
                JOIN users u ON t.id_user = u.id_user 
                JOIN jadwal j ON t.id_jadwal = j.id_jadwal
                WHERE DATE(t.tanggal_booking) BETWEEN '$tgl_a' AND '$tgl_b'
                ORDER BY t.tanggal_booking DESC";
        break;

    case 'jadwal':
        $judul_laporan = "Laporan Jadwal & Trip";
        $sql = "SELECT j.*, g.nama_gunung, u.nama_lengkap as guide 
                FROM jadwal j
                JOIN gunung g ON j.id_gunung = g.id_gunung
                LEFT JOIN users u ON j.id_penjaga = u.id_user
                WHERE j.tanggal_berangkat BETWEEN '$tgl_a' AND '$tgl_b'
                ORDER BY j.tanggal_berangkat DESC";
        break;

    case 'users':
        $judul_laporan = "Laporan Data Pengguna";
        // User ga wajib pake tanggal, tapi kita kasih opsi filter created_at
        $sql = "SELECT * FROM users WHERE DATE(created_at) BETWEEN '$tgl_a' AND '$tgl_b' ORDER BY nama_lengkap ASC";
        break;

    case 'gunung':
        $judul_laporan = "Laporan Data Master Gunung";
        // Gunung data statis, abaikan tanggal
        $sql = "SELECT * FROM gunung ORDER BY nama_gunung ASC";
        break;
    
    case 'reviews':
        $judul_laporan = "Laporan Feedback & Testimoni";
        $sql = "SELECT r.*, u.nama_lengkap, g.nama_gunung 
                FROM reviews r
                JOIN users u ON r.id_user = u.id_user
                JOIN gunung g ON r.id_gunung = g.id_gunung
                WHERE DATE(r.tanggal_review) BETWEEN '$tgl_a' AND '$tgl_b'";
        break;
}

// Eksekusi Query
$stmt = $conn->prepare($sql);
$stmt->execute();
?>

<div class="content">
    <div class="mb-4">
        <h3 class="fw-bold text-success"><i class="fa-solid fa-print"></i> Pusat Laporan</h3>
        <p class="text-muted">Rekap data dan cetak laporan PDF.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body bg-light rounded-4">
            <form method="GET" class="row g-3 align-items-end">
                
                <div class="col-md-3">
                    <label class="fw-bold mb-1">Jenis Laporan</label>
                    <select name="tipe" class="form-select">
                        <option value="transaksi" <?= $tipe=='transaksi'?'selected':'' ?>>Transaksi (Keuangan)</option>
                        <option value="jadwal" <?= $tipe=='jadwal'?'selected':'' ?>>Jadwal Trip & History</option>
                        <option value="users" <?= $tipe=='users'?'selected':'' ?>>Data User / Peserta</option>
                        <option value="reviews" <?= $tipe=='reviews'?'selected':'' ?>>Testimoni</option>
                        <option value="gunung" <?= $tipe=='gunung'?'selected':'' ?>>Data Gunung (Master)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="fw-bold mb-1">Dari Tanggal</label>
                    <input type="date" name="tgl_a" class="form-control" value="<?= $tgl_a; ?>">
                </div>
                <div class="col-md-3">
                    <label class="fw-bold mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_b" class="form-control" value="<?= $tgl_b; ?>">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter"></i> Tampilkan</button>
                    <a href="cetak_laporan.php?tipe=<?= $tipe; ?>&tgl_a=<?= $tgl_a; ?>&tgl_b=<?= $tgl_b; ?>" target="_blank" class="btn btn-danger w-100">
                        <i class="fa-solid fa-file-pdf"></i> Cetak PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-success"><?= $judul_laporan; ?></h5>
            <small class="text-muted">Periode: <?= date('d M Y', strtotime($tgl_a)); ?> - <?= date('d M Y', strtotime($tgl_b)); ?></small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <?php if($tipe == 'transaksi'): ?>
                                <th>Tanggal</th>
                                <th>Invoice</th>
                                <th>Pemesan</th>
                                <th>Status</th>
                                <th class="text-end">Nominal</th>
                            <?php elseif($tipe == 'jadwal'): ?>
                                <th>Tujuan</th>
                                <th>Tgl Berangkat</th>
                                <th>Guide</th>
                                <th>Status</th>
                                <th>Harga</th>
                            <?php elseif($tipe == 'users'): ?>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>No HP</th>
                                <th>Join Date</th>
                            <?php elseif($tipe == 'gunung'): ?>
                                <th>Nama Gunung</th>
                                <th>Lokasi</th>
                                <th>Deskripsi</th>
                            <?php elseif($tipe == 'reviews'): ?>
                                <th>Tanggal</th>
                                <th>User</th>
                                <th>Gunung</th>
                                <th>Rating</th>
                                <th>Komentar</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $total_uang = 0;
                        if($stmt->rowCount() > 0) {
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>

                            <?php if($tipe == 'transaksi'): 
                                $total_uang += $row['total_bayar']; 
                            ?>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_booking'])); ?></td>
                                <td class="font-monospace">#<?= $row['kode_booking']; ?></td>
                                <td><?= $row['nama_lengkap']; ?></td>
                                <td><span class="badge bg-secondary"><?= $row['status_bayar']; ?></span></td>
                                <td class="text-end fw-bold">Rp <?= number_format($row['total_bayar']); ?></td>
                            
                            <?php elseif($tipe == 'jadwal'): ?>
                                <td><?= $row['nama_gunung']; ?></td>
                                <td><?= date('d M Y', strtotime($row['tanggal_berangkat'])); ?></td>
                                <td><?= $row['guide'] ?: '-'; ?></td>
                                <td><?= strtoupper($row['status_trip']); ?></td>
                                <td>Rp <?= number_format($row['harga']); ?></td>

                            <?php elseif($tipe == 'users'): ?>
                                <td><?= $row['nama_lengkap']; ?></td>
                                <td><?= $row['username']; ?></td>
                                <td><?= strtoupper($row['role']); ?></td>
                                <td><?= $row['no_hp']; ?></td>
                                <td><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>

                            <?php elseif($tipe == 'gunung'): ?>
                                <td class="fw-bold"><?= $row['nama_gunung']; ?></td>
                                <td><?= $row['lokasi']; ?></td>
                                <td><?= substr($row['deskripsi'], 0, 50); ?>...</td>
                            
                            <?php elseif($tipe == 'reviews'): ?>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_review'])); ?></td>
                                <td><?= $row['nama_lengkap']; ?></td>
                                <td><?= $row['nama_gunung']; ?></td>
                                <td class="text-warning"><?= str_repeat('★', $row['rating']); ?></td>
                                <td><?= $row['komentar']; ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-4'>Tidak ada data pada periode ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                    
                    <?php if($tipe == 'transaksi'): ?>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">TOTAL PENDAPATAN</td>
                            <td class="text-end fw-bold text-success fs-5">Rp <?= number_format($total_uang); ?></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>

                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>