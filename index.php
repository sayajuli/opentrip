<?php
session_start();
require 'config/koneksi.php';

// Query Trip Terbaru
$sql_trip = "SELECT j.*, g.nama_gunung, g.gambar, g.lokasi 
             FROM jadwal j JOIN gunung g ON j.id_gunung = g.id_gunung 
             WHERE j.status_trip = 'buka' AND j.tanggal_berangkat > CURDATE() 
             ORDER BY j.tanggal_berangkat ASC LIMIT 3";
$stmt_trip = $conn->query($sql_trip);

// Query Review 
$sql_rev = "SELECT r.*, u.nama_lengkap, g.nama_gunung 
            FROM reviews r 
            JOIN users u ON r.id_user = u.id_user 
            JOIN gunung g ON r.id_gunung = g.id_gunung 
            ORDER BY r.rating DESC LIMIT 5";
$stmt_rev = $conn->query($sql_rev);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jerry OpenTrip - Jelajah Alam Indonesia</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; }

        /* NAVBAR TRANSPARAN */
        .navbar { transition: 0.4s; padding: 20px 0; }
        .navbar.scrolled { background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 10px 0; }
        .navbar.scrolled .nav-link { color: #333 !important; }
        .navbar.scrolled .navbar-brand { color: #198754 !important; }
        .navbar.scrolled .navbar-toggler {
            border-color: rgba(0,0,0,0.1);
        }
        .navbar.scrolled .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2833, 37, 41, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        /* HERO PARALLAX */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.3)), url('assets/img/bg.jpg');
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            color: white;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
        }

        /* SECTION PARALLAX INTERLUDE */
        .parallax-section {
            background: linear-gradient(rgba(25, 135, 84, 0.8), rgba(20, 108, 67, 0.8)), url('assets/img/bg-secondary.jpg');
            background-attachment: fixed;
            background-size: cover;
            padding: 100px 0;
            color: white;
        }

        .card-trip { transition: transform 0.3s; border: none; border-radius: 15px; overflow: hidden; }
        .card-trip:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        
        .btn-alam { background-color: #198754; color: white; border: none; padding: 10px 25px; border-radius: 50px; font-weight: 600; transition: 0.3s; }
        .btn-alam:hover { background-color: #146c43; color: white; transform: scale(1.05); }

        /* Testimonial Avatar */
        .testi-img { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 2px solid #198754; background: #eee; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="#">
                <i class="fa-solid fa-mountain-sun"></i> Jerry<span class="text-warning">OpenTrip</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link text-white active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#about">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#trip">Paket Trip</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#review">Kata Mereka</a></li>
                    
                    <li class="nav-item ms-lg-3">
                        <?php if(isset($_SESSION['status']) && $_SESSION['status'] == 'login'): ?>
                            <a href="user/index.php" class="btn btn-warning rounded-pill fw-bold px-4 text-dark">Dashboard</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-light rounded-pill fw-bold px-4 nav-link">Masuk / Daftar</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="home" class="hero-section">
        <div class="container text-center">
            <h5 class="text-warning fw-bold text-uppercase ls-2 mb-3">Open Trip Bandung Raya</h5>
            <h1 class="display-3 fw-bold mb-4">Jelajahi Atap Indonesia<br>Tanpa Ribet</h1>
            <p class="fs-5 mb-5 mx-auto w-75 d-none d-md-block">
                Start point fleksibel: Padalarang & Stasiun Bandung.<br> 
                Kami urus logistiknya, kamu nikmati pemandangannya.
            </p>
            <a href="#trip" class="btn btn-alam btn-lg shadow-lg">Cari Gunung Impianmu <i class="fa-solid fa-arrow-down ms-2"></i></a>
        </div>
    </section>

    <section id="about" class="py-5 bg-white">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=1000&auto=format&fit=crop" class="img-fluid rounded-4 shadow-lg" alt="Pendaki">
                </div>
                <div class="col-lg-6 ps-lg-5">
                    <h6 class="text-success fw-bold text-uppercase">Kenapa Kami?</h6>
                    <h2 class="fw-bold mb-4">Solusi Healing Warga Bandung Raya</h2>
                    <p class="text-secondary mb-4">
                        Jerry OpenTrip lahir di Padalarang untuk memfasilitasi kamu yang ingin mendaki tapi nggak punya temen atau males ribet urus alat. Kita kumpul bareng, naik bareng, senang bareng.
                    </p>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fa-solid fa-map-location-dot fa-2x text-warning me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Tikum Strategis</h6>
                                    <small class="text-muted">Stasiun Padalarang (KBP) & Stasiun Bandung.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fa-solid fa-utensils fa-2x text-warning me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Makan Terjamin</h6>
                                    <small class="text-muted">Logistik enak & bergizi selama pendakian.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fa-solid fa-user-shield fa-2x text-warning me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Guide Lokal</h6>
                                    <small class="text-muted">Dipandu akamsi yang hafal jalur & ramah.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <i class="fa-solid fa-camera fa-2x text-warning me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Dokumentasi</h6>
                                    <small class="text-muted">Pulang bawa stok foto estetik buat feeds.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="parallax-section text-center">
        <div class="container">
            <i class="fa-solid fa-quote-left fa-3x mb-3 opacity-50"></i>
            <h2 class="fw-light fst-italic">"Gunung tidak hanya menantang fisikmu, tapi juga meruntuhkan egomu."</h2>
            <p class="mt-3 opacity-75">- Jerry OpenTrip Squad</p>
        </div>
    </section>

    <section id="trip" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h6 class="text-success fw-bold text-uppercase">Jadwal Terdekat</h6>
                <h2 class="fw-bold">Siapkan Ranselmu!</h2>
            </div>

            <div class="row g-4">
                <?php if($stmt_trip->rowCount() > 0): ?>
                    <?php while($row = $stmt_trip->fetch(PDO::FETCH_ASSOC)) { ?>
                    <div class="col-md-4">
                        <div class="card card-trip h-100 shadow-sm">
                            <div class="position-relative">
                                <img src="uploads/<?= $row['gambar']; ?>" class="card-img-top object-fit-cover" style="height: 250px;">
                                <div class="position-absolute top-0 end-0 m-3 badge bg-white text-success shadow">
                                    <i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($row['tanggal_berangkat'])); ?>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="fw-bold"><?= $row['nama_gunung']; ?></h5>
                                <p class="text-muted small"><i class="fa-solid fa-map-pin text-danger"></i> <?= $row['lokasi']; ?></p>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="d-block text-muted">Mulai dari</small>
                                        <span class="text-success fw-bold fs-5">Rp <?= number_format($row['harga']); ?></span>
                                    </div>
                                    <a href="detail.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-outline-success rounded-pill">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted">
                        <p>Belum ada jadwal open trip saat ini. Tunggu update selanjutnya!</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="text-center mt-5">
                <a href="user/trips.php" class="btn btn-alam btn-lg shadow">Lihat Semua Jadwal <i class="fa-solid fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </section>

    <section id="review" class="py-5 bg-white">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-3">Apa Kata<br><span class="text-success">Alumni Trip?</span></h2>
                    <p class="text-muted mb-4">Ratusan pendaki dari Bandung Raya sudah membuktikan serunya nanjak bareng Jerry OpenTrip.</p>
                    <a href="user/trips.php" class="btn btn-outline-dark rounded-pill">Lihat Trip Selesai</a>
                </div>
                <div class="col-lg-8">
                    <div class="d-flex overflow-auto gap-4 pb-4 px-1" style="scrollbar-width: thin;">
                        <?php if($stmt_rev->rowCount() > 0): ?>
                            <?php while($rev = $stmt_rev->fetch(PDO::FETCH_ASSOC)) { ?>
                            <div class="card border-0 shadow-sm bg-light p-4 flex-shrink-0" style="width: 320px; border-radius: 20px;">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" class="testi-img me-3">
                                    <div>
                                        <h6 class="fw-bold mb-0"><?= substr($rev['nama_lengkap'], 0, 15); ?></h6>
                                        <small class="text-muted" style="font-size: 11px;">Trip <?= $rev['nama_gunung']; ?></small>
                                    </div>
                                </div>
                                <div class="text-warning mb-2">
                                    <?= str_repeat('<i class="fa-solid fa-star"></i>', $rev['rating']); ?>
                                </div>
                                <p class="small text-secondary fst-italic">"<?= substr($rev['komentar'], 0, 100); ?>..."</p>
                                
                                <?php if(!empty($rev['foto'])): ?>
                                    <img src="uploads/review/<?= $rev['foto']; ?>" class="rounded mt-2 w-100 object-fit-cover" style="height: 120px;">
                                <?php endif; ?>
                            </div>
                            <?php } ?>
                        <?php else: ?>
                            <div class="text-muted p-4">Belum ada review. Jadilah yang pertama!</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="fw-bold text-success mb-3">JerryOpenTrip</h4>
                    <p class="text-white-50">Partner pendakian terbaik di Bandung Raya. Aman, Nyaman, dan Menyenangkan.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white fs-5"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="text-white fs-5"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="#" class="text-white fs-5"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">Titik Kumpul (Tikum)</h5>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><i class="fa-solid fa-train me-2"></i> Stasiun Padalarang (KBP)</li>
                        <li class="mb-2"><i class="fa-solid fa-train-subway me-2"></i> Stasiun Bandung (Pintu Utara)</li>
                        <li class="mb-2"><i class="fa-solid fa-location-dot me-2"></i> Kota Baru Parahyangan</li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">Kontak</h5>
                    <p class="text-white-50 mb-1">WA: 0812-3456-7890</p>
                    <p class="text-white-50">Email: halo@jerryopentrip.com</p>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center small text-white-50">
                &copy; <?= date('Y'); ?> Jerry OpenTrip. All Rights Reserved.
            </div>
        </div>
    </footer>

    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
                // Ganti warna link jadi gelap
                document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('text-white'));
            } else {
                navbar.classList.remove('scrolled');
                // Balikin warna link jadi putih
                document.querySelectorAll('.nav-link').forEach(el => el.classList.add('text-white'));
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>