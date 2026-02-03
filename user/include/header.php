<?php
session_start();
// --- VALIDASI LOGIN (SATPAM) ---
// Cek apakah belum login ATAU role-nya bukan user?
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'user') {
    // Kalau belum, tendang balik ke halaman login
    header("Location: ../login.php?pesan=belum_login");
    exit; // PENTING: Stop eksekusi script di bawahnya
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jerry OpenTrip - Jelajahi Alam Indonesia</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* SETUP FONT GLOBAL */
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f8f9fa; /* Abu sangat muda biar gak sakit mata */
        }

        /* TOMBOL ALAM (Hijau Khas Jerry Trip) */
        .btn-alam { 
            background-color: #198754; 
            color: white; 
            border: none; 
            transition: all 0.3s;
        }
        .btn-alam:hover { 
            background-color: #146c43; 
            color: white; 
            transform: translateY(-2px); 
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
        }

        /* EFEK HOVER CARD */
        .card-hover {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        }

        /* WARNA TEXT KHUSUS */
        .text-alam { color: #198754; }
    </style>
</head>
<body>

<?php
// AUTO UPDATE STATUS
// Setiap user buka halaman, sistem cek apakah ada trip yg udah lewat tanggal / penuh
if(file_exists('../api/auto_update.php')) {
    include '../api/auto_update.php';
}
?>