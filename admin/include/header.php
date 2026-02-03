<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php?pesan=belum_login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
      <?php 
        // Jika $pageTitle ada isinya, tampilkan. Jika tidak, tampilkan Default.
        if(isset($pageTitle)){
            echo $pageTitle . " | Jerry OpenTRIP";
        } else {
            echo "Admin | Jerry OpenTRIP";
        }
        ?>
    </title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        /* LOGIC RESPONSIVE SIDEBAR */
        
        /* Tampilan Desktop (Layar Besar) */
        @media (min-width: 992px) {
            .sidebar-admin {
                width: 260px;
                height: 100vh;
                position: fixed; /* Nempel kiri terus */
                top: 0;
                left: 0;
                z-index: 1000;
                overflow-y: auto;
            }
            .content {
                margin-left: 260px; /* Dorong konten ke kanan biar ga ketutupan sidebar */
                padding: 30px;
                min-height: 100vh;
                background-color: #f8f9fa;
            }
            .mobile-header {
                display: none; /* Umpetin tombol toggle di laptop */
            }
        }

        /* Tampilan Mobile (Layar Kecil) */
        @media (max-width: 991.98px) {
            .sidebar-admin {
                /* Biarkan Bootstrap Offcanvas yang ngatur (Hidden default) */
            }
            .content {
                margin-left: 0; /* Full width */
                padding: 20px;
                background-color: #f8f9fa;
            }
            .mobile-header {
                display: block; /* Munculin tombol toggle */
                background: white;
                padding: 10px 20px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                margin-bottom: 20px;
                border-radius: 10px;
            }
        }

        /* Styling Sidebar Warna */
        .sidebar-admin {
            background-color: var(--color-primary) !important; /* Hijau */
            color: white;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-admin a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-radius: 8px;
            margin-bottom: 5px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .sidebar-admin a:hover, .sidebar-admin a.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
            padding-left: 25px; /* Efek geser dikit */
        }
    </style>
</head>
<body>

<div class="p-3 d-lg-none">
    <div class="mobile-header d-flex justify-content-between align-items-center">
        <span class="fw-bold text-success"><i class="fa-solid fa-mountain-sun"></i> Admin Panel</span>
        <button class="btn btn-alam btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fa-solid fa-bars"></i> Menu
        </button>
    </div>
</div>