<?php
session_start();
// Pastikan Login Penjaga
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'penjaga') {
    exit("Akses Ditolak");
}

require('../fpdf/fpdf.php'); 
require('../config/koneksi.php');

$id_jadwal = $_GET['id'];
$id_guide  = $_SESSION['id_user'];

// 1. Query Info Trip (Secure Check)
$sql = "SELECT j.*, g.nama_gunung 
        FROM jadwal j JOIN gunung g ON j.id_gunung = g.id_gunung 
        WHERE j.id_jadwal = ? AND j.id_penjaga = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_jadwal, $id_guide]);
$d = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$d) die("Data tidak ditemukan atau Anda bukan guide trip ini.");

// 2. Query Peserta
$sql_p = "SELECT t.*, u.nama_lengkap, u.no_hp 
          FROM transaksi t JOIN users u ON t.id_user = u.id_user 
          WHERE t.id_jadwal = ? AND t.status_bayar = 'lunas' 
          ORDER BY u.nama_lengkap ASC";
$stmt_p = $conn->prepare($sql_p);
$stmt_p->execute([$id_jadwal]);

// --- GENERATE PDF ---
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'MANIFEST PESERTA - JERRY OPENTRIP',0,1,'C');
        $this->SetFont('Arial','I',10);
        $this->Cell(0,5,'Pegangan Guide Lapangan',0,1,'C');
        $this->Ln(5);
        $this->Line(10, 30, 200, 30);
        $this->Ln(5);
    }
}

$pdf = new PDF('P','mm','A4');
$pdf->AddPage();

// INFO TRIP
$pdf->SetFont('Arial','B',11);
$pdf->Cell(40,8,'Gunung',0,0);      $pdf->Cell(5,8,':',0,0); $pdf->SetFont('Arial','',11); $pdf->Cell(0,8, $d['nama_gunung'],0,1);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(40,8,'Tanggal Naik',0,0); $pdf->Cell(5,8,':',0,0); $pdf->SetFont('Arial','',11); $pdf->Cell(0,8, date('d M Y', strtotime($d['tanggal_berangkat'])),0,1);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(40,8,'Guide',0,0);        $pdf->Cell(5,8,':',0,0); $pdf->SetFont('Arial','',11); $pdf->Cell(0,8, $_SESSION['nama'],0,1);

$pdf->Ln(5);

// TABEL HEADER
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(10,10,'No',1,0,'C',true);
$pdf->Cell(70,10,'Nama Peserta',1,0,'L',true);
$pdf->Cell(40,10,'No. HP',1,0,'C',true);
$pdf->Cell(20,10,'Pax',1,0,'C',true);
$pdf->Cell(50,10,'Ket / Absen',1,1,'C',true);

// TABEL ISI
$pdf->SetFont('Arial','',10);
$no = 1;
$total = 0;

while($p = $stmt_p->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(10,10,$no++,1,0,'C');
    $pdf->Cell(70,10, substr($p['nama_lengkap'], 0, 35),1,0,'L');
    $pdf->Cell(40,10, $p['no_hp'],1,0,'C');
    $pdf->Cell(20,10, $p['jumlah_peserta'],1,0,'C');
    $pdf->Cell(50,10, '',1,1,'C'); 
    $total += $p['jumlah_peserta'];
}

// FOOTER TOTAL
$pdf->SetFont('Arial','B',10);
$pdf->Cell(120,10,'TOTAL PESERTA (PAX)',1,0,'R');
$pdf->Cell(20,10, $total,1,1,'C');

$pdf->Output();
?>