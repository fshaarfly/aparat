<?php
session_start();
include 'database.php';

// Pastikan pengguna login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data dari form
$nama_lengkap = $_POST['nama_lengkap'] ?? '';
$nim = $_POST['nim'] ?? '';
$tahun_ajaran = $_POST['tahun_ajaran'] ?? '';
$semester = $_POST['semester'] ?? '';
$jurusan = $_POST['jurusan'] ?? '';
$prodi = $_POST['prodi'] ?? '';


// Ambil data profil lama
$query = "SELECT * FROM profile WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$current_profile = $result->num_rows > 0 ? $result->fetch_assoc() : null;
$current_profile_image = $current_profile ? $current_profile['profile_image'] : '';

// Fungsi upload gambar yang diperbaiki
function uploadImage($file, $dir = "uploads/")
{
  if (empty($file['name'])) {
    return null; // Kembalikan null jika tidak ada file
  }

  $max_size = 2 * 1024 * 1024; // 2MB
  $valid_extensions = ['jpg', 'jpeg', 'png'];

  if ($file['size'] > $max_size) {
    throw new Exception("Ukuran file terlalu besar. Maksimal 2 MB.");
  }

  $image_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if (!in_array($image_extension, $valid_extensions)) {
    throw new Exception("Ekstensi gambar tidak valid. Hanya JPG, JPEG, atau PNG.");
  }

  // Generate unique filename
  $image_name = time() . '_' . uniqid() . '.' . $image_extension;
  $target_file = $dir . $image_name;

  // Pindahkan file
  if (!move_uploaded_file($file['tmp_name'], $target_file)) {
    throw new Exception("Gagal mengupload file.");
  }

  return $target_file;
}

// Proses upload gambar
try {
  // Cek apakah ada file gambar baru
  if (!empty($_FILES['profile_image']['name'])) {
    $profile_image = uploadImage($_FILES['profile_image']);
  } else {
    // Gunakan gambar lama jika tidak ada gambar baru
    $profile_image = $current_profile_image;
  }

  // Persiapkan query update atau insert
  if ($current_profile) {
    $query = "UPDATE profile SET 
              profile_image = ?, 
              nama_lengkap = ?, 
              nim = ?, 
              tahun_ajaran = ?,
              semester = ?, 
              jurusan = ?, 
              prodi = ? 
              WHERE user_id = ?";
  } else {
    $query = "INSERT INTO profile 
              (profile_image, nama_lengkap, nim, tahun_ajaran, semester, jurusan, prodi, user_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
  }

  // Siapkan statement
  $stmt = $db->prepare($query);

  // Bind parameter dengan tipe data yang sesuai
  $stmt->bind_param(
    'sssssssi',
    $profile_image,
    $nama_lengkap,
    $nim,
    $tahun_ajaran,
    $semester,
    $jurusan,
    $prodi,
    $user_id
  );

  // Eksekusi query
  if ($stmt->execute()) {
    echo "<script>
            alert('Profil berhasil diperbarui.');
            window.location.href = 'dashboard.php';
          </script>";
    exit();
  } else {
    throw new Exception("Gagal memperbarui profil: " . $stmt->error);
  }
} catch (Exception $e) {
  // Tangani kesalahan
  echo "<script>
          alert('" . $e->getMessage() . "');
          window.location.href = 'dashboard.php';
        </script>";
  exit();
}
