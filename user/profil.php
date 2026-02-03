<?php
session_start();
require '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php"); exit;
}

include 'include/header.php';
include 'include/navbar.php';

// Ambil Data Terbaru User
$id = $_SESSION['id_user'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->execute([$id]);
$d = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="text-center mb-4">
                <h3 class="fw-bold text-success">Edit Profil</h3>
                <p class="text-muted">Perbarui informasi akunmu di sini.</p>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="../api/user_profil.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= $d['nama_lengkap']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" class="form-control bg-light" value="<?= $d['username']; ?>" readonly>
                            <small class="text-muted" style="font-size: 11px;">Username tidak dapat diubah.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= $d['email']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">No WhatsApp</label>
                                <input type="number" name="hp" class="form-control" value="<?= $d['no_hp']; ?>" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="alert alert-light border border-warning">
                            <i class="fa-solid fa-lock text-warning me-2"></i> 
                            <strong>Ganti Password</strong><br>
                            <small class="text-muted">Kosongkan kolom di bawah jika tidak ingin mengganti password.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="******">
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="btn_update" class="btn btn-alam fw-bold py-2 rounded-pill">
                                Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>