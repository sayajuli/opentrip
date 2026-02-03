<?php
session_start();
require '../config/koneksi.php';
include 'include/header.php';
include 'include/navbar.php';
?>

<div class="container pb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-success">Cara Booking Trip</h2>
        <p class="text-muted">Mudahnya memulai petualangan barumu di Jerry OpenTrip.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 text-center p-4">
                <div class="mb-3">
                    <span class="badge bg-success rounded-circle p-3 fs-4">1</span>
                </div>
                <h5 class="fw-bold">Pilih Trip</h5>
                <p class="text-muted small">Cari destinasi impianmu di menu "Cari Trip". Baca detail fasilitas dan jadwalnya.</p>
                <i class="fa-solid fa-map-location-dot fa-3x text-success opacity-25 mt-3"></i>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 text-center p-4">
                <div class="mb-3">
                    <span class="badge bg-success rounded-circle p-3 fs-4">2</span>
                </div>
                <h5 class="fw-bold">Booking & Bayar</h5>
                <p class="text-muted small">Klik "Booking", tentukan jumlah peserta, lalu transfer sesuai nominal ke rekening resmi.</p>
                <i class="fa-solid fa-money-bill-transfer fa-3x text-success opacity-25 mt-3"></i>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 text-center p-4">
                <div class="mb-3">
                    <span class="badge bg-success rounded-circle p-3 fs-4">3</span>
                </div>
                <h5 class="fw-bold">Upload Bukti</h5>
                <p class="text-muted small">Upload bukti transfer di menu "Tagihan". Tunggu admin memverifikasi pembayaranmu.</p>
                <i class="fa-solid fa-cloud-arrow-up fa-3x text-success opacity-25 mt-3"></i>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 text-center p-4">
                <div class="mb-3">
                    <span class="badge bg-success rounded-circle p-3 fs-4">4</span>
                </div>
                <h5 class="fw-bold">Siap Berangkat</h5>
                <p class="text-muted small">Jika status "Lunas", tiketmu akan muncul di menu "Tiket Saya". Siap packing!</p>
                <i class="fa-solid fa-person-hiking fa-3x text-success opacity-25 mt-3"></i>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-5">
        <a href="trips.php" class="btn btn-alam rounded-pill px-5 py-3 fw-bold shadow">Mulai Petualangan Sekarang <i class="fa-solid fa-arrow-right ms-2"></i></a>
    </div>
</div>

<?php include 'include/footer.php'; ?>