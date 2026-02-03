<?php
session_start();
require '../config/koneksi.php';

// --- TAMBAH REKENING ---
if (isset($_POST['btn_simpan'])) {
    $bank = $_POST['nama_bank'];
    $norek = $_POST['nomor_rekening'];
    $an = $_POST['atas_nama'];
    
    // Upload Logo
    $logo_baru = null;
    if (!empty($_FILES['logo']['name'])) {
        $foto = $_FILES['logo']['name'];
        $tmp = $_FILES['logo']['tmp_name'];
        $logo_baru = date('dmYHis') . '_' . $foto;
        move_uploaded_file($tmp, "../uploads/" . $logo_baru);
    }

    try {
        $sql = "INSERT INTO rekening (nama_bank, nomor_rekening, atas_nama, logo_bank) VALUES (?, ?, ?, ?)";
        $conn->prepare($sql)->execute([$bank, $norek, $an, $logo_baru]);
        header("Location: ../admin/rekening.php?pesan=sukses");
    } catch (PDOException $e) { die($e->getMessage()); }
}

// --- UPDATE REKENING ---
elseif (isset($_POST['btn_update'])) {
    $id = $_POST['id_rekening'];
    $bank = $_POST['nama_bank'];
    $norek = $_POST['nomor_rekening'];
    $an = $_POST['atas_nama'];
    $logo_lama = $_POST['logo_lama'];
    
    $logo_final = $logo_lama;
    
    // Cek Ganti Logo
    if (!empty($_FILES['logo']['name'])) {
        $foto = $_FILES['logo']['name'];
        $tmp = $_FILES['logo']['tmp_name'];
        $logo_final = date('dmYHis') . '_' . $foto;
        move_uploaded_file($tmp, "../uploads/" . $logo_final);
        
        // Hapus logo lama
        if($logo_lama && file_exists("../uploads/" . $logo_lama)) {
            unlink("../uploads/" . $logo_lama);
        }
    }

    try {
        $sql = "UPDATE rekening SET nama_bank=?, nomor_rekening=?, atas_nama=?, logo_bank=? WHERE id_rekening=?";
        $conn->prepare($sql)->execute([$bank, $norek, $an, $logo_final, $id]);
        header("Location: ../admin/rekening.php?pesan=update");
    } catch (PDOException $e) { die($e->getMessage()); }
}

// --- HAPUS REKENING ---
elseif (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Hapus file logo dulu
    $stmt = $conn->prepare("SELECT logo_bank FROM rekening WHERE id_rekening = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
    
    if($data['logo_bank'] && file_exists("../uploads/" . $data['logo_bank'])) {
        unlink("../uploads/" . $data['logo_bank']);
    }
    
    $conn->prepare("DELETE FROM rekening WHERE id_rekening=?")->execute([$id]);
    header("Location: ../admin/rekening.php?pesan=hapus");
}
?>