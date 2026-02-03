<?php
session_start();
require '../config/koneksi.php';

// Pastikan Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    try {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id_review = :id");
        $stmt->execute([':id' => $id]);
        
        header("Location: ../admin/reviews.php?pesan=hapus");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>