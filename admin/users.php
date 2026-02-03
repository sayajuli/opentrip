<?php 
$pageTitle = "Kelola User";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';

// --- 1. SETUP PAGINATION & SEARCH ---
$batas = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$keyword = isset($_GET['cari']) ? $_GET['cari'] : "";
$filter_role = isset($_GET['role']) ? $_GET['role'] : "";

// --- 2. QUERY DINAMIS ---
$sql_dasar = "FROM users WHERE (nama_lengkap LIKE :keyword OR username LIKE :keyword)";
$params = [':keyword' => "%$keyword%"];

if(!empty($filter_role)){
    $sql_dasar .= " AND role = :role";
    $params[':role'] = $filter_role;
}

// Hitung Total
$stmt_count = $conn->prepare("SELECT COUNT(*) " . $sql_dasar);
$stmt_count->execute($params);
$jumlah_data = $stmt_count->fetchColumn();
$total_halaman = ceil($jumlah_data / $batas);

// Ambil Data
$stmt = $conn->prepare("SELECT * " . $sql_dasar . " ORDER BY id_user DESC LIMIT $halaman_awal, $batas");
$stmt->execute($params);
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success"><i class="fa-solid fa-users"></i> Data Pengguna</h3>
        <button type="button" class="btn btn-alam" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fa-solid fa-user-plus"></i> Tambah User
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
                    <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Role --</option>
                        <option value="admin" <?= $filter_role == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="penjaga" <?= $filter_role == 'penjaga' ? 'selected' : '' ?>>Penjaga</option>
                        <option value="user" <?= $filter_role == 'user' ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
                <div class="col">
                    <div class="input-group input-group-sm">
                        <input type="text" name="cari" class="form-control" placeholder="Cari nama/username..." value="<?= $keyword; ?>">
                        <button class="btn btn-outline-success" type="submit"><i class="fa-solid fa-search"></i></button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Kontak</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = $halaman_awal + 1;
                        if($stmt->rowCount() > 0) {
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <strong><?= $row['nama_lengkap']; ?></strong><br>
                                <small class="text-muted">@<?= $row['username']; ?></small>
                            </td>
                            <td>
                                <small><i class="fa-solid fa-envelope"></i> <?= $row['email']; ?></small><br>
                                <small><i class="fa-brands fa-whatsapp"></i> <?= $row['no_hp']; ?></small>
                            </td>
                            <td>
                                <?php if($row['role']=='admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php elseif($row['role']=='penjaga'): ?>
                                    <span class="badge bg-warning text-dark">Penjaga</span>
                                <?php else: ?>
                                    <span class="badge bg-success">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="users_edit.php?id=<?= $row['id_user']; ?>" class="btn btn-sm btn-warning text-white rounded-pill px-3">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <?php if($row['id_user'] != $_SESSION['id_user']): ?>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 btn-hapus" 
                                        data-id="<?= $row['id_user']; ?>" 
                                        data-nama="<?= $row['nama_lengkap']; ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } } else { ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Total <?= $jumlah_data; ?> data</small>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= $halaman == 1 ? 'disabled' : '' ?>">
                            <a class="page-link text-success" href="?halaman=<?= $halaman - 1; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&role=<?= $filter_role; ?>"><</a>
                        </li>
                        <?php for($i = 1; $i <= $total_halaman; $i++) : ?>
                            <li class="page-item <?= $halaman == $i ? 'active' : '' ?>">
                                <a class="page-link <?= $halaman == $i ? 'bg-success border-success' : 'text-success' ?>" href="?halaman=<?= $i; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&role=<?= $filter_role; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $halaman == $total_halaman ? 'disabled' : '' ?>">
                            <a class="page-link text-success" href="?halaman=<?= $halaman + 1; ?>&limit=<?= $batas; ?>&cari=<?= $keyword; ?>&role=<?= $filter_role; ?>">></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../api/admin_users.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col">
                            <label>No HP</label>
                            <input type="text" name="hp" class="form-control" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Role / Jabatan</label>
                        <select name="role" class="form-select" required>
                            <option value="user">User (Pendaki)</option>
                            <option value="penjaga">Penjaga (Guide)</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="btn_simpan" class="btn btn-alam">Simpan</button>
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
                    text: "User ini akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `../api/admin_users.php?hapus=${id}`;
                    }
                })
            });
        });
    });
</script>

<?php 
if(isset($_GET['pesan'])) { 
    if($_GET['pesan'] == 'sukses') echo "<script>Swal.fire('Berhasil', 'User baru ditambahkan', 'success');</script>";
    elseif($_GET['pesan'] == 'update') echo "<script>Swal.fire('Berhasil', 'Data user diupdate', 'success');</script>";
    elseif($_GET['pesan'] == 'hapus') echo "<script>Swal.fire('Terhapus', 'User berhasil dihapus', 'success');</script>";
    elseif($_GET['pesan'] == 'gagal') echo "<script>Swal.fire('Gagal', 'Username sudah dipakai', 'error');</script>";
}
include 'include/footer.php'; 
?>