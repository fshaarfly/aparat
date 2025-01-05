-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Jan 2025 pada 00.09
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aparat`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `otp_lupa`
--

CREATE TABLE `otp_lupa` (
  `nomor` varchar(50) NOT NULL,
  `otp` varchar(50) NOT NULL,
  `waktu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `otp_register`
--

CREATE TABLE `otp_register` (
  `nomor` varchar(50) NOT NULL,
  `otp` varchar(50) NOT NULL,
  `waktu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `otp_register`
--

INSERT INTO `otp_register` (`nomor`, `otp`, `waktu`) VALUES
('6283161579431', '196056', '1734870458'),
('hahhaahha', '271116', '1736062525');

-- --------------------------------------------------------

--
-- Struktur dari tabel `profile`
--

CREATE TABLE `profile` (
  `user_id` int(11) NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `nim` varchar(50) NOT NULL,
  `tahun_ajaran` varchar(50) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `prodi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profile`
--

INSERT INTO `profile` (`user_id`, `profile_image`, `nama_lengkap`, `nim`, `tahun_ajaran`, `semester`, `jurusan`, `prodi`) VALUES
(7, 'uploads/1734868720_6767fef059dbc.png', 'Fasha Ar-Rafly', '4342411071', '2024-2025', 'Semester 1', 'Informatika', 'Teknologi Rekayasa Perangkat Lunak'),
(13, 'uploads/1735070256_676b12307498d.png', 'Gojo Satoru', '', '', '', '', ''),
(15, 'uploads/1736064994_677a3fe2a48e9.png', 'Kharlos Daylo Saut Silaban', '4342411073', '2024', '1', 'Teknik Informatika', 'TRPL');

-- --------------------------------------------------------

--
-- Struktur dari tabel `surat`
--

CREATE TABLE `surat` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nim` varchar(50) NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `prodi` varchar(100) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `alasan` text NOT NULL,
  `jenis_surat` varchar(100) NOT NULL,
  `status_surat` varchar(50) NOT NULL,
  `alasan_penolakan` text NOT NULL,
  `tanggal_buat` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `surat`
--

INSERT INTO `surat` (`id`, `user_id`, `nama`, `nim`, `tahun_ajaran`, `jurusan`, `prodi`, `semester`, `alasan`, `jenis_surat`, `status_surat`, `alasan_penolakan`, `tanggal_buat`) VALUES
(69, 7, 'Fasha Ar-Rafly', '4342411071', '2024-2025', 'Informatika', 'Teknologi Rekayasa Perangkat Lunak', 'Semester 1', 'asdasd', 'SKM', 'Ditolak', 'alasan kamu kurang meyakinkan', '2024-12-25'),
(70, 7, 'Fasha Ar-Rafly', '4342411071', '2024-2025', 'Informatika', 'Teknologi Rekayasa Perangkat Lunak', 'Semester 1', 'ASDASDASD', 'SCA', 'Diterima', '', '2025-01-04'),
(71, 15, 'kharlos daylo saut silaban', '4342411073', '2024', 'Teknik Informatika', 'TRPL', '1', 'surat keterangan Mahasiswa', 'SKM', 'Diterima', '', '2025-01-04'),
(74, 17, 'nurul khotimah', '4342411066', '2024-2025', 'informatika', 'TRPL', 'Semester 1', 'mahasiswa', 'SKM', 'Diterima', '', '2025-01-04'),
(75, 17, 'nurul khotimah', '4342411066', '2024-2025', 'informatika', 'TRPL', 'Semester 1', 'mahasiswa', 'SCA', 'Diterima', '', '2025-01-04'),
(76, 17, 'nurul khotimah', '4342411066', '2024-2025', 'informatika', 'TRPL', 'Semester 1', 'mahasiswa', 'TAS', 'Ditolak', 'Alasan dan keaadaan yang kamu sampaikan tidak sesuai', '2025-01-04'),
(77, 17, 'nurul khotimah', '4342411066', '2024-2025', 'informatika', 'TRPL', 'Semester 1', 'mahasiswa', 'LKA', 'Diterima', '', '2025-01-04'),
(78, 15, 'kharlos daylo saut silaban', '4342411073', '2024', 'Teknik Informatika', 'TRPL', '1', 'surat keterangan Mahasiswa', 'SKM', 'Ditolak', 'Kamu sudah memberikan surat yang sama sebelum nya dalam kurung waktu yang dekat\r\n', '2025-01-05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nomor` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `role` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nomor`, `created_at`, `role`, `remember_token`) VALUES
(7, 'fshaarfly', 'bb4c2a154307e4f64212bea38c8ea47834148dd07c8e5378723014b8202886a4', '6283161579431', '2024-12-22 04:25:35', 0, 'faea4bdae28d6528022ec7b17eae22e9'),
(14, 'admin123', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', '083161579431', '2024-12-25 03:35:00', 1, '60c2831dc7cc0b1aace8afd735ac963d'),
(15, 'kharlos', '1a13baa0becc061d3887ed48bdc33f0ce119f47670a79f2fe7717db34e46e814', '0895362070050', '2025-01-04 07:02:11', 0, NULL),
(17, 'nurulkhotimah', '7776dffa87516251c0d1bb1a321c0d5c22e30cea75a3db1018218c621f6334c4', '082174623843', '2025-01-04 23:06:08', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `surat`
--
ALTER TABLE `surat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `surat`
--
ALTER TABLE `surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `surat`
--
ALTER TABLE `surat`
  ADD CONSTRAINT `surat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
