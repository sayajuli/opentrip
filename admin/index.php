<?php 
include 'include/header.php'; 
include 'include/sidebar.php'; 
include '../api/auto_update.php';
require '../config/koneksi.php';

// Hitung Data Sederhana (PDO)
$tot_user = $conn->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$tot_gunung = $conn->query("SELECT COUNT(*) FROM gunung")->fetchColumn();
$tot_jadwal = $conn->query("SELECT COUNT(*) FROM jadwal WHERE status_trip='buka'")->fetchColumn();
$tot_transaksi = $conn->query("SELECT COUNT(*) FROM transaksi WHERE status_bayar='pending'")->fetchColumn();
?>

<div class="content">
    <nav class="navbar navbar-light bg-white shadow-sm mb-4 rounded px-3">
        <span class="navbar-brand mb-0 h1">Dashboard Overview</span>
        <span class="text-muted">Halo, <b><?= $_SESSION['nama']; ?></b></span>
    </nav>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-white" style="background: #2E8B57;">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2" style="font-size: 12px;">Total Pendaki</h6>
                    <h2 class="fw-bold mb-0"><?= $tot_user; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-white" style="background: #8B4513;">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2" style="font-size: 12px;">Destinasi Gunung</h6>
                    <h2 class="fw-bold mb-0"><?= $tot_gunung; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-white" style="background: #D2691E;">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2" style="font-size: 12px;">Jadwal Buka</h6>
                    <h2 class="fw-bold mb-0"><?= $tot_jadwal; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-white bg-danger">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2" style="font-size: 12px;">Perlu Validasi</h6>
                    <h2 class="fw-bold mb-0"><?= $tot_transaksi; ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="alert alert-success">
        Selamat datang di Panel Admin <b>Jerry OpenTRIP</b>. Silakan kelola data melalui sidebar di sebelah kiri.
    </div>
</div>

<?php include 'include/footer.php'; ?>