<?php
session_start();


if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
  header('Location: index.php');
  exit;
}

if (isset($_POST['logout'])) {
  session_destroy();
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



  if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, "/", "", true, true);
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
  <title>404 Page Not Found</title>
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

  <style>
    .wrap {
      text-align: center;
    }

    ._404-text {
      font-size: 100px;
      font-weight: 700;
      text-align: center;
      position: relative;
      display: inline-block;
      color: #003298;

      &:before {
        content: attr(data-text);
        position: absolute;
        top: 0;
        left: 2px;
        color: #003298;
        text-shadow: -1px 0 blue;
        clip: rect(0, 800px, 0, 0);
        animation: glitch-1 1s linear alternate-reverse infinite;
      }

      &:after {
        content: attr(data-text);
        position: absolute;
        top: 0;
        left: -2px;
        color: darken(#01A8FF, 33%);
        text-shadow: 1px 0 red;
        clip: rect(0, 800px, 0, 0);
        animation: glitch-2 .6s linear alternate-reverse infinite .2s;
      }
    }

    @keyframes glitch-1 {
      0% {
        clip: rect(40px, 800px, 70px, 0px);
      }

      15% {
        clip: rect(130px, 800px, 131px, 0px);
      }

      50% {
        clip: rect(90px, 800px, 96px, 0px);
      }

      75% {
        clip: rect(125px, 800px, 185px, 0px);
      }

      87% {
        clip: rect(70px, 800px, 100px, 0px);
      }

      100% {
        clip: rect(130px, 800px, 130px, 0px);
      }
    }

    @keyframes glitch-2 {
      0% {
        clip: rect(20px, 800px, 80px, 0px);
      }

      15% {
        clip: rect(100px, 800px, 105px, 0px);
      }

      50% {
        clip: rect(100px, 800px, 95px, 0px);
      }

      75% {
        clip: rect(60px, 800px, 60px, 0px);
      }

      87% {
        clip: rect(145px, 800px, 160px, 0px);
      }

      100% {
        clip: rect(185px, 800px, 185px, 0px);
      }
    }
  </style>
</head>

<body class="d-flex flex-column h-100">
  <!-- Navigation-->
  <nav
    class="navbar navbar-expand-lg navbar-light py-0 sticky-top mb-5"
    style="background-color: #003298">
    <div class="container px-lg-0 px-sm-5">
      <a class="navbar-brand" href="dashboard.php"><img
          src="img\Logo Aparat.png"
          alt=""
          style="width: 100px"
          class="py-2" /></a>
      <!-- Hamburger Menu-->
      <button
        class="navbar-toggler border-0"
        type="button"
        id="sidebarToggle">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder gap-2">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="buatsurat.php">Buat Surat</a></li>
          <li class="nav-item"><a class="nav-link" href="riwayat.php">Riwayat</a></li>
          <li class="nav-item"><a class="nav-link" href="hubungikami.php">Hubungi Kami</a></li>
        </ul>
        <div class="dropdown">
          <button
            class="btn dropdown-toggle d-flex align-items-center gap-1"
            type="button"
            id="dropdownMenuButton"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            style="background-color: #003298; border: none">
            <img
              src="img/profile.svg"
              style="width: 50px" />
            <div style="text-align: left">
              <small class="text-light"><?php echo $_SESSION['username']; ?></small>
              <small class="d-block text-light" style="font-size: 12px">Mahasiswa</small>
            </div>
          </button>
          <ul
            class="dropdown-menu font-sm"
            aria-labelledby="dropdownMenuButton">
            <li>
              <form action="dashboard.php" method="POST">
                <button class="dropdown-item" id="logout" name="logout"> <i class="fa-solid fa-right-from-bracket me-2" style="color: #003298;"></i>Log Out</button>
              </form>
            </li>
          </ul>
        </div>
      </div>
  </nav>
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <button id="sidebarClose" class="btn-close p-2"></button>
    </div>
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder gap-2">
      <li class="nav-item">
        <a class="nav-link" href="dashboard.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="buatsurat.php">Buat Surat</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="riwayat.php">Riwayat</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="hubungikami.php">Hubungi Kami</a>
      </li>
    </ul>
    <div class="dropdown">
      <button
        class="btn dropdown-toggle d-flex align-items-center gap-1"
        type="button"
        id="dropdownMenuButton"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        style="background-color: #003298; border: none">
        <img
          src="img/profile.svg"
          style="width: 50px" />
        <div style="text-align: left">
          <small class="text-light"><?php echo $_SESSION['username']; ?></small>
          <small class="d-block text-light" style="font-size: 12px">Mahasiswa</small>
        </div>
      </button>
      <ul
        class="dropdown-menu font-sm"
        aria-labelledby="dropdownMenuButton">
        <li>
          <form action="dashboard.php" method="POST">
            <button class="dropdown-item" id="logout" name="logout"> <i class="fa-solid fa-right-from-bracket me-2" style="color: #003298;"></i>Log Out</button>
          </form>
        </li>
      </ul>
    </div>
  </div>

  <!-- 404 Error Text -->
  <div class="d-flex justify-content-center align-items-center flex-column text-center text-dark" style="height: 80vh;">
    <div class="wrap">
      <div class="_404-text" data-text="404">404</div>
    </div>
    <p class="lead text-gray-800 mb-4">Halaman Tidak Ditemukan</p>
    <p class="text-gray-500 mb-4">Halaman yang Anda cari tidak dapat ditemukan.</p>
    <a href="dashboard.php" class="btn btn-primary">&larr; Back to Dashboard</a>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/scripts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
</body>