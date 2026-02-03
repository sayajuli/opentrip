<?php 
$pageTitle = "Edit Rekening";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM rekening WHERE id_rekening = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$data) { echo "<script>window.location='rekening.php';</script>"; exit; }
?>

<div class="content">
    <div class="mb-4">
        <a href="rekening.php" class="btn btn-outline-secondary btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        <h3 class="fw-bold text-success">Edit Metode Pembayaran</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-4" style="max-width: 600px;">
        <div class="card-body p-4">
            <form action="../api/admin_rekening.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_rekening" value="<?= $data['id_rekening']; ?>">
                <input type="hidden" name="logo_lama" value="<?= $data['logo_bank']; ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Bank / E-Wallet</label>
                    <input type="text" name="nama_bank" class="form-control" value="<?= $data['nama_bank']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nomor Rekening</label>
                    <input type="text" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="nomor_rekening" class="form-control" value="<?= $data['nomor_rekening']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Atas Nama</label>
                    <input type="text" name="atas_nama" class="form-control" value="<?= $data['atas_nama']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Logo Saat Ini</label><br>
                    <?php if($data['logo_bank']): ?>
                        <img src="../uploads/<?= $data['logo_bank']; ?>" height="50" class="mb-2 border p-1 rounded">
                    <?php else: ?>
                        <span class="text-muted small">Tidak ada logo</span>
                    <?php endif; ?>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti logo.</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="btn_update" class="btn btn-alam fw-bold px-4">Update Bank</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>