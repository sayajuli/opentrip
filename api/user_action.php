<?php
session_start();
require '../config/koneksi.php';

// Cek Login User
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'user') {
    exit("Akses Ditolak");
}

$act = isset($_GET['act']) ? $_GET['act'] : '';
$id_user = $_SESSION['id_user'];

// --- 1. LOGIC CANCEL TRIP (H-14) ---
if ($act == 'cancel') {
    $id_trx = $_POST['id_transaksi'];
    
    // Ambil Tanggal Trip
    $sql = "SELECT j.tanggal_berangkat 
            FROM transaksi t JOIN jadwal j ON t.id_jadwal = j.id_jadwal 
            WHERE t.id_transaksi = ? AND t.id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_trx, $id_user]);
    $d = $stmt->fetch();

    if ($d) {
        $start = new DateTime($d['tanggal_berangkat']);
        $now   = new DateTime();
        $diff  = $now->diff($start)->days;
        
        // Syarat: Tanggal belum lewat DAN Masih H-14 ke atas
        if ($start > $now && $diff >= 14) {
            // Update status jadi 'batal'
            $upd = $conn->prepare("UPDATE transaksi SET status_bayar = 'batal' WHERE id_transaksi = ?");
            $upd->execute([$id_trx]);
            header("Location: ../user/upcoming.php?pesan=sukses_batal");
        } else {
            echo "<script>alert('Gagal! Pembatalan hanya bisa dilakukan H-14 sebelum keberangkatan.'); window.location='../user/upcoming.php';</script>";
        }
    } else {
        header("Location: ../user/upcoming.php");
    }
}

// --- 2. LOGIC SIMPAN REVIEW (DENGAN FOTO) ---
elseif ($act == 'review') {
    // Tangkap Data
    $id_transaksi = $_POST['id_transaksi'];
    $id_gunung    = $_POST['id_gunung'];
    $rating       = $_POST['rating'];
    $komentar     = htmlspecialchars($_POST['komentar']);
    
    // Default foto null (kalau user gak upload)
    $nama_foto = null;

    // Cek apakah ada file foto yang diupload?
    if (!empty($_FILES['foto_review']['name'])) {
        $foto      = $_FILES['foto_review']['name'];
        $tmp_foto  = $_FILES['foto_review']['tmp_name'];
        $ext       = pathinfo($foto, PATHINFO_EXTENSION);
        
        // Validasi Ekstensi Gambar
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if(in_array(strtolower($ext), $allowed)) {
            
            $nama_foto = "REV_" . $id_user . "_" . date('YmdHis') . "." . $ext;
            $path      = "../uploads/review/";
    
            
            if (!is_dir($path)) mkdir($path, 0777, true);
    
            
            move_uploaded_file($tmp_foto, $path . $nama_foto);
        }
    }

    try {
        // Query Insert
        $sql = "INSERT INTO reviews (id_user, id_gunung, id_transaksi, rating, komentar, foto) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $conn->prepare($sql)->execute([
            $id_user, 
            $id_gunung, 
            $id_transaksi, 
            $rating, 
            $komentar, 
            $nama_foto
        ]);

        header("Location: ../user/history.php?pesan=sukses_review");
    } catch (PDOException $e) {
        die("Error Review: " . $e->getMessage());
    }
}
?>