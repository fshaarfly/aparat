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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
  <link href="css/styles.css" rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
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
  <form action="update_profile.php" method="POST" enctype="multipart/form-data">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="  -ms-overflow-style: none; 
  scrollbar-width: none;">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Account Settings</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Tampilkan foto profil -->
            <div class="text-center mb-3">
              <!-- Menampilkan gambar profil, jika belum ada, akan menggunakan gambar default -->
              <img id="profileImagePreview" src="<?php echo $profile_image ?: 'uploads/default_profile.svg'; ?>" alt="Profile Picture" class="img-fluid" style="width: 100px; height: 100px; border-radius: 50%;" />
              <input type="file" name="profile_image" id="profile_image" accept=".png, .jpg, .jpeg" class="form-control mt-2" onchange="previewImage(event)" />
            </div>

            <!-- Form untuk Nama Lengkap -->
            <div class="form-group">
              <label for="nama_lengkap">Nama Lengkap</label>
              <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($nama_lengkap) ?>" required />
            </div>

            <!-- Form untuk NIM -->
            <div class="form-group">
              <label for="nim">NIM</label>
              <input type="text" class="form-control" id="nim" name="nim" value="<?php echo htmlspecialchars($nim); ?>" required />
            </div>

            <!-- Form untuk Tahun Ajaran -->
            <div class="form-group">
              <label for="academic_year">Tahun Ajaran</label>
              <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" value="<?php echo htmlspecialchars($tahun_ajaran); ?>" required />
            </div>

            <!-- Form untuk Jurusan -->
            <div class="form-group">
              <label for="department">Jurusan</label>
              <input type="text" class="form-control" id="jurusan" name="jurusan" value="<?php echo htmlspecialchars($jurusan); ?>" required />
            </div>

            <!-- Form untuk Program Studi -->
            <div class="form-group">
              <label for="program">Prodi</label>
              <input type="text" class="form-control" id="prodi" name="prodi" value="<?php echo htmlspecialchars($prodi); ?>" required />
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="buttnn w-100" name="update_profile">Update Profile</button>
            <button type="button" class="btn btn-gray w-100" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <h3 class="text-center fw-semibold" data-aos="fade-up" style="color:#003298">Riwayat</h3>
  <div class="container-fluid d-flex justify-content-center align-items-center mt-3 mb-5" data-aos="fade-up">
    <div class="table-responsive collapsible py-4 px-5 w-100 mx-0 mx-sm-5">
      <table class="table w-100 table-hover table-striped table-bordered text-dark text-center align-middle" id="myTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>NIM</th>
            <th>Prodi</th>
            <th>Tahun Ajaran</th>
            <th>Jenis Surat</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Detail</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "SELECT * FROM surat WHERE user_id = ?";
          $stmt = $db->prepare($query);
          $stmt->bind_param("i", $_SESSION['user_id']);
          $stmt->execute();
          $result = $stmt->get_result();
          while ($row = $result->fetch_assoc()) {
            $formattedDate = $row['tanggal_buat'] ? date("d/M/Y", strtotime($row['tanggal_buat'])) : 'Tanggal Tidak Valid';
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['nama'] . "</td>";
            echo "<td>" . $row['nim'] . "</td>";
            echo "<td>" . $row['prodi'] . "</td>";
            echo "<td>" . $row['tahun_ajaran'] . "</td>";
            echo "<td>" . $row['jenis_surat'] . "</td>";
            echo "<td>" . $formattedDate . "</td>";

            // Kolom Status
            if ($row['status_surat'] == 'Pending') {
              echo "<td><a class='text-warning'>Pending</a></td>";
            } elseif ($row['status_surat'] == 'Diterima') {
              echo "<td><a class='text-success'>Diterima</a></td>";
            } elseif ($row['status_surat'] == 'Ditolak') {
              echo "<td><p class='text-danger mb-0'>Ditolak</p></td>";
            } else {
              echo "<td><p class='text-muted mb-0'>Status Tidak Diketahui</p></td>";
            }

            // Kolom Detail
            echo "<td><a href='detail.php?id=" . $row['id'] . "' class='btn btn-gray'>Detail</a></td>";

            // Kolom Action
            if ($row['status_surat'] == 'Pending') {
              echo "<td><p class='text-warning mb-0'>Menunggu <br> Persetujuan</p></td>";
            } elseif ($row['status_surat'] == 'Diterima') {
              echo "<td><a href='generate_word.php?id=" . $row['id'] . "' class='btn btn-success'>Download</a></td>";
            } elseif ($row['status_surat'] == 'Ditolak') {
              echo "<td><p class='text-danger mb-0'>Ditolak</p></td>";
            } else {
              echo "<td><p class='text-muted mb-0'>Tidak Ada Aksi</p></td>";
            }

            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
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

  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#myTable').DataTable();
    });

    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function() {
        const output = document.getElementById('profileImagePreview');
        output.src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>
</body>

</html>