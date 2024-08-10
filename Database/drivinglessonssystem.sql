-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 26, 2024 at 03:57 PM
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `feedback_text`, `feedback_date`) VALUES
(2, 4, 'fdfhdfhdh', '2024-06-26 08:36:45'),
(3, 5, 'hdhdfjt', '2024-06-26 08:37:16'),
(4, 3, 'the site is good', '2024-06-27 14:02:23');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `forum`
--

INSERT INTO `forum` (`forum_id`, `forum_name`, `description`, `created_at`, `user_id`) VALUES
(1, 'FAILING YOUR DRIVING TEST', 'WHAT THE MOST COMMON REASONS FOR FAILING YOUR DRIVING TEST', '2024-06-14 05:22:39', 5),
(2, 'deviate abruptly', 'What risks are caused when you deviate abruptly between lanes?', '2024-06-27 14:04:30', 3);

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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `user_id`, `lesson_date`, `created_at`, `price`, `teacher_name`, `location`, `student_name`, `lesson_title`) VALUES
(31, 3, '2024-07-01 09:22:00', '2024-07-21 06:23:27', 100, 'Mahmoudnaamneh1', 'Northern District', 'karimkarim12', 'Lesson 1: Introduction to Driving	'),
(32, 3, '2024-07-02 09:24:00', '2024-07-21 06:25:11', 200, 'Mahmoudnaamneh1', 'Northern District', 'karimkarim12', 'Lesson 2: Highway Driving'),
(33, 3, '2024-07-22 09:31:00', '2024-07-21 06:32:44', 150, 'Mahmoudnaamneh1', 'Northern District', 'karimkarim12', 'Lesson 3:parking'),
(34, 8, '2024-08-04 14:37:00', '2024-07-21 11:38:37', 200, 'lala1', 'Tel Aviv District', 'jen1', 'Lesson 1: Introduction to Driving	'),
(35, 8, '2024-08-05 14:41:00', '2024-07-21 11:42:10', 200, 'lala1', 'Haifa District', 'oferofer', 'Lesson 1: Introduction to Driving	');

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

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`material_id`, `material_name`, `material_type`, `material_src`, `created_at`, `user_id`) VALUES
(9, 'Theory material', 'document', 'uploads/goldenlearning.pdf', '2024-06-30 15:48:10', 3);

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`progress_id`, `user_id`, `progress_details`, `progress_date`, `teacher_name`, `student_name`, `grade`) VALUES
(14, 4, 'Lesson 1: Introduction to Driving', '2024-07-01', 'Mahmoudnaamneh1', 'karimkarim12', '70'),
(15, 4, 'Lesson 2: Highway Driving', '2024-07-02', 'Mahmoudnaamneh1', 'karimkarim12', '90'),
(16, 4, 'Lesson 3:parking', '2024-07-22', 'Mahmoudnaamneh1', 'karimkarim12', '60'),
(17, 10, 'Lesson 1: Introduction to Driving', '2024-08-04', 'lala1', 'jen1', '80'),
(18, 5, 'Lesson 1: Introduction to Driving', '2024-08-05', 'lala1', 'oferofer', '100');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `replies`
--

INSERT INTO `replies` (`reply_id`, `forum_id`, `user_id`, `reply_text`, `created_at`) VALUES
(1, 1, 5, 'NOT MAKING EFFECTIVE OBSERVATIONS AT JUNCTIONS', '2024-06-14 05:26:32'),
(2, 1, 3, 'NOT USING MIRRORS CORRECTLY WHEN CHANGING DIRECTION', '2024-06-14 05:28:01'),
(3, 2, 5, 'The deviation might delay other drivers.\r\n', '2024-06-27 14:05:54');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `user_id`, `report_text`, `report_date`, `student_name`, `teacher_name`) VALUES
(2, 3, 'good boy', '2024-06-26', 'oferofer', 'Mahmoudnaamneh1'),
(3, 8, 'good girl', '2024-06-27', 'jen1', 'lala1'),
(4, 3, 'ofer is just amazing', '2024-06-27', 'oferofer', 'Mahmoudnaamneh1');

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `testresults`
--

INSERT INTO `testresults` (`result_id`, `test_id`, `user_id`, `test_date`, `result`) VALUES
(19, 22, 10, '2024-07-23', 'Pass');

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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`test_id`, `test_name`, `created_at`, `user_id`, `test_date`, `location`, `student_name`, `tester_name`, `price`) VALUES
(21, 'karim driving test', '2024-07-23 22:23:44', 3, '2024-07-31 01:23:00', 'Northern District', 'karimkarim12', 'Mahmoudnaamneh1', 200),
(22, 'jen driving test', '2024-07-23 22:39:44', 8, '2024-07-23 13:38:00', 'Tel Aviv District', 'jen1', 'lala1', 200);

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `role_id`, `created_at`, `status`, `driving_license_number`) VALUES
(3, 'Mahmoudnaamneh1', 'MAty12345', 'Mahmoud Naamneh', 'mahmoudnaamneh22@gmail.com', 2, '2024-06-11 03:45:55', 'approved', '089949703'),
(4, 'karimkarim12', 'MAty12345', 'karim karim', 'karimkarim@gmail.com', 3, '2024-06-11 04:18:09', 'approved', ''),
(5, 'oferofer', 'MAty12345', 'ofer ofer', 'oferofer@gmail.com', 3, '2024-06-11 21:05:06', 'approved', ''),
(7, 'MahmoudNaamneh', 'MAty12345', 'Mahmoud Naamneh', 'mahmoudnaamneh5@gmail.com', 1, '2024-06-11 21:29:19', 'approved', ''),
(8, 'lala1', 'MAty12345', 'lala lala', 'lalalala@gmail.com', 2, '2024-06-26 08:56:16', 'approved', '089949701'),
(9, 'onana1', 'MAty12345', 'onana kama', 'onanakama@gmail.com', 2, '2024-06-26 12:11:04', 'approved', '089949700'),
(10, 'jen1', 'MAty12345', 'jen len', 'jenlen@gmail.com', 3, '2024-06-26 12:12:36', 'approved', '');

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
