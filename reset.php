<?php
session_start();
include 'database.php';

// Pastikan halaman hanya dapat diakses setelah verifikasi OTP
if (!isset($_SESSION['verified_user'])) {
  // Jika sesi tidak ditemukan, arahkan ke halaman login atau OTP
  header("Location: forgot.php");
  exit();
}

if (isset($_POST['ubah'])) {
  // Tangkap data dari form
  $new_password = mysqli_real_escape_string($db, $_POST['reset']);
  $nomor = $_SESSION['verified_user']; // Ambil nomor WA dari sesi

  // Enkripsi kata sandi baru menggunakan SHA-256
  $hash_password = hash("sha256", $new_password);

  // Perbarui password di database
  $query_check = "SELECT password FROM users WHERE nomor = '$nomor'";
  $result = mysqli_query($db, $query_check);
  $user = mysqli_fetch_assoc($result);

  if ($user && $user['password'] === $hash_password) {
    // Jika password baru sama dengan password lama, tampilkan alert
    $gagal_script = "
        <script>
        Swal.fire({
            title: 'Password baru tidak boleh sama dengan password lama.',
            icon: 'warning',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'my-confirm-button' 
            }
        });
        </script>
        ";
  } else {
    // Perbarui password di database
    $query = "UPDATE users SET password = '$hash_password' WHERE nomor = '$nomor'";
    if (mysqli_query($db, $query)) {
      // Hapus sesi untuk keamanan
      unset($_SESSION['verified_user']);
      $berhasil_script = "
            <script>
            Swal.fire({
                title: 'Password berhasil diperbarui.',
                text: 'Silakan login dengan password baru.',
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'my-confirm-button' 
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php';
                }
            });
            </script>
            ";
    } else {
      $gagal_script = "
            <script>
            Swal.fire({
                title: 'Terjadi kesalahan silahkan coba lagi.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'my-confirm-button' 
                }
            });
            </script>
            ";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Aparat | Reset Password</title>
  <link rel="icon" type="image/x-icon" href="img/iconpolibatam.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="icon" type="image/x-icon" href="/img/iconpolibatam.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
  <style>
    button {
      font-weight: 700;
      width: 100%;
      font-size: 15px;
      padding: 15px;
      background-color: #003298;
      color: white;
      border: 1px solid #003298;
      border-radius: 12px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background-color: #ffffff;
      border: 1px solid #003298;
      color: #003298;
    }
  </style>
</head>

<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row corner box-area">
      <div class="col-lg-6 d-flex justify-content-center">
        <div class="row align-items-center mx-2 mx-sm-5" style="margin: 7rem 0">
          <div class="logo mb-2">
            <img src="img/Logo Aparat-blue.png" style="width: 50%" />
          </div>
          <h4 class="mt-n4">
            <a href="index.php" class="text-dark fw-bold">Masukkan Password Baru</a>
          </h4>
          <form action="" method="POST">
            <div class="form-group mb-2">
              <label class="text-dark fw-bold mb-2" for="reset">New Password <span class="required">*</span></label>
              <input type="password" id="reset" name="reset" placeholder="Masukkan password baru" required class="input-group" />
              <span class="password-toggle-icon-pwbaru"><i
                  class="fas fa-eye-slash"
                  id="togglePassword"
                  style="color: #808080"></i></span>
            </div>
            <button class="mb-3" type="submit" name="ubah">Ubah Password</button>
          </form>
        </div>
      </div>

      <div class="col-lg-6 p-0 d-none d-lg-block">
        <img src="img/Login Page.png" class="img-fluid image" />
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    const passwordField = document.getElementById("reset");
    const togglePassword = document.getElementById("togglePassword");

    togglePassword.addEventListener("click", function() {
      if (passwordField.type === "password") {
        passwordField.type = "text";
        togglePassword.classList.remove("fa-eye-slash");
        togglePassword.classList.add("fa-eye");
      } else {
        passwordField.type = "password";
        togglePassword.classList.remove("fa-eye");
        togglePassword.classList.add("fa-eye-slash");
      }
    });
  </script>
  <?php
  if (isset($berhasil_script)) {
    echo $berhasil_script;
  }
  if (isset($gagal_script)) {
    echo $gagal_script;
  }
  ?>
</body>

</html>