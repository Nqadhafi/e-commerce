-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 09, 2024 at 03:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_onlineku`
--
CREATE DATABASE IF NOT EXISTS `toko_onlineku` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `toko_onlineku`;
-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id_admin` int(11) NOT NULL,
  `user_admin` varchar(50) NOT NULL,
  `password_admin` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`id_admin`, `user_admin`, `password_admin`) VALUES
(1, 'admin', 'tokoonline123');

-- --------------------------------------------------------

--
-- Table structure for table `tb_keranjang`
--

CREATE TABLE `tb_keranjang` (
  `id` int(11) NOT NULL,
  `id_keranjang` varchar(50) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `qty_keranjang` int(11) NOT NULL,
  `subtotal_keranjang` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_keranjang`
--

INSERT INTO `tb_keranjang` (`id`, `id_keranjang`, `id_produk`, `qty_keranjang`, `subtotal_keranjang`) VALUES
(54, '#66b112a07b219', 21, 1, 7500),
(55, '#66b11a0ea4437', 19, 4, 200000),
(56, '#66b11a0ea4437', 20, 8, 24000),
(57, '#66b228b9b3dad', 20, 1, 3000),
(58, '#66b22c71a30de', 19, 1, 50000),
(59, '#66b22cb316f0d', 20, 1, 3000),
(60, '#66b22f8d99b60', 20, 1, 3000);

-- --------------------------------------------------------

--
-- Table structure for table `tb_ongkir`
--

CREATE TABLE `tb_ongkir` (
  `id_ongkir` int(11) NOT NULL,
  `provinsi_ongkir` varchar(100) NOT NULL,
  `jumlah_ongkir` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_ongkir`
--

INSERT INTO `tb_ongkir` (`id_ongkir`, `provinsi_ongkir`, `jumlah_ongkir`) VALUES
(1, 'Aceh', 50000),
(3, 'Sumatera Utara', 0),
(4, 'Sumatera Barat', 50000),
(5, 'Riau', 0),
(6, 'Jambi', 0),
(7, 'Sumatera Selatan', 0),
(8, 'Bengkulu', 0),
(9, 'Lampung', 0),
(10, 'Kepulauan Bangka Belitung\r\n', 0),
(11, 'Kepulauan Riau', 0),
(12, 'DKI Jakarta', 0),
(13, 'Jawa Barat', 0),
(14, 'Jawa Tengah', 0),
(15, 'DI Yogyakarta', 0),
(16, 'Jawa Timur', 0),
(17, 'Banten', 0),
(18, 'Bali', 0),
(19, 'Nusa Tenggara Barat (NTB)', 0),
(20, 'Nusa Tenggara Timur (NTT)', 0),
(21, 'Kalimantan Barat', 0),
(22, 'Kalimantan Tengah', 0),
(23, 'Kalimantan Selatan', 0),
(24, 'Kalimantan Timur', 0),
(25, 'Kalimantan Utara', 0),
(26, 'Sulawesi Utara', 0),
(27, 'Sulawesi Tengah', 0),
(29, 'Sulawesi Selatan', 0),
(30, 'Sulawesi Tenggara', 0),
(31, 'Gorontalo', 0),
(32, 'Sulawesi Barat', 0),
(33, 'Maluku', 0),
(34, 'Maluku Utara', 0),
(35, 'Papua', 0),
(36, 'Papua Barat', 0),
(37, 'Papua Selatan', 0),
(38, 'Papua Tengah', 0),
(39, 'Papua Pegunungan', 0),
(40, 'Papua Barat Daya', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_order`
--

CREATE TABLE `tb_order` (
  `id_order` varchar(50) NOT NULL,
  `resi_order` varchar(50) NOT NULL,
  `namacust_order` varchar(50) NOT NULL,
  `email_order` varchar(50) NOT NULL,
  `nohp_order` bigint(20) NOT NULL,
  `alamat_order` varchar(100) NOT NULL,
  `tanggal_order` date NOT NULL DEFAULT current_timestamp(),
  `id_ongkir` int(11) NOT NULL,
  `grandtotal_order` bigint(20) NOT NULL,
  `after_ongkir_order` bigint(20) NOT NULL,
  `status_order` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_produk`
--

CREATE TABLE `tb_produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(30) NOT NULL,
  `harga_produk` int(10) NOT NULL,
  `deskripsi_produk` varchar(50) NOT NULL,
  `gambar_produk` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `tb_keranjang`
--
ALTER TABLE `tb_keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `tb_ongkir`
--
ALTER TABLE `tb_ongkir`
  ADD PRIMARY KEY (`id_ongkir`);

--
-- Indexes for table `tb_order`
--
ALTER TABLE `tb_order`
  ADD PRIMARY KEY (`id_order`);

--
-- Indexes for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_keranjang`
--
ALTER TABLE `tb_keranjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `tb_ongkir`
--
ALTER TABLE `tb_ongkir`
  MODIFY `id_ongkir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tb_produk`
--
ALTER TABLE `tb_produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
