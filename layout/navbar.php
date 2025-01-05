<?php
include 'database.php';
if (isset($_POST['update_profile'])) {
  // Ambil data yang dimasukkan pengguna
  $nama_lengkap = mysqli_real_escape_string($db, $_POST['nama_lengkap']);
  $nim = mysqli_real_escape_string($db, $_POST['nim']);
  $tahun_ajaran = mysqli_real_escape_string($db, $_POST['tahun_ajaran']);
  $semester = mysqli_real_escape_string($db, $_POST['semester']);
  $jurusan = mysqli_real_escape_string($db, $_POST['jurusan']);
  $prodi = mysqli_real_escape_string($db, $_POST['prodi']);

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
                tahun_ajaran = '$tahun_ajaran',
                semester = '$semester', 
                jurusan = '$jurusan', 
                prodi = '$prodi', 
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
  $tahun_ajaran = $profile_data['tahun_ajaran']; // Tahun ajaran
  $semester = $profile_data['semester']; // Tahun ajaran
  $jurusan = $profile_data['jurusan']; // Jurusan
  $prodi = $profile_data['prodi']; // Program studi
} else {
  // Jika data tidak ditemukan, berikan pesan atau lakukan sesuatu
  $profile_data = null;
}

$profile_image = isset($profile_data['profile_image']) ? 'uploads/' . ltrim($profile_data['profile_image'], 'uploads/') : 'uploads/default_profile.svg';
$nama_lengkap = $profile_data['nama_lengkap'] ?? ''; // Nama lengkap default kosong
$nim = $profile_data['nim'] ?? ''; // NIM default kosong
$tahun_ajaran = $profile_data['tahun_ajaran'] ?? ''; // Tahun ajaran default kosong
$semester = $profile_data['semester'] ?? ''; // Semester default kosong
$jurusan = $profile_data['jurusan'] ?? ''; // Jurusan default kosong
$prodi = $profile_data['prodi'] ?? ''; // Program studi default kosong

?>
<nav
  class="navbar navbar-expand-lg navbar-light py-0 sticky-top mb-5"
  style="background-color: #003298">
  <div class="container px-lg-0 px-sm-5">
    <a class="navbar-brand" href="dashboard.php"><img
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
            src="<?php echo $profile_image ?: 'uploads/default_profile.svg'; ?>"
            style="width: 35px; height:35px; border-radius: 50%; border:solid 2px #fff; object-fit:cover;"
            class="me-1" />
          <div style="text-align: left">
            <small class="text-light">
              <?php echo htmlspecialchars($nama_lengkap ?: $_SESSION['username']); ?>
            </small>
            <small class="d-block text-light" style="font-size: 12px">Mahasiswa</small>
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
            <form action="dashboard.php" method="POST">
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
        <small class="d-block text-light" style="font-size: 12px">Mahasiswa</small>
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
        <form action="dashboard.php" method="POST">
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
            <label for="nim">NIM</label>
            <input type="text" class="form-control" id="nim" name="nim" placeholder="Contoh: 4342400867" value="<?php echo htmlspecialchars($nim); ?>" />
          </div>

          <!-- Form untuk Tahun Ajaran -->
          <div class="form-group mt-2">
            <label for="academic_year">Tahun Ajaran</label>
            <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" placeholder="Contoh: 2024-2025" value="<?php echo htmlspecialchars($tahun_ajaran); ?>" />
          </div>

          <!-- Form untuk Jurusan -->
          <div class="form-group mt-2">
            <label for="department">Jurusan</label>
            <input type="text" class="form-control" id="jurusan" name="jurusan" placeholder="Contoh: Informatika" value="<?php echo htmlspecialchars($jurusan); ?>" />
          </div>

          <!-- Form untuk Program Studi -->
          <div class="form-group mt-2">
            <label for="program">Prodi</label>
            <input type="text" class="form-control" id="prodi" name="prodi" placeholder="Contoh: Teknologi Rekayasa Perangkat Lunak" value="<?php echo htmlspecialchars($prodi); ?>" />
          </div>
          <div class="form-group mt-2">
            <label for="program">Semester</label>
            <input type="text" class="form-control" id="prodi" name="semester" placeholder="Contoh: Semester 1" value="<?php echo htmlspecialchars($semester); ?>" />
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

<script>
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
</script>