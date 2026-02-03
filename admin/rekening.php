<?php 
$pageTitle = "Kelola Rekening";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success"><i class="fa-solid fa-credit-card"></i> Metode Pembayaran</h3>
        <button type="button" class="btn btn-alam" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fa-solid fa-plus"></i> Tambah Bank
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Logo</th>
                            <th>Nama Bank</th>
                            <th>Info Rekening</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $stmt = $conn->query("SELECT * FROM rekening ORDER BY id_rekening DESC");
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <?php if($row['logo_bank']): ?>
                                    <img src="../uploads/<?= $row['logo_bank']; ?>" height="40" class="object-fit-contain">
                                <?php else: ?>
                                    <span class="badge bg-secondary">No Logo</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold"><?= $row['nama_bank']; ?></td>
                            <td>
                                <div class="fw-bold fs-5 text-dark"><?= $row['nomor_rekening']; ?></div>
                                <small class="text-muted">A.n <?= $row['atas_nama']; ?></small>
                            </td>
                            <td>
                                <a href="rekening_edit.php?id=<?= $row['id_rekening']; ?>" class="btn btn-sm btn-warning text-white rounded-pill px-3">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 btn-hapus" 
                                        data-id="<?= $row['id_rekening']; ?>" 
                                        data-nama="<?= $row['nama_bank']; ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if($stmt->rowCount() == 0): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-wallet fa-3x mb-3"></i><br>
                    Belum ada metode pembayaran.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Tambah Rekening Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../api/admin_rekening.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Bank / E-Wallet</label>
                        <input type="text" name="nama_bank" class="form-control" placeholder="Contoh: BCA, Mandiri, GoPay" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Rekening</label>
                        <input type="text" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="nomor_rekening" class="form-control" placeholder="Contoh: 1234567890" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Atas Nama</label>
                        <input type="text" name="atas_nama" class="form-control" placeholder="Pemilik Rekening" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Logo Bank (Opsional)</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <small class="text-muted">Format PNG/JPG (Transparan lebih bagus)</small>
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
                    text: "User tidak akan bisa memilih bank ini lagi.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `../api/admin_rekening.php?hapus=${id}`;
                    }
                })
            });
        });
    });
</script>

<?php 
if(isset($_GET['pesan'])) { 
    if($_GET['pesan'] == 'sukses') echo "<script>Swal.fire('Berhasil', 'Rekening baru ditambahkan', 'success');</script>";
    elseif($_GET['pesan'] == 'update') echo "<script>Swal.fire('Berhasil', 'Data rekening diupdate', 'success');</script>";
    elseif($_GET['pesan'] == 'hapus') echo "<script>Swal.fire('Terhapus', 'Rekening dihapus', 'success');</script>";
}
include 'include/footer.php'; 
?>