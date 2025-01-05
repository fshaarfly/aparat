<?php
session_start(); // Pastikan sesi dimulai
include 'database.php';

if (isset($_POST['verify_otp'])) {
    $otp_input = mysqli_real_escape_string($db, $_POST['otp']);
    $nomor = $_SESSION['nomor']; // Ambil nomor WA dari sesi

    // Verifikasi OTP
    $query = "SELECT * FROM otp_register WHERE nomor = '$nomor' AND otp = '$otp_input'";
    $result = mysqli_query($db, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['verified_user'] = $nomor; // Simpan status verifikasi
        $delete_query = "DELETE FROM otp_register WHERE nomor = '$nomor'";
        mysqli_query($db, $delete_query);

        $username = $_SESSION['username'];
        $hash_password = $_SESSION['password'];
        $sql = "INSERT INTO users (nomor, username, password) VALUES 
        ('$nomor', '$username', '$hash_password')";

        if ($db->query($sql)) {
            $berhasil_script = "
          <script>
          Swal.fire({
              title: 'Register Berhasil',
              text: 'Anda akan diarahkan ke halaman login.',
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
        }
    } else {
        $gagal_script = "
        <script>
        Swal.fire({
            title: 'Kode OTP salah atau sudah kedaluwarsa!',
            text: 'Mohon Untuk Coba Lagi.',
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
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Aparat | Verifikasi OTP</title>
    <link rel="icon" type="image/x-icon" href="img/iconpolibatam.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="icon" type="image/x-icon" href="/img/iconpolibatam.png" />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css"
        rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
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
    <div
        class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row corner box-area">
            <div class="col-lg-6 d-flex justify-content-center">
                <div class="row align-items-center mx-2 mx-sm-5" style="margin: 7rem 0">
                    <div class="logo mb-2">
                        <img src="img/Logo Aparat-blue.png" style="width: 50%" />
                    </div>
                    <h3 class="mt-n4">
                        <a href="index.php" class="text-dark fw-bold">Masukkan Kode OTP Register Anda</a>
                    </h3>
                    <form action="" method="POST">
                        <div class="form-group mb-2">
                            <label class="text-dark fw-bold mb-2" for="otp">Kode OTP <span class="required">*</span></label>
                            <input
                                type="text"
                                id="otp"
                                name="otp"
                                placeholder="Masukkan kode OTP"
                                required
                                class="input-group" />
                        </div>
                        <button
                            class="mb-3"
                            type="submit"
                            name="verify_otp">
                            Verify
                        </button>
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
    <?php
    if (isset($gagal_script)) {
        echo $gagal_script;
    }
    if (isset($berhasil_script)) {
        echo $berhasil_script;
    }
    ?>
</body>

</html>