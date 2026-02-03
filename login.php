<?php
session_start();
// Redirect Logic
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    if ($_SESSION['role'] == 'admin') header("Location: admin/index.php");
    else if ($_SESSION['role'] == 'user') header("Location: user/index.php");
    else if ($_SESSION['role'] == 'penjaga') header("Location: penjaga/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Jerry OpenTRIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background-color: var(--color-bg);">

    <div class="min-vh-100 d-flex align-items-center justify-content-center p-4">
        <div class="card card-trip shadow-lg border-0 rounded-4" style="width: 100%; max-width: 400px;">
            <div class="card-body p-4 p-md-5">
                
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-success"><i class="fa-solid fa-mountain-sun"></i></h2>
                    <h4 class="fw-bold" style="color: var(--color-secondary);">Selamat Datang</h4>
                    <p class="text-muted small">Login untuk melanjutkan</p>
                </div>

                <form action="api/auth.php" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control rounded-3" id="floatingInput" name="username" placeholder="Username" required autocomplete="off">
                        <label for="floatingInput">Username</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control rounded-3" id="floatingPassword" name="password" placeholder="Password" required>
                        <label for="floatingPassword">Password</label>
                    </div>

                    <button type="submit" name="btn_login" class="btn btn-alam w-100 py-2 fs-5 fw-bold rounded-pill mb-3">
                        Masuk <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>

                    <div class="text-center">
                        <span class="text-muted small">Belum punya akun? </span>
                        <a href="register.php" class="text-success fw-bold text-decoration-none">Daftar</a>
                    </div>
                    
                    <div class="text-center mt-3 border-top pt-3">
                        <a href="index.php" class="text-secondary small text-decoration-none">
                            <i class="fa-solid fa-house"></i> Kembali ke Home
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if(isset($_GET['pesan'])) { ?>
        <script>
            <?php if($_GET['pesan'] == 'gagal') { ?>
                Swal.fire({icon: 'error', title: 'Login Gagal', text: 'Username atau Password salah!', confirmButtonColor: '#8B4513'});
            <?php } elseif($_GET['pesan'] == 'sukses_daftar') { ?>
                Swal.fire({icon: 'success', title: 'Berhasil Daftar!', text: 'Silakan login sekarang.', confirmButtonColor: '#2E8B57'});
            <?php } ?>
        </script>
    <?php } ?>

</body>
</html>