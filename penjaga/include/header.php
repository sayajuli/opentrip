<?php
// Cek Sesi Penjaga
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'penjaga') {
    header("Location: ../login.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Guide - Jerry OpenTrip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fa-solid fa-person-hiking me-2"></i> Guide Panel
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navGuide">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navGuide">
            <ul class="navbar-nav ms-auto text-center">
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php"><i class="fa-solid fa-list-check me-1"></i> Jadwal Tugas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="mb-4"></div>