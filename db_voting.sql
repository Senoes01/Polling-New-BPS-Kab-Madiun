-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Agu 2026 pada 03.13
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_voting`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`) VALUES
(1, 'admin', '$2y$12$s0gNbejw5Th9XJ4.aMI3X.clZzV3DSWLy20JAxz74iOZLNc4AgNye');

-- --------------------------------------------------------

--
-- Struktur dari tabel `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `code` char(1) NOT NULL,
  `name` varchar(150) NOT NULL,
  `position` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `candidates` 
--

INSERT INTO `candidates` (`id`, `code`, `name`, `position`) VALUES
(1, 'A', 'Aditya Chandra Yudistra, SE', NULL),
(2, 'B', 'Iama\'adi, S.Mn.', NULL),
(3, 'C', 'Elisabet Tri Laksmi, SST., MM', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `indicators`
--

CREATE TABLE `indicators` (
  `id` int(11) NOT NULL,
  `category_no` tinyint(4) NOT NULL,
  `category` varchar(100) NOT NULL,
  `focus` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `indicators`
--

INSERT INTO `indicators` (`id`, `category_no`, `category`, `focus`, `code`, `name`, `description`) VALUES
(1, 1, 'BRAIN (Kinerja & Inovasi)', 'Kualitas kerja, penguasaan tugas, dan daya cipta.', '1.1', 'Kompetensi Teknis', 'Sejauh mana kandidat menguasai bidang tugasnya dan menjadi rujukan/tempat bertanya saat rekan kerja menemui kendala?'),
(2, 1, 'BRAIN (Kinerja & Inovasi)', 'Kualitas kerja, penguasaan tugas, dan daya cipta.', '1.2', 'Inovasi & Inisiatif', 'Sejauh mana kandidat memberikan ide kreatif, cara kerja baru, atau solusi digital yang mempermudah pekerjaan di Satker?'),
(3, 1, 'BRAIN (Kinerja & Inovasi)', 'Kualitas kerja, penguasaan tugas, dan daya cipta.', '1.3', 'Problem Solving', 'Sejauh mana kandidat mampu berpikir objektif, tenang, dan memberikan solusi efektif saat menghadapi tekanan/krisis pekerjaan?'),
(4, 2, 'BEAUTY (Komunikasi, Citra Diri & Pengaruh Positif)', 'Soft skill, keteladanan sikap, dan energi positif di tempat kerja.', '2.1', 'Komunikasi Efektif', 'Sejauh mana kandidat mampu menyampaikan gagasan secara jelas, santun, persuasif, serta mau mendengarkan masukan orang lain?'),
(5, 2, 'BEAUTY (Komunikasi, Citra Diri & Pengaruh Positif)', 'Soft skill, keteladanan sikap, dan energi positif di tempat kerja.', '2.2', 'Role Model & Profesionalisme', 'Sejauh mana kandidat menampilkan sikap profesional, percaya diri, rapi, dan menjaga citra positif institusi?'),
(6, 2, 'BEAUTY (Komunikasi, Citra Diri & Pengaruh Positif)', 'Soft skill, keteladanan sikap, dan energi positif di tempat kerja.', '2.3', 'Daya Pengaruh Positif', 'Sejauh mana kehadiran kandidat mampu memberikan motivasi, inspirasi, dan membangun suasana kerja yang menyenangkan?'),
(7, 3, 'BEHAVIOR (Integritas, Kolaborasi, & Pelayanan)', 'Kesesuaian perilaku Core Values BerAKHLAK dan Budaya Kerja BPS', '3.1', 'Integritas & Kedisiplinan', 'Sejauh mana kandidat menunjukkan keselarasan antara ucapan dan tindakan, jujur, bertanggung jawab, serta tepat waktu?'),
(8, 3, 'BEHAVIOR (Integritas, Kolaborasi, & Pelayanan)', 'Kesesuaian perilaku dengan Core Values BerAKHLAK dan Budaya Kerja BPS', '3.2', 'Kolaborasi & Kerjasama Tim', 'Sejauh mana kandidat aktif membantu rekan kerja tanpa membeda-bedakan dan mengedepankan kepentingan tim/Satker?'),
(9, 3, 'BEHAVIOR (Integritas, Kolaborasi, & Pelayanan)', 'Kesesuaian perilaku dengan Core Values BerAKHLAK dan Budaya Kerja BPS', '3.3', 'Orientation to Service', 'Sejauh mana kandidat bersikap ramah, empati, dan resposif dalam memberikan pelayanan baik internal maupun kepada mitra/pengguna data?'),
(10, 4, 'KETELADANAN UMUM (Rekomendasi Akhir)', 'Penilaian kelayakan menyeluruh sebagai wujud Insan Statistik Teladan', '4.1', 'Kelayakan Keteladanan', 'Secara keseluruhan, seberapa layak kandidat ini menjadi representasi/sosok \'Insan Statistik Teladan\' yang membawa nama baik Satker?');

-- --------------------------------------------------------

--
-- Struktur dari tabel `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `nama_penilai` varchar(120) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `catatan_tambahan` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `polls`
--

