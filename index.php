<?php
include 'api/auto_update.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jerry OpenTRIP - Jelajah Alam Indonesia</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom fixed-top shadow-sm">
      <div class="container">
        <a class="navbar-brand text-success fw-bold" href="#">
            <i class="fa-solid fa-mountain-sun"></i> Jerry OpenTRIP
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link active" href="#home">Beranda</a></li>
            <li class="nav-item"><a class="nav-link" href="#jadwal">Open Trip</a></li>
            <li class="nav-item"><a class="nav-link" href="#testimoni">Testimoni</a></li>
            <li class="nav-item ms-0 ms-lg-2 mt-2 mt-lg-0">
                <a href="login.php" class="btn btn-outline-success rounded-pill px-4 fw-bold">Masuk</a>
            </li>
            <li class="nav-item ms-0 ms-lg-2 mt-2 mt-lg-0">
                <a href="register.php" class="btn btn-alam rounded-pill px-4 fw-bold">Daftar</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <header class="hero-parallax" id="home">
        <div class="container text-center">
            <h1 class="display-3 fw-bold text-white text-shadow">Jelajahi Keindahan Alam</h1>
            <p class="lead text-white mb-4 text-shadow">Temukan pengalaman mendaki terbaik bersama pemandu profesional.</p>
            <a href="#jadwal" class="btn btn-alam btn-lg shadow-lg">
                <i class="fa-solid fa-person-hiking"></i> Lihat Jadwal Trip
            </a>
        </div>
    </header>

    <section id="jadwal" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: var(--color-secondary);">Jadwal Open Trip Terdekat</h2>
                <div class="d-flex justify-content-center">
                    <div style="height: 4px; width: 60px; background-color: var(--color-primary); border-radius: 2px;"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card card-trip h-100">
                        <div style="height: 200px; background-color: #ddd; display: flex; align-items: center; justify-content: center;">
                            <span class="text-muted"><i class="fa-solid fa-image fa-2x"></i><br>Foto Gunung</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Gunung Semeru</h5>
                            <p class="text-muted small mb-2"><i class="fa-solid fa-calendar"></i> 20 Maret 2026</p>
                            <p class="card-text small text-secondary">Nikmati keindahan Ranu Kumbolo dan puncak Mahameru yang legendaris.</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-success">Rp 850.000</span>
                                <a href="login.php" class="btn btn-sm btn-alam rounded-pill px-3">Booking</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4 text-white" style="background-color: var(--color-secondary);">
        <div class="container text-center">
            <small>&copy; <?= date('Y'); ?> Jerry OpenTRIP. All Rights Reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>