<?php 
$pageTitle = "Verifikasi Pembayaran";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';

// --- 1. SETUP PARAMETER ---
$status_view = isset($_GET['status']) ? $_GET['status'] : 'menunggu_verifikasi';
$batas       = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$halaman     = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;
$keyword     = isset($_GET['cari']) ? $_GET['cari'] : "";

// --- 2. BUILD QUERY ---
// Base Where Clause (Gabungin Logic Tab + Search)
$where = "WHERE 1=1";
$params = [];

// A. Filter Berdasarkan Tab
if($status_view == 'menunggu_verifikasi') {
    $where .= " AND t.status_bayar IN ('menunggu_verifikasi', 'pending')";
    $order = "ORDER BY t.tanggal_booking ASC"; // Prioritas yang lama
} else {
    $where .= " AND t.status_bayar IN ('lunas', 'tolak', 'batal')";
    $order = "ORDER BY t.tanggal_booking DESC"; // Yang baru di atas
}

// B. Filter Search (Invoice / Nama)
if (!empty($keyword)) {
    $where .= " AND (t.kode_booking LIKE :kw OR u.nama_lengkap LIKE :kw)";
    $params[':kw'] = "%$keyword%";
}

// --- 3. EKSEKUSI ---

// Hitung Total Data (Untuk Pagination)
$sql_count = "SELECT COUNT(*) FROM transaksi t 
              JOIN users u ON t.id_user = u.id_user 
              $where";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->execute($params);
$jumlah_data = $stmt_count->fetchColumn();
$total_halaman = ceil($jumlah_data / $batas);

// Ambil Data Halaman Ini
$sql_data = "SELECT t.*, u.nama_lengkap, g.nama_gunung, j.tanggal_berangkat 
             FROM transaksi t
             JOIN users u ON t.id_user = u.id_user
             JOIN jadwal j ON t.id_jadwal = j.id_jadwal
             JOIN gunung g ON j.id_gunung = g.id_gunung
             $where 
             $order 
             LIMIT $halaman_awal, $batas";
$stmt = $conn->prepare($sql_data);
$stmt->execute($params);
?>

