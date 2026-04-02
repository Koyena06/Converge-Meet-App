CREATE database meetup_db;
use meetup_db;
-- Converge Meetup App - Full Database Export
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. DATABASE STRUCTURE
-- --------------------------------------------------------

-- Users Table
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Friends Table
CREATE TABLE `friends` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int DEFAULT NULL,
  `receiver_id` int DEFAULT NULL,
  `status` enum('pending','accepted') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Interests Table
CREATE TABLE `interests` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  `interest_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Places Table
CREATE TABLE `places` (
  `place_id` int NOT NULL AUTO_INCREMENT,
  `place_name` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  PRIMARY KEY (`place_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User Location Table (Renamed as requested)
CREATE TABLE `user_location` (
  `user_id` int NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Events Table
CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `creator_id` int DEFAULT NULL,
  `place_id` int DEFAULT NULL,
  `event_time` datetime DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User Interests Mapping
CREATE TABLE `user_interests` (
  `user_id` int NOT NULL,
  `interest_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`interest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. DATA DUMPING
-- --------------------------------------------------------

-- Dumping Users
INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'KOYENA SUTRADHAR', 'koyena.sutradhar@gmail.com', '$2y$10$wI6eMwN7RlnDDZab0hvXKOOXpe9YEU3a912qTcFziwOSrVcuJK.je'),
(2, 'Mrinalee Mishra', 'mrinalee@gmail.com', '$2y$10$QQB9W4x3xd1iNHtOxIYyP.4hFZ2hQ/y.HzCL3gZRiNW'),
(3, 'Sreeparna Chatterjee', 'sreeparna@gmail.com', '$2y$10$5fa5m8Txk86i91lFuXubguGbh1ulUNSWAhGr5A9XQt8'),
(4, 'Ashutosh De', 'ashutosh@gmail.com', 'ashutosh');

-- Dumping Interests
INSERT INTO `interests` (`id`, `interest_name`) VALUES
(1, 'Web Development'), (2, 'AI'), (3, 'Data Science'), (4, 'Music'), (5, 'Gaming'), (6, 'Sports'), (7, 'Photography');

-- Dumping User Interests
INSERT INTO `user_interests` (`user_id`, `interest_id`) VALUES (1, 4), (2, 4), (2, 5);

-- Dumping Friends (Status updated to match your friend circle request)
-- 1 is friends with 2,3 | 2 is friends with 1 | 3 is friends with 2,4
INSERT INTO `friends` (`sender_id`, `receiver_id`, `status`) VALUES
(1, 2, 'accepted'),
(1, 3, 'accepted'),
(3, 2, 'accepted'),
(3, 4, 'accepted');

-- Dumping Places (All set to Bhubaneswar coordinates)
INSERT INTO `places` (`place_id`, `place_name`, `category`, `lat`, `lng`) VALUES
(1, 'City Cafe ', 'Coffee', 20.29610000, 85.82450000),
(2, 'Tech Park ', 'Web Development', 20.35330000, 85.81150000),
(3, 'Gaming Arena', 'Gaming', 20.29000000, 85.84500000),
(4, 'Central Library', 'Data Science', 20.24500000, 85.80100000);

-- Dumping Events
INSERT INTO `events` (`id`, `creator_id`, `place_id`, `event_time`, `description`) VALUES
(1, 1, 1, '2026-03-26 15:53:00', 'code over coffee'),
(2, 2, 1, '2026-04-06 00:32:00', 'My Birthday at the City Cafe'),
(3, 2, 3, '2026-04-20 13:52:00', 'Gaming together');

-- Dumping initial user_location
INSERT INTO `user_location` (`user_id`, `latitude`, `longitude`) VALUES
(1, 20.2961, 85.8245),
(2, 20.3547, 85.8131),
(3, 22.2535, 84.9012),
(4, 20.1757, 85.7762);

-- 3. FINAL CONSTRAINTS
-- --------------------------------------------------------
ALTER TABLE `user_interests`
  ADD CONSTRAINT `ui_u_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ui_i_fk` FOREIGN KEY (`interest_id`) REFERENCES `interests` (`id`) ON DELETE CASCADE;

COMMIT;