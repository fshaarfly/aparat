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



  if (isset($_COOKIE['remember'])) {
    setcookie('remember', '', time() - 3600, "/", "", true, true);
  }
}

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // Query database untuk mendapatkan data berdasarkan ID
  $query = "SELECT * FROM surat WHERE id = ?";
  $stmt = $db->prepare($query);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  // Cek apakah data ditemukan
  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
  } else {
    echo "Data tidak ditemukan.";
    exit;
  }
} else {
  echo "ID tidak ditemukan.";
  exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete']) && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $deleteId = intval($_POST['id']);

    // Cek apakah penghapusan telah dikonfirmasi
    if (isset($_POST['confirmed']) && $_POST['confirmed'] === 'yes') {
      $deleteQuery = "DELETE FROM surat WHERE id = ?";
      $stmt = $db->prepare($deleteQuery);

      if ($stmt) {
        $stmt->bind_param("i", $deleteId);
        if ($stmt->execute()) {
          $hapus_script = "
                  <script>
                      Swal.fire({
                          title: 'Deleted!',
                          text: 'Data berhasil dihapus.',
                          icon: 'success',
                          confirmButtonColor: '#003289'
                      }).then(() => {
                          window.location.href = 'riwayat.php';
                      });
                  </script>
                  ";
        }
      }
    } else {
      // Tampilkan SweetAlert untuk meminta konfirmasi
      $hapus_script = "
          <script>
              Swal.fire({
                  title: 'Apakah anda yakin?',
                  text: 'Kamu tidak akan dapat mengembalikannya!',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#003289',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Ya, Hapus!',
                  cancelButtonText: 'Batal',
              }).then((result) => {
                  if (result.isConfirmed) {
                      // Kirim ulang form dengan konfirmasi
                      const form = document.createElement('form');
                      form.method = 'POST';
                      form.action = '';
                      form.innerHTML = `
                          <input type='hidden' name='id' value='$deleteId'>
                          <input type='hidden' name='delete' value='1'>
                          <input type='hidden' name='confirmed' value='yes'>
                      `;
                      document.body.appendChild(form);
                      form.submit();
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aparat | Riwayat</title>
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
  <style>
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
      width: 100%;
      /* Pastikan gambar selalu 100% dari container */
      height: auto;
      /* Pertahankan rasio aspek */
      max-height: 80vh;
      /* Batas maksimal tinggi gambar */
    }
  </style>
</head>

<body>
  <?php
  include 'layout/navbar.php';
  ?>
  <div class="col-xxl-12 d-flex justify-content-center align-items-center my-5" data-aos="fade-up">
    <div class="container mx-0 mx-sm-5 p-0">
      <div class="collapsible py-4">
        <div class="head px-5">
          <div class="row d-flex align-items-center ">
            <div class="col-6 px-0 px-lg-5 d-flex align-items-center">
              <h6 class="text-dark mb-0 pe-2"><?php echo $data['id']; ?></h6>
              <h6 class="text-dark mb-0 pe-2"><?php echo $data['nama']; ?></h6>
              <h6 class="text-dark mb-0"><?php echo $data['jenis_surat']; ?></h6>
            </div>
            <div class="col-6 px-0 px-lg-5 d-flex justify-content-end">
              <?php
              if ($data['status_surat'] == 'Pending') {
                echo "<a class='text-warning'>Pending</a>";
              } elseif ($data['status_surat'] == 'Diterima') {
                echo "<a class='text-success'>Diterima</a>";
              } elseif ($data['status_surat'] == 'Ditolak') {
                echo "<p class='text-danger mb-0'>Ditolak</p>";
              } else {
                echo "<p class='text-muted mb-0'>Status Tidak Diketahui</p>";
              }
              ?>
            </div>
          </div>

        </div>

        <hr class="text-dark">
        <div class="row px-5 py-2">

          <div class="col-lg-6 col-sm-12 px-0 px-lg-5">
            <form action="">
              <label class="form-label text-dark">Nama Lengkap</label>
              <input type="text" class="form-control mb-3" placeholder="Contoh: Gojo Satoru" required value="<?php echo $data['nama']; ?>">

              <label class="form-label text-dark">Nim</label>
              <input type="text" class="form-control mb-3" placeholder="Contoh: 4342411071" required value="<?php echo $data['nim']; ?>">

              <label class="form-label text-dark">Tahun Ajaran</label>
              <input type="text" class="form-control mb-3" placeholder="Contoh: 2024" required value="<?php echo $data['tahun_ajaran']; ?>">
          </div>

          <div class="col-lg-6 col-sm-12 px-0 px-lg-5">
            <label class="form-label text-dark">Jurusan<span class="required">*</span></label>
            <input type="text" class="form-control mb-3" placeholder="Contoh: Informatika" required value="<?php echo $data['jurusan']; ?>">

            <label class="form-label text-dark">Prodi</label>
            <input type="text" class="form-control mb-3" placeholder="Contoh: TRPL" required value="<?php echo $data['prodi']; ?>">

            <label class="form-label text-dark">Semester</label>
            <input type="text" class="form-control mb-3" placeholder="Contoh: TRPL" required value="<?php echo $data['semester']; ?>">
          </div>
          <div class="col-lg-12 px-0 px-lg-5">
            <label class="form-label text-dark">Alasan Membuat Surat</label>
            <input type="text" class="form-control mb-3" placeholder="Contoh: Untuk Surat Keterangan Mahasiswa" required value="<?php echo $data['alasan']; ?>">

            <?php if (!empty($data['alasan_penolakan'])): ?>
              <!-- Jika alasan_penolakan ada di database, tampilkan label dan input -->
              <label class="form-label text-dark">Alasan Penolakan</label>
              <input type="text" class="form-control mb-3" required value="<?php echo $data['alasan_penolakan']; ?>">
            <?php endif; ?>
          </div>

          </form>
          <div class="col-6 px-0 px-lg-5 mt-2">
            <a href="riwayat.php" class="btn btn-gray">Kembali</a>
          </div>
          <div class="col-6 px-0 px-lg-5 mt-2 d-flex align-items-center justify-content-end">
            <form method="POST" action="">
              <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
              <button type="submit" name="delete" class="me-3 btn btn-danger">Hapus</button>
            </form>
            <?php
            $query = "SELECT * FROM surat WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($data['status_surat'] == 'Pending') {
              echo "<td><p class='text-warning mb-0'>Menunggu<br>Persetujuan</p></td>";
            } elseif ($data['status_surat'] == 'Diterima') {
              echo "<td><a href='generate_word.php?id=" . $data['id'] . "' class='btn btn-success'>Download</a></td>";
            } elseif ($data['status_surat'] == 'Ditolak') {
              echo "<td><p class='text-danger mb-0'>Ditolak</p></td>";
            } else {
              echo "<td><p class='text-muted mb-0'>Tidak Ada Aksi</p></td>";
            }
            ?>
          </div>
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
  if (isset($logout_script)) {
    echo $logout_script;
  }
  if (isset($hapus_script)) {
    echo $hapus_script;
  }
  ?>
</body>

</html>