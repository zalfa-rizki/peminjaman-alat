-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Apr 2026 pada 12.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_peminjaman`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `alat`
--

CREATE TABLE `alat` (
  `id` int(11) NOT NULL,
  `nama_alat` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.jpg',
  `stok` int(11) DEFAULT NULL,
  `status` enum('tersedia','kosong') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `alat`
--

INSERT INTO `alat` (`id`, `nama_alat`, `foto`, `stok`, `status`) VALUES
(21, 'kamera', '1776858723_WhatsApp Image 2026-04-22 at 18.49.51.jpeg', 43, 'tersedia'),
(45, 'senter', '1776858855_WhatsApp Image 2026-04-22 at 18.49.53.jpeg', 24, 'tersedia'),
(62, 'mouse', '1776858846_WhatsApp Image 2026-04-22 at 18.49.52 (2).jpeg', 71, 'tersedia'),
(978, 'powerbank', '1776858828_WhatsApp Image 2026-04-22 at 18.49.52 (1).jpeg', 40, 'tersedia'),
(979, 'proyektor', '1776858811_WhatsApp Image 2026-04-22 at 18.49.53 (1).jpeg', 25, 'tersedia'),
(980, 'keyboard', '1776858123_Screenshot 2026-04-22 170709.png', 50, 'tersedia');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `aksi` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `user_id`, `aksi`, `keterangan`, `tanggal`) VALUES
(107, 8, 'Login', 'User \'bagas\' berhasil login ke sistem.', '2026-04-22 14:23:45'),
(108, 8, 'Logout', 'User \'bagas\' telah keluar dari sistem.', '2026-04-23 00:14:06'),
(109, 3, 'Login', 'User \'peminjam\' berhasil login ke sistem.', '2026-04-23 00:14:16'),
(110, 3, 'Logout', 'User \'peminjam\' telah keluar dari sistem.', '2026-04-23 00:14:34'),
(111, 4, 'Login', 'User \'admin\' berhasil login ke sistem.', '2026-04-23 00:14:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `alat_id` int(11) DEFAULT NULL,
  `tanggal_pinjam` date DEFAULT NULL,
  `tanggal_kembali_seharusnya` date DEFAULT NULL,
  `tanggal_kembali_realitas` date DEFAULT NULL,
  `status_pinjam` enum('pending','disetujui','menunggu_pengembalian','kembali','ditolak') NOT NULL DEFAULT 'pending',
  `denda` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `user_id`, `alat_id`, `tanggal_pinjam`, `tanggal_kembali_seharusnya`, `tanggal_kembali_realitas`, `status_pinjam`, `denda`) VALUES
(1, 6, 21, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(2, 6, 978, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(3, 6, 978, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(4, 6, 978, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(5, 6, 21, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(6, 6, 21, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(7, 6, 978, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(8, 6, 978, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(9, 7, 21, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(10, 3, 978, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(11, 3, 21, '2026-04-09', NULL, '2026-04-09', 'kembali', 102755000),
(12, 8, 978, '2026-04-09', NULL, '2026-04-11', 'kembali', 411060000),
(13, 3, 62, '2026-04-10', NULL, '2026-04-11', 'kembali', 411060000),
(14, 3, 45, '2026-04-10', NULL, '2026-04-11', 'kembali', 411060000),
(15, 3, 45, '2026-04-10', NULL, '2026-04-11', 'kembali', 411060000),
(16, 8, 62, '2026-04-10', NULL, '2026-04-11', 'kembali', 411060000),
(17, 3, 21, '2026-04-11', '2026-04-16', '2026-04-11', 'kembali', 0),
(18, 3, 45, '2026-04-11', '2026-04-22', '2026-04-11', 'kembali', 0),
(19, 3, 21, '2026-04-11', '2026-04-14', '2026-04-11', 'kembali', 0),
(20, 6, 62, '2026-04-11', '2026-04-12', '2026-04-11', 'kembali', 0),
(21, 3, 978, '2026-04-11', '2026-04-15', '2026-04-13', 'kembali', 0),
(22, 3, 978, '2026-04-11', '2026-04-22', '2026-04-13', 'kembali', 0),
(23, 9, 45, '2026-04-11', '2026-04-22', NULL, 'pending', 0),
(24, 9, 62, '2026-04-11', '2026-04-12', NULL, 'pending', 0),
(25, 9, 21, '2026-04-12', '2026-04-13', '2026-04-13', 'kembali', 0),
(26, 9, 62, '2026-04-13', '2026-04-14', '2026-04-15', 'kembali', 20000),
(27, 9, 62, '2026-04-13', '2026-04-14', '2026-04-15', 'kembali', 20000),
(28, 8, 45, '2026-04-14', '2026-04-15', NULL, 'pending', 0),
(29, 7, 21, '2026-04-14', '2026-04-15', '2026-04-15', 'kembali', 0),
(30, 6, 978, '2026-04-14', '2026-04-15', '2026-04-15', 'kembali', 0),
(31, 8, 45, '2026-04-08', '2026-04-10', '2026-04-15', 'kembali', 100000),
(32, 9, 21, '2026-04-16', '2026-04-24', '2026-04-16', 'kembali', 0),
(34, 3, 21, '2026-04-12', '2026-04-13', '2026-04-16', 'kembali', 60000),
(35, 6, 62, '2026-04-16', '2026-04-21', '2026-04-16', 'kembali', 0),
(36, 3, 21, '2026-04-12', '2026-04-13', '2026-04-16', 'kembali', 60000),
(37, 3, 21, '2026-04-16', '2026-04-17', '2026-04-16', 'kembali', 0),
(38, 7, 979, '2026-04-16', '2026-04-17', NULL, 'pending', 0),
(39, 9, 980, '2026-04-21', '2026-04-22', NULL, 'pending', 0),
(40, 20, 979, '2026-04-22', '2026-04-23', '2026-04-22', 'kembali', 0),
(41, 20, 21, '2026-04-22', '2026-04-27', NULL, 'disetujui', 0),
(42, 8, 62, '2026-04-22', '2026-04-23', NULL, 'pending', 0),
(43, 8, 45, '2026-04-22', '2026-04-28', NULL, 'pending', 0),
(44, 19, 978, '2026-04-22', '2026-05-01', '2026-04-22', 'kembali', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','petugas','peminjam') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(3, 'peminjam', '$2y$10$J1OgRIkSzrJIAlOlrp6qHOUJdlGhYb1MFyn2IneDMpNu37LP5py/C', 'peminjam'),
(4, 'admin', '$2y$10$YNSIW/sQsbJx99aXiU0SAeVC9edhGuFw5eBV0Hr.cMgzZ8MFJ.l3S', 'admin'),
(5, 'petugas', '$2y$10$lhTsFeA9pP8NVugqgbkjuutHxcnvm5kTxZZE6Wn/eDRloUIBXk.UK', 'petugas'),
(6, 'ilham', '$2y$10$rAv3EDoZvpTZBVzetwGGxOPE5u4TM8.AO6Xev2At6//74/qd5cYjy', 'peminjam'),
(7, 'andika', '$2y$10$28yup6j6GdXQtirvaYD/l.jbS3412xVHJugWvo5qXFR0k4OxlfBES', 'peminjam'),
(8, 'bagas', '$2y$10$5h77yq.lLUxh925PtDX8jOMzSMS/LrEFGiSrPpOS/Oy1Ka3Yr.fX.', 'peminjam'),
(9, 'herman', '$2y$10$zIlJooFvWU1ShJmL5R2RhePcRzfLWYY.knkJ7Fx65jRUkZS/OZg02', 'peminjam'),
(19, 'kelly', '$2y$10$UsNi5XMgsbasrzy3YA6P5e9TVitHJ8Hp52v/zm0cYfM19TQb9CmEW', 'peminjam'),
(20, 'arjuna', '$2y$10$ba7xEMKILA8wXYC54jUOMexPDcq84zrpPtPqvwidLARoAeVi7VAs2', 'peminjam');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `alat_id` (`alat_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `alat`
--
ALTER TABLE `alat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=981;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`alat_id`) REFERENCES `alat` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
