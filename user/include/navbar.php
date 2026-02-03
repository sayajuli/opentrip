<nav class="navbar navbar-expand-lg navbar-user fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-success fs-4" href="index.php">
            <i class="fa-solid fa-mountain-sun"></i> Jerry<span class="text-dark">OpenTrip</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title fw-bold text-success">Menu User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link nav-link-user <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-user <?= basename($_SERVER['PHP_SELF']) == 'upcoming.php' ? 'active' : '' ?>" href="upcoming.php">Tiket Saya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-user <?= basename($_SERVER['PHP_SELF']) == 'trips.php' ? 'active' : '' ?>" href="trips.php">Cari Trip</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-user <?= basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active' : '' ?>" href="payment.php">Tagihan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-user <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>" href="history.php">Riwayat</a>
                    </li>
                </ul>

                <div class="dropdown mt-3 mt-lg-0">
                    <button class="btn btn-outline-success dropdown-toggle rounded-pill px-4" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-user-circle me-1"></i> <?= substr($_SESSION['nama'], 0, 10); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-start dropdown-menu-lg-end  border-0 shadow mt-2">
                        <li><a class="dropdown-item" href="profil.php"><i class="fa-solid fa-gear me-2"></i> Edit Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<div style="margin-top: 80px;"></div>