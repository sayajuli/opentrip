<?php 
$pageTitle = "Kelola Gunung";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';

// --- 1. SETUP VARIABEL PAGINATION & SEARCH ---
// Ambil limit data (default 10)
$batas = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
// Ambil halaman aktif (default 1)
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
// Tentukan mulai data dari index ke berapa
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// Ambil kata kunci pencarian
$keyword = isset($_GET['cari']) ? $_GET['cari'] : "";
$filter_lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : "";

// --- 2. LOGIC QUERY (Flexible: Bisa Search + Filter) ---
// Base Query
$sql_dasar = "FROM gunung WHERE (nama_gunung LIKE :keyword OR lokasi LIKE :keyword)";
$params = [':keyword' => "%$keyword%"];

// Tambahan Filter Lokasi (Dropdown)
if(!empty($filter_lokasi)){
    $sql_dasar .= " AND lokasi = :lokasi";
    $params[':lokasi'] = $filter_lokasi;
}

// A. Hitung Total Data (Buat Pagination)
$stmt_count = $conn->prepare("SELECT COUNT(*) " . $sql_dasar);
$stmt_count->execute($params);
$jumlah_data = $stmt_count->fetchColumn();
$total_halaman = ceil($jumlah_data / $batas);

// B. Ambil Data Halaman Ini
$stmt = $conn->prepare("SELECT * " . $sql_dasar . " ORDER BY id_gunung DESC LIMIT $halaman_awal, $batas");
$stmt->execute($params);

// C. Ambil List Lokasi Unik (Buat Dropdown Filter)
$stmt_lokasi = $conn->query("SELECT DISTINCT lokasi FROM gunung ORDER BY lokasi ASC");
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success"><i class="fa-solid fa-mountain"></i> Data Gunung</h3>
        <button type="button" class="btn btn-alam" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fa-solid fa-plus"></i> Tambah Gunung
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            
            <form method="GET" class="row g-2 mb-4 align-items-center">
                
                <div class="col-auto">
                    <select name="limit" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="10" <?= $batas == 10 ? 'selected' : '' ?>>10 Data</option>
                        <option value="20" <?= $batas == 20 ? 'selected' : '' ?>>20 Data</option>
                        <option value="50" <?= $batas == 50 ? 'selected' : '' ?>>50 Data</option>
                    </select>
                </div>

                <div class="col-auto">
                    <select name="lokasi" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Lokasi --</option>
                        <?php while($lok = $stmt_lokasi->fetch(PDO::FETCH_ASSOC)) { ?>
                            <option value="<?= $lok['lokasi']; ?>" <?= $filter_lokasi == $lok['lokasi'] ? 'selected' : '' ?>>
                                <?= $lok['lokasi']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col">
                    <div class="input-group input-group-sm">
                        <input type="text" name="cari" class="form-control" placeholder="Cari gunung..." value="<?= $keyword; ?>">
                        <button class="btn btn-outline-success" type="submit"><i class="fa-solid fa-search"></i></button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Gunung</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = $halaman_awal + 1; // Nomor urut ngikutin halaman
                        if($stmt->rowCount() > 0) {
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <img src="../uploads/<?= $row['gambar']; ?>" width="70" height="70" class="rounded-3 object-fit-cover shadow-sm">
                            </td>
                            <td class="fw-bold"><?= $row['nama_gunung']; ?></td>
                            <td><i class="fa-solid fa-location-dot text-danger"></i> <?= $row['lokasi']; ?></td>
                            <td>
                                <a href="gunung_edit.php?id=<?= $row['id_gunung']; ?>" class="btn btn-sm btn-warning text-white rounded-pill px-3">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 btn-hapus" 
                                        data-id="<?= $row['id_gunung']; ?>" 
                                        data-nama="<?= $row['nama_gunung']; ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else { 
                        ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-magnifying-glass fa-2x mb-3"></i><br>
                                Data tidak ditemukan.
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Menampilkan <?= $stmt->rowCount(); ?> dari total <?= $jumlah_data; ?> data
                </small>

                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= $halaman == 1 ? 'disabled' : '' ?>">
                            <a class="page-link text-success" href="?halaman=<?= $halaman - 1; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&lokasi=<?= $filter_lokasi; ?>">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        </li>

                        <?php for($i = 1; $i <= $total_halaman; $i++) : ?>
                            <li class="page-item <?= $halaman == $i ? 'active' : '' ?>">
                                <a class="page-link <?= $halaman == $i ? 'bg-success border-success' : 'text-success' ?>" 
                                   href="?halaman=<?= $i; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&lokasi=<?= $filter_lokasi; ?>">
                                    <?= $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $halaman == $total_halaman ? 'disabled' : '' ?>">
                            <a class="page-link text-success" href="?halaman=<?= $halaman + 1; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&lokasi=<?= $filter_lokasi; ?>">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Tambah Gunung Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../api/admin_gunung.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Gunung</label>
                            <input type="text" name="nama_gunung" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Lokasi (Provinsi)</label>
                            <input type="text" name="lokasi" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto Gunung</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="btn_simpan" class="btn btn-alam fw-bold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnHapus = document.querySelectorAll('.btn-hapus');
        btnHapus.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                Swal.fire({
                    title: 'Hapus ' + nama + '?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `../api/admin_gunung.php?hapus=${id}`;
                    }
                })
            });
        });
    });
</script>

<?php 
if(isset($_GET['pesan'])) { 
    if($_GET['pesan'] == 'sukses') echo "<script>Swal.fire('Berhasil', 'Data tersimpan', 'success');</script>";
    elseif($_GET['pesan'] == 'update') echo "<script>Swal.fire('Berhasil', 'Data diupdate', 'success');</script>";
    elseif($_GET['pesan'] == 'hapus') echo "<script>Swal.fire('Terhapus', 'Data dihapus', 'success');</script>";
}
include 'include/footer.php'; 
?>