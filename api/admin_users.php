<?php
session_start();
require '../config/koneksi.php';

// --- TAMBAH USER ---
if (isset($_POST['btn_simpan'])) {
    $nama = $_POST['nama'];
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $email = $_POST['email'];
    $hp = $_POST['hp'];
    $role = $_POST['role'];

    // Cek Username
    $cek = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
    $cek->execute([$user]);
    if($cek->rowCount() > 0){
        header("Location: ../admin/users.php?pesan=gagal");
        exit;
    }

    try {
        $sql = "INSERT INTO users (username, password, nama_lengkap, email, no_hp, role) VALUES (?, ?, ?, ?, ?, ?)";
        $conn->prepare($sql)->execute([$user, $pass, $nama, $email, $hp, $role]);
        header("Location: ../admin/users.php?pesan=sukses");
    } catch (PDOException $e) { die($e->getMessage()); }
}

// --- UPDATE USER ---
elseif (isset($_POST['btn_update'])) {
    $id = $_POST['id_user'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $hp = $_POST['hp'];
    $role = $_POST['role'];
    
    // Logic Password
    if(!empty($_POST['password'])) {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama_lengkap=?, email=?, no_hp=?, role=?, password=? WHERE id_user=?";
        $params = [$nama, $email, $hp, $role, $pass, $id];
    } else {
        $sql = "UPDATE users SET nama_lengkap=?, email=?, no_hp=?, role=? WHERE id_user=?";
        $params = [$nama, $email, $hp, $role, $id];
    }

    try {
        $conn->prepare($sql)->execute($params);
        header("Location: ../admin/users.php?pesan=update");
    } catch (PDOException $e) { die($e->getMessage()); }
}

// --- HAPUS USER ---
elseif (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Validasi: Jangan hapus diri sendiri (Session Admin)
    if($id == $_SESSION['id_user']) {
        header("Location: ../admin/users.php?pesan=gagal_hapus_diri");
        exit;
    }
    
    $conn->prepare("DELETE FROM users WHERE id_user=?")->execute([$id]);
    header("Location: ../admin/users.php?pesan=hapus");
}
?>