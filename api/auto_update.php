<?php
// Pastikan file ini dipanggil setelah koneksi database tersedia

if (isset($conn)) {
    try {
        // 1. UPDATE STATUS 'SELESAI' (Jika tanggal SUDAH LEWAT)
        $sql_expired = "UPDATE jadwal 
                        SET status_trip = 'selesai' 
                        WHERE tanggal_berangkat < CURDATE() 
                        AND status_trip IN ('buka', 'tutup')";
        $conn->exec($sql_expired);

        // 2. [BARU] UPDATE STATUS 'TUTUP' (Jika HARI INI BERANGKAT)
        $sql_today = "UPDATE jadwal 
                      SET status_trip = 'tutup' 
                      WHERE tanggal_berangkat = CURDATE() 
                      AND status_trip = 'buka'";
        $conn->exec($sql_today);

        // 3. UPDATE STATUS 'TUTUP' (Jika Kuota Penuh)
        $sql_full = "UPDATE jadwal j 
                     SET status_trip = 'tutup' 
                     WHERE status_trip = 'buka' 
                     AND (
                        SELECT COALESCE(SUM(jumlah_peserta), 0) 
                        FROM transaksi t 
                        WHERE t.id_jadwal = j.id_jadwal 
                        AND t.status_bayar = 'lunas'
                     ) >= j.kuota_maks";
        $conn->exec($sql_full);

        // 3. (Opsional) BUKA LAGI KALO ADA YG BATAL
        $sql_reopen = "UPDATE jadwal j 
                       SET status_trip = 'buka' 
                       WHERE status_trip = 'tutup' 
                       AND tanggal_berangkat > CURDATE()
                       AND (
                            SELECT COALESCE(SUM(jumlah_peserta), 0) 
                            FROM transaksi t 
                            WHERE t.id_jadwal = j.id_jadwal 
                            AND t.status_bayar = 'lunas'
                       ) < j.kuota_maks";
        $conn->exec($sql_reopen);

    } catch (PDOException $e) {
    }
}
?>