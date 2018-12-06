-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 05, 2018 at 08:32 AM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.0.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `acc_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `somtum_user`
--

CREATE TABLE `somtum_user` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `visited` int(11) UNSIGNED DEFAULT '0',
  `lastvisited` int(11) DEFAULT NULL,
  `ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `session_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `somtum_user`
--

INSERT INTO `somtum_user` (`id`, `username`, `salt`, `password`, `token`, `status`, `name`, `phone`, `create_date`, `visited`, `lastvisited`, `ip`, `session_id`) VALUES
(1, 'admin@localhost', '5c078cceec367', 'af78ea237a4ff2b57a4f48c70a4adde0fd7765b9', '2bf3820b69fc6f6478cccdfbf6b8a301c6dc4526', 1, 'แอดมิน', '', '2018-12-04 09:27:26', 2, 1543924359, '110.168.79.223', 'h8sh5pjdm34jsapcanpiqihsh2');

-- --------------------------------------------------------

--
-- Table structure for table `somtum_vote`
--

CREATE TABLE `somtum_vote` (
  `user` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `vote_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `ip` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `somtum_user`
--
ALTER TABLE `somtum_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `somtum_vote`
--
ALTER TABLE `somtum_vote`
  ADD PRIMARY KEY (`user`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `somtum_user`
--
ALTER TABLE `somtum_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
