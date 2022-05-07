-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 08, 2022 at 12:43 AM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adx-pay`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_b_r_i_s`
--

CREATE TABLE `auth_b_r_i_s` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timestamp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile` varchar(225) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saldo` int(11) DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `verified` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `profile`, `saldo`, `point`, `username`, `password`, `phone_number`, `email`, `deleted`, `verified`, `created_at`, `updated_at`) VALUES
(7, 'Krisna Tobim', NULL, NULL, NULL, 'CUS112502935', '$2y$10$MT.9T0G0u70OdrdfGkfIkeTN0ncc8b4uc9EiD8mD15lnkSgmnCes.', NULL, NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 'guskd', NULL, NULL, 1000, 'CUS301656655', '$2y$10$AlpggRZcAI/KBbzFdbora.5dZ4DZ7jSIGaKdBZWywbqTFpWPy9Yoa', NULL, NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, 'Sartika Putri', NULL, NULL, NULL, 'CUS55211121', '$2y$10$9lfiopxZ51msEGLoJroXQe.bCltF7gO0seRJ84q6XszCBS50Kcz42', NULL, NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(54, 'Aryaak', NULL, NULL, NULL, 'CUS585030710', '$2y$10$KPka6hVGRoHyvOFgZhRrP.d9ckU.pA97EzHm.PC39rm6cL.pT0cwK', '81936522573', NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(57, 'Dika', NULL, 5100, NULL, 'CUS1433542919', '$2y$10$A8qD/dbKTTOTFH9uJUwJEuIMYItaHoW4VXbleuAwAX2pLPSQ3pXBm', NULL, NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(59, 'arimbawadx', 'Profile_CUS0511994064.jpg', 235452, NULL, 'CUS0511994064', '$2y$10$zQZPw3N5iUTRJDgmnhW7meY/nDQQSTS.vlg/s5j4If/A2OU.CaXZu', '085847801933', 'yogade9595.yd@gmail.com', 0, 1, '2021-11-20 13:33:24', '2022-01-07 08:12:59'),
(60, 'Pakman Tantra', NULL, NULL, NULL, 'CUS186563403', '$2y$10$RK9sAtvltC26siAB0AkGJOTXUn6ZOTeqDkN5MszazaKLgPY9AfVLy', '87861572547', NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(61, 'Ketut Bana', NULL, NULL, NULL, 'CUS1750299312', '$2y$10$3Y5TtmasUELmSFDDhYIiG.gWapHWY23JST1wLsJlV3p5DJmeibWd6', '87855493616', NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(62, 'Manik', NULL, NULL, NULL, 'CUS1536959376', '$2y$10$EHKjXHRU9yPcxcNjhmmnhefk4qM68Wq2Hho0Q8Xn9iG0SWoI7YQvy', '81246577392', NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(63, 'Suparthini', NULL, NULL, NULL, 'CUS2020833262', '$2y$10$Ti/hoUY8WKislMe7iAOMX.Oqjr/kTLpwTsf3Hmay6/LGB/S.7fj8y', NULL, NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(64, 'Pakyan Tama', NULL, NULL, NULL, 'CUS687658367', '$2y$10$nK7iNzztbZjrf3K5A6MisOknZouQsGS3ob5G68r.YpnbPUSmZholG', NULL, NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(65, 'Ibu arya', NULL, NULL, NULL, 'CUS938554708', '$2y$10$ylvqsGSFu2JcyJz5xS.QP.rn7kaWPr9I.XGCPkeFDsKP0/LSLvJMm', NULL, NULL, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `customer_services`
--

CREATE TABLE `customer_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `saldo` int(11) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_services`
--

INSERT INTO `customer_services` (`id`, `name`, `saldo`, `username`, `password`, `phone_number`, `email`, `deleted`, `created_at`, `updated_at`) VALUES
(35, 'arimbawadx', NULL, 'CS0511994064', '$2y$10$DutTrognKeJxka0GKqlKg.3XOLuqgYJkQS5NdLQE.rqBK5LW2q16S', '085847801933', 'yogade9595.yd@gmail.com', 0, '2021-11-20 13:31:55', '2021-11-20 13:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `hutangs`
--

CREATE TABLE `hutangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `trxid_api` varchar(225) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominal` int(11) NOT NULL,
  `sisa` int(11) NOT NULL,
  `keterangan` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Lunas',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hutangs`
--

INSERT INTO `hutangs` (`id`, `customer_id`, `trxid_api`, `nominal`, `sisa`, `keterangan`, `status`, `created_at`, `updated_at`) VALUES
(12, 7, '', 62000, 0, 'Pulsa 60k', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, 8, '', 52000, 0, 'Pembelian dana 50k', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(15, 12, '', 62073, 0, 'Pembelian paket kuota smartfren', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, 54, '', 7000, 0, 'Pembelian Produk X5', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, 54, '', 10000, 0, 'Pembayaran COD', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, 8, '', 52000, 0, 'Pembelian Produk DNA50', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 60, '', 60000, 0, 'Pembelian (XCX5) COMBO XTRA 5GB+5GB YTB+20MNT 30HR', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, 61, '', 60000, 0, '(XCX5) COMBO XTRA 5GB+5GB YTB+20MNT 30HR', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 60, '', 60000, 0, '(ID10) PURE 10GB 30HR', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 63, '', 52000, 0, 'Listrik50', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, 63, '', 75000, 0, '(XCX5) COMBO XTRA 5GB+5GB YTB+20MNT 30HR', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(28, 7, '', 60000, 0, 'Pulsa 60', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(29, 8, '', 100000, 0, '(XCL21) COMBO LITE 12GB+8GB 4G+1GB YTB 30HR', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, 63, '', 52000, 0, 'Listrik50', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(31, 54, '', 7000, 7000, 'Pulsa 7000', 'Belum Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 8, '', 77000, 0, 'Pulsa 75000', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(33, 60, '', 60000, 0, 'Paket dudut', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(34, 60, '', 12000, 0, 'Pulsa 10', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(35, 60, '', 12000, 0, 'Pls10', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(36, 62, '', 52000, 52000, 'Pulsa 50', 'Belum Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(37, 60, '', 60000, 0, 'Paket data', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(38, 64, '', 12000, 0, 'Pulsa 10 pada tanggal 10 desember', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(39, 64, '', 50000, 42000, 'Paket data', 'Belum Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(40, 63, '', 180000, 0, 'Beli refulator gas', 'Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 65, '', 50000, 50000, 'Paket data internet', 'Belum Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(42, 54, '', 7000, 7000, 'Pulsa5k', 'Belum Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(43, 8, '', 77000, 77000, 'Plsa 75k', 'Belum Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, 7, '', 62000, 62000, 'Pulsa 60', 'Belum Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(45, 7, '', 27000, 27000, 'Pulsa 25k', 'Belum Lunas', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(16, '2021_11_20_214507_create_auth_b_r_i_s_table', 0),
(17, '2021_11_20_214507_create_customer_services_table', 0),
(18, '2021_11_20_214507_create_customers_table', 0),
(19, '2021_11_20_214507_create_hutangs_table', 0),
(20, '2021_11_20_214507_create_mutations_table', 0),
(21, '2021_11_20_214508_add_foreign_keys_to_hutangs_table', 0),
(22, '2021_12_20_125417_create_auth_b_r_i_s_table', 0),
(23, '2021_12_20_125417_create_customer_services_table', 0),
(24, '2021_12_20_125417_create_customers_table', 0),
(25, '2021_12_20_125417_create_hutangs_table', 0),
(26, '2021_12_20_125417_create_mutations_table', 0),
(27, '2021_12_20_125418_add_foreign_keys_to_hutangs_table', 0),
(28, '2022_01_08_074214_create_auth_b_r_i_s_table', 0),
(29, '2022_01_08_074214_create_customer_services_table', 0),
(30, '2022_01_08_074214_create_customers_table', 0),
(31, '2022_01_08_074214_create_hutangs_table', 0),
(32, '2022_01_08_074214_create_mutations_table', 0),
(33, '2022_01_08_074215_add_foreign_keys_to_hutangs_table', 0);

-- --------------------------------------------------------

--
-- Table structure for table `mutations`
--

CREATE TABLE `mutations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_transaksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_transfer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_deposit` int(11) DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idcust` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trxid_api` bigint(20) DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_b_r_i_s`
--
ALTER TABLE `auth_b_r_i_s`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_services`
--
ALTER TABLE `customer_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hutangs`
--
ALTER TABLE `hutangs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hutangs_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mutations`
--
ALTER TABLE `mutations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_b_r_i_s`
--
ALTER TABLE `auth_b_r_i_s`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `customer_services`
--
ALTER TABLE `customer_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `hutangs`
--
ALTER TABLE `hutangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `mutations`
--
ALTER TABLE `mutations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1518;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hutangs`
--
ALTER TABLE `hutangs`
  ADD CONSTRAINT `hutangs_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
