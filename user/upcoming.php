<?php
session_start();
<<<<<<< HEAD
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php"); exit;
=======
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'user') {
    // Kalau belum, tendang balik ke halaman login
    header("Location: ../login.php?pesan=belum_login");
    exit; // PENTING: Stop eksekusi script di bawahnya
>>>>>>> 418c4562026a96cd4ca033bf5fee065c81a2cd23
}
require '../config/koneksi.php';
include 'include/header.php';
include 'include/navbar.php';

$id_user = $_SESSION['id_user'];

// Query: Ambil Trip yg LUNAS dan Belum Berangkat
$sql = "SELECT t.*, j.tanggal_berangkat, j.tanggal_selesai, g.nama_gunung, g.gambar 
        FROM transaksi t
        JOIN jadwal j ON t.id_jadwal = j.id_jadwal
        JOIN gunung g ON j.id_gunung = g.id_gunung
        WHERE t.id_user = $id_user 
        AND t.status_bayar = 'lunas' 
        AND j.tanggal_berangkat >= CURDATE()
        ORDER BY j.tanggal_berangkat ASC";
$stmt = $conn->query($sql);
?>

<div class="container pb-5">
    <h4 class="fw-bold mb-4 ps-2 border-start border-4 border-warning">Tiket Saya (Akan Datang)</h4>

    <?php if($stmt->rowCount() == 0): ?>
        <div class="text-center py-5 text-muted">
            <i class="fa-solid fa-ticket fa-3x mb-3 opacity-50"></i><br>
            Kamu belum punya tiket trip yang aktif.<br>
            <a href="trips.php" class="btn btn-sm btn-alam rounded-pill mt-3">Cari Trip Dulu Yuk</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                $start = new DateTime($row['tanggal_berangkat']);
                $now   = new DateTime();
                
                $start->setTime(0,0,0);
                $now->setTime(0,0,0);
                
                $diff  = $now->diff($start);
                $days  = $diff->days;
                $is_today = ($start == $now);

                $bisa_cancel = ($start > $now && $days >= 14);
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="position-relative">
                        <img src="../uploads/<?= $row['gambar']; ?>" class="card-img-top object-fit-cover" style="height: 180px;">
                        
                        <div class="position-absolute bottom-0 start-0 m-3 badge bg-white text-dark shadow-sm font-monospace">
                            #<?= $row['kode_booking']; ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="mb-3">
                            <?php if($is_today): ?>
                                <div class="badge bg-success w-100 py-2 fs-6 animate-pulse">
                                    <i class="fa-solid fa-person-hiking me-2"></i> SEDANG BERJALAN
                                </div>
                            <?php elseif($days <= 3): ?>
                                <span class="badge bg-danger text-white mb-1">
                                    <i class="fa-solid fa-fire me-1"></i> <?= $days; ?> Hari Lagi
                                </span>
                                <small class="text-danger d-block fw-bold" style="font-size:11px;">Siapkan perlengkapanmu!</small>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark mb-1">
                                    <i class="fa-solid fa-clock me-1"></i> <?= $days; ?> Hari Lagi
                                </span>
                            <?php endif; ?>
                        </div>

                        <h5 class="fw-bold mb-1"><?= $row['nama_gunung']; ?></h5>
                        <p class="text-muted small mb-3">
                            <i class="fa-regular fa-calendar me-2"></i> <?= date('d M Y', strtotime($row['tanggal_berangkat'])); ?>
                        </p>

                        <div class="d-grid gap-2">
                             <a href="../detail.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-outline-secondary rounded-pill btn-sm">
                                <i class="fa-solid fa-circle-info me-1"></i> Info Trip
                            </a>
                            
                            <?php if($bisa_cancel): ?>
                                <button type="button" class="btn btn-outline-danger rounded-pill btn-sm" 
                                        onclick="confirmCancel(<?= $row['id_transaksi']; ?>)">
                                    <i class="fa-solid fa-ban me-1"></i> Batalkan Trip
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function confirmCancel(id) {
        Swal.fire({
            title: 'Yakin mau batal?',
            text: "Pembatalan hanya bisa dilakukan H-14. Dana akan dikembalikan sesuai kebijakan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.action = '../api/user_action.php?act=cancel';
                form.method = 'POST';
                
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id_transaksi';
                input.value = id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        })
    }
</script>

<style>
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    .animate-pulse {
        animation: pulse 2s infinite;
    }
</style>

<?php include 'include/footer.php'; ?>