<div class="content">
    <div class="mb-4">
        <h3 class="fw-bold text-success"><i class="fa-solid fa-file-invoice-dollar"></i> Transaksi Masuk</h3>
    </div>

    <ul class="nav nav-pills mb-3 gap-2" style="width: fit-content;">
        <li class="nav-item">
            <a class="nav-link <?= $status_view == 'menunggu_verifikasi' ? 'bg-success text-white shadow' : 'bg-white text-secondary border' ?>" 
               href="?status=menunggu_verifikasi">
               <i class="fa-solid fa-bell me-2"></i> Perlu Verifikasi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $status_view == 'riwayat' ? 'bg-success text-white shadow' : 'bg-white text-secondary border' ?>" 
               href="?status=riwayat">
               <i class="fa-solid fa-clock-rotate-left me-2"></i> Riwayat Transaksi
            </a>
        </li>
    </ul>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            
            <form method="GET" class="row g-2 mb-4 align-items-center">
                <input type="hidden" name="status" value="<?= $status_view; ?>">

                <div class="col-auto">
                    <select name="limit" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="10" <?= $batas == 10 ? 'selected' : '' ?>>10 Data</option>
                        <option value="20" <?= $batas == 20 ? 'selected' : '' ?>>20 Data</option>
                        <option value="50" <?= $batas == 50 ? 'selected' : '' ?>>50 Data</option>
                    </select>
                </div>
                
                <div class="col">
                    <div class="input-group input-group-sm">
                        <input type="text" name="cari" class="form-control" placeholder="Cari No Invoice / Nama..." value="<?= $keyword; ?>">
                        <button class="btn btn-outline-success" type="submit"><i class="fa-solid fa-search"></i></button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Pemesan</th>
                            <th>Trip & Tanggal</th>
                            <th>Total Bayar</th>
                            <th>Bukti TF</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($stmt->rowCount() > 0) {
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                        ?>
                        <tr>
                            <td class="font-monospace text-primary fw-bold">#<?= $row['kode_booking']; ?></td>
                            <td>
                                <strong><?= $row['nama_lengkap']; ?></strong><br>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($row['tanggal_booking'])); ?></small>
                            </td>
                            <td>
                                <i class="fa-solid fa-mountain text-success"></i> <?= $row['nama_gunung']; ?><br>
                                <small class="text-muted"><i class="fa-solid fa-calendar"></i> <?= date('d M Y', strtotime($row['tanggal_berangkat'])); ?></small>
                            </td>
                            <td class="fw-bold text-danger">Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if(!empty($row['bukti_bayar'])): ?>
                                    <button type="button" class="btn btn-sm btn-outline-dark" 
                                            onclick="lihatBukti('../uploads/bukti/<?= $row['bukti_bayar']; ?>', '<?= $row['kode_booking']; ?>')">
                                        <i class="fa-solid fa-image"></i> Cek
                                    </button>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Belum Upload</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $st = $row['status_bayar'];
                                    if($st=='lunas') echo '<span class="badge bg-success">LUNAS</span>';
                                    elseif($st=='menunggu_verifikasi') echo '<span class="badge bg-warning text-dark">CEK BUKTI</span>';
                                    elseif($st=='pending') echo '<span class="badge bg-secondary">BELUM BAYAR</span>';
                                    elseif($st=='tolak') echo '<span class="badge bg-danger">DITOLAK</span>';
                                ?>
                            </td>
                            <td>
                                <?php if($st == 'menunggu_verifikasi'): ?>
                                    <div class="d-flex gap-1">
                                        <a href="../api/admin_transaksi.php?act=terima&id=<?= $row['id_transaksi']; ?>" 
                                           class="btn btn-sm btn-success confirm-acc" title="Terima Pembayaran">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger btn-tolak" 
                                                data-id="<?= $row['id_transaksi']; ?>" title="Tolak Pembayaran">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <small class="text-muted">-</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } } else { ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Tidak ada data transaksi ditemukan.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Total <?= $jumlah_data; ?> transaksi</small>
                
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= $halaman == 1 ? 'disabled' : '' ?>">
                            <a class="page-link text-success" 
                               href="?halaman=<?= $halaman - 1; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&status=<?= $status_view; ?>">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <?php for($i = 1; $i <= $total_halaman; $i++) : ?>
                            <li class="page-item <?= $halaman == $i ? 'active' : '' ?>">
                                <a class="page-link <?= $halaman == $i ? 'bg-success border-success' : 'text-success' ?>" 
                                   href="?halaman=<?= $i; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&status=<?= $status_view; ?>">
                                    <?= $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $halaman == $total_halaman ? 'disabled' : '' ?>">
                            <a class="page-link text-success" 
                               href="?halaman=<?= $halaman + 1; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&status=<?= $status_view; ?>">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalBukti" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Bukti Transfer <span id="labelInvoice"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0 bg-light">
                <img id="imgBukti" src="" class="img-fluid" alt="Bukti Transfer">
            </div>
            <div class="modal-footer justify-content-center">
                <a id="linkDownload" href="" download class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-download"></i> Download Gambar</a>
            </div>
        </div>
    </div>
</div>

<script>
    function lihatBukti(url, invoice) {
        document.getElementById('imgBukti').src = url;
        document.getElementById('linkDownload').href = url;
        document.getElementById('labelInvoice').innerText = '#' + invoice;
        var myModal = new bootstrap.Modal(document.getElementById('modalBukti'));
        myModal.show();
    }

    document.addEventListener("DOMContentLoaded", function() {
        const btnAcc = document.querySelectorAll('.confirm-acc');
        btnAcc.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                Swal.fire({
                    title: 'Verifikasi Pembayaran?',
                    text: "Pastikan uang sudah masuk ke rekening mutasi!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Terima (Lunas)',
                    confirmButtonColor: '#198754'
                }).then((result) => {
                    if (result.isConfirmed) window.location.href = url;
                });
            });
        });

        const btnTolak = document.querySelectorAll('.btn-tolak');
        btnTolak.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Tolak Pembayaran?',
                    text: "Transaksi akan dibatalkan. Peserta harus upload ulang.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tolak',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `../api/admin_transaksi.php?act=tolak&id=${id}`;
                    }
                });
            });
        });
    });
</script>

<?php 
if(isset($_GET['pesan'])) { 
    if($_GET['pesan'] == 'lunas') echo "<script>Swal.fire('Berhasil', 'Pembayaran diverifikasi LUNAS.', 'success');</script>";
    elseif($_GET['pesan'] == 'tolak') echo "<script>Swal.fire('Ditolak', 'Pembayaran ditolak.', 'info');</script>";
}
include 'include/footer.php'; 
?>