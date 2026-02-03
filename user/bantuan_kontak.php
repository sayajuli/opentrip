<?php
session_start();
require '../config/koneksi.php';
include 'include/header.php';
include 'include/navbar.php';

// Nomor WA Admin (Ganti dengan nomor asli, format 628xxx tanpa +)
$no_wa = "6281234567890"; 
$pesan_default = "Halo Admin Jerry OpenTrip, saya butuh bantuan mengenai...";
$link_wa = "https://wa.me/$no_wa?text=" . urlencode($pesan_default);
?>

<div class="container pb-5">
    <div class="row justify-content-center align-items-center">
        <div class="col-lg-6 mb-4">
            <span class="badge bg-warning text-dark mb-2">Customer Service</span>
            <h1 class="fw-bold text-dark display-5 mb-3">Butuh Bantuan?</h1>
            <p class="text-secondary fs-5 mb-4">Tim support kami siap membantumu Senin - Minggu (08.00 - 21.00 WIB).</p>
            
            <a href="<?= $link_wa; ?>" target="_blank" class="btn btn-success btn-lg rounded-pill px-5 py-3 fw-bold shadow-sm mb-3">
                <i class="fa-brands fa-whatsapp fa-lg me-2"></i> Chat WhatsApp
            </a>
            
            <div class="mt-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light p-3 rounded-circle me-3 text-success">
                        <i class="fa-solid fa-envelope fa-xl"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Email Support</small>
                        <span class="fw-bold">help@jerryopentrip.com</span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="bg-light p-3 rounded-circle me-3 text-success">
                        <i class="fa-solid fa-location-dot fa-xl"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Kantor Pusat</small>
                        <span class="fw-bold">Depan St. Padalarang, Padalarang, Kabupaten Bandung Barat, Jawa Barat</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 offset-lg-1">
            <div class="card border-0 shadow rounded-4 overflow-hidden bg-success text-white">
                <div class="card-body p-5 text-center">
                    <i class="fa-solid fa-headset fa-6x mb-4 text-white-50"></i>
                    <h4 class="fw-bold">Punya Pertanyaan Spesifik?</h4>
                    <p class="small text-white-50">Silakan hubungi kami untuk info privat trip, corporate gathering, atau kerjasama.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>