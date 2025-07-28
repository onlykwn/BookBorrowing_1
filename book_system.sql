-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2025 at 09:40 AM
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
-- Database: `book_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `status` enum('Available','Borrowed') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `genre`, `status`) VALUES
(5, 'The Shining', 'Stephen King', 'Horror', 'Available'),
(6, 'Mexican Gothic', 'Silvia Moreno-Gacia', 'Horror', 'Available'),
(7, 'Bird Box', 'Josh Malerman', 'Horror', 'Available'),
(8, 'Pride and Prejudice', 'Jane Austern', 'Romance', 'Available'),
(9, 'The Notebook', 'Nicholas Sparks', 'Romance', 'Available'),
(10, 'It End With Us', 'Colleen Hoover', 'Romance', 'Available'),
(11, 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', 'Fantasy', 'Available'),
(12, 'The Name of the Wind', 'Patrick Rothfuss', 'Fantasy', 'Available'),
(13, 'Mistborn: The Final Empire', 'Brandon Sanderson', 'Fantasy', 'Available'),
(14, 'Gone Girl', 'Gillian Flynn', 'Mystery', 'Available'),
(15, 'The Girl with the Dragon Tattoo', 'Stieg Larsson', 'Mystery', 'Available'),
(16, 'The Da Vinci Code', 'Dan Brown', 'Mystery', 'Available'),
(32, 'awdawsda', 'dasdsadsad', 'Romance', 'Borrowed'),
(33, 'bhsgyagsh', 'adsdsafdsafdw', 'Romance', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `borrower_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`borrower_id`, `name`, `contact`) VALUES
(11, 'Queenie Mae', '0912345674'),
(12, 'arianne', '0912345674'),
(13, 'arianne', '0912345674'),
(14, 'arianne', '0912345674'),
(15, 'arianne', '0912345674'),
(16, 'arianne', '0912345674'),
(17, 'arianne', '0912345674'),
(18, 'arianne', '0245678910'),
(19, 'arianne', '0245678910'),
(20, 'arianne', '0245678910'),
(21, 'GFCTCJY', '0912345674'),
(22, 'arianne', '0912345674'),
(23, 'arianne', '0245678910'),
(24, 'GFCTCJY', '0912345674'),
(25, 'GFCTCJY', '0912345674'),
(26, 'GFCTCJY', '0912345674');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_records`
--

CREATE TABLE `borrow_records` (
  `record_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `borrower_id` int(11) DEFAULT NULL,
  `borrow_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `borrower_name` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_records`
--

INSERT INTO `borrow_records` (`record_id`, `book_id`, `borrower_id`, `borrow_date`, `due_date`, `borrower_name`, `contact`) VALUES
(31, 32, 26, '2025-07-27', '2025-07-29', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','student') NOT NULL DEFAULT 'student',
  `status` enum('pending','approved') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `status`) VALUES
(6, 'Admin', '$2y$10$UAkarqIKJKbuvQ286iWT5OUTTxw.x6fOP59SqfvbS9C6OFC4Hx1cu', '', 'approved'),
(9, 'student', '$2y$10$oRLBeJ8ayAVG.PXuMfKo0uLURuchsAwKtmOg4U3exrkG23Eh2AIo2', 'student', 'approved'),
(18, 'staff', '$2y$10$ROcnSfQ3nBoqeze31NrAc.80SI8Kwk8S.3hjjOiYz22sSpuqXwSWa', 'staff', 'approved'),
(19, 'Staffqueen', '$2y$10$Ob1XcF0u7lEF5/EWmjld9efiCaaZSG5VJ/vBxcsdiwwPvzpy9fH52', 'staff', 'approved'),
(20, 'Staffkwn', '$2y$10$PSEpfJaH0D1X26UlTsqVHulaEnyAf5ksHxjHt.Vtg0E4A7kg5HrS.', 'staff', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`borrower_id`);

--
-- Indexes for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `borrower_id` (`borrower_id`);

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
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `borrower_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `borrow_records`
--
ALTER TABLE `borrow_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD CONSTRAINT `borrow_records_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `borrow_records_ibfk_2` FOREIGN KEY (`borrower_id`) REFERENCES `borrowers` (`borrower_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
