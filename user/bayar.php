<?php
session_start();
require '../config/koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php"); exit;
}

// 2. Cek ID Transaksi di URL
if (!isset($_GET['id'])) {
    header("Location: payment.php"); exit;
}

$id_transaksi = $_GET['id'];
$id_user = $_SESSION['id_user'];

// 3. Ambil Data Transaksi (Amankan query biar cuma pemilik yg bisa liat)
$sql = "SELECT t.*, j.harga, j.tanggal_berangkat, g.nama_gunung, g.gambar 
        FROM transaksi t
        JOIN jadwal j ON t.id_jadwal = j.id_jadwal
        JOIN gunung g ON j.id_gunung = g.id_gunung
        WHERE t.id_transaksi = ? AND t.id_user = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$id_transaksi, $id_user]);
$d = $stmt->fetch(PDO::FETCH_ASSOC);

// Kalau data gak ketemu (atau user iseng ganti ID di URL)
if (!$d) { 
    echo "<script>alert('Transaksi tidak ditemukan!'); window.location='payment.php';</script>"; 
    exit; 
}

// 4. Ambil Daftar Rekening Admin
$rekening = $conn->query("SELECT * FROM rekening");

// Panggil Tampilan Baru
include 'include/header.php';
include 'include/navbar.php';
?>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <a href="payment.php" class="text-decoration-none text-muted mb-3 d-inline-block">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Tagihan
            </a>

            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-success text-white text-center py-4">
                    <small class="text-white-50 text-uppercase ls-1">Total Pembayaran</small>
                    <h1 class="fw-bold my-2">Rp <?= number_format($d['total_bayar'], 0, ',', '.'); ?></h1>
                    <div class="badge bg-white text-success rounded-pill px-3">
                        Order ID: #<?= $d['kode_booking']; ?>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="../uploads/<?= $d['gambar']; ?>" class="rounded-3 object-fit-cover me-3" width="80" height="80">
                        <div>
                            <h6 class="fw-bold mb-1"><?= $d['nama_gunung']; ?></h6>
                            <p class="text-muted small mb-0">
                                <i class="fa-regular fa-calendar me-1"></i> <?= date('d M Y', strtotime($d['tanggal_berangkat'])); ?><br>
                                <i class="fa-solid fa-user-group me-1"></i> <?= $d['jumlah_peserta']; ?> Pax
                            </p>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning border-0 d-flex align-items-center" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>
                        <div class="small text-muted">
                            Pastikan nominal transfer sesuai hingga 3 digit terakhir agar verifikasi otomatis lebih cepat.
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fa-solid fa-building-columns me-2 text-success"></i> Transfer Bank
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded-bottom-4">
                        <?php while($bank = $rekening->fetch(PDO::FETCH_ASSOC)) { ?>
                        <div class="list-group-item p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <?php if($bank['logo_bank']): ?>
                                        <img src="../uploads/<?= $bank['logo_bank']; ?>" width="50" class="me-3">
                                    <?php else: ?>
                                        <div class="bg-light p-2 rounded fw-bold me-3 text-secondary" style="width: 50px; text-align:center; font-size:12px;">
                                            <?= $bank['nama_bank']; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: 1px;">
                                            <?= $bank['nomor_rekening']; ?>
                                        </div>
                                        <small class="text-muted">a.n <?= $bank['atas_nama']; ?></small>
                                    </div>
                                </div>

                                <button class="btn btn-sm btn-light border text-secondary rounded-circle" 
                                        onclick="copyText('<?= $bank['nomor_rekening']; ?>')" 
                                        data-bs-toggle="tooltip" title="Salin Nomor">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Konfirmasi Pembayaran</h5>
                    
                    <form action="../api/user_upload.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_transaksi" value="<?= $d['id_transaksi']; ?>">
                        
                        <div class="mb-4 text-center border rounded-3 p-4 bg-light position-relative" id="dropArea">
                            <i class="fa-solid fa-cloud-arrow-up fa-3x text-secondary mb-3"></i>
                            <h6 class="fw-bold">Upload Bukti Transfer</h6>
                            <p class="text-muted small mb-3">Format: JPG, PNG, JPEG. Maks 2MB.</p>
                            
                            <input type="file" name="bukti" id="fileInput" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" 
                                   accept="image/*" style="cursor: pointer;" required onchange="previewImage(this)">
                            
                            <img id="preview" src="" class="img-fluid rounded mt-2 d-none shadow-sm" style="max-height: 200px;">
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" name="btn_upload" class="btn btn-alam btn-lg fw-bold rounded-pill">
                                Kirim Bukti Pembayaran <i class="fa-solid fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // 1. Fungsi Copy Rekening
    function copyText(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Pake SweetAlert kecil (Toast)
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            })
            Toast.fire({
                icon: 'success',
                title: 'Nomor rekening disalin!'
            })
        });
    }

    // 2. Fungsi Preview Gambar Sebelum Upload
    function previewImage(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('preview');
                img.src = e.target.result;
                img.classList.remove('d-none'); // Munculkan gambar
            }
            reader.readAsDataURL(file);
        }
    }
</script>

<?php include 'include/footer.php'; ?>