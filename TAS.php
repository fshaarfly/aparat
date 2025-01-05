<?php
include 'database.php';
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
  header('Location: index.php');
  exit;
}
if ($_SESSION['role'] != 0) {
  header("Location: dashboard_dosen.php");
  exit();
}

if (!isset($_SESSION['user_id'])) {
  echo "Error: user_id tidak ditemukan di sesi!";
  exit;
}

if (isset($_POST['buatsurat'])) {
  // Pastikan user_id diambil dari sesi
  $user_id = $_SESSION['user_id'];  // Ambil user_id dari sesi

  // Query menggunakan prepared statement
  $stmt = mysqli_prepare($db, "INSERT INTO surat (user_id, nama, nim, tahun_ajaran, jurusan, prodi, semester, alasan, jenis_surat, status_surat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $jenis_surat = "TAS";
  $status_surat = "Pending"; // default status

  // Bind parameter ke prepared statement
  mysqli_stmt_bind_param($stmt, "isssssssss", $user_id, $_POST['nama'], $_POST['nim'], $_POST['tahun_ajaran'], $_POST['jurusan'], $_POST['prodi'], $_POST['semester'], $_POST['alasan'], $jenis_surat, $status_surat);

  // Eksekusi query
  if (mysqli_stmt_execute($stmt)) {
    $berhasil_script = "
    <script>
    Swal.fire({
        title: 'Berhasil Membuat Surat',
        text: 'Anda akan diarahkan ke halaman riwayat.',
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#003289' 
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'riwayat.php';
        }
    });
    </script>
";
  } else {
    echo "Gagal menyimpan data: " . mysqli_error($db);
  }

  // Tutup statement
  mysqli_stmt_close($stmt);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aparat | Buat Surat</title>
  <link rel="icon" type="image/x-icon" href="/img/iconpolibatam.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    rel="icon"
    type="image/x-icon"
    href="img/iconpolibatam.png" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
  <link href="css/styles.css" rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
</head>
<style>
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
    width: 100%;
    /* Pastikan gambar selalu 100% dari container */
    height: auto;
    /* Pertahankan rasio aspek */
    max-height: 80vh;
    /* Batas maksimal tinggi gambar */
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
</style>

<body>
  <?php
  include 'layout/navbar.php';
  ?>

  <div class="col-xxl-12 d-flex justify-content-center align-items-center my-5" data-aos="fade-up">
    <div class="container mx-0 mx-sm-5 p-0">
      <div class="collapsible py-4">
        <h5 class="text-dark text-center">Transkrip Akademik Sementara</h5>
        <hr class="text-dark">
        <div class="row px-5 py-2">

          <div class="col-lg-6 col-sm-12 px-0 px-lg-5">
            <form method="POST">
              <label class="form-label text-dark">Nama Lengkap<span class="required">*</span></label>
              <input type="text" name="nama" class="form-control mb-3" placeholder="Contoh: Gojo Satoru" value="<?php echo htmlspecialchars($nama_lengkap) ?? ''; ?>" required>

              <label class="form-label text-dark">Nim<span class="required">*</span></label>
              <input type="text" name="nim" class="form-control mb-3" placeholder="Contoh: 4342411071" value="<?php echo htmlspecialchars($nim) ?? ''; ?>" required>

              <label class="form-label text-dark">Tahun Ajaran<span class="required">*</span></label>
              <input type="text" name="tahun_ajaran" class="form-control mb-3" placeholder="Contoh: 2024" value="<?php echo htmlspecialchars($tahun_ajaran) ?? ''; ?>" required>
          </div>

          <div class="col-lg-6 col-sm-12 px-0 px-lg-5">
            <label class="form-label text-dark">Jurusan<span class="required">*</span></label>
            <input type="text" name="jurusan" class="form-control mb-3" placeholder="Contoh: Informatika" value="<?php echo htmlspecialchars($jurusan) ?? ''; ?>" required>

            <label class="form-label text-dark">Prodi<span class="required">*</span></label>
            <input type="text" name="prodi" class="form-control mb-3" placeholder="Contoh: TRPL" value="<?php echo htmlspecialchars($prodi) ?? ''; ?>" required>

            <label class="form-label text-dark">Semester<span class="required">*</span></label>
            <input type="text" name="semester" class="form-control mb-3" placeholder="Contoh: Semester 1" value="<?php echo htmlspecialchars($semester) ?? ''; ?>" required>
          </div>
          <div class="col-lg-12 px-0 px-lg-5">
            <label class="form-label text-dark">Alasan Membuat Surat<span class="required">*</span></label>
            <input type="text" name="alasan" class="form-control mb-3" placeholder="Contoh: Untuk Surat Keterangan Mahasiswa" required>
          </div>
          <div class="col-12 px-0 px-lg-5 mt-2">
            <button type="submit" name="buatsurat" class="buttnn w-100">Buat Surat</button>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  </header>
  <footer class="mt-auto">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center">
          <hr style="color: #003298;">
          <p class="px-5 px-lg-0" style="color: #003298;">
            &copy; 2024 Designed & Developed by HYPEBIZZ
          </p>
        </div>
      </div>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/scripts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
  <?php
  if (isset($berhasil_script)) {
    echo $berhasil_script;
  }
  ?>
</body>

</html>