<?php
// Pastikan path FPDF benar
require('../fpdf/fpdf.php'); 
require('../config/koneksi.php');

$id_jadwal = $_GET['id'];
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'semua';

// 1. Ambil Info Jadwal (Header)
$sql = "SELECT j.*, g.nama_gunung, u.nama_lengkap as guide 
        FROM jadwal j 
        JOIN gunung g ON j.id_gunung = g.id_gunung 
        LEFT JOIN users u ON j.id_penjaga = u.id_user 
        WHERE j.id_jadwal = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_jadwal]);
$d = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$d) die("Data tidak ditemukan");

// 2. Query Peserta (Filter Dinamis)
$sql_p = "SELECT t.*, u.nama_lengkap, u.no_hp 
          FROM transaksi t 
          JOIN users u ON t.id_user = u.id_user 
          WHERE t.id_jadwal = :id";

// Tambahkan Filter jika bukan 'semua'
if($status_filter != 'semua') {
    $sql_p .= " AND t.status_bayar = :status";
}

$sql_p .= " ORDER BY u.nama_lengkap ASC";

$stmt_p = $conn->prepare($sql_p);
$params = [':id' => $id_jadwal];
if($status_filter != 'semua') {
    $params[':status'] = $status_filter;
}
$stmt_p->execute($params);


// --- SETUP PDF ---
class PDF extends FPDF {
    function Header() {
        global $d, $status_filter;
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'MANIFEST PESERTA - JERRY OPEN TRIP',0,1,'C');
        
        $this->SetFont('Arial','I',10);
        // Tampilkan Info Filter di Judul PDF
        $info_filter = ($status_filter == 'semua') ? '(Semua Status)' : '(Status: '.strtoupper($status_filter).')';
        $this->Cell(0,5,'Gunung: '.$d['nama_gunung'].' | Tgl: '.date('d M Y', strtotime($d['tanggal_berangkat'])).' '.$info_filter,0,1,'C');
        
        $this->Ln(5);
        $this->Line(10, 30, 200, 30);
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().' | Dicetak pada: '.date('d-m-Y H:i'),0,0,'C');
    }
}

$pdf = new PDF('P','mm','A4');
$pdf->AddPage();

// HEADER TABEL
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(10,10,'No',1,0,'C',true);
$pdf->Cell(60,10,'Nama Peserta',1,0,'L',true);
$pdf->Cell(35,10,'No. HP',1,0,'C',true);
$pdf->Cell(20,10,'Pax',1,0,'C',true);
$pdf->Cell(30,10,'Status',1,0,'C',true);
$pdf->Cell(35,10,'Keterangan',1,1,'C',true); 

// ISI TABEL
$pdf->SetFont('Arial','',10);
$no = 1;
$total_pax = 0;

if($stmt_p->rowCount() > 0) {
    while($p = $stmt_p->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell(10,10,$no++,1,0,'C');
        $pdf->Cell(60,10, substr($p['nama_lengkap'], 0, 30),1,0,'L');
        $pdf->Cell(35,10, $p['no_hp'],1,0,'C');
        $pdf->Cell(20,10, $p['jumlah_peserta'],1,0,'C');
        
        // Status Bayar di PDF
        $status_text = strtoupper($p['status_bayar']);
        if($p['status_bayar'] == 'menunggu_verifikasi') $status_text = 'VERIFIKASI';
        $pdf->SetFont('Arial','',8); // Kecilin dikit font status
        $pdf->Cell(30,10, $status_text,1,0,'C');
        $pdf->SetFont('Arial','',10); // Balikin font
        
        $pdf->Cell(35,10, '',1,1,'C'); // Kosongin buat paraf/checklist
        $total_pax += $p['jumlah_peserta'];
    }

    // FOOTER TOTAL
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(105,10,'TOTAL PESERTA (PAX)',1,0,'R');
    $pdf->Cell(20,10, $total_pax,1,1,'C');
} else {
    $pdf->Cell(190,10,'Tidak ada data peserta untuk filter ini.',1,1,'C');
}

$pdf->Output();
?>