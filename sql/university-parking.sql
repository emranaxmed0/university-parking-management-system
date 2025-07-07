-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2025 at 08:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `university-parking`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminlog`
--

CREATE TABLE `adminlog` (
  `loginID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL,
  `action` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedbackID` int(11) NOT NULL,
  `Role` enum('student','staff','visitor') NOT NULL,
  `userID` int(11) NOT NULL,
  `feedbackText` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parkingspace`
--

CREATE TABLE `parkingspace` (
  `spaceID` int(11) NOT NULL,
  `zoneID` char(1) DEFAULT NULL,
  `status` enum('available','occupied') DEFAULT 'available',
  `type` varchar(50) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parkingspace`
--

INSERT INTO `parkingspace` (`spaceID`, `zoneID`, `status`, `type`, `userID`) VALUES
(1, '1', 'available', 'normal', NULL),
(2, '1', 'available', 'normal', NULL),
(3, '1', 'available', 'normal', NULL),
(4, '1', 'available', 'normal', NULL),
(5, '1', 'available', 'normal', NULL),
(6, '1', 'available', 'normal', NULL),
(7, '1', 'available', 'normal', NULL),
(8, '1', 'available', 'normal', NULL),
(9, '1', 'available', 'normal', NULL),
(10, '1', 'available', 'normal', NULL),
(11, '1', 'available', 'normal', NULL),
(12, '1', 'available', 'normal', NULL),
(13, '1', 'available', 'normal', NULL),
(14, '1', 'available', 'normal', NULL),
(15, '1', 'available', 'normal', NULL),
(16, '1', 'available', 'normal', NULL),
(17, '1', 'available', 'normal', NULL),
(18, '1', 'available', 'normal', NULL),
(19, '1', 'available', 'disabled', NULL),
(21, '2', 'available', 'normal', NULL),
(22, '2', 'available', 'normal', NULL),
(23, '2', 'available', 'normal', NULL),
(24, '2', 'available', 'normal', NULL),
(25, '2', 'available', 'normal', NULL),
(26, '2', 'available', 'normal', NULL),
(27, '2', 'available', 'normal', NULL),
(28, '2', 'available', 'normal', NULL),
(29, '2', 'available', 'normal', NULL),
(30, '2', 'available', 'normal', NULL),
(31, '2', 'available', 'normal', NULL),
(32, '2', 'available', 'normal', NULL),
(33, '2', 'available', 'normal', NULL),
(34, '2', 'available', 'disabled', NULL),
(35, '2', 'available', 'disabled', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `sessionID` int(11) NOT NULL,
  `Role` enum('student','staff','visitor') NOT NULL,
  `userID` int(11) NOT NULL,
  `spaceID` int(11) DEFAULT NULL,
  `checkInTime` datetime DEFAULT current_timestamp(),
  `checkOutTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`sessionID`, `Role`, `userID`, `spaceID`, `checkInTime`, `checkOutTime`) VALUES
