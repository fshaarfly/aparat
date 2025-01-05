<?php
session_start();
include 'database.php';


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
    <link href="css/styles.css" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
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

<body>
    <div class="bg">
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
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-2 small fw-bolder gap-2">
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
                            Ayo Mulai Validasi Surat Mahasiswa!
                        </h5>
                        <div
                            class="d-grid gap-3 d-sm-flex justify-content-center justify-content-lg-start">
                            <a
                                class="btn btn-primary btn-lg px-5 py-3 me-sm-3 fs-7 fw-semibold"
                                href="validasi_dosen.php">Mulai Validasi</a>
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
                </div>
                <div class="col-xxl-4 text-center">
                    <a href="validasi_dosen.php">
                        <p class="my-2 mt-4" style="color: #fff;">Validasi</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
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
    <?php
    if (isset($logout_script)) {
        echo $logout_script;
    }
    ?>
</body>

</html>