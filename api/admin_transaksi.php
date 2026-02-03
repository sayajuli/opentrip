<?php
session_start();
require '../config/koneksi.php';

// Pastikan Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("Akses Ditolak");
}

if (isset($_GET['act']) && isset($_GET['id'])) {
    $act = $_GET['act'];
    $id = $_GET['id'];

    try {
        if ($act == 'terima') {
            // Ubah jadi LUNAS
            $sql = "UPDATE transaksi SET status_bayar = 'lunas' WHERE id_transaksi = ?";
            $conn->prepare($sql)->execute([$id]);
            header("Location: ../admin/transaksi.php?status=menunggu_verifikasi&pesan=lunas");
        
        } elseif ($act == 'tolak') {
            // Ubah jadi TOLAK (User harus booking ulang / upload ulang nanti)
            $sql = "UPDATE transaksi SET status_bayar = 'tolak' WHERE id_transaksi = ?";
            $conn->prepare($sql)->execute([$id]);
            header("Location: ../admin/transaksi.php?status=menunggu_verifikasi&pesan=tolak");
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>