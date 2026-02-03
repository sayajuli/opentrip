<div class="sidebar-admin offcanvas-lg offcanvas-start" tabindex="-1" id="sidebarMenu">
    
    <div class="offcanvas-header justify-content-between">
        <h5 class="offcanvas-title fw-bold">Menu Admin</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body d-block p-3">
        <div class="text-center mb-4 mt-2">
            <h4 class="fw-bold"><i class="fa-solid fa-mountain-sun"></i> Jerry Trip</h4>
            <small class="text-white-50">Administrator</small>
        </div>

        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge w-25 text-center"></i> Dashboard
        </a>
        
        <hr class="text-white-50 my-3">
        <small class="text-white-50 px-3 text-uppercase fw-bold" style="font-size: 11px;">Master Data</small>
        
        <a href="gunung.php" class="<?= basename($_SERVER['PHP_SELF']) == 'gunung.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-mountain w-25 text-center"></i> Data Gunung
        </a>
        <a href="rekening.php" class="<?= basename($_SERVER['PHP_SELF']) == 'rekening.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-credit-card w-25 text-center"></i> Rekening
        </a>
        <a href="users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-users w-25 text-center"></i> Data User
        </a>

        <hr class="text-white-50 my-3">
        <small class="text-white-50 px-3 text-uppercase fw-bold" style="font-size: 11px;">Transaksi</small>

        <a href="jadwal.php" class="<?= basename($_SERVER['PHP_SELF']) == 'jadwal.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-calendar-days w-25 text-center"></i> Atur Jadwal
        </a>
        <a href="transaksi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'transaksi.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice-dollar w-25 text-center"></i> Audit Payment
        </a>

        <hr class="text-white-50 my-3">
        <small class="text-white-50 px-3 text-uppercase fw-bold" style="font-size: 11px;">Laporan & Feedback</small>

        <a href="history.php" class="<?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-clock-rotate-left w-25 text-center"></i> History Trip
        </a>
        
        <a href="reviews.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-star w-25 text-center"></i> Testimoni User
        </a>

        <a href="laporan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-print w-25 text-center"></i> Laporan
        </a>

        <div class="mt-5 pt-4 border-top border-secondary">
            <a href="../logout.php" class="text-warning fw-bold">
                <i class="fa-solid fa-right-from-bracket w-25 text-center"></i> Keluar
            </a>
        </div>
    </div>
</div>

<div class="content-admin">