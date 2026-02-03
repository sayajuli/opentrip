<?php
require('../fpdf/fpdf.php');
require('../config/koneksi.php');

// Tangkap Filter
$tipe   = isset($_GET['tipe']) ? $_GET['tipe'] : 'transaksi';
$tgl_a  = isset($_GET['tgl_a']) ? $_GET['tgl_a'] : date('Y-m-01');
$tgl_b  = isset($_GET['tgl_b']) ? $_GET['tgl_b'] : date('Y-m-d');

// LOGIC QUERY (Sama persis kayak di frontend, copas aja querynya)
switch ($tipe) {
    case 'transaksi':
        $judul = "LAPORAN KEUANGAN & TRANSAKSI";
        $sql = "SELECT t.*, u.nama_lengkap 
                FROM transaksi t JOIN users u ON t.id_user = u.id_user 
                WHERE DATE(t.tanggal_booking) BETWEEN '$tgl_a' AND '$tgl_b' 
                ORDER BY t.tanggal_booking ASC";
        break;
    case 'jadwal':
        $judul = "LAPORAN JADWAL OPEN TRIP";
        $sql = "SELECT j.*, g.nama_gunung, u.nama_lengkap as guide 
                FROM jadwal j JOIN gunung g ON j.id_gunung = g.id_gunung 
                LEFT JOIN users u ON j.id_penjaga = u.id_user 
                WHERE j.tanggal_berangkat BETWEEN '$tgl_a' AND '$tgl_b' ORDER BY j.tanggal_berangkat ASC";
        break;
    case 'users':
        $judul = "LAPORAN DATA PENGGUNA";
        $sql = "SELECT * FROM users WHERE DATE(created_at) BETWEEN '$tgl_a' AND '$tgl_b'";
        break;
    case 'reviews':
        $judul = "LAPORAN TESTIMONI PENGGUNA";
        $sql = "SELECT r.*, u.nama_lengkap, g.nama_gunung FROM reviews r 
                JOIN users u ON r.id_user = u.id_user JOIN gunung g ON r.id_gunung = g.id_gunung 
                WHERE DATE(r.tanggal_review) BETWEEN '$tgl_a' AND '$tgl_b'";
        break;
    case 'gunung':
        $judul = "DATA MASTER GUNUNG";
        $sql = "SELECT * FROM gunung ORDER BY nama_gunung ASC";
        $tgl_a = null; // Gak pake tanggal
        break;
}

// SETUP PDF
class PDF extends FPDF {
    function Header() {
        global $judul, $tgl_a, $tgl_b;
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'JERRY OPENTRIP - ADMIN PANEL',0,1,'C');
        $this->SetFont('Arial','B',12);
        $this->Cell(0,10, $judul,0,1,'C');
        
        if($tgl_a) {
            $this->SetFont('Arial','I',10);
            $this->Cell(0,5,'Periode: '.date('d-m-Y', strtotime($tgl_a)).' s/d '.date('d-m-Y', strtotime($tgl_b)),0,1,'C');
        }
        $this->Ln(5);
        $this->Line(10, 35, 200, 35);
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo(),0,0,'C');
    }
}

$pdf = new PDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

$stmt = $conn->query($sql);

// --- RENDER TABEL SESUAI TIPE ---

if($tipe == 'transaksi') {
    // Header
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(230,230,230);
    $pdf->Cell(10,8,'No',1,0,'C',true);
    $pdf->Cell(30,8,'Tgl Booking',1,0,'C',true);
    $pdf->Cell(35,8,'Invoice',1,0,'C',true);
    $pdf->Cell(50,8,'Nama Pemesan',1,0,'L',true);
    $pdf->Cell(30,8,'Status',1,0,'C',true);
    $pdf->Cell(35,8,'Total (Rp)',1,1,'R',true);
    
    // Body
    $pdf->SetFont('Arial','',9);
    $no=1; $total=0;
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
        $pdf->Cell(10,8,$no++,1,0,'C');
        $pdf->Cell(30,8,date('d/m/Y',strtotime($r['tanggal_booking'])),1,0,'C');
        $pdf->Cell(35,8,'#'.$r['kode_booking'],1,0,'C');
        $pdf->Cell(50,8,substr($r['nama_lengkap'],0,25),1,0,'L');
        $pdf->Cell(30,8,ucfirst($r['status_bayar']),1,0,'C');
        $pdf->Cell(35,8,number_format($r['total_bayar']),1,1,'R');
        $total += $r['total_bayar'];
    }
    // Total
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(155,8,'TOTAL PENDAPATAN',1,0,'R');
    $pdf->Cell(35,8,'Rp '.number_format($total),1,1,'R');
}

