<?php
session_start();
require '../config/koneksi.php';
include 'include/header.php';
include 'include/navbar.php';
?>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-success">Syarat & Ketentuan</h2>
                <p class="text-muted">Harap dibaca dengan seksama sebelum melakukan pemesanan.</p>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-contract text-warning me-2"></i> Umum</h5>
                    <ul class="text-secondary mb-4">
                        <li class="mb-2">Peserta dianggap sehat jasmani dan rohani serta tidak memiliki riwayat penyakit yang membahayakan pendakian.</li>
                        <li class="mb-2">Peserta wajib membawa kartu identitas (KTP/SIM) asli saat hari keberangkatan.</li>
                        <li class="mb-2">Pihak Jerry OpenTrip berhak membatalkan trip sewaktu-waktu jika terjadi *Force Majeure* (Bencana alam, penutupan jalur, dll).</li>
                    </ul>

                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-money-bill-wave text-success me-2"></i> Pembayaran & Booking</h5>
                    <ul class="text-secondary mb-4">
                        <li class="mb-2">Booking dianggap sah jika peserta telah melunasi pembayaran dan mengunggah bukti transfer.</li>
                        <li class="mb-2">Batas waktu pelunasan (Booking) ditutup pada <strong>H-10</strong> sebelum keberangkatan.</li>
                        <li class="mb-2">Harga yang tertera sudah termasuk fasilitas yang disebutkan di halaman detail trip.</li>
                    </ul>

                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-ban text-danger me-2"></i> Pembatalan & Refund</h5>
                    <ul class="text-secondary mb-4">
                        <li class="mb-2">Peserta dapat membatalkan trip dan mendapatkan refund 100% jika pembatalan dilakukan maksimal <strong>H-14</strong> sebelum keberangkatan.</li>
                        <li class="mb-2">Pembatalan yang dilakukan di bawah H-14 tidak mendapatkan refund (uang hangus), namun slot bisa dipindahtangankan ke orang lain (ganti nama).</li>
                        <li class="mb-2">Jika trip dibatalkan oleh pihak Jerry OpenTrip (bukan karena *Force Majeure*), uang peserta akan dikembalikan 100%.</li>
                    </ul>

                    <div class="alert alert-success border-0 rounded-3 mt-4 text-center">
                        <small class="fw-bold">Dengan mendaftar trip, peserta dianggap telah menyetujui semua ketentuan di atas.</small>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>