<?php 
$pageTitle = "Edit Gunung";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';

// Ambil ID dari URL
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM gunung WHERE id_gunung = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$data) {
    echo "<script>window.location='gunung.php';</script>";
    exit;
}
?>

<div class="content">
    <div class="mb-4">
        <a href="gunung.php" class="btn btn-outline-secondary btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        <h3 class="fw-bold text-success">Edit Data Gunung</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="../api/admin_gunung.php" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="id_gunung" value="<?= $data['id_gunung']; ?>">
                <input type="hidden" name="gambar_lama" value="<?= $data['gambar']; ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama Gunung</label>
                        <input type="text" name="nama_gunung" class="form-control" value="<?= $data['nama_gunung']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" value="<?= $data['lokasi']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="5" required><?= $data['deskripsi']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Gambar Saat Ini</label><br>
                    <img src="../uploads/<?= $data['gambar']; ?>" width="150" class="rounded mb-2 border p-1">
                    <div class="alert alert-info py-2 small">
                        <i class="fa-solid fa-info-circle"></i> Biarkan kosong jika tidak ingin mengubah gambar.
                    </div>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" name="btn_update" class="btn btn-alam fw-bold px-4">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>