(1, 'student', 2, 3, '2025-07-02 14:48:59', '2025-07-02 14:49:01'),
(2, 'student', 2, 3, '2025-07-02 14:49:02', '2025-07-02 14:49:03'),
(3, 'student', 2, 7, '2025-07-02 14:49:05', '2025-07-02 14:49:08'),
(4, 'student', 2, 1, '2025-07-02 14:50:09', '2025-07-02 14:50:11'),
(5, 'student', 2, 1, '2025-07-02 14:52:45', '2025-07-02 14:52:47'),
(6, 'student', 2, 1, '2025-07-02 14:52:47', '2025-07-02 14:52:48'),
(7, 'student', 2, 1, '2025-07-02 14:52:55', '2025-07-02 14:53:00'),
(8, 'student', 2, 3, '2025-07-02 14:53:02', '2025-07-02 14:53:06'),
(9, 'student', 2, 1, '2025-07-02 15:00:26', '2025-07-02 15:00:30'),
(10, 'student', 2, 1, '2025-07-02 15:01:13', NULL),
(11, 'student', 3, 2, '2025-07-02 15:04:14', '2025-07-02 15:04:18'),
(12, 'student', 3, 1, '2025-07-02 15:07:20', '2025-07-02 15:07:28'),
(13, 'student', 3, 7, '2025-07-02 15:08:15', '2025-07-02 15:08:22'),
(14, 'staff', 1, 33, '2025-07-02 15:41:04', '2025-07-02 15:41:10'),
(15, 'staff', 1, 21, '2025-07-02 15:44:00', '2025-07-02 15:44:03'),
(16, 'staff', 1, 21, '2025-07-04 08:24:24', '2025-07-04 08:24:35'),
(17, 'staff', 1, 21, '2025-07-06 22:38:14', '2025-07-06 22:42:54'),
(18, 'student', 4, 1, '2025-07-06 22:46:44', '2025-07-06 22:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staffID` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('staff') DEFAULT 'staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffID`, `username`, `password`, `email`, `phone`, `role`) VALUES
(1, 'jKamau', '$2y$10$hoeL6ZHJWtCsJ2zf0eFfuuAZGY3yIg1bQFLxUGHnM5X6MP9Vb0xHS', 'j.kamau@university.edu', NULL, 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentID` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('student') DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentID`, `username`, `password`, `email`, `phone`, `role`) VALUES
(2, 'Ahmed', '$2y$10$SaeczDcRuIja1X0HcVor9./YNeyFUsmcBq2255D58pmZiXkpCcq.u', 'i.ahmed@university.edu', NULL, 'student'),
(3, 'Moses', '$2y$10$MiT9fTDvIyEL3oz/hcoqSu2pJKQq9Jpz2zJ.oDzKbIY6dw0ZzZYTe', 'o.moses@university.edu', NULL, 'student'),
(4, 'jLopes', '$2y$10$trO4cNYNhs91QpRd4vzjbeANSBj0izWffBb6M/Oktq0MloqbYg70.', 'j.lopes@university.edu', NULL, 'student');

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE `visitor` (
  `visitorID` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('visitor') DEFAULT 'visitor',
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor`
--

INSERT INTO `visitor` (`visitorID`, `username`, `password`, `phone`, `role`, `email`) VALUES
(1, 'gAlicia', '$2y$10$tbGZCpe8wxjaH7tf.UdyCu0aDXQruDLV/OTGe.4Ohck6CFoc3lImu', NULL, 'visitor', 'aliciagomez@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--

CREATE TABLE `zone` (
  `zoneID` char(1) NOT NULL,
  `zoneName` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL,
  `availableSpace` int(11) NOT NULL,
  `Role` enum('student','staff','visitor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zone`
--

INSERT INTO `zone` (`zoneID`, `zoneName`, `capacity`, `availableSpace`, `Role`) VALUES
('1', 'Zone A', 19, 16, 'student'),
('2', 'Zone B', 15, 15, 'staff'),
('3', 'Zone C', 0, 0, 'visitor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminlog`
--
ALTER TABLE `adminlog`
  ADD PRIMARY KEY (`loginID`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedbackID`);

--
-- Indexes for table `parkingspace`
--
ALTER TABLE `parkingspace`
  ADD PRIMARY KEY (`spaceID`),
  ADD KEY `zoneID` (`zoneID`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`sessionID`),
  ADD KEY `spaceID` (`spaceID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staffID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studentID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`visitorID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `zone`
--
ALTER TABLE `zone`
  ADD PRIMARY KEY (`zoneID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminlog`
--
ALTER TABLE `adminlog`
  MODIFY `loginID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parkingspace`
--
ALTER TABLE `parkingspace`
  MODIFY `spaceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `sessionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `studentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `visitor`
--
ALTER TABLE `visitor`
  MODIFY `visitorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parkingspace`
--
ALTER TABLE `parkingspace`
  ADD CONSTRAINT `parkingspace_ibfk_1` FOREIGN KEY (`zoneID`) REFERENCES `zone` (`zoneID`);

--
-- Constraints for table `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`spaceID`) REFERENCES `parkingspace` (`spaceID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