INSERT INTO `polls` (`id`, `nama_penilai`, `nip`, `catatan_tambahan`, `submitted_at`) VALUES
(1, 'Bella', '1234567890', 'hallo okey noted', '2026-08-12 03:27:23'),
(2, 'Azzara', '1234', 'siap pak', '2026-08-12 07:51:05'),
(3, 'putri', '123', 'okk', '2026-08-12 08:09:32'),
(4, 'kau', '43566', 'okkk', '2026-08-12 08:22:44'),
(5, 'ikk', '999', 'oke gas', '2026-08-12 08:30:13'),
(6, 'wizaaa', '11111', '', '2026-08-12 08:45:36'),
(7, 'mumet', '99999', '', '2026-08-12 08:57:27'),
(8, 'sss', '333333333', '', '2026-08-12 09:00:11'),
(9, 'rrr', '1233', 'kkk', '2026-08-13 00:55:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `indicator_id` int(11) NOT NULL,
  `score` tinyint(4) NOT NULL
) ;

--
-- Dumping data untuk tabel `ratings`
--

INSERT INTO `ratings` (`id`, `poll_id`, `candidate_id`, `indicator_id`, `score`) VALUES
(1, 1, 1, 1, 3),
(2, 1, 1, 2, 2),
(3, 1, 1, 3, 3),
(4, 1, 1, 4, 4),
(5, 1, 1, 5, 2),
(6, 1, 1, 6, 3),
(7, 1, 1, 7, 5),
(8, 1, 1, 8, 2),
(9, 1, 1, 9, 4),
(10, 1, 1, 10, 4),
(11, 1, 2, 1, 1),
(12, 1, 2, 2, 4),
(13, 1, 2, 3, 2),
(14, 1, 2, 4, 2),
(15, 1, 2, 5, 3),
(16, 1, 2, 6, 3),
(17, 1, 2, 7, 3),
(18, 1, 2, 8, 4),
(19, 1, 2, 9, 2),
(20, 1, 2, 10, 2),
(21, 1, 3, 1, 3),
(22, 1, 3, 2, 2),
(23, 1, 3, 3, 3),
(24, 1, 3, 4, 2),
(25, 1, 3, 5, 2),
(26, 1, 3, 6, 4),
(27, 1, 3, 7, 1),
(28, 1, 3, 8, 3),
(29, 1, 3, 9, 2),
(30, 1, 3, 10, 4),
(31, 2, 1, 1, 2),
(32, 2, 1, 2, 3),
(33, 2, 1, 3, 4),
(34, 2, 1, 4, 1),
(35, 2, 1, 5, 5),
(36, 2, 1, 6, 2),
(37, 2, 1, 7, 5),
(38, 2, 1, 8, 4),
(39, 2, 1, 9, 4),
(40, 2, 1, 10, 4),
(41, 2, 2, 1, 2),
(42, 2, 2, 2, 2),
(43, 2, 2, 3, 5),
(44, 2, 2, 4, 4),
(45, 2, 2, 5, 2),
(46, 2, 2, 6, 3),
(47, 2, 2, 7, 2),
(48, 2, 2, 8, 2),
(49, 2, 2, 9, 2),
(50, 2, 2, 10, 2),
(51, 2, 3, 1, 1),
(52, 2, 3, 2, 1),
(53, 2, 3, 3, 1),
(54, 2, 3, 4, 2),
(55, 2, 3, 5, 2),
(56, 2, 3, 6, 2),
(57, 2, 3, 7, 1),
(58, 2, 3, 8, 1),
(59, 2, 3, 9, 3),
(60, 2, 3, 10, 3),
(61, 3, 1, 1, 2),
(62, 3, 1, 2, 4),
(63, 3, 1, 3, 3),
(64, 3, 1, 4, 3),
(65, 3, 1, 5, 3),
(66, 3, 1, 6, 3),
(67, 3, 1, 7, 3),
(68, 3, 1, 8, 3),
(69, 3, 1, 9, 3),
(70, 3, 1, 10, 4),
(71, 3, 2, 1, 3),
(72, 3, 2, 2, 3),
(73, 3, 2, 3, 3),
(74, 3, 2, 4, 5),
(75, 3, 2, 5, 2),
(76, 3, 2, 6, 3),
(77, 3, 2, 7, 1),
(78, 3, 2, 8, 3),
(79, 3, 2, 9, 4),
(80, 3, 2, 10, 2),
(81, 3, 3, 1, 1),
(82, 3, 3, 2, 2),
(83, 3, 3, 3, 2),
(84, 3, 3, 4, 1),
(85, 3, 3, 5, 4),
(86, 3, 3, 6, 3),
(87, 3, 3, 7, 3),
(88, 3, 3, 8, 1),
(89, 3, 3, 9, 3),
(90, 3, 3, 10, 1),
(91, 4, 1, 1, 3),
(92, 4, 1, 2, 4),
(93, 4, 1, 3, 3),
(94, 4, 1, 4, 4),
(95, 4, 1, 5, 4),
(96, 4, 1, 6, 2),
(97, 4, 1, 7, 2),
(98, 4, 1, 8, 3),
(99, 4, 1, 9, 4),
(100, 4, 1, 10, 5),
(101, 4, 2, 1, 2),
(102, 4, 2, 2, 1),
(103, 4, 2, 3, 2),
(104, 4, 2, 4, 2),
(105, 4, 2, 5, 3),
(106, 4, 2, 6, 5),
(107, 4, 2, 7, 4),
(108, 4, 2, 8, 4),
(109, 4, 2, 9, 5),
(110, 4, 2, 10, 5),
(111, 4, 3, 1, 1),
(112, 4, 3, 2, 1),
(113, 4, 3, 3, 1),
(114, 4, 3, 4, 1),
(115, 4, 3, 5, 4),
(116, 4, 3, 6, 1),
(117, 4, 3, 7, 2),
(118, 4, 3, 8, 1),
(119, 4, 3, 9, 1),
(120, 4, 3, 10, 1),
(121, 5, 1, 1, 5),
(122, 5, 1, 2, 4),
(123, 5, 1, 3, 4),
(124, 5, 1, 4, 1),
(125, 5, 1, 5, 1),
(126, 5, 1, 6, 3),
(127, 5, 1, 7, 2),
(128, 5, 1, 8, 3),
(129, 5, 1, 9, 3),
(130, 5, 1, 10, 4),
(131, 5, 2, 1, 1),
(132, 5, 2, 2, 2),
(133, 5, 2, 3, 4),
(134, 5, 2, 4, 3),
(135, 5, 2, 5, 4),
(136, 5, 2, 6, 3),
(137, 5, 2, 7, 3),
(138, 5, 2, 8, 4),
(139, 5, 2, 9, 5),
(140, 5, 2, 10, 1),
(141, 5, 3, 1, 3),
(142, 5, 3, 2, 3),
(143, 5, 3, 3, 2),
(144, 5, 3, 4, 2),
(145, 5, 3, 5, 2),
(146, 5, 3, 6, 3),
(147, 5, 3, 7, 4),
(148, 5, 3, 8, 2),
(149, 5, 3, 9, 3),
(150, 5, 3, 10, 2),
(151, 6, 1, 1, 1),
(152, 6, 1, 2, 2),
(153, 6, 1, 3, 3),
(154, 6, 1, 4, 5),
(155, 6, 1, 5, 3),
(156, 6, 1, 6, 3),
(157, 6, 1, 7, 5),
(158, 6, 1, 8, 4),
(159, 6, 1, 9, 4),
(160, 6, 1, 10, 3),
(161, 6, 2, 1, 4),
(162, 6, 2, 2, 4),
(163, 6, 2, 3, 2),
(164, 6, 2, 4, 4),
(165, 6, 2, 5, 4),
(166, 6, 2, 6, 3),
(167, 6, 2, 7, 4),
(168, 6, 2, 8, 3),
(169, 6, 2, 9, 4),
(170, 6, 2, 10, 1),
(171, 6, 3, 1, 2),
(172, 6, 3, 2, 3),
(173, 6, 3, 3, 4),
(174, 6, 3, 4, 4),
(175, 6, 3, 5, 3),
(176, 6, 3, 6, 3),
(177, 6, 3, 7, 4),
(178, 6, 3, 8, 3),
(179, 6, 3, 9, 3),
(180, 6, 3, 10, 1),
(181, 7, 1, 1, 4),
(182, 7, 1, 2, 4),
(183, 7, 1, 3, 5),
(184, 7, 1, 4, 5),
(185, 7, 1, 5, 5),
(186, 7, 1, 6, 4),
(187, 7, 1, 7, 5),
(188, 7, 1, 8, 5),
(189, 7, 1, 9, 4),
(190, 7, 1, 10, 4),
(191, 7, 2, 1, 3),
(192, 7, 2, 2, 4),
(193, 7, 2, 3, 3),
(194, 7, 2, 4, 2),
(195, 7, 2, 5, 2),
(196, 7, 2, 6, 2),
(197, 7, 2, 7, 2),
(198, 7, 2, 8, 2),
(199, 7, 2, 9, 4),
(200, 7, 2, 10, 3),
(201, 7, 3, 1, 1),
(202, 7, 3, 2, 2),
(203, 7, 3, 3, 2),
(204, 7, 3, 4, 3),
(205, 7, 3, 5, 2),
(206, 7, 3, 6, 1),
(207, 7, 3, 7, 2),
(208, 7, 3, 8, 2),
(209, 7, 3, 9, 2),
(210, 7, 3, 10, 2),
(211, 8, 1, 1, 3),
(212, 8, 1, 2, 4),
(213, 8, 1, 3, 4),
(214, 8, 1, 4, 4),
(215, 8, 1, 5, 4),
(216, 8, 1, 6, 4),
(217, 8, 1, 7, 3),
(218, 8, 1, 8, 4),
(219, 8, 1, 9, 4),
(220, 8, 1, 10, 5),
(221, 8, 2, 1, 2),
(222, 8, 2, 2, 3),
(223, 8, 2, 3, 2),
(224, 8, 2, 4, 3),
(225, 8, 2, 5, 2),
(226, 8, 2, 6, 5),
(227, 8, 2, 7, 5),
(228, 8, 2, 8, 4),
(229, 8, 2, 9, 3),
(230, 8, 2, 10, 5),
(231, 8, 3, 1, 2),
(232, 8, 3, 2, 2),
(233, 8, 3, 3, 3),
(234, 8, 3, 4, 2),
(235, 8, 3, 5, 2),
(236, 8, 3, 6, 2),
(237, 8, 3, 7, 3),
(238, 8, 3, 8, 3),
(239, 8, 3, 9, 2),
(240, 8, 3, 10, 1),
(241, 9, 1, 1, 5),
(242, 9, 1, 2, 3),
(243, 9, 1, 3, 3),
(244, 9, 1, 4, 3),
(245, 9, 1, 5, 3),
(246, 9, 1, 6, 4),
(247, 9, 1, 7, 4),
(248, 9, 1, 8, 3),
(249, 9, 1, 9, 5),
(250, 9, 1, 10, 5),
(251, 9, 2, 1, 3),
(252, 9, 2, 2, 3),
(253, 9, 2, 3, 5),
(254, 9, 2, 4, 3),
(255, 9, 2, 5, 3),
(256, 9, 2, 6, 4),
(257, 9, 2, 7, 3),
(258, 9, 2, 8, 3),
(259, 9, 2, 9, 4),
(260, 9, 2, 10, 1),
(261, 9, 3, 1, 2),
(262, 9, 3, 2, 2),
(263, 9, 3, 3, 3),
(264, 9, 3, 4, 2),
(265, 9, 3, 5, 2),
(266, 9, 3, 6, 3),
(267, 9, 3, 7, 3),
(268, 9, 3, 8, 2),
(269, 9, 3, 9, 3),
(270, 9, 3, 10, 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indeks untuk tabel `indicators`
--
ALTER TABLE `indicators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indeks untuk tabel `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- Indeks untuk tabel `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rating` (`poll_id`,`candidate_id`,`indicator_id`),
  ADD KEY `fk_rating_candidate` (`candidate_id`),
  ADD KEY `fk_rating_indicator` (`indicator_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `indicators`
--
ALTER TABLE `indicators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_rating_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rating_indicator` FOREIGN KEY (`indicator_id`) REFERENCES `indicators` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rating_poll` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
