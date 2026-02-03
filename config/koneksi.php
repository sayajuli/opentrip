<?php 
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_jery_opentrip";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi Database Gagal. Hubungi Admin.");
}
?>