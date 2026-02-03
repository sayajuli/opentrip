<?php 
$pageTitle = "Edit Jadwal";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM jadwal WHERE id_jadwal = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$data) { echo "<script>window.location='jadwal.php';</script>"; exit; }

// Ambil List Penjaga Lagi
$opt_penjaga = $conn->query("SELECT * FROM users WHERE role='penjaga' ORDER BY nama_lengkap ASC");
?>

<div class="content">
    <div class="mb-4">
        <a href="jadwal.php" class="btn btn-outline-secondary btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        <h3 class="fw-bold text-success">Edit Jadwal Trip</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="../api/admin_jadwal.php" method="POST">
                <input type="hidden" name="id_jadwal" value="<?= $data['id_jadwal']; ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Penjaga (Guide)</label>
                        <select name="id_penjaga" class="form-select">
                            <option value="">-- Belum Ditentukan --</option>
                            <?php while($p = $opt_penjaga->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option value="<?= $p['id_user']; ?>" <?= $data['id_penjaga'] == $p['id_user'] ? 'selected' : '' ?>>
                                    <?= $p['nama_lengkap']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Status Trip</label>
                        <select name="status_trip" class="form-select">
                            <option value="buka" <?= $data['status_trip']=='buka'?'selected':''; ?>>Buka (Open Registration)</option>
                            <option value="tutup" <?= $data['status_trip']=='tutup'?'selected':''; ?>>Tutup (Full/Close)</option>
                            <option value="selesai" <?= $data['status_trip']=='selesai'?'selected':''; ?>>Selesai</option>
                            <option value="batal" <?= $data['status_trip']=='batal'?'selected':''; ?>>Batal</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Tanggal Berangkat</label>
                        <input type="date" name="tgl_berangkat" class="form-control" value="<?= $data['tanggal_berangkat']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control" value="<?= $data['tanggal_selesai']; ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Harga (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="harga" class="form-control input-rupiah" 
                                   value="<?= number_format($data['harga'],0,',','.'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Kuota Maks</label>
                        <input type="number" name="kuota" class="form-control" value="<?= $data['kuota_maks']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4"><?= $data['deskripsi']; ?></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="btn_update" class="btn btn-alam fw-bold px-4">Update Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>