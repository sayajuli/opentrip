<?php
// Pastikan path ke fpdf.php dan koneksi.php sudah benar
require('../fpdf/fpdf.php'); 
require('../config/koneksi.php');

// 1. Tangkap Filter
$tipe   = isset($_GET['tipe']) ? $_GET['tipe'] : 'transaksi';
$tgl_a  = isset($_GET['tgl_a']) ? $_GET['tgl_a'] : date('Y-m-01');
$tgl_b  = isset($_GET['tgl_b']) ? $_GET['tgl_b'] : date('Y-m-d');

// 2. Setup Judul & Header Kolom
$title = "";
$header = [];
$width = []; // Lebar masing-masing kolom

switch ($tipe) {
    case 'transaksi':
        $title = 'LAPORAN PENDAPATAN (LUNAS)'; // Judul diperjelas
        $header = ['No', 'Tanggal', 'Invoice', 'Pemesan', 'Status', 'Nominal'];
        $width  = [10, 30, 45, 50, 25, 30];
        
        // QUERY KHUSUS TRANSAKSI (HANYA LUNAS)
        $sql = "SELECT t.*, u.nama_lengkap 
                FROM transaksi t 
                JOIN users u ON t.id_user = u.id_user 
                WHERE (DATE(t.tanggal_booking) BETWEEN '$tgl_a' AND '$tgl_b')
                AND t.status_bayar = 'lunas' 
                ORDER BY t.tanggal_booking DESC";
        break;

    case 'jadwal':
        $title = 'LAPORAN JADWAL TRIP';
        $header = ['No', 'Gunung', 'Tgl Naik', 'Guide', 'Status', 'Harga'];
        $width  = [10, 40, 30, 40, 30, 40];
        $sql = "SELECT j.*, g.nama_gunung, u.nama_lengkap as guide 
                FROM jadwal j
                JOIN gunung g ON j.id_gunung = g.id_gunung
                LEFT JOIN users u ON j.id_penjaga = u.id_user
                WHERE j.tanggal_berangkat BETWEEN '$tgl_a' AND '$tgl_b'
                ORDER BY j.tanggal_berangkat DESC";
        break;

    case 'users':
        $title = 'LAPORAN DATA PESERTA';
        $header = ['No', 'Nama Lengkap', 'Username', 'Role', 'No HP', 'Join Date'];
        $width  = [10, 50, 30, 25, 35, 40];
        $sql = "SELECT * FROM users WHERE DATE(created_at) BETWEEN '$tgl_a' AND '$tgl_b' ORDER BY nama_lengkap ASC";
        break;

    case 'gunung':
        $title = 'DATA MASTER GUNUNG';
        $header = ['No', 'Nama Gunung', 'Lokasi', 'Harga Default', 'Ket'];
        $width  = [10, 50, 50, 40, 40];
        $sql = "SELECT * FROM gunung ORDER BY nama_gunung ASC";
        break;
        
    case 'reviews':
        $title = 'LAPORAN FEEDBACK USER';
        $header = ['No', 'Tanggal', 'User', 'Gunung', 'Rating', 'Komentar'];
        $width  = [10, 25, 35, 35, 20, 65];
        $sql = "SELECT r.*, u.nama_lengkap, g.nama_gunung 
                FROM reviews r
                JOIN users u ON r.id_user = u.id_user
                JOIN gunung g ON r.id_gunung = g.id_gunung
                WHERE DATE(r.tanggal_review) BETWEEN '$tgl_a' AND '$tgl_b'";
        break;
}

// 3. Eksekusi Query
$stmt = $conn->prepare($sql);
$stmt->execute();

// 4. Buat PDF Class Custom
class PDF extends FPDF {
    // Header Halaman
    function Header() {
        global $title, $tgl_a, $tgl_b;
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'JERRY OPEN TRIP - PUSAT LAPORAN',0,1,'C');
        $this->SetFont('Arial','B',12);
        $this->Cell(0,10, $title,0,1,'C');
        $this->SetFont('Arial','I',10);
        $this->Cell(0,5,'Periode: '.date('d-m-Y', strtotime($tgl_a)).' s/d '.date('d-m-Y', strtotime($tgl_b)),0,1,'C');
        $this->Ln(5);
        $this->Line(10, 35, 200, 35); // Garis bawah header
        $this->Ln(5);
    }

    // Footer Halaman
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().' | Dicetak pada: '.date('d-m-Y H:i'),0,0,'C');
    }
}

