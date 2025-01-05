<?php
session_start();
include 'database.php';


if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
  header('Location: index.php');
  exit;
}

if ($_SESSION['role'] != 0) {
  header("Location: dashboard_dosen.php");
  exit();
}

if (isset($_POST['logout'])) {
  session_destroy();
  setcookie('remember', '', time() - 3600, "/", "", true, true);
  $logout_script = "
      <script>
      Swal.fire({
          title: 'Logout Berhasil',
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
  <title>Aparat | Dashboard</title>
  <link
    rel="icon"
    type="image/x-icon"
    href="img\iconpolibatam.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css"
    rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
  <style>
    .bg {
      background-color: #003298;
      border-radius: 50px;
    }

    .dropdown-menu {
      opacity: 0;
      transform: translateY(-10px);
      transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .dropdown-menu.show {
      opacity: 1;
      transform: translateY(0);
    }

    .dropdown-toggle::after {
      transition: transform 0.3s ease;
    }

    .buttnn {
      background-color: #003298;
      color: white;
      border-radius: 0.375rem;
      border: 1px solid #ced4da;
      padding: 0.5rem 0.75rem;
      font-size: 15px;
      transition: 0.3s ease;
      border: #003298 2px solid;
    }

    .buttnn:hover {
      background-color: #fff;
      color: #003298;

    }

    #profileImagePreview {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
    }

    #cropModal .modal-dialog {
      max-width: 90%;
      /* Maksimal 90% dari lebar layar */
      margin: auto;

    }

    #imageToCrop {
      max-width: 100%;
      /* Pastikan gambar selalu 100% dari container */
      height: auto;
      /* Pertahankan rasio aspek */
      max-height: 80vh;
      /* Batas maksimal tinggi gambar */
    }
  </style>

</head>

<body class="d-flex flex-column h-100">
  <div class="bg">
    <?php
    include 'layout/navbar.php';
    ?>
    <!-- Header-->
    <header class="py-5">
      <div class="container px-5 py-5 pt-sm-0" data-aos="fade-up">
        <div class="row gx-5 align-items-center">
          <!-- Kolom teks -->
          <div class="col-xxl-8 col-lg-6 text-center text-lg-start aos-init aos-animate" data-aos="fade-up">
            <h6 class="text-light">
              Selamat datang <strong><?php echo !empty($nama_lengkap) ? $nama_lengkap : $_SESSION['username']; ?></strong> di,
            </h6>
            <h1 class="display-3 fw-bolder mb-3">
              <span class="text-light">Aplikasi Permintaan Surat Menyurat.</span>
            </h1>
            <h5 class="mb-4 text-light">
              Buat Surat Anda Dengan Cepat dan Mudah di Website
              Kami!
            </h5>
            <div
              class="d-grid gap-3 d-sm-flex justify-content-center justify-content-lg-start">
              <a
                class="btn btn-primary btn-lg px-5 py-3 me-sm-3 fs-7 fw-semibold"
                href="buatsurat.php">Mulai Buat Surat</a>
            </div>
          </div>
          <!-- Kolom gambar -->
          <div
            class="col-xxl-4 col-lg-6 d-flex justify-content-center mt-5 mt-lg-0 d-none d-lg-block">
            <img
              class="img-fluid"
              src="img/writing.svg"
              alt="Ilustrasi menulis surat" />
          </div>
        </div>
      </div>
    </header>
  </div>
  <section class=" py-5 mt-0 mt-lg-6">
    <div class="container px-5 py-5 pt-sm-0" data-aos="fade-up">
      <div class="row gx-5 align-items-center">
        <div class="col-xxl-6 d-none d-lg-block p-0">
          <img
            style="max-width: 100%; height: 500px;"
            src="img/pc.svg" alt="">
        </div>
        <div class="text-section2 col-xxl-6">
          <p class="text-justify fw-normal fs-4" style="color: #003298;">Aplikasi Permintaan Surat Menyurat (APARAT) adalah situs web Polibatam yang menyediakan layanan terkait permintaan surat menyurat untuk mahasiswa yang dijalankan oleh pihak akademik Polibatam.</p>
          <p class="text-justify fw-normal fs-4" style="color: #003298;">Website ini mencakup <strong>Surat Keterangan Mahasiswa, Surat Pengajuan Kartu Mahasiswa, Transkrip Akademik Sementara, dan Lembar Kemajuan Akademik.</strong></p>
        </div>
      </div>
  </section>

  <footer class="text-white d-flex align-items-center" style="background-color: #003298;">
    <div class="container">
      <div class="row py-4 d-flex align-items-center text-md-start">
        <div class="col-xxl-2 d-flex justify-content-center align-items-center">
          <a href="#"><img src="img\Logo Aparat.png" style="width: 100px;" alt=""></a>
        </div>
        <div class="col-xxl-4 text-center">
          <p class="my-2 mt-4 text-light">Alamat: Jl. Ahmad Yani Batam Kota.</p>
          <p class="my-2">Kota Batam, Kepulauan Riau, Indonesia.</p>
        </div>
        <div class="col-xxl-2 text-center">
          <a href="#">
            <p class="my-2 mt-4" style="color: #fff;">Home</p>
          </a>
          <a href="buatsurat.php">
            <p class="my-2" style="color: #fff;">Buat Surat</p>
          </a>
        </div>
        <div class="col-xxl-4 text-center">
          <a href="riwayat.php">
            <p class="my-2 mt-4" style="color: #fff;">Riwayat</p>
          </a>
          <a href="hubungikami.php">
            <p class="my-2" style="color: #fff;">Hubungi Kami</p>
          </a>
        </div>
      </div>
      <div class="col-xxl-12 text-center">
        <hr>
        <p>
          Copyright &copy; Designed & Developed by HYPEBIZZ 2024
        </p>
      </div>
    </div>

  </footer>



  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
  <?php
  if (isset($logout_script)) {
    echo $logout_script;
  }
  ?>
</body>

</html>