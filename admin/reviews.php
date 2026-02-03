<?php 
$pageTitle = "Kelola Testimoni";
include 'include/header.php'; 
include 'include/sidebar.php'; 
require '../config/koneksi.php';
?>

<div class="content">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-star text-warning"></i> Testimoni Pengguna</h3>

    <div class="row">
        <?php 
        $sql = "SELECT reviews.*, users.nama_lengkap, users.username, gunung.nama_gunung 
                FROM reviews 
                JOIN users ON reviews.id_user = users.id_user 
                JOIN gunung ON reviews.id_gunung = gunung.id_gunung 
                ORDER BY reviews.id_review DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
        ?>
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-0"><?= $row['nama_lengkap']; ?> <small class="text-muted">(@<?= $row['username']; ?>)</small></h6>
                            <small class="text-success"><i class="fa-solid fa-mountain"></i> Trip <?= $row['nama_gunung']; ?></small>
                        </div>
                        <div class="text-warning small">
                            <?php for($i=0; $i<$row['rating']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                        </div>
                    </div>
                    
                    <p class="bg-light p-3 rounded-3 fst-italic text-muted small">
                        "<?= $row['komentar']; ?>"
                    </p>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-secondary"><?= date('d M Y', strtotime($row['tanggal_review'])); ?></small>
                        
                        <a href="../api/admin_review.php?hapus=<?= $row['id_review']; ?>" 
                           class="btn btn-sm btn-outline-danger rounded-pill"
                           onclick="return confirm('Hapus review ini? Tindakan tidak bisa dibatalkan.')">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($stmt->rowCount() == 0): ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa-regular fa-comment-dots fa-3x mb-3"></i><br>
                Belum ada review masuk.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'hapus') { ?>
    <script>Swal.fire('Terhapus', 'Review berhasil dihapus.', 'success');</script>
<?php } ?>

<?php include 'include/footer.php'; ?>