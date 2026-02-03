<?php
session_start();
require '../config/koneksi.php';

if (isset($_POST['btn_upload'])) {
    $id = $_POST['id_transaksi'];
    
    // Validasi file
    $foto      = $_FILES['bukti']['name'];
    $tmp_foto  = $_FILES['bukti']['tmp_name'];
    $error     = $_FILES['bukti']['error'];

    if ($error === 0) {
        $ext = pathinfo($foto, PATHINFO_EXTENSION);
        $foto_baru = "T_" . date('YmdHis') . "_" . rand(100,999) . "." . $ext;
        
        $path = "../uploads/bukti/";
        if (!is_dir($path)) mkdir($path, 0777, true);

        if (move_uploaded_file($tmp_foto, $path . $foto_baru)) {
            
            $sql = "UPDATE transaksi SET bukti_bayar = ?, status_bayar = 'menunggu_verifikasi' WHERE id_transaksi = ?";
            $conn->prepare($sql)->execute([$foto_baru, $id]);
            
            header("Location: ../user/index.php?pesan=sukses_upload");
        }
    } else {
        echo "<script>alert('Gagal upload gambar!'); history.back();</script>";
    }
}
?>