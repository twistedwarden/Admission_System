-- Admission Management System Database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `admission_system`
CREATE DATABASE IF NOT EXISTS `admission_system`;
USE `admission_system`;

-- Table structure for table `admin`
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert admin user
INSERT INTO `admin` (`username`, `password`, `email`, `name`) VALUES
('admin', '$2y$10$YourHashedPasswordHere', 'admin@example.com', 'System Administrator');

-- Table structure for table `programs`
CREATE TABLE `programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `department` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample programs
INSERT INTO `programs` (`code`, `name`, `description`, `department`) VALUES
('BSCS', 'Bachelor of Science in Computer Science', 'A program focusing on theoretical foundations of computation and algorithms.', 'Computer Studies'),
('BSIT', 'Bachelor of Science in Information Technology', 'A program focusing on practical applications of computing technology.', 'Computer Studies'),
('BSN', 'Bachelor of Science in Nursing', 'A program preparing students for careers in nursing.', 'Health Sciences'),
('BSBA', 'Bachelor of Science in Business Administration', 'A program focusing on business management and administration.', 'Business'),
('BSECE', 'Bachelor of Science in Electronics Engineering', 'A program focusing on electronics and communications engineering.', 'Engineering');

-- Table structure for table `requirements`
CREATE TABLE `requirements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 1,
  `applicant_type` enum('new','returning','transfer','all') NOT NULL DEFAULT 'all',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample requirements
INSERT INTO `requirements` (`name`, `description`, `required`, `applicant_type`) VALUES
('High School Transcript', 'Official transcript of records from high school', 1, 'new'),
('Birth Certificate', 'NSO/PSA authenticated birth certificate', 1, 'all'),
('2x2 Photo', 'Recent 2x2 colored photo with white background', 1, 'all'),
('Medical Certificate', 'Medical certificate from licensed physician', 1, 'all'),
('Previous School ID', 'Valid ID from previous school', 1, 'transfer'),
('Transfer Credentials', 'Honorable dismissal and transcript from previous college', 1, 'transfer'),
('Recommendation Letter', 'Recommendation letter from previous institution', 0, 'transfer'),
('Previous School Records', 'Transcript of records from previous semesters', 1, 'returning');

-- Table structure for table `applications`
CREATE TABLE `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `program_id` int(11) NOT NULL,
  `applicant_type` enum('new','returning','transfer') NOT NULL,
  `previous_school` varchar(100) DEFAULT NULL,
  `previous_program` varchar(100) DEFAULT NULL,
  `status` enum('pending','under_review','accepted','rejected','incomplete') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','processing','paid') NOT NULL DEFAULT 'unpaid',
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_reference` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_no` (`reference_no`),
  KEY `program_id` (`program_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `documents`
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `requirement_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `application_id` (`application_id`),
  KEY `requirement_id` (`requirement_id`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `status_updates`
CREATE TABLE `status_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `status` enum('pending','under_review','accepted','rejected','incomplete') NOT NULL,
  `notes` text DEFAULT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `application_id` (`application_id`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `status_updates_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `status_updates_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `admin` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `email_logs`
CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `sent_to` varchar(100) NOT NULL,
  `status` enum('sent','failed') NOT NULL,
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `application_id` (`application_id`),
  CONSTRAINT `email_logs_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Update admin password
UPDATE `admin` SET `password` = '$2y$10$S4y0wnOkQVJxH7rQg9Y5V.yQV0RL8HLNEhvxcpEq7aLeWnPy6JJ5m' WHERE `username` = 'admin';

COMMIT;