// 5. Render PDF
$pdf = new PDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(230,230,230); 

// Render Header Tabel
for($i=0; $i<count($header); $i++) {
    $pdf->Cell($width[$i], 10, $header[$i], 1, 0, 'C', true);
}
$pdf->Ln();

// Render Isi Tabel
$pdf->SetFont('Arial','',10);
$no = 1;
$total_pendapatan = 0;

if($stmt->rowCount() > 0) {
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell($width[0], 10, $no++, 1, 0, 'C');

        if($tipe == 'transaksi') {
            $total_pendapatan += $row['total_bayar'];
            $pdf->Cell($width[1], 10, date('d/m/y', strtotime($row['tanggal_booking'])), 1, 0, 'C');
            $pdf->Cell($width[2], 10, '#'.$row['kode_booking'], 1, 0, 'L');
            $pdf->Cell($width[3], 10, substr($row['nama_lengkap'], 0, 20), 1, 0, 'L');
            $pdf->Cell($width[4], 10, strtoupper($row['status_bayar']), 1, 0, 'C');
            $pdf->Cell($width[5], 10, number_format($row['total_bayar']), 1, 0, 'R');
        } 
        elseif($tipe == 'jadwal') {
            $pdf->Cell($width[1], 10, substr($row['nama_gunung'],0,20), 1, 0, 'L');
            $pdf->Cell($width[2], 10, date('d/m/y', strtotime($row['tanggal_berangkat'])), 1, 0, 'C');
            $pdf->Cell($width[3], 10, $row['guide'] ?: '-', 1, 0, 'L');
            $pdf->Cell($width[4], 10, strtoupper($row['status_trip']), 1, 0, 'C');
            $pdf->Cell($width[5], 10, number_format($row['harga']), 1, 0, 'R');
        }
        elseif($tipe == 'users') {
            $pdf->Cell($width[1], 10, substr($row['nama_lengkap'],0,25), 1, 0, 'L');
            $pdf->Cell($width[2], 10, $row['username'], 1, 0, 'L');
            $pdf->Cell($width[3], 10, strtoupper($row['role']), 1, 0, 'C');
            $pdf->Cell($width[4], 10, $row['no_hp'], 1, 0, 'C');
            $pdf->Cell($width[5], 10, date('d/m/Y', strtotime($row['created_at'])), 1, 0, 'C');
        }
        elseif($tipe == 'gunung') {
            $pdf->Cell($width[1], 10, $row['nama_gunung'], 1, 0, 'L');
            $pdf->Cell($width[2], 10, substr($row['lokasi'],0,20), 1, 0, 'L');
            $pdf->Cell($width[3], 10, number_format($row['harga']), 1, 0, 'R');
            $pdf->Cell($width[4], 10, '-', 1, 0, 'C');
        }
        elseif($tipe == 'reviews') {
            $pdf->Cell($width[1], 10, date('d/m/y', strtotime($row['tanggal_review'])), 1, 0, 'C');
            $pdf->Cell($width[2], 10, substr($row['nama_lengkap'],0,15), 1, 0, 'L');
            $pdf->Cell($width[3], 10, substr($row['nama_gunung'],0,15), 1, 0, 'L');
            $pdf->Cell($width[4], 10, $row['rating'].' Bintang', 1, 0, 'C');
            $pdf->Cell($width[5], 10, substr($row['komentar'],0,30).'...', 1, 0, 'L');
        }

        $pdf->Ln();
    }
} else {
    $pdf->Cell(array_sum($width), 10, 'Tidak ada data pada periode ini.', 1, 1, 'C');
}

// 6. Total Pendapatan (Hanya Muncul di Tipe Transaksi)
if($tipe == 'transaksi') {
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell($width[0]+$width[1]+$width[2]+$width[3]+$width[4], 10, 'TOTAL PENDAPATAN BERSIH', 1, 0, 'R');
    $pdf->Cell($width[5], 10, 'Rp '.number_format($total_pendapatan), 1, 1, 'R');
}

$pdf->Output();
?>