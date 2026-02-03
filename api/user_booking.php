<?php
session_start();
require '../config/koneksi.php'; // Pastikan path koneksi bener

// 1. Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    header("Location: ../login.php");
    exit;
}

// 2. Cek Tombol Submit
if (isset($_POST['btn_booking'])) {
    $id_user   = $_SESSION['id_user'];
    $id_jadwal = $_POST['id_jadwal'];
    $jumlah    = (int) $_POST['jumlah_peserta']; // Pastikan integer
    $harga     = (int) $_POST['harga_satuan'];   // Pastikan integer
    
    // Hitung Total
    $total_bayar = $jumlah * $harga;

    // 3. Validasi Kuota (Cek lagi takutnya abis duluan)
    $stmt_cek = $conn->prepare("SELECT kuota_maks, 
                               (SELECT SUM(jumlah_peserta) FROM transaksi WHERE id_jadwal = ? AND status_bayar='lunas') as terisi 
                               FROM jadwal WHERE id_jadwal = ?");
    $stmt_cek->execute([$id_jadwal, $id_jadwal]);
    $d = $stmt_cek->fetch();
    
    $sisa = $d['kuota_maks'] - ($d['terisi'] ?? 0);

    if ($jumlah > $sisa) {
        echo "<script>alert('Yah, Kuota Habis atau Tidak Cukup!'); window.location='../detail.php?id=$id_jadwal';</script>";
        exit;
    }

    // 4. Generate Kode Booking (INV-TGL-RANDOM)
    $kode = "INV-" . date('Ymd') . "-" . rand(1000, 9999);

    try {
        // 5. Simpan ke Transaksi
        $sql = "INSERT INTO transaksi (kode_booking, id_user, id_jadwal, jumlah_peserta, total_bayar, status_bayar, tanggal_booking) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt_insert = $conn->prepare($sql);
        $stmt_insert->execute([$kode, $id_user, $id_jadwal, $jumlah, $total_bayar]);

        // Ambil ID Transaksi barusan
        $id_transaksi = $conn->lastInsertId();

        // 6. Redirect ke Halaman Bayar
        header("Location: ../user/bayar.php?id=$id_transaksi");

    } catch (PDOException $e) {
        die("Error Booking: " . $e->getMessage());
    }
} else {
    // Kalau akses langsung tanpa klik tombol
    header("Location: ../index.php");
}
?>