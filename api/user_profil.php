<?php
session_start();
require '../config/koneksi.php';

if (isset($_POST['btn_update'])) {
    $id    = $_SESSION['id_user'];
    $nama  = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $hp    = htmlspecialchars($_POST['hp']);
    $pass  = $_POST['password'];

    try {
        if (!empty($pass)) {
            // Update dengan Password Baru
            $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET nama_lengkap=?, email=?, no_hp=?, password=? WHERE id_user=?";
            $params = [$nama, $email, $hp, $pass_hash, $id];
        } else {
            // Update Data Saja (Tanpa Password)
            $sql = "UPDATE users SET nama_lengkap=?, email=?, no_hp=? WHERE id_user=?";
            $params = [$nama, $email, $hp, $id];
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        // --- UPDATE SESSION BIAR LANGSUNG BERUBAH ---
        // Ini penting biar nama di pojok kanan atas navbar langsung ganti
        $_SESSION['nama'] = $nama; 
        
        header("Location: ../user/profil.php?pesan=sukses_profil");

    } catch (PDOException $e) {
        // Balikin ke profil kalau error
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: ../user/index.php");
}
?>