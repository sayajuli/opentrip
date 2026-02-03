<footer class="bg-white pt-5 pb-3 border-top mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5 class="fw-bold text-success mb-3"><i class="fa-solid fa-mountain-sun"></i> JerryOpenTrip</h5>
                <p class="text-secondary small">
                    Platform open trip terpercaya untuk menjelajahi keindahan alam Indonesia.
                    Aman, nyaman, dan penuh pengalaman baru.
                </p>
            </div>
            <div class="col-lg-2 col-6 mb-4">
                <h6 class="fw-bold mb-3">Menu</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="index.php" class="text-decoration-none text-secondary">Dashboard</a></li>
                    <li class="mb-2"><a href="trips.php" class="text-decoration-none text-secondary">Cari Trip</a></li>
                    <li class="mb-2"><a href="history.php" class="text-decoration-none text-secondary">Riwayat</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6 mb-4">
                <h6 class="fw-bold mb-3">Bantuan</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="bantuan_syarat.php" class="text-decoration-none text-secondary">Syarat &
                            Ketentuan</a></li>
                    <li class="mb-2"><a href="bantuan_cara.php" class="text-decoration-none text-secondary">Cara
                            Booking</a></li>
                    <li class="mb-2"><a href="bantuan_kontak.php" class="text-decoration-none text-secondary">Hubungi
                            Kami</a></li>
                </ul>
            </div>
            <div class="col-lg-4 mb-4">
                <h6 class="fw-bold mb-3">Hubungi Kami</h6>
                <p class="small text-secondary mb-1"><i class="fa-brands fa-whatsapp me-2"></i> +62 812-3456-7890</p>
                <p class="small text-secondary mb-1"><i class="fa-regular fa-envelope me-2"></i> help@jerryopentrip.com
                </p>
                <p class="small text-secondary"><i class="fa-solid fa-map-pin me-2"></i> Padalarang, Indonesia</p>
            </div>
        </div>
        <hr>
        <div class="text-center small text-secondary">
            &copy; <?= date('Y'); ?> Jerry OpenTrip. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="../assets/js/script.js"></script>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const pesan = urlParams.get('pesan');

    if (pesan == 'sukses_upload') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Upload!',
            text: 'Bukti pembayaran sedang diverifikasi admin.',
            confirmButtonColor: '#198754'
        });
    } else if (pesan == 'sukses_profil') {
        Swal.fire({
            icon: 'success',
            title: 'Profil Diupdate',
            text: 'Data diri kamu berhasil diperbarui.',
            confirmButtonColor: '#198754'
        });
    }
</script>
</body>

</html>