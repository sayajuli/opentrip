<?php
session_start();
require '../config/koneksi.php';

// --- LOGIKA SIMPAN DATA BARU ---
if (isset($_POST['btn_simpan'])) {
    $nama   = $_POST['nama_gunung'];
    $lokasi = $_POST['lokasi'];
    $desc   = $_POST['deskripsi'];
    
    // Upload Gambar
    $foto      = $_FILES['gambar']['name'];
    $tmp_foto  = $_FILES['gambar']['tmp_name'];
    $foto_baru = date('dmYHis') . '_' . $foto;
    $path      = "../uploads/" . $foto_baru;

    if (move_uploaded_file($tmp_foto, $path)) {
        try {
            // SQL INSERT
            $sql = "INSERT INTO gunung (nama_gunung, deskripsi, lokasi, gambar) 
                    VALUES (:nama, :desc, :lokasi, :gambar)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nama'   => $nama, 
                ':desc'   => $desc, 
                ':lokasi' => $lokasi, 
                ':gambar' => $foto_baru
            ]);
            
            header("Location: ../admin/gunung.php?pesan=sukses");
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}

// --- LOGIKA UPDATE DATA ---
elseif (isset($_POST['btn_update'])) {
    $id     = $_POST['id_gunung'];
    $nama   = $_POST['nama_gunung'];
    $lokasi = $_POST['lokasi'];
    $desc   = $_POST['deskripsi'];
    $gambar_lama = $_POST['gambar_lama'];
    
    // Cek apakah user upload gambar baru?
    if ($_FILES['gambar']['error'] === 4) {
        $foto_final = $gambar_lama;
    } else {
        $foto      = $_FILES['gambar']['name'];
        $tmp_foto  = $_FILES['gambar']['tmp_name'];
        $foto_final = date('dmYHis') . '_' . $foto;
        $path      = "../uploads/" . $foto_final;
        
        move_uploaded_file($tmp_foto, $path);
        
        if(file_exists("../uploads/" . $gambar_lama)) {
            unlink("../uploads/" . $gambar_lama);
        }
    }

    try {
        // SQL UPDATE
        $sql = "UPDATE gunung SET 
                nama_gunung = :nama, 
                deskripsi = :desc, 
                lokasi = :lokasi, 
                gambar = :gambar 
                WHERE id_gunung = :id";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([
            ':nama'   => $nama, 
            ':desc'   => $desc, 
            ':lokasi' => $lokasi, 
            ':gambar' => $foto_final,
            ':id'     => $id
        ]);
        
        header("Location: ../admin/gunung.php?pesan=update");
    } catch (PDOException $e) {
        die("Error Update: " . $e->getMessage());
    }
}

// --- LOGIKA HAPUS DATA ---
elseif (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Ambil info gambar dulu buat dihapus filenya
    $cek = $conn->prepare("SELECT gambar FROM gunung WHERE id_gunung = ?");
    $cek->execute([$id]);
    $data = $cek->fetch();
    
    if($data && file_exists("../uploads/" . $data['gambar'])) {
        unlink("../uploads/" . $data['gambar']);
    }

    $del = $conn->prepare("DELETE FROM gunung WHERE id_gunung = ?");
    $del->execute([$id]);
    header("Location: ../admin/gunung.php?pesan=hapus");
}
?>