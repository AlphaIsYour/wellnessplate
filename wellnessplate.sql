-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 10, 2025 at 09:59 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wellnessplate`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_resep_kondisi` (IN `p_id_resep` VARCHAR(10), IN `p_new_kondisi` VARCHAR(10))   BEGIN     DECLARE old_kondisi VARCHAR(10);
    SELECT id_kondisi INTO old_kondisi     FROM resep     WHERE id_resep = p_id_resep;
    UPDATE resep     SET id_kondisi = p_new_kondisi     WHERE id_resep = p_id_resep;
    UPDATE resep_kondisi     SET id_kondisi = p_new_kondisi     WHERE id_resep = p_id_resep     AND id_kondisi = old_kondisi;  END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `update_resep_kondisi` (`p_id_resep` VARCHAR(10)) RETURNS VARCHAR(10) CHARSET utf8mb4 DETERMINISTIC READS SQL DATA BEGIN
    DECLARE kondisi_id VARCHAR(10);
    
    
    SELECT rk.id_kondisi
    INTO kondisi_id
    FROM resep_kondisi rk
    WHERE rk.id_resep = p_id_resep
    LIMIT 1;
    
    
    RETURN kondisi_id;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `update_resep_tags` (`p_id_resep` VARCHAR(10)) RETURNS TEXT CHARSET utf8mb4 DETERMINISTIC READS SQL DATA BEGIN
    DECLARE tag_list TEXT;
    
    SELECT GROUP_CONCAT(t.nama_tag ORDER BY t.nama_tag ASC SEPARATOR ', ')
    INTO tag_list
    FROM resep_tags rt
    JOIN tags t ON rt.id_tag = t.id_tag
    WHERE rt.id_resep = p_id_resep;
    
    RETURN COALESCE(tag_list, '');
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` varchar(10) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama`, `email`) VALUES
('1', 'hasana', '12345678', 'hasana', 'hasana@gmail.com'),
('ADM9781391', 'Hasan', 'hasan', 'Hasan Nasgore', 'hasan@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `bahan`
--

CREATE TABLE `bahan` (
  `id_bahan` varchar(10) NOT NULL,
  `nama_bahan` varchar(100) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bahan`
--

INSERT INTO `bahan` (`id_bahan`, `nama_bahan`, `satuan`) VALUES
('BHNA09F1B0', 'Duren', 'buah');

-- --------------------------------------------------------

--
-- Table structure for table `gizi`
--

CREATE TABLE `gizi` (
  `id_gizi` varchar(10) NOT NULL,
  `id_resep` varchar(10) DEFAULT NULL,
  `kalori` int DEFAULT NULL,
  `protein` int DEFAULT NULL,
  `karbohidrat` int DEFAULT NULL,
  `lemak` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gizi_resep`
--

CREATE TABLE `gizi_resep` (
  `id_gizi_resep` varchar(10) NOT NULL,
  `id_resep` varchar(10) NOT NULL,
  `kalori` decimal(10,2) DEFAULT NULL,
  `protein` decimal(10,2) DEFAULT NULL,
  `karbohidrat` decimal(10,2) DEFAULT NULL,
  `lemak` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kondisi_kesehatan`
--

CREATE TABLE `kondisi_kesehatan` (
  `id_kondisi` varchar(10) NOT NULL,
  `nama_kondisi` varchar(100) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `deskripsi` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kondisi_kesehatan`
--

INSERT INTO `kondisi_kesehatan` (`id_kondisi`, `nama_kondisi`, `slug`, `deskripsi`) VALUES
('K01', 'Panas Dalam', 'panas_dalam', 'Ya panas Dalam'),
('KND1629A19', 'Perut Mual', 'perut_mual', 'Perut Mual');

-- --------------------------------------------------------

--
-- Table structure for table `resep`
--

CREATE TABLE `resep` (
  `id_resep` varchar(10) NOT NULL,
  `id_admin` varchar(10) DEFAULT NULL,
  `id_kondisi` varchar(10) DEFAULT NULL,
  `nama_resep` varchar(100) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `image` varchar(100) NOT NULL,
  `tags` text,
  `cara_buat` text,
  `tanggal_dibuat` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resep`
--

INSERT INTO `resep` (`id_resep`, `id_admin`, `id_kondisi`, `nama_resep`, `deskripsi`, `image`, `tags`, `cara_buat`, `tanggal_dibuat`) VALUES
('1', 'ADM9781391', 'K01', 'Salad Ayam Panggang Rendah Kalori', 'Salad segar dengan potongan ayam panggang tanpa kulit, sayuran hijau, tomat ceri, dan dressing lemon rendah lemak. Pilihan tepat untuk diet dan menjaga gula darah. Sangat direkomendasikan bagi Anda yang aktif.', '1.jpg', 'Mie', 'Cara membuat', '2025-06-02'),
('2', 'ADM9781391', 'KND1629A19', 'Smoothie Bayam Pisang Antioksidan', 'Smoothie hijau kaya serat dan vitamin dari bayam, pisang, dan sedikit jahe. Cocok untuk sarapan atau camilan sehat. Memberikan energi tahan lama sepanjang hari.', '2.jpg', '', 'Cara membuat', '2025-06-02'),
('3', 'ADM9781391', 'K01', 'Ikan Salmon Panggangg Omega-3', 'Fillet ikan salmon dipanggang dengan bumbu minimalis, disajikan dengan brokoli kukus. Sumber omega-3 yang baik untuk jantung dan otak. Rasanya lezat dan mudah dibuat.', '3.jpg', '', 'Cara membuat', '2025-06-02'),
('4', 'ADM9781391', 'K01', 'Mie Shirataki Goreng Seafood Lezat', 'Mie shirataki rendah kalori digoreng dengan udang, cumi, dan sayuran segar pilihan. Alternatif mie yang lebih sehat dan aman untuk penderita diabetes. Kenyang lebih lama.', '4.jpg', 'Ayam', 'Cara membuat', '2025-06-01');

-- --------------------------------------------------------

--
-- Table structure for table `resep_bahan`
--

CREATE TABLE `resep_bahan` (
  `id_resep_bahan` varchar(10) NOT NULL,
  `id_resep` varchar(10) DEFAULT NULL,
  `id_bahan` varchar(10) DEFAULT NULL,
  `jumlah` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resep_bahan`
--

INSERT INTO `resep_bahan` (`id_resep_bahan`, `id_resep`, `id_bahan`, `jumlah`) VALUES
('RB00000004', '3', 'BHNA09F1B0', 6),
('RB00000005', '1', 'BHNA09F1B0', 2),
('RB00000006', '4', 'BHNA09F1B0', 4);

-- --------------------------------------------------------

--
-- Table structure for table `resep_kondisi`
--

CREATE TABLE `resep_kondisi` (
  `id_resep_kondisi` int NOT NULL,
  `id_resep` varchar(50) DEFAULT NULL,
  `id_kondisi` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resep_kondisi`
--

INSERT INTO `resep_kondisi` (`id_resep_kondisi`, `id_resep`, `id_kondisi`, `created_at`) VALUES
(1, '1', 'K01', '2025-06-02 02:05:46'),
(2, '2', 'K01', '2025-06-10 02:06:18'),
(3, '4', 'K01', '2025-06-10 04:17:01'),
(4, '3', 'K01', '2025-06-10 04:17:21');

--
-- Triggers `resep_kondisi`
--
DELIMITER $$
CREATE TRIGGER `after_resep_kondisi_delete` AFTER DELETE ON `resep_kondisi` FOR EACH ROW BEGIN
    
    UPDATE resep 
    SET id_kondisi = update_resep_kondisi(OLD.id_resep)
    WHERE id_resep = OLD.id_resep;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_resep_kondisi_insert` AFTER INSERT ON `resep_kondisi` FOR EACH ROW BEGIN
    
    UPDATE resep 
    SET id_kondisi = update_resep_kondisi(NEW.id_resep)
    WHERE id_resep = NEW.id_resep;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_resep_kondisi_update` AFTER UPDATE ON `resep_kondisi` FOR EACH ROW BEGIN
    
    IF OLD.id_resep != NEW.id_resep THEN
        
        UPDATE resep 
        SET id_kondisi = update_resep_kondisi(OLD.id_resep)
        WHERE id_resep = OLD.id_resep;
        
        
        UPDATE resep 
        SET id_kondisi = update_resep_kondisi(NEW.id_resep)
        WHERE id_resep = NEW.id_resep;
    ELSE
        
        UPDATE resep 
        SET id_kondisi = update_resep_kondisi(NEW.id_resep)
        WHERE id_resep = NEW.id_resep;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `resep_tags`
--

CREATE TABLE `resep_tags` (
  `id_resep_tag` int NOT NULL,
  `id_resep` varchar(50) DEFAULT NULL,
  `id_tag` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resep_tags`
--

INSERT INTO `resep_tags` (`id_resep_tag`, `id_resep`, `id_tag`, `created_at`) VALUES
(10, '1', 1, '2025-06-10 21:36:11'),
(11, '4', 5, '2025-06-10 21:36:31');

--
-- Triggers `resep_tags`
--
DELIMITER $$
CREATE TRIGGER `after_resep_tags_delete` AFTER DELETE ON `resep_tags` FOR EACH ROW BEGIN
    UPDATE resep 
    SET tags = update_resep_tags(OLD.id_resep)
    WHERE id_resep = OLD.id_resep;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_resep_tags_insert` AFTER INSERT ON `resep_tags` FOR EACH ROW BEGIN
    UPDATE resep 
    SET tags = update_resep_tags(NEW.id_resep)
    WHERE id_resep = NEW.id_resep;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_resep_tags_update` AFTER UPDATE ON `resep_tags` FOR EACH ROW BEGIN
    IF OLD.id_resep != NEW.id_resep THEN
        
        UPDATE resep 
        SET tags = update_resep_tags(OLD.id_resep)
        WHERE id_resep = OLD.id_resep;
        
        
        UPDATE resep 
        SET tags = update_resep_tags(NEW.id_resep)
        WHERE id_resep = NEW.id_resep;
    ELSE
        
        UPDATE resep 
        SET tags = update_resep_tags(NEW.id_resep)
        WHERE id_resep = NEW.id_resep;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id_tag` int NOT NULL,
  `nama_tag` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id_tag`, `nama_tag`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Mie', 'mie', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(2, 'Nasi', 'nasi', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(3, 'Sayur', 'sayur', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(4, 'Daging', 'daging', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(5, 'Ayam', 'ayam', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(6, 'Ikan', 'ikan', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(7, 'Sup', 'sup', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(8, 'Goreng', 'goreng', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(9, 'Tumis', 'tumis', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(10, 'Rebus', 'rebus', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(11, 'Kukus', 'kukus', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(12, 'Panggang', 'panggang', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(13, 'Berkuah', 'berkuah', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(14, 'Kering', 'kering', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(15, 'Pedas', 'pedas', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(16, 'Manis', 'manis', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(17, 'Asam', 'asam', '2025-06-10 04:27:11', '2025-06-10 04:27:11'),
(18, 'Asin', 'asin', '2025-06-10 04:27:11', '2025-06-10 04:27:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` varchar(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `nama_lengkap`, `tanggal_lahir`, `jenis_kelamin`, `created_at`, `updated_at`) VALUES
('U000000001', 'Ubay', '$2y$10$9WHkgegQK5AIj9iSZ4.GMONZlDppYS4.DpGe80piVTgA5aO0CA8r.', 'ubayy@gmail.com', 'Ubay', '2000-03-07', 'L', '2025-06-11 01:56:07', '2025-06-11 01:56:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `bahan`
--
ALTER TABLE `bahan`
  ADD PRIMARY KEY (`id_bahan`);

--
-- Indexes for table `gizi`
--
ALTER TABLE `gizi`
  ADD PRIMARY KEY (`id_gizi`),
  ADD KEY `id_resep` (`id_resep`);

--
-- Indexes for table `gizi_resep`
--
ALTER TABLE `gizi_resep`
  ADD PRIMARY KEY (`id_gizi_resep`),
  ADD UNIQUE KEY `uq_id_resep` (`id_resep`);

--
-- Indexes for table `kondisi_kesehatan`
--
ALTER TABLE `kondisi_kesehatan`
  ADD PRIMARY KEY (`id_kondisi`),
  ADD UNIQUE KEY `idx_slug` (`slug`);

--
-- Indexes for table `resep`
--
ALTER TABLE `resep`
  ADD PRIMARY KEY (`id_resep`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_kondisi` (`id_kondisi`);

--
-- Indexes for table `resep_bahan`
--
ALTER TABLE `resep_bahan`
  ADD PRIMARY KEY (`id_resep_bahan`),
  ADD KEY `id_resep` (`id_resep`),
  ADD KEY `id_bahan` (`id_bahan`);

--
-- Indexes for table `resep_kondisi`
--
ALTER TABLE `resep_kondisi`
  ADD PRIMARY KEY (`id_resep_kondisi`),
  ADD UNIQUE KEY `unique_resep_kondisi` (`id_resep`,`id_kondisi`),
  ADD KEY `id_kondisi` (`id_kondisi`);

--
-- Indexes for table `resep_tags`
--
ALTER TABLE `resep_tags`
  ADD PRIMARY KEY (`id_resep_tag`),
  ADD UNIQUE KEY `unique_resep_tag` (`id_resep`,`id_tag`),
  ADD KEY `id_tag` (`id_tag`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id_tag`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `resep_kondisi`
--
ALTER TABLE `resep_kondisi`
  MODIFY `id_resep_kondisi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `resep_tags`
--
ALTER TABLE `resep_tags`
  MODIFY `id_resep_tag` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id_tag` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gizi`
--
ALTER TABLE `gizi`
  ADD CONSTRAINT `gizi_ibfk_1` FOREIGN KEY (`id_resep`) REFERENCES `resep` (`id_resep`);

--
-- Constraints for table `gizi_resep`
--
ALTER TABLE `gizi_resep`
  ADD CONSTRAINT `fk_gizi_resep_resep` FOREIGN KEY (`id_resep`) REFERENCES `resep` (`id_resep`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resep`
--
ALTER TABLE `resep`
  ADD CONSTRAINT `resep_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `resep_ibfk_2` FOREIGN KEY (`id_kondisi`) REFERENCES `kondisi_kesehatan` (`id_kondisi`) ON UPDATE CASCADE;

--
-- Constraints for table `resep_bahan`
--
ALTER TABLE `resep_bahan`
  ADD CONSTRAINT `resep_bahan_ibfk_1` FOREIGN KEY (`id_resep`) REFERENCES `resep` (`id_resep`),
  ADD CONSTRAINT `resep_bahan_ibfk_2` FOREIGN KEY (`id_bahan`) REFERENCES `bahan` (`id_bahan`);

--
-- Constraints for table `resep_kondisi`
--
ALTER TABLE `resep_kondisi`
  ADD CONSTRAINT `resep_kondisi_ibfk_1` FOREIGN KEY (`id_resep`) REFERENCES `resep` (`id_resep`) ON DELETE CASCADE,
  ADD CONSTRAINT `resep_kondisi_ibfk_2` FOREIGN KEY (`id_kondisi`) REFERENCES `kondisi_kesehatan` (`id_kondisi`) ON DELETE CASCADE;

--
-- Constraints for table `resep_tags`
--
ALTER TABLE `resep_tags`
  ADD CONSTRAINT `resep_tags_ibfk_1` FOREIGN KEY (`id_resep`) REFERENCES `resep` (`id_resep`) ON DELETE CASCADE,
  ADD CONSTRAINT `resep_tags_ibfk_2` FOREIGN KEY (`id_tag`) REFERENCES `tags` (`id_tag`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
