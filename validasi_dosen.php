<?php
include 'database.php';
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
  header('Location: index.php');
  exit;
}
if ($_SESSION['role'] != 1) {
  header("Location: dashboard.php");
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
// Periksa apakah form sudah disubmit
if (isset($_POST['action'])) {
  $id = $_POST['id'];  // Ambil ID dari form
  $action = $_POST['action'];  // Ambil aksi yang dikirimkan

  // Ambil nomor user yang terkait dengan surat berdasarkan ID surat
  $query_nomor = "SELECT users.nomor FROM users JOIN surat ON users.id = surat.user_id WHERE surat.id = ?";
  $stmt_nomor = $db->prepare($query_nomor);
  $stmt_nomor->bind_param("i", $id);  // Bind ID surat
  $stmt_nomor->execute();
  $result_nomor = $stmt_nomor->get_result();

  if ($result_nomor->num_rows > 0) {
    $row_nomor = $result_nomor->fetch_assoc();
    $nomor = $row_nomor['nomor'];  // Ambil nomor dari tabel users
  } else {
    echo "<script>alert('Nomor tidak ditemukan.'); window.location.href='validasi_dosen.php';</script>";
    exit();
  }

  // Menangani aksi 'Tolak'
  if ($action === 'Tolak') {
    $status_baru = 'Ditolak';
    $alasan_penolakan = $_POST['alasan_penolakan'];  // Ambil alasan dari textarea

    $_SESSION['nomor'] = $nomor;

    $curl = curl_init();
    $data = [
      'target' => $nomor,
      'message' => implode(PHP_EOL, [
        "Surat Anda dengan ID $id telah ditolak karena $alasan_penolakan",
        "'ini link website'"
      ]),
    ];
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: VADszheBGjj9RcrbXdpb"));
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_URL, "https://api.fonnte.com/send");
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      echo 'Curl error: ' . curl_error($curl);
    } else {
      // Optionally log the response
      echo 'Response from Fonnte API: ' . $response;
    }
    curl_close($curl);

    $query = "UPDATE surat SET status_surat = ?, alasan_penolakan = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ssi", $status_baru, $alasan_penolakan, $id);  // Bind parameter untuk alasan dan ID

    if ($stmt->execute()) {
      echo "<script>alert('Status berhasil diubah menjadi $status_baru dan alasan telah dikirim.'); window.location.href='validasi_dosen.php';</script>";
      exit();
    } else {
      echo "<script>alert('Gagal mengubah status: " . $stmt->error . "');</script>";
    }
  }
  // Menangani aksi 'Terima'
  elseif ($action === 'Terima') {
    $status_baru = 'Diterima';

    $_SESSION['nomor'] = $nomor;

    $curl = curl_init();
    $data = [
      'target' => $nomor,
      'message' => implode(PHP_EOL, [
        "Surat Anda dengan ID $id telah diterima",
        "'ini link website'"
      ]),
    ];
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: VADszheBGjj9RcrbXdpb"));
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_URL, "https://api.fonnte.com/send");
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      echo 'Curl error: ' . curl_error($curl);
    } else {
      // Optionally log the response
      echo 'Response from Fonnte API: ' . $response;
    }
    curl_close($curl);

    // Update status di database untuk menerima surat
    $query = "UPDATE surat SET status_surat = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("si", $status_baru, $id);

    if ($stmt->execute()) {
      echo "<script>alert('Status berhasil diubah menjadi $status_baru.'); window.location.href='validasi_dosen.php';</script>";
      exit();
    } else {
      echo "<script>alert('Gagal mengubah status: " . $stmt->error . "');</script>";
    }
  }
}









