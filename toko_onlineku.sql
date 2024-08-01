-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2024 at 04:04 AM
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
-- Table structure for table `tb_keranjang`
--

CREATE TABLE `tb_keranjang` (
  `id_keranjang` bigint(20) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `qty_keranjang` int(11) NOT NULL,
  `subtotal_keranjang` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_order`
--

CREATE TABLE `tb_order` (
  `id_order` bigint(20) NOT NULL,
  `id_keranjang` bigint(20) NOT NULL,
  `namacust_order` varchar(50) NOT NULL,
  `email_order` varchar(50) NOT NULL,
  `nohp_order` bigint(20) NOT NULL,
  `alamat_order` varchar(100) NOT NULL,
  `grandtotal_order` bigint(20) NOT NULL,
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
-- Dumping data for table `tb_produk`
--

INSERT INTO `tb_produk` (`id_produk`, `nama_produk`, `harga_produk`, `deskripsi_produk`, `gambar_produk`) VALUES
(18, 'Next JS', 24000, 'Ini next js', '66aa49f90a42c.png'),
(19, 'Kubernetes', 50000, 'Joki kubernetes', '66aa4a0ebfde8.png'),
(20, 'Orang hitam', 3000, 'Anjay nigga', '66aa4bbb60c10.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_keranjang`
--
ALTER TABLE `tb_keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD UNIQUE KEY `id_keranjang` (`id_keranjang`);

--
-- Indexes for table `tb_order`
--
ALTER TABLE `tb_order`
  ADD PRIMARY KEY (`id_order`),
  ADD UNIQUE KEY `id_keranjang` (`id_keranjang`);

--
-- Indexes for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_produk`
--
ALTER TABLE `tb_produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
