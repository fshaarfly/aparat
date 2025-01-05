<?php
include 'database.php';
session_start();

if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
  if ($_SESSION['role'] == 1) {
    header('Location: dashboard_dosen.php');
    exit;
  } elseif ($_SESSION['role'] == 0) {
    header('Location: dashboard.php');
    exit;
  }
}

if (isset($_POST['register'])) {
  $nomor = mysqli_real_escape_string($db,  $_POST['nomor']);
  $username = mysqli_real_escape_string($db,  $_POST['username']);
  $password = mysqli_real_escape_string($db,  $_POST['password']);
  $hash_password = hash("sha256", $password);

  // Cek apakah username atau nomor sudah terdaftar
  $query_check = "SELECT * FROM users WHERE nomor = '$nomor' OR username = '$username'";
  $result_check = mysqli_query($db, $query_check);

  if (mysqli_num_rows($result_check) > 0) {
    // Jika nomor atau username sudah terdaftar
    $gagal_script = "
      <script>
        Swal.fire({
          title: 'Register Gagal',
          text: 'Username atau Nomor WA sudah terdaftar.',
          icon: 'error',
          confirmButtonText: 'OK',
          customClass: {
            confirmButton: 'my-confirm-button'
          }
        });
      </script>
    ";
  } else {
    try {
      $_SESSION['nomor'] = $nomor;
      $_SESSION['username'] = $username;
      $_SESSION['password'] = $hash_password;

      // Generate OTP
      $otp = rand(100000, 999999);
      $time = time();

      // Simpan OTP ke database
      $query_otp = "INSERT INTO otp_register (nomor, otp, waktu) VALUES ('$nomor', '$otp', '$time')";
      mysqli_query($db, $query_otp);

      // Kirim OTP menggunakan Fonnte
      $curl = curl_init();
      $data = [
        'target' => $nomor,
        'message' => implode(PHP_EOL, [
          "Kode OTP Register Anda: $otp",
          "Jangan berikan kode ini pada siapapun."
        ]),
      ];
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: VADszheBGjj9RcrbXdpb"));
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
      curl_setopt($curl, CURLOPT_URL, "https://api.fonnte.com/send");
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
      curl_exec($curl);
      curl_close($curl);

      // Redirect ke halaman verify_otp_register
      header("Location: verify_otp_register.php");
      exit();
    } catch (Exception $e) {
      echo "
        <script>
          Swal.fire({
            title: 'Register Gagal',
            text: 'Terjadi kesalahan. Silakan coba lagi.',
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
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Aparat | Register</title>
  <link rel="icon" type="image/x-icon" href="img/iconpolibatam.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
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
        <div class="row align-items-center mx-2 mx-sm-5 my-4">
          <div class="logo mb-2">
            <img src="img\Logo Aparat-blue.png" style="width: 50%" />
          </div>
          <h1 class="mt-n4">
            <a href="register.php" class="text-dark fw-bold">Sign In.</a>
          </h1>
          <form action="register.php" method="POST" id="registerform">
            <div class="form-group mb-2">
              <label class="text-dark fw-bold mb-2" for="username">Nomor WA <span class="required">*</span></label>
              <input
                type="text"
                id="nomor"
                name="nomor"
                placeholder="Contoh: 08123567890"
                required=""
                class="input-group" />
            </div>
            <div class="form-group mb-2">
              <label class="text-dark fw-bold mb-2" for="username">Username <span class="required">*</span></label>
              <input
                type="text"
                id="username"
                name="username"
                placeholder="Masukkan username"
                required=""
                class="input-group" />
            </div>
            <div class="form-group mb-2">
              <label class="text-dark fw-bold mb-2" for="password">Password <span class="required">*</span></label>
              <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                required=""
                class="input-group mb-n2" />
              <span class="password-toggle-icon-reg"><i
                  class="fas fa-eye-slash"
                  id="togglePassword"
                  style="color: #808080"></i></span>
            </div>
            <button
              class="mb-3"
              type="submit"
              name="register">
              Sign In
            </button>
            <p class="text-dark text-center" style="font-size: 12px">
              Sudah Punya Akun?
              <a class="daftardisini" href="index.php">Login disini</a>
            </p>
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
    const passwordField = document.getElementById("password");
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

    const form = document.getElementById("registerform");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");

    form.addEventListener("submit", function(event) {
      let errorMessages = [];

      // Validasi Username
      if (usernameInput.value.length < 5) {
        errorMessages.push("Username harus minimal 5 karakter.");
      }

      // Validasi Password
      if (passwordInput.value.length < 8) {
        errorMessages.push("Password harus minimal 8 karakter.");
      }

      // Tampilkan SweetAlert Jika Ada Error
      if (errorMessages.length > 0) {
        event.preventDefault(); // Cegah pengiriman form
        Swal.fire({
          icon: "error",
          title: "Register Gagal",
          html: errorMessages.join("<br>"), // Gabungkan pesan error dengan baris baru
          confirmButtonText: "OK",
          customClass: {
            confirmButton: 'my-confirm-button'
          }

        });
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