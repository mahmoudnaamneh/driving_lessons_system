-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 28, 2024 at 08:35 AM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `drivinglessonssystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `feedback_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `feedback_text` text,
  `feedback_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `feedback_text`, `feedback_date`) VALUES
(5, 4, 'Great website with everything useful', '2024-09-27 08:46:42');

-- --------------------------------------------------------

--
-- Table structure for table `forum`
--

DROP TABLE IF EXISTS `forum`;
CREATE TABLE IF NOT EXISTS `forum` (
  `forum_id` int NOT NULL AUTO_INCREMENT,
  `forum_name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`forum_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `forum`
--

INSERT INTO `forum` (`forum_id`, `forum_name`, `description`, `created_at`, `user_id`) VALUES
(3, 'driver\'s test experience', 'Can you describe your experience of taking your driver\'s test for the first time?', '2024-09-27 08:56:30', 10);

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
CREATE TABLE IF NOT EXISTS `lessons` (
  `lesson_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `lesson_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price` int NOT NULL,
  `teacher_name` varchar(255) DEFAULT NULL,
  `location` enum('Northern District','Southern District','Center District','Haifa District','Tel Aviv District','Jerusalem District') DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `lesson_title` varchar(255) NOT NULL,
  PRIMARY KEY (`lesson_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `user_id`, `lesson_date`, `created_at`, `price`, `teacher_name`, `location`, `student_name`, `lesson_title`) VALUES
(38, 3, '2024-07-01 10:20:00', '2024-09-27 07:22:24', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 1'),
(39, 3, '2024-07-02 10:22:00', '2024-09-27 07:23:19', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 2'),
(40, 3, '2024-07-03 10:22:00', '2024-09-27 07:24:03', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 3'),
(41, 3, '2024-07-04 10:26:00', '2024-09-27 07:26:43', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 4'),
(42, 3, '2024-07-05 10:27:00', '2024-09-27 07:29:26', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 5'),
(43, 3, '2024-07-06 10:30:00', '2024-09-27 07:32:53', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 6'),
(44, 3, '2024-07-07 10:33:00', '2024-09-27 07:38:11', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 7'),
(45, 3, '2024-07-08 10:41:00', '2024-09-27 07:43:49', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 8'),
(46, 3, '2024-07-09 10:44:00', '2024-09-27 07:49:03', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 9'),
(47, 3, '2024-07-10 10:49:00', '2024-09-27 07:50:31', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 10'),
(48, 3, '2024-07-11 10:51:00', '2024-09-27 07:55:48', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 11'),
(49, 3, '2024-07-12 10:56:00', '2024-09-27 07:56:33', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 12'),
(50, 3, '2024-07-13 10:58:00', '2024-09-27 07:59:03', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 13'),
(51, 3, '2024-07-14 11:00:00', '2024-09-27 08:00:56', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 14'),
(52, 3, '2024-07-15 11:01:00', '2024-09-27 08:01:41', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 15'),
(53, 3, '2024-07-16 11:01:00', '2024-09-27 08:02:10', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 16'),
(54, 3, '2024-07-17 11:04:00', '2024-09-27 08:06:23', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 17'),
(55, 3, '2024-07-18 11:06:00', '2024-09-27 08:07:44', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 18'),
(56, 3, '2024-07-19 11:08:00', '2024-09-27 08:08:37', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 19'),
(57, 3, '2024-07-20 11:08:00', '2024-09-27 08:09:16', 100, 'Eleanor', 'Northern District', 'William', 'Lesson 20'),
(58, 8, '2024-09-24 12:26:00', '2024-09-27 09:29:20', 200, 'Oscar', 'Tel Aviv District', 'James', 'Lesson 1'),
(59, 8, '2024-09-25 12:29:00', '2024-09-27 09:30:11', 200, 'Oscar', 'Tel Aviv District', 'James', 'Lesson 2'),
(60, 9, '2024-09-26 12:35:00', '2024-09-27 09:35:55', 150, 'Lucy', 'Haifa District', 'jen', 'Lesson 1'),
(61, 9, '2024-09-27 13:36:00', '2024-09-27 09:37:25', 150, 'Lucy', 'Haifa District', 'jen', 'Lesson 2');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

DROP TABLE IF EXISTS `materials`;
CREATE TABLE IF NOT EXISTS `materials` (
  `material_id` int NOT NULL AUTO_INCREMENT,
  `material_name` varchar(100) NOT NULL,
  `material_type` enum('video','document','question') DEFAULT NULL,
  `material_src` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`material_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

DROP TABLE IF EXISTS `progress`;
CREATE TABLE IF NOT EXISTS `progress` (
  `progress_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `progress_details` text,
  `progress_date` date DEFAULT NULL,
  `teacher_name` varchar(255) DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`progress_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`progress_id`, `user_id`, `progress_details`, `progress_date`, `teacher_name`, `student_name`, `grade`) VALUES
(19, 4, 'Lesson 1', '2024-07-01', 'Eleanor', 'William', '70'),
(20, 4, 'Lesson 2', '2024-07-02', 'Eleanor', 'William', '80'),
(21, 4, 'Lesson 3', '2024-07-03', 'Eleanor', 'William', '90'),
(22, 4, 'Lesson 4', '2024-07-04', 'Eleanor', 'William', '80'),
(23, 4, 'Lesson 5', '2024-07-05', 'Eleanor', 'William', '100'),
(24, 4, 'Lesson 6', '2024-07-06', 'Eleanor', 'William', '70'),
(25, 4, 'Lesson 7', '2024-07-07', 'Eleanor', 'William', '80'),
(26, 4, 'Lesson 8', '2024-07-08', 'Eleanor', 'William', '100'),
(27, 4, 'Lesson 9', '2024-07-09', 'Eleanor', 'William', '90'),
(28, 4, 'Lesson 10', '2024-07-10', 'Eleanor', 'William', '80'),
(29, 4, 'Lesson 11', '2024-07-11', 'Eleanor', 'William', '70'),
(30, 4, 'Lesson 12', '2024-07-12', 'Eleanor', 'William', '90'),
(31, 4, 'Lesson 13', '2024-07-13', 'Eleanor', 'William', '80'),
(32, 4, 'Lesson 14', '2024-07-14', 'Eleanor', 'William', '100'),
(33, 4, 'Lesson 15', '2024-07-15', 'Eleanor', 'William', '70'),
(34, 4, 'Lesson 16', '2024-07-16', 'Eleanor', 'William', '100'),
(35, 4, 'Lesson 17', '2024-07-17', 'Eleanor', 'William', '90'),
(36, 4, 'Lesson 18', '2024-07-18', 'Eleanor', 'William', '80'),
(37, 4, 'Lesson 19', '2024-07-19', 'Eleanor', 'William', '70'),
(38, 4, 'Lesson 20', '2024-07-20', 'Eleanor', 'William', '100'),
(39, 5, 'Lesson 1', '2024-09-24', 'Oscar', 'James', '80'),
(40, 5, 'Lesson 2', '2024-09-25', 'Oscar', 'James', '70'),
(41, 10, 'Lesson 1', '2024-09-26', 'Lucy', 'jen', '70'),
(42, 10, 'Lesson 2', '2024-09-27', 'Lucy', 'jen', '90');

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

DROP TABLE IF EXISTS `replies`;
CREATE TABLE IF NOT EXISTS `replies` (
  `reply_id` int NOT NULL AUTO_INCREMENT,
  `forum_id` int NOT NULL,
  `user_id` int NOT NULL,
  `reply_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reply_id`),
  KEY `forum_id` (`forum_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `replies`
--

INSERT INTO `replies` (`reply_id`, `forum_id`, `user_id`, `reply_text`, `created_at`) VALUES
(4, 3, 4, 'The driving test was tough but I passed.', '2024-09-27 09:05:50');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `report_text` text,
  `report_date` date DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `teacher_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`report_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `user_id`, `report_text`, `report_date`, `student_name`, `teacher_name`) VALUES
(5, 3, 'He was a great student, I wish him the best.', '2024-09-27', 'William', 'Eleanor'),
(6, 8, 'He is a great student, I wish him the best.', '2024-09-27', 'James', 'Oscar'),
(7, 9, 'she is a great student, I wish her the best.', '2024-09-27', 'jen', 'Lucy');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(3, 'Student'),
(2, 'Teacher');

-- --------------------------------------------------------

--
-- Table structure for table `testresults`
--

DROP TABLE IF EXISTS `testresults`;
CREATE TABLE IF NOT EXISTS `testresults` (
  `result_id` int NOT NULL AUTO_INCREMENT,
  `test_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `test_date` date DEFAULT NULL,
  `result` enum('Pass','Fail') NOT NULL,
  PRIMARY KEY (`result_id`),
  KEY `user_id` (`user_id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `testresults`
--

INSERT INTO `testresults` (`result_id`, `test_id`, `user_id`, `test_date`, `result`) VALUES
(21, 23, 4, '2024-07-22', 'Pass');

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

DROP TABLE IF EXISTS `tests`;
CREATE TABLE IF NOT EXISTS `tests` (
  `test_id` int NOT NULL AUTO_INCREMENT,
  `test_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  `test_date` datetime DEFAULT NULL,
  `location` enum('Northern District','Southern District','Center District','Haifa District','Tel Aviv District','Jerusalem District') DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `tester_name` varchar(20) NOT NULL,
  `price` int NOT NULL,
  PRIMARY KEY (`test_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`test_id`, `test_name`, `created_at`, `user_id`, `test_date`, `location`, `student_name`, `tester_name`, `price`) VALUES
(23, 'William Internal driving test', '2024-09-27 09:00:37', 3, '2024-07-22 11:58:00', 'Northern District', 'William', 'Eleanor', 150);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `driving_license_number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `role_id`, `created_at`, `status`, `driving_license_number`) VALUES
(3, 'Eleanor', '123', 'Eleanor Thompson', 'eleanorthompson@gmail.com', 2, '2024-06-11 03:45:55', 'approved', '089949703'),
(4, 'William', '123', 'William Jackson', 'williamjackson@gmail.com', 3, '2024-06-11 04:18:09', 'approved', ''),
(5, 'James', '123', 'James Wright', 'jameswright@gmail.com', 3, '2024-06-11 21:05:06', 'approved', ''),
(7, 'MahmoudNaamneh', '123', 'Mahmoud Naamneh', 'mahmoudnaamneh5@gmail.com', 1, '2024-06-11 21:29:19', 'approved', ''),
(8, 'Oscar', '123', 'Oscar Edwards', 'oscaredwards@gmail.com', 2, '2024-06-26 08:56:16', 'approved', '089949701'),
(9, 'Lucy', '123', 'Lucy Green', 'lucygreen@gmail.com', 2, '2024-06-26 12:11:04', 'approved', '089949700'),
(10, 'jen', '123', 'jen White', 'jenwhite@gmail.com', 3, '2024-06-26 12:12:36', 'approved', ''),
(11, 'Simon_Sh', '123', 'Simon Shakkour', 'semaanzahe@gmail.com', 1, '2024-09-26 11:51:18', 'approved', '');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `forum`
--
ALTER TABLE `forum`
  ADD CONSTRAINT `forum_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `forum` (`forum_id`),
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `testresults`
--
ALTER TABLE `testresults`
  ADD CONSTRAINT `testresults_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `testresults_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`test_id`);

--
-- Constraints for table `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
