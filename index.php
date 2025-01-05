<?php
include 'database.php';
session_start();

// Jika Remember Me aktif
if (isset($_COOKIE['remember'])) {
  $token = $_COOKIE['remember'];
  $stmt = $db->prepare("SELECT id, username, role FROM users WHERE remember_token = ? LIMIT 1");
  $stmt->bind_param('s', $token);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $_SESSION['user_id'] = $data['id'];  // Menyimpan user_id di sesi
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['is_login'] = true;

    // Redirect sesuai role
    header('Location: ' . ($data['role'] == 1 ? 'dashboard_dosen.php' : 'dashboard.php'));
    exit;
  }
}

// Jika sesi login sudah aktif
if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
  header('Location: ' . ($_SESSION['role'] == 1 ? 'dashboard_dosen.php' : 'dashboard.php'));
  exit;
}

// Proses login
if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $hash_password = hash("sha256", $password);

  // Gunakan prepared statements untuk mencegah SQL injection
  $stmt = $db->prepare("SELECT id, username, role FROM users WHERE username = ? AND password = ? LIMIT 1");
  $stmt->bind_param('ss', $username, $hash_password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();

    // Set sesi login
    $_SESSION['user_id'] = $data['id'];  // Menyimpan user_id di sesi
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['is_login'] = true;

    // Jika Remember Me diaktifkan
    if (isset($_POST['remember'])) {
      $token = bin2hex(random_bytes(16)); // Token unik
      setcookie('remember', $token, time() + (60 * 60 * 24 * 30), "/", "", true, true); // 30 hari
      // Simpan token di basis data
      $stmt = $db->prepare("UPDATE users SET remember_token = ? WHERE username = ?");
      $stmt->bind_param('ss', $token, $username);
      $stmt->execute();
    }

    header('Location: ' . ($data['role'] == 1 ? 'dashboard_dosen.php' : 'dashboard.php'));
    exit;
  } else {
    $gagal_script = "
        <script>
        Swal.fire({
            title: 'Username atau Password Salah',
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

  $stmt->close();
}
$db->close();
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
  <title>Aparat | Login</title>
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
        <div class="row align-items-center mx-2 mx-sm-5 my-5">
          <div class="logo mb-2">
            <img src="img\Logo Aparat-blue.png" style="width: 50%" />
          </div>
          <h1 class="mt-n4">
            <a href="index.php" class="text-dark fw-bold">Log in.</a>
          </h1>
          <form action="index.php" method="POST">
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
            <div class="form-group">
              <label class="text-dark fw-bold mb-2" for="password">Password <span class="required">*</span></label>
              <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                required=""
                class="input-group mb-n3" />
              <span class="password-toggle-icon"><i
                  class="fas fa-eye-slash"
                  id="togglePassword"
                  style="color: #808080"></i></span>
            </div>
            <div class="form-check mb-3 d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <input type="checkbox" id="remember" name="remember" />
                <label class="text-dark ms-1" for="remember" style="font-size:12px">Remember me</label>
              </div>
              <a class="fst-italic" style="font-size: 12px" href="forgot.php">Forgot Password?</a>
            </div>
            <button
              class="mb-3"
              type="submit"
              name="login">
              Log In
            </button>
            <p class="text-dark text-center" style="font-size: 12px">
              Tidak memiliki akun?
              <a class="daftardisini" href="register.php">Daftar disini</a>
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
  </script>

  <?php
  if (isset($gagal_script)) {
    echo $gagal_script;
  }
  ?>
</body>

</html>