elseif($tipe == 'jadwal') {
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(230,230,230);
    $pdf->Cell(10,8,'No',1,0,'C',true);
    $pdf->Cell(50,8,'Tujuan',1,0,'L',true);
    $pdf->Cell(30,8,'Berangkat',1,0,'C',true);
    $pdf->Cell(40,8,'Guide',1,0,'L',true);
    $pdf->Cell(30,8,'Status',1,0,'C',true);
    $pdf->Cell(30,8,'Harga',1,1,'R',true);
    
    $pdf->SetFont('Arial','',9);
    $no=1; 
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
        $pdf->Cell(10,8,$no++,1,0,'C');
        $pdf->Cell(50,8,$r['nama_gunung'],1,0,'L');
        $pdf->Cell(30,8,date('d/m/Y',strtotime($r['tanggal_berangkat'])),1,0,'C');
        $pdf->Cell(40,8,$r['guide']?:'-',1,0,'L');
        $pdf->Cell(30,8,strtoupper($r['status_trip']),1,0,'C');
        $pdf->Cell(30,8,number_format($r['harga']),1,1,'R');
    }
}

elseif($tipe == 'users') {
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(230,230,230);
    $pdf->Cell(10,8,'No',1,0,'C',true);
    $pdf->Cell(50,8,'Nama Lengkap',1,0,'L',true);
    $pdf->Cell(35,8,'Username',1,0,'L',true);
    $pdf->Cell(30,8,'Role',1,0,'C',true);
    $pdf->Cell(35,8,'No HP',1,0,'C',true);
    $pdf->Cell(30,8,'Join Date',1,1,'C',true);
    
    $pdf->SetFont('Arial','',9);
    $no=1; 
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
        $pdf->Cell(10,8,$no++,1,0,'C');
        $pdf->Cell(50,8,substr($r['nama_lengkap'],0,25),1,0,'L');
        $pdf->Cell(35,8,$r['username'],1,0,'L');
        $pdf->Cell(30,8,strtoupper($r['role']),1,0,'C');
        $pdf->Cell(35,8,$r['no_hp'],1,0,'C');
        $pdf->Cell(30,8,date('d/m/Y',strtotime($r['created_at'])),1,1,'C');
    }
}

elseif($tipe == 'gunung') {
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(230,230,230);
    $pdf->Cell(10,8,'No',1,0,'C',true);
    $pdf->Cell(50,8,'Nama Gunung',1,0,'L',true);
    $pdf->Cell(40,8,'Lokasi',1,0,'L',true);
    $pdf->Cell(90,8,'Deskripsi Singkat',1,1,'L',true);
    
    $pdf->SetFont('Arial','',9);
    $no=1; 
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
        $pdf->Cell(10,8,$no++,1,0,'C');
        $pdf->Cell(50,8,$r['nama_gunung'],1,0,'L');
        $pdf->Cell(40,8,$r['lokasi'],1,0,'L');
        $pdf->Cell(90,8,substr($r['deskripsi'],0,50).'...',1,1,'L');
    }
}

elseif($tipe == 'reviews') {
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(10,8,'No',1,0,'C');
    $pdf->Cell(30,8,'Tanggal',1,0,'C');
    $pdf->Cell(40,8,'User',1,0,'L');
    $pdf->Cell(40,8,'Gunung',1,0,'L');
    $pdf->Cell(15,8,'Rate',1,0,'C');
    $pdf->Cell(55,8,'Komentar',1,1,'L');
    
    $pdf->SetFont('Arial','',9);
    $no=1;
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
        $pdf->Cell(10,8,$no++,1,0,'C');
        $pdf->Cell(30,8,date('d/m/Y',strtotime($r['tanggal_review'])),1,0,'C');
        $pdf->Cell(40,8,$r['nama_lengkap'],1,0,'L');
        $pdf->Cell(40,8,$r['nama_gunung'],1,0,'L');
        $pdf->Cell(15,8,$r['rating'],1,0,'C');
        $pdf->Cell(55,8,substr($r['komentar'],0,30).'...',1,1,'L');
    }
}

// TTD Admin
$pdf->Ln(15);
$pdf->SetFont('Arial','',10);
$pdf->Cell(130);
$pdf->Cell(60,5,'Dicetak oleh Admin,',0,1,'C');
$pdf->Ln(20);
$pdf->Cell(130);
$pdf->Cell(60,5,'( .................................. )',0,1,'C');

$pdf->Output();
?>