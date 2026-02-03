<?php
session_start();
require '../config/koneksi.php';

// --- LOGIKA LOGIN  ---
if (isset($_POST['btn_login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama']    = $user['nama_lengkap'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['status']  = "login";

            switch ($user['role']) {
                case 'admin':   header("Location: ../admin/index.php"); break;
                case 'user':    header("Location: ../user/index.php"); break;
                case 'penjaga': header("Location: ../penjaga/index.php"); break;
            }
            exit();
        } else {
            header("Location: ../login.php?pesan=gagal");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: ../login.php?pesan=gagal_sistem");
        exit();
    }
}

// --- LOGIKA REGISTER ---
elseif (isset($_POST['btn_register'])) {
    
    // 1. Sanitasi & Tangkap Input
    $nama     = htmlspecialchars(trim($_POST['nama_lengkap']));
    $email    = htmlspecialchars(trim($_POST['email']));
    $hp       = htmlspecialchars(trim($_POST['no_hp']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $role_default = 'user'; 

    // --- VALIDASI BACKEND (Satpam) ---
    
    // A. Cek Panjang Password
    if (strlen($password) < 6) {
        header("Location: ../register.php?pesan=password_pendek");
        exit();
    }

    // B. Cek No HP harus Angka
    if (!is_numeric($hp)) {
        header("Location: ../register.php?pesan=hp_bukan_angka");
        exit();
    }

    try {
        // 2. Cek Username Kembar
        $stmt_cek = $conn->prepare("SELECT id_user FROM users WHERE username = :username");
        $stmt_cek->execute([':username' => $username]);

        if ($stmt_cek->rowCount() > 0) {
            header("Location: ../register.php?pesan=gagal_username_ada");
            exit();
        }

        // 3. Insert Data
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, nama_lengkap, email, no_hp, role) 
                VALUES (:user, :pass, :nama, :email, :hp, :role)";
        
        $stmt = $conn->prepare($sql);
        $saved = $stmt->execute([
            ':user'  => $username,
            ':pass'  => $pass_hash,
            ':nama'  => $nama,
            ':email' => $email,
            ':hp'    => $hp,
            ':role'  => $role_default
        ]);

        if ($saved) {
            header("Location: ../login.php?pesan=sukses_daftar");
        }

    } catch (PDOException $e) {
        header("Location: ../register.php?pesan=gagal_sistem"); 
        exit();
    }
}

// Jika akses langsung
else {
    header("Location: ../login.php");
}
?>