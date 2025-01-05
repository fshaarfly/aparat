<?php
session_start(); // Pastikan sesi dimulai
include 'database.php';

if (isset($_POST['submit_otp'])) {
  $nomor = mysqli_real_escape_string($db, $_POST['nomor']);
  $username = mysqli_real_escape_string($db, $_POST['username']);

  // Cek validitas nomor WA dan username
  $query = "SELECT * FROM users WHERE nomor = '$nomor' AND username = '$username'";
  $result = mysqli_query($db, $query);

  if (mysqli_num_rows($result) > 0) {
    // Simpan nomor WA ke dalam sesi
    $_SESSION['nomor'] = $nomor;

    // Generate OTP
    $otp = rand(100000, 999999);
    $time = time();

    // Simpan OTP ke database
    $query_otp = "INSERT INTO otp_lupa (nomor, otp, waktu) VALUES ('$nomor', '$otp', '$time')";
    mysqli_query($db, $query_otp);

    // Kirim OTP menggunakan Fonnte
    $curl = curl_init();
    $data = [
      'target' => $nomor,
      'message' => implode(PHP_EOL, [
        "Kode OTP Lupa Password Anda: $otp",
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

    // Redirect ke halaman verify_otp
    header("Location: verify_otp_lupa.php");
    exit();
  } else {
    $gagal_script = "
        <script>
        Swal.fire({
            title: 'Nomor WA atau Username tidak dapat ditemukan',
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
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Aparat | Forgot Password</title>
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
          <h3 class="mt-n4">
            <a href="index.php" class="text-dark fw-bold">Forgot Password?</a>
          </h3>
          <form action="forgot.php" method="POST">
            <div class="form-group mb-2">
              <label class="text-dark fw-bold mb-2" for="no_wa">Nomor WA <span class="required">*</span></label>
              <input type="text" id="no_wa" name="nomor" placeholder="Contoh: 083161579431" required class="input-group" />
            </div>
            <div class="form-group mb-4">
              <label class="text-dark fw-bold mb-2" for="username">Username <span class="required">*</span></label>
              <input type="text" id="username" name="username" placeholder="Masukkan Username" required class="input-group" />
            </div>
            <button class="mb-3" type="submit" name="submit_otp">Recover</button>
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
  ?>
</body>

</html>