if (isset($_POST['update_profile'])) {
  // Ambil data yang dimasukkan pengguna
  $nama_lengkap = mysqli_real_escape_string($db, $_POST['nama_lengkap']);
  $nim = mysqli_real_escape_string($db, $_POST['nim']);

  // Proses gambar profil (upload file)
  if ($_FILES['profile_image']['name'] != '') {
    $profile_image = $_FILES['profile_image']['name'];
    $file_extension = strtolower(pathinfo($profile_image, PATHINFO_EXTENSION));
    $allowed_extensions = ['png', 'jpg', 'jpeg'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_image);

    if (!in_array($file_extension, $allowed_extensions)) {
      echo "<script>alert('File tidak valid. Hanya file PNG, JPG, dan JPEG yang diizinkan.');</script>";
      exit;
    }
    $file_mime = mime_content_type($_FILES['profile_image']['tmp_name']);
    $allowed_mime_types = ['image/png', 'image/jpeg'];
    if (!in_array($file_mime, $allowed_mime_types)) {
      echo "<script>alert('Jenis file tidak diizinkan.');</script>";
      exit;
    }
    // Pindahkan file gambar ke direktori tujuan
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
      echo "<script>alert('File berhasil diunggah.');</script>";
    } else {
      echo "<script>alert('Terjadi kesalahan saat mengunggah file.');</script>";
      exit;
    }
  } else {
    // Jika tidak ada gambar baru, gunakan gambar yang lama
    $profile_image = $profile_data['profile_image'];
  }

  // Update data di database
  $query = "UPDATE profile SET 
                nama_lengkap = '$nama_lengkap', 
                nim = '$nim', 
                profile_image = '$profile_image' 
              WHERE user_id = '$user_id'";

  if (mysqli_query($db, $query)) {
    echo "<script>alert('Profile updated successfully');</script>";
  } else {
    echo "<script>alert('Error updating profile');</script>";
  }
}

// Pastikan session user_id sudah diatur
$user_id = $_SESSION['user_id'];

// Ambil data profil dari database
$query = "SELECT * FROM profile WHERE user_id = '$user_id'";
$result = mysqli_query($db, $query);

// Pastikan data ditemukan
if ($result && mysqli_num_rows($result) > 0) {
  $profile_data = mysqli_fetch_assoc($result);
  $profile_image = $profile_data['profile_image']; // Menggunakan profile_image
  $nama_lengkap = $profile_data['nama_lengkap']; // Nama lengkap
  $nim = $profile_data['nim']; // Nomor Induk Mahasiswa

} else {
  // Jika data tidak ditemukan, berikan pesan atau lakukan sesuatu
  $profile_data = null;
}

$profile_image = isset($profile_data['profile_image']) ? 'uploads/' . ltrim($profile_data['profile_image'], 'uploads/') : 'uploads/default_profile.svg';
$nama_lengkap = $profile_data['nama_lengkap'] ?? ''; // Nama lengkap default kosong
$nim = $profile_data['nim'] ?? ''; // NIM default kosong

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
  <title>Website Surat Poilibatam</title>
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
      max-width: 100%;
      /* Pastikan gambar selalu 100% dari container */
      height: auto;
      /* Pertahankan rasio aspek */
      max-height: 80vh;
      /* Batas maksimal tinggi gambar */
    }
  </style>
</head>

