<?php
// 1. Panggil Library FPDF (Mundur 1 folder karena kita di admin)
require('../fpdf/fpdf.php');
require('../config/koneksi.php');

// 2. Ambil ID Jadwal
if (!isset($_GET['id'])) {
    header("Location: jadwal.php");
    exit;
}
$id = $_GET['id'];

// 3. Query Data Jadwal (Header Laporan)
$sql_jadwal = "SELECT jadwal.*, gunung.nama_gunung, users.nama_lengkap as guide 
               FROM jadwal 
               JOIN gunung ON jadwal.id_gunung = gunung.id_gunung
               LEFT JOIN users ON jadwal.id_penjaga = users.id_user 
               WHERE jadwal.id_jadwal = ?";
$stmt = $conn->prepare($sql_jadwal);
$stmt->execute([$id]);
$d = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$d) die("Data tidak ditemukan");

// 4. Query Data Peserta (Isi Tabel)
$sql_peserta = "SELECT transaksi.*, users.nama_lengkap, users.no_hp 
                FROM transaksi 
                JOIN users ON transaksi.id_user = users.id_user 
                WHERE transaksi.id_jadwal = ? 
                ORDER BY users.nama_lengkap ASC";
$stmt_p = $conn->prepare($sql_peserta);
$stmt_p->execute([$id]);

// --- MULAI GENERATE PDF ---

class PDF extends FPDF
{
    // Header Halaman (Logo & Judul)
    function Header()
    {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'JERRY OPENTRIP - MANIFEST PESERTA',0,1,'C');
        
        $this->SetFont('Arial','',10);
        $this->Cell(0,5,'Laporan Data Peserta & Status Pembayaran',0,1,'C');
        
        $this->Ln(5);
        $this->Cell(0,1,'','B',1,'C'); // Garis bawah header
        $this->Ln(5);
    }

    // Footer Halaman (Nomor Halaman)
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Init PDF (A4 Portrait)
$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// BAGIAN 1: INFO TRIP
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40, 6, 'Nama Gunung', 0, 0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->Cell(100, 6, $d['nama_gunung'], 0, 1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(40, 6, 'Tanggal Trip', 0, 0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->Cell(100, 6, date('d-m-Y', strtotime($d['tanggal_berangkat'])) . ' s/d ' . date('d-m-Y', strtotime($d['tanggal_selesai'])), 0, 1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(40, 6, 'Guide / Penjaga', 0, 0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->Cell(100, 6, ($d['guide'] ? $d['guide'] : 'Belum Ditentukan'), 0, 1);

$pdf->Ln(5); // Spasi

// BAGIAN 2: HEADER TABEL
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(230, 230, 230); // Warna abu-abu buat header tabel

// Lebar Kolom: No(10), Kode(30), Nama(60), HP(35), Pax(15), Status(35)
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Kode Booking', 1, 0, 'C', true);
$pdf->Cell(60, 8, 'Nama Peserta', 1, 0, 'L', true);
$pdf->Cell(35, 8, 'No. HP', 1, 0, 'C', true);
$pdf->Cell(15, 8, 'Pax', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Status Bayar', 1, 1, 'C', true);

// BAGIAN 3: ISI TABEL (LOOPING)
$pdf->SetFont('Arial','',9);
$no = 1;

if($stmt_p->rowCount() > 0) {
    while($row = $stmt_p->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell(10, 8, $no++, 1, 0, 'C');
        $pdf->Cell(30, 8, '#'.$row['kode_booking'], 1, 0, 'C');
        $pdf->Cell(60, 8, substr($row['nama_lengkap'], 0, 30), 1, 0, 'L'); // Batasi panjang nama biar ga berantakan
        $pdf->Cell(35, 8, $row['no_hp'], 1, 0, 'C');
        $pdf->Cell(15, 8, $row['jumlah_peserta'], 1, 0, 'C');
        
        // Status Bayar
        $status = ucfirst($row['status_bayar']); // Biar huruf depan besar
        $pdf->Cell(35, 8, $status, 1, 1, 'C');
    }
} else {
    $pdf->Cell(185, 8, 'Belum ada peserta terdaftar.', 1, 1, 'C');
}

// BAGIAN 4: Tanda Tangan (Opsional)
$pdf->Ln(20);
$pdf->SetFont('Arial','',10);

// Geser ke kanan buat TTD
$pdf->Cell(120); 
$pdf->Cell(60, 5, 'Dicetak pada: ' . date('d-m-Y H:i'), 0, 1, 'C');
$pdf->Cell(120); 
$pdf->Cell(60, 5, 'Admin Jerry OpenTRIP', 0, 1, 'C');
$pdf->Ln(20); // Spasi buat tanda tangan
$pdf->Cell(120); 
$pdf->Cell(60, 5, '( ..................................... )', 0, 1, 'C');

// Output PDF ke Browser
$pdf->Output();
?>