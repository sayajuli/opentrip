<?php 
$pageTitle = "Edit User";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$data) { echo "<script>window.location='users.php';</script>"; exit; }
?>

<div class="content">
    <div class="mb-4">
        <a href="users.php" class="btn btn-outline-secondary btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        <h3 class="fw-bold text-success">Edit Data User</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="../api/admin_users.php" method="POST">
                <input type="hidden" name="id_user" value="<?= $data['id_user']; ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= $data['nama_lengkap']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" class="form-control" value="<?= $data['username']; ?>" disabled readonly>
                        <small class="text-muted">Username tidak bisa diubah.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $data['email']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">No HP / WhatsApp</label>
                        <input type="text" name="hp" class="form-control" value="<?= $data['no_hp']; ?>" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Role</label>
                    <select name="role" class="form-select">
                        <option value="user" <?= $data['role']=='user'?'selected':''; ?>>User (Pendaki)</option>
                        <option value="penjaga" <?= $data['role']=='penjaga'?'selected':''; ?>>Penjaga (Guide)</option>
                        <option value="admin" <?= $data['role']=='admin'?'selected':''; ?>>Admin</option>
                    </select>
                </div>

                <div class="alert alert-warning mb-3">
                    <i class="fa-solid fa-lock"></i> <strong>Ganti Password</strong><br>
                    <small>Isi kolom di bawah ini HANYA JIKA ingin mengganti password user. Jika tidak, biarkan kosong.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin diganti">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="btn_update" class="btn btn-alam fw-bold px-4">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>