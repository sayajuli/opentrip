<?php
session_start();
if (isset($_SESSION['status'])) { header("Location: index.php"); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Jerry OpenTRIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background-color: var(--color-bg);">

    <div class="min-vh-100 d-flex align-items-center justify-content-center p-4">
        <div class="card card-trip shadow-lg border-0 rounded-4" style="width: 100%; max-width: 500px;">
            <div class="card-body p-4 p-md-5">
                
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-success"><i class="fa-solid fa-user-plus"></i></h2>
                    <h4 class="fw-bold" style="color: var(--color-secondary);">Buat Akun Baru</h4>
                </div>

                <form action="api/auth.php" method="POST">
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control rounded-3" name="nama_lengkap" placeholder="Nama" required>
                        <label>Nama Lengkap</label>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control rounded-3" name="email" placeholder="Email" required>
                                <label>Email</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control rounded-3" 
                                       name="no_hp" 
                                       placeholder="HP" 
                                       required 
                                       inputmode="numeric"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <label>No HP/WA (Angka)</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control rounded-3" name="username" placeholder="Username" required>
                        <label>Username</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control rounded-3" name="password" placeholder="Password" required minlength="6">
                        <label>Password (Min. 6 Karakter)</label>
                    </div>

                    <button type="submit" name="btn_register" class="btn btn-alam w-100 py-2 fs-5 fw-bold rounded-pill mb-3">
                        Daftar Sekarang
                    </button>

                    <div class="text-center">
                        <span class="text-muted small">Sudah punya akun? </span>
                        <a href="login.php" class="text-success fw-bold text-decoration-none">Login</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if(isset($_GET['pesan'])) { ?>
        <script>
            // 1. Password Pendek
            <?php if($_GET['pesan'] == 'password_pendek') { ?>
                Swal.fire({icon: 'warning', title: 'Password Lemah', text: 'Password minimal harus 6 karakter ya bang!', confirmButtonColor: '#8B4513'});
            
            // 2. Username Sudah Ada
            <?php } elseif($_GET['pesan'] == 'gagal_username_ada') { ?>
                Swal.fire({icon: 'warning', title: 'Oops!', text: 'Username sudah dipakai orang lain.', confirmButtonColor: '#8B4513'});

            // 3. Error Database / Sistem (Pengganti JSON Error)
            <?php } elseif($_GET['pesan'] == 'gagal_sistem') { ?>
                Swal.fire({icon: 'error', title: 'Terjadi Kesalahan', text: 'Sistem sedang sibuk atau ada masalah koneksi database.', confirmButtonColor: '#d33'});
            
            // 4. Default Error
            <?php } elseif($_GET['pesan'] == 'gagal') { ?>
                Swal.fire({icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat mendaftar.', confirmButtonColor: '#8B4513'});
            <?php } ?>
        </script>
    <?php } ?>

</body>
</html>