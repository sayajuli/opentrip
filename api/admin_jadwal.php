<?php
session_start();
require '../config/koneksi.php';

// --- TAMBAH JADWAL BARU ---
if (isset($_POST['btn_simpan'])) {
    $id_gunung = $_POST['id_gunung'];
    $id_penjaga = !empty($_POST['id_penjaga']) ? $_POST['id_penjaga'] : NULL;
    $tgl_b = $_POST['tgl_berangkat'];
    $tgl_s = $_POST['tgl_selesai'];
    
    // Bersihin Harga
    $harga = str_replace('.', '', $_POST['harga']);
    $kuota = $_POST['kuota'];
    $deskripsi = $_POST['deskripsi'];

    try {
        $sql = "INSERT INTO jadwal (id_gunung, id_penjaga, tanggal_berangkat, tanggal_selesai, harga, kuota_maks, deskripsi, status_trip) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'buka')";
        $conn->prepare($sql)->execute([$id_gunung, $id_penjaga, $tgl_b, $tgl_s, $harga, $kuota, $deskripsi]);
        header("Location: ../admin/jadwal.php?pesan=sukses");
    } catch (PDOException $e) { die($e->getMessage()); }
}

// --- UPDATE JADWAL ---
elseif (isset($_POST['btn_update'])) {
    $id = $_POST['id_jadwal'];
    $id_penjaga = !empty($_POST['id_penjaga']) ? $_POST['id_penjaga'] : NULL;
    $tgl_b = $_POST['tgl_berangkat'];
    $tgl_s = $_POST['tgl_selesai'];
    $harga = str_replace('.', '', $_POST['harga']);
    $kuota = $_POST['kuota'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status_trip'];

    try {
        $sql = "UPDATE jadwal SET id_penjaga=?, tanggal_berangkat=?, tanggal_selesai=?, harga=?, kuota_maks=?, deskripsi=?, status_trip=? WHERE id_jadwal=?";
        $conn->prepare($sql)->execute([$id_penjaga, $tgl_b, $tgl_s, $harga, $kuota, $deskripsi, $status, $id]);
        header("Location: ../admin/jadwal.php?pesan=update");
    } catch (PDOException $e) { die($e->getMessage()); }
}

// --- BATALKAN TRIP (Update Status & Alasan) ---
elseif (isset($_GET['batal'])) {
    $id = $_GET['batal'];
    $alasan = $_GET['alasan']; // Diambil dari input SweetAlert

    try {
        $sql = "UPDATE jadwal SET status_trip='batal', alasan_batal=? WHERE id_jadwal=?";
        $conn->prepare($sql)->execute([$alasan, $id]);
        
        // TODO Nanti: Kalau ada user yg udah bayar, bisa kirim notif/refund disini
        
        header("Location: ../admin/jadwal.php?pesan=dibatal");
    } catch (PDOException $e) { die($e->getMessage()); }
}
?>