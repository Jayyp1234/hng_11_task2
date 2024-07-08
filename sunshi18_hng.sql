-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 07, 2024 at 08:03 PM
-- Server version: 8.0.37-cll-lve
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sunshi18_hng`
--

-- --------------------------------------------------------

--
-- Table structure for table `apidatatable`
--

CREATE TABLE `apidatatable` (
  `id` int NOT NULL,
  `privatekey` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tokenexpiremin` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `servername` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `apidatatable`
--

INSERT INTO `apidatatable` (`id`, `privatekey`, `tokenexpiremin`, `servername`) VALUES
(1, '454gasfwe3sf24a23W4233453423dfsdfw', '60', 'ENETWORK_SERVER');

-- --------------------------------------------------------

--
-- Table structure for table `Organisations`
--

CREATE TABLE `Organisations` (
  `orgId` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `Organisations`
--

INSERT INTO `Organisations` (`orgId`, `name`, `description`, `createdAt`, `updatedAt`) VALUES
('0tTzv1720375677', 'Florence\'s Organisation', NULL, '2024-07-07 18:07:57', '2024-07-07 18:07:57'),
('7leIz1720376606', 'Kevin', 'This is my second Organisation', '2024-07-07 18:23:26', '2024-07-07 18:23:26'),
('iFhvF1720375662', 'Johpaul\'s Organisation', NULL, '2024-07-07 18:07:42', '2024-07-07 18:07:42');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userId` int NOT NULL,
  `publicKey` varchar(200) COLLATE utf8mb3_unicode_ci NOT NULL,
  `firstName` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `lastName` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`userId`, `publicKey`, `firstName`, `lastName`, `email`, `password`, `phone`, `status`, `createdAt`, `updatedAt`) VALUES

-- --------------------------------------------------------

--
-- Table structure for table `User_Organisation`
--

CREATE TABLE `User_Organisation` (
  `id` int NOT NULL,
  `userId` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `orgId` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `User_Organisation`
--

INSERT INTO `User_Organisation` (`id`, `userId`, `orgId`, `createdAt`, `updatedAt`) VALUES
(1, 'JPnQJ1720375662', 'iFhvF1720375662', '2024-07-07 18:07:42', '2024-07-07 18:07:42'),
(2, 'P8scX1720375677', '0tTzv1720375677', '2024-07-07 18:07:57', '2024-07-07 18:07:57'),
(3, 'JPnQJ1720375662', '7leIz1720376606', '2024-07-07 18:23:26', '2024-07-07 18:23:26'),
(4, 'P8scX1720375677', 'JPnQJ1720375662', '2024-07-07 18:40:48', '2024-07-07 18:40:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apidatatable`
--
ALTER TABLE `apidatatable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Organisations`
--
ALTER TABLE `Organisations`
  ADD PRIMARY KEY (`orgId`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `User_Organisation`
--
ALTER TABLE `User_Organisation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apidatatable`
--
ALTER TABLE `apidatatable`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `userId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `User_Organisation`
--
ALTER TABLE `User_Organisation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
