-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2024 at 06:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `otp_lupa`
--

CREATE TABLE `otp_lupa` (
  `nomor` varchar(50) NOT NULL,
  `otp` varchar(50) NOT NULL,
  `waktu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp_register`
--

CREATE TABLE `otp_register` (
  `nomor` varchar(50) NOT NULL,
  `otp` varchar(50) NOT NULL,
  `waktu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_register`
--

INSERT INTO `otp_register` (`nomor`, `otp`, `waktu`) VALUES
('6283161579431', '196056', '1734870458');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
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
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`user_id`, `profile_image`, `nama_lengkap`, `nim`, `tahun_ajaran`, `semester`, `jurusan`, `prodi`) VALUES
(7, 'uploads/1734868720_6767fef059dbc.png', 'Fasha Ar-Rafly', '4342411071', '2024-2025', 'Semester 1', 'Informatika', 'Teknologi Rekayasa Perangkat Lunak'),
(13, 'uploads/1735070256_676b12307498d.png', 'Gojo Satoru', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `surat`
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
-- Dumping data for table `surat`
--

INSERT INTO `surat` (`id`, `user_id`, `nama`, `nim`, `tahun_ajaran`, `jurusan`, `prodi`, `semester`, `alasan`, `jenis_surat`, `status_surat`, `alasan_penolakan`, `tanggal_buat`) VALUES
(69, 7, 'Fasha Ar-Rafly', '4342411071', '2024-2025', 'Informatika', 'Teknologi Rekayasa Perangkat Lunak', 'Semester 1', 'asdasd', 'SKM', 'Pending', '', '2024-12-25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nomor`, `created_at`, `role`, `remember_token`) VALUES
(7, 'fshaarfly', 'bb4c2a154307e4f64212bea38c8ea47834148dd07c8e5378723014b8202886a4', '6283161579431', '2024-12-22 04:25:35', 0, '4df0112436ae1c88d9f47777ea6da968'),
(14, 'admin123', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', '083161579431', '2024-12-25 03:35:00', 1, 'de6a390a72281a240ace70b7001779fe');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `surat`
--
ALTER TABLE `surat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `surat`
--
ALTER TABLE `surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `surat`
--
ALTER TABLE `surat`
  ADD CONSTRAINT `surat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