<body>
  <nav
    class="navbar navbar-expand-lg navbar-light py-0 sticky-top mb-5"
    style="background-color: #003298">
    <div class="container px-lg-0 px-sm-5">
      <a class="navbar-brand" href="dashboard_dosen.php"><img
          src="img\Logo Aparat.png"
          alt=""
          style="width: 100px"
          class="py-2" /></a>
      <button
        class="navbar-toggler border-0"
        type="button"
        id="sidebarToggle">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder gap-2">
          <li class="nav-item"><a class="nav-link" href="dashboard_dosen.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="validasi_dosen.php">Validasi</a></li>

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
              src="<?php echo $profile_image ?: 'uploads/default_profile.svg'; ?>"
              style="width: 35px; height:35px; border-radius: 50%; border:solid 2px #fff; object-fit:cover;"
              class="me-1" />
            <div style="text-align: left">
              <small class="text-light">
                <?php echo htmlspecialchars($nama_lengkap ?: $_SESSION['username']); ?>
              </small>
              <small class="d-block text-light" style="font-size: 12px">Dosen</small>
            </div>
          </button>
          <ul
            class="dropdown-menu font-sm"
            aria-labelledby="dropdownMenuButton">
            <li>
              <form action="" method="POST">
                <button type="button" class="dropdown-item" id="settings" name="settings" data-bs-toggle="modal"
                  data-bs-target="#exampleModal"> <i class="fa-solid fa-user me-2" style="color: #003298;"></i>Account</button>
              </form>
            </li>
            <li>
              <form action="dashboard_dosen.php" method="POST">
                <button type="submit" class="dropdown-item" id="logout" name="logout"> <i class="fa-solid fa-right-from-bracket me-2" style="color: #003298;"></i>Log Out</button>
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
        <a class="nav-link" href="dashboard_dosen.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="validasi_dosen.php">Validasi</a>
    </ul>
    <dclass="dropdown">
      <button
        class="btn dropdown-toggle d-flex align-items-center gap-1"
        type="button"
        id="dropdownMenuButton"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        style="background-color: #003298; border: none">
        <img
          src="<?php echo $profile_image ?: 'uploads/default_profile.svg'; ?>"
          style="width: 35px; height:35px; border-radius: 50%; border:solid 2px #fff; object-fit:cover;"
          class="me-1" />
        <div style="text-align: left">
          <small class="text-light">
            <?php echo htmlspecialchars($nama_lengkap ?: $_SESSION['username']); ?>
          </small>
          <small class="d-block text-light" style="font-size: 12px">Dosen</small>
        </div>
      </button>
      <ul
        class="dropdown-menu font-sm"
        aria-labelledby="dropdownMenuButton">
        <li>
          <form action="" method="POST">
            <button type="button" class="dropdown-item" id="settings" name="settings" data-bs-toggle="modal"
              data-bs-target="#exampleModal"> <i class="fa-solid fa-user me-2" style="color: #003298;"></i>Account</button>
          </form>
        </li>
        <li>
        <li>
          <form action="dashboard_dosen.php" method="POST">
            <button class="dropdown-item" id="logout" name="logout"> <i class="fa-solid fa-right-from-bracket me-2" style="color: #003298;"></i>Log Out</button>
          </form>
        </li>
      </ul>
      </dclass=>
  </div>
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
            </div>
            <div class="form-group mt-2">
              <label for="nama_lengkap">Foto Profil</label>
              <input type="file" name="profile_image" id="profile_image" accept=".png, .jpg, .jpeg" class="form-control" onchange="previewImage(event)" />
            </div>
            <!-- Form untuk Nama Lengkap -->
            <div class="form-group mt-2">
              <label for="nama_lengkap">Nama Lengkap</label>
              <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Contoh: Gojo Satoru" value="<?php echo htmlspecialchars($nama_lengkap) ?>" />
            </div>

            <!-- Form untuk NIM -->
            <div class="form-group mt-2">
              <label for="nim">NIP</label>
              <input type="text" class="form-control" id="nim" name="nim" placeholder="Contoh: 4342400867" value="<?php echo htmlspecialchars($nim); ?>" />
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="buttnn w-100" name="update_profile">Update Profile</button>
            <button type="button" class="btn btn-gray w-100" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="cropModalLabel">Crop Image</h5>
            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body d-flex justify-content-center">
            <img id="imageToCrop" style="max-width: 100%; height: 700px;" />
          </div>
          <div class="modal-footer">
            <button type="button" class="buttnn w-100" id="cropImage">Crop & Save</button>
            <button type="button" class="btn btn-gray w-100" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <h3 class="text-center fw-semibold" data-aos="fade-up" style="color:#003298">Pending</h3>
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
          $query = "SELECT * FROM surat WHERE status_surat ='Pending' ";
          $stmt = $db->prepare($query);
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
            } else {
              echo "<td><p class='text-muted mb-0'>Status Tidak Diketahui</p></td>";
            }

            // Kolom Detail
            echo "<td><a href='detail_dosen.php?id=" . $row['id'] . "' class='btn btn-gray'>Detail</a></td>";

            if ($row['status_surat'] == 'Pending') {
              echo "<td>
                  <form method='POST' action='validasi_dosen.php'>
                      <input type='hidden' name='id' value='{$row['id']}'>
                      <button type='button' class='btn btn-danger mb-0' data-bs-toggle='modal' data-bs-target='#modalTolak'>Tolak</button>
                      <button type='submit' name='action' value='Terima' class='btn btn-success mb-0'>Terima</button>
                  </form>
              </td>";
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
  <h3 class="text-center fw-semibold" data-aos="fade-up" style="color:#003298">Riwayat</h3>
  <div class="container-fluid d-flex justify-content-center align-items-center mt-3 mb-5" data-aos="fade-up">
    <div class="table-responsive collapsible py-4 px-5 w-100 mx-0 mx-sm-5">
      <table class="table w-100 table-hover table-striped table-bordered text-dark text-center align-middle" id="myTable1">
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
          $query = "SELECT * FROM surat WHERE status_surat = 'Diterima' OR status_surat = 'Ditolak'";
          $stmt = $db->prepare($query);
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
            echo "<td><a href='detail_dosen.php?id=" . $row['id'] . "' class='btn btn-gray'>Detail</a></td>";

            if ($row['status_surat'] == 'Diterima') {
              echo "<td><a class='text-success'>Diterima</a></td>";
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
  <div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="formTolak" action="validasi_dosen.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTolakLabel">Alasan Penolakan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="alasan_penolakan" class="form-label">Alasan</label>
              <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="3" placeholder="Contoh: Perbaiki Alasan Membuat Surat Karena Kurang Tepat" required></textarea>
            </div>
            <!-- Input tersembunyi untuk mengirim ID surat -->
            <input type="hidden" id="id" name="id" value="">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-gray" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="action" value="Tolak" class="btn btn-danger mb-0">Kirim</button>
          </div>
        </form>
      </div>
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
    $(document).ready(function() {
      $('#myTable1').DataTable();
    });

    let cropper;
    const profileImageInput = document.getElementById("profile_image");
    const imageToCrop = document.getElementById("imageToCrop");

    profileImageInput.addEventListener("change", function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          if (!imageToCrop) {
            console.error("Element #imageToCrop not found");
            return;
          }

          // Tampilkan gambar ke elemen imageToCrop
          imageToCrop.src = e.target.result;

          // Tampilkan modal crop
          const cropModal = new bootstrap.Modal(document.getElementById("cropModal"));
          cropModal.show();

          // Inisialisasi Cropper.js
          if (cropper) cropper.destroy(); // Hapus instance sebelumnya jika ada
          cropper = new Cropper(imageToCrop, {
            aspectRatio: 1, // 1:1 untuk foto profil
            viewMode: 1,
            responsive: true, // Pastikan Cropper.js menyesuaikan ukuran
            modal: true,
            scalable: true,
            autoCropArea: 1, // Area crop otomatis menyesuaikan
            minContainerWidth: Math.min(window.innerWidth, 500), // Lebar minimal container
            minContainerHeight: Math.min(window.innerHeight, 500), // Tinggi minimal container
          });
        };
        reader.readAsDataURL(file);
      }
    });

    document.getElementById("cropImage").addEventListener("click", function() {
      if (cropper) {
        const canvas = cropper.getCroppedCanvas({
          width: 500, // Resolusi hasil crop
          height: 500,
        });

        const croppedImagePreview = document.getElementById("profileImagePreview");
        canvas.toBlob(function(blob) {
          const croppedUrl = URL.createObjectURL(blob);
          croppedImagePreview.src = croppedUrl;

          const file = new File([blob], "cropped_image.png", {
            type: "image/png"
          });
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(file);
          profileImageInput.files = dataTransfer.files;
        });

        const cropModal = bootstrap.Modal.getInstance(document.getElementById("cropModal"));
        cropModal.hide();
      } else {
        console.error("Cropper is not initialized");
      }
    });

    // Set ID surat pada modal ketika tombol "Tolak" diklik
    const buttonsTolak = document.querySelectorAll('button[data-bs-toggle="modal"][data-bs-target="#modalTolak"]');
    buttonsTolak.forEach(button => {
      button.addEventListener('click', function() {
        const row = button.closest('tr');
        const id = row.querySelector('td').textContent; // Ambil ID dari kolom pertama
        document.getElementById('id').value = id; // Set ID pada input tersembunyi
      });
    });
  </script>
  <?php
  if (isset($logout_script)) {
    echo $logout_script;
  }
  ?>
</body>